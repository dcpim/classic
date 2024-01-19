#!/usr/bin/python3
# Automate remote endpoint

import pymysql
import connix
import time
import run
import os
import base64

print("Content-Type: application/json; charset=utf-8")
print()
form = connix.form()

if 'auth' not in form or 'action' not in form or 'node' not in form:
	run.error("Missing parameters.")

if form['auth'] != run.config('AUTOMATE_TOKEN'):
	run.error("Unauthorized.")

action = form['action']
node = form['node']

if node == "local":
	run.error("Unauthorized.")

db = pymysql.connect(host=os.environ['DB_HOST'], user=os.environ['DB_USER'], password=os.environ['DB_PASS'], database=os.environ['DB_DATABASE'])
cur = db.cursor()

if action == "fetch": # List remote pipelines
	cur.execute("SELECT id,pipeline,params,repeats FROM automate WHERE next_run < UNIX_TIMESTAMP() AND repeats != 0 AND node = %s;", (node))
	pipelines = cur.fetchall()
	output = []
	for pipeline in pipelines:
		cur.execute("UPDATE automate SET next_run = %s WHERE id = %s;", ((int(time.time()) + int(pipeline[3])), pipeline[0]))
		output.append({'id': pipeline[0], 'pipeline': pipeline[1], 'params': pipeline[2]})
	print(output)
elif action == "push": # Save results of a remote pipeline
	if 'status' not in form or 'pipeline' not in form or 'duration' not in form:
		run.error("Missing parameters.")
	try:
		if 'data' in form:
			data_bytes = form['data'].replace(' ','+').encode("utf-8")
			data_decoded_bytes = base64.b64decode(data_bytes)
			data = data_decoded_bytes.decode("utf-8")
		elif 'datafile' in form:
			datafile = connix.alphanum(form['datafile'])
			connix.cmd("aws s3 cp {}/{} /tmp/{}".format(run.config('AUTOMATE_BUCKET'), datafile, datafile))
			with open("/tmp/{}".format(datafile), 'r') as fd:
				data = fd.read()
			connix.cmd("rm -f /tmp/{}".format(datafile))
			connix.cmd("aws s3 rm {}/{}".format(run.config('AUTOMATE_BUCKET'), datafile))
		else:
			run.error("Missing parameters.")
		status = int(form['status'])
		duration = int(form['duration'])
		id = int(form['pipeline'])
		cur.execute("SELECT repeats,notify,history,pipeline FROM automate WHERE id = %s AND node = %s;", (id, node))
		pipelines = cur.fetchall()
		for pipeline in pipelines: # Ensures that the pipeline is valid
			cur.execute("UPDATE automate SET result = %s, output = %s, duration = %s, last_run = %s, next_run = %s WHERE id = %s;", (status, data, duration, connix.now(), (int(time.time()) + int(pipeline[0])), id))
			if pipeline[1] == 1: # Save history
				cur.execute("INSERT INTO automate_runs (id, output, date, result) VALUES (%s, %s, %s, %s);", (id, data, connix.now(), status))
			if pipeline[2] == 1 and status == 0: # Send notification on failure
				run.notify("Automation [{}] failed on node [{}].".format(pipeline[3], node))
	except Exception as err:
		run.notify("Automation engine failed: {}".format(str(err)))
		run.error("Internal error.")
else:
	run.error("Unknown action.")

db.commit()
db.close()
