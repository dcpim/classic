#!/usr/bin/python3
# Automation engine

import pymysql
import connix
import time
import os

all_start = time.time()
all_success = 0
all_failure = 0

folder = "/var/www/html/pipelines/"
pipeline = ""

with open("/etc/apache2/sites-available/default-ssl.conf", 'r') as fd:
	data = fd.read()
	db_user = connix.in_tag(data, "DB_USER ", "\n")
	db_pass = connix.in_tag(data, "DB_PASS ", "\n")
	db_host = connix.in_tag(data, "DB_HOST ", "\n")
	db_database = connix.in_tag(data, "DB_DATABASE ", "\n")

db = pymysql.connect(host=db_host, user=db_user, password=db_pass, database=db_database)
cur = db.cursor()

CONFIG = {}
cur.execute("SELECT k,v FROM config;")
results = cur.fetchall()
for result in results:
	CONFIG[result[0]] = result[1]

envs = "export HTTP_REFERER=https://{}/automate;export DB_DATABASE={};export DB_HOST={};export DB_USER={};export DB_PASS={};".format(CONFIG['SERVER_HOST'], db_database, db_host, db_user, db_pass)

cur.execute("SELECT id,pipeline,params,next_run,repeats,notify,history FROM automate WHERE repeats != 0 AND node = 'local';")
pipelines = cur.fetchall()

for pipeline in pipelines:
	if int(pipeline[3]) < int(time.time()):
		print(pipeline[1])
		cur = db.cursor()
		params = "export QUERY_STRING=\"{}\";".format(pipeline[2])
		cur.execute("UPDATE automate SET last_run = %s WHERE id = %s;", (connix.now(), pipeline[0]))
		cur.execute("UPDATE automate SET next_run = %s WHERE id = %s;", ((int(time.time()) + int(pipeline[4])), pipeline[0]))
		try:
			start = time.time()
			output = connix.cmd("{}{}{}{} 2>&1".format(envs, params, folder, pipeline[1]))
			end = time.time()
			duration = end - start
			cur.execute("UPDATE automate SET output = %s, duration = %s WHERE id = %s;", (output, duration, pipeline[0]))
			if "DONE" in output: # Pipeline was successful
				cur.execute("UPDATE automate SET result = %s WHERE id = %s;", (1, pipeline[0]))
				if pipeline[6] == 1: # Save history
					cur.execute("INSERT INTO automate_runs (id, output, date, result) VALUES (%s, %s, %s, %s);", (pipeline[0], output, connix.now(), 1))
				all_success += 1
			else: # Pipeline failed
				cur.execute("UPDATE automate SET result = %s WHERE id = %s;", (0, pipeline[0]))
				if pipeline[6] == 1: # Save history
					cur.execute("INSERT INTO automate_runs (id, output, date, result) VALUES (%s, %s, %s, %s);", (pipeline[0], output, connix.now(), 0))
				if pipeline[5] == 1: # Send notification
					errmsg = connix.in_tag(output, "ERROR:</b> ", " <span").replace("'", '').replace('"', '')[:99]
					if errmsg == "":
						errmsg = connix.in_tag(output, "Error: ", "\n").replace("'", '').replace('"', '')[:99]
					connix.cmd("aws sns publish --region '{}' --topic-arn '{}' --message 'Automation [{}] failed: {}'".format(CONFIG['AWS_REGION'], CONFIG['SNS_ARN'], pipeline[1], errmsg))
				all_failure += 1
		except Exception as err: # Automation engine failed
			all_failure += 1
			cur.execute("UPDATE automate SET result = %s WHERE id = %s;", (0, pipeline[0]))
			try:
				cur.execute("UPDATE automate SET output = %s WHERE id = %s;", (err, pipeline[0]))
			except:
				cur.execute("UPDATE automate SET output = %s WHERE id = %s;", ("Unknown automation error.", pipeline[0]))
			if pipeline[5] == 1:
				try:
					connix.cmd("aws sns publish --region '{}' --topic-arn '{}' --message 'Automation failed for [{}] with error [{}]'".format(CONFIG['AWS_REGION'], CONFIG['SNS_ARN'], pipeline[1], err))
				except:
					connix.cmd("aws sns publish --region '{}' --topic-arn '{}' --message 'Automation failed for [{}] with an unknown error'".format(CONFIG['AWS_REGION'], CONFIG['SNS_ARN'], pipeline[1]))
		db.commit()

all_end = time.time()
all_duration = all_end - all_start
cur = db.cursor()
cur.execute("INSERT INTO automate_stats (date, duration, success, failure) VALUES (%s, %s, %s, %s);", (connix.now(), all_duration, all_success, all_failure))
db.commit()

db.close()

