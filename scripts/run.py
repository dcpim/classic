#!/usr/bin/python3
# This module provides simple pipeline style utility functions

from pathlib import Path
import pymysql
import connix
import inspect
import time
import sys
import os

# Pipeline functions
pipeline = 0
start_time = time.time()*1000

# Time how long a pipeline takes to run
def timing():
	global start_time
	end_time = time.time()*1000
	total_time = end_time - start_time
	if total_time > 60000:
		return "{}m".format(int(total_time/60000))
	elif total_time > 1000:
		return "{}s".format(int(total_time/1000))
	else:
		return "{}ms".format(int(total_time))

# Add an entry to the event log
def log(event, msg):
	try:
		fname = os.environ['REQUEST_URI'].split('/')[-1].split('?')[0]
	except:
		fname = inspect.currentframe().f_back.f_back.f_code.co_filename.split('/')[-1]
	try:
		raddr = os.environ['REMOTE_ADDR']
	except:
		raddr = "localhost"
	try:
		db = pymysql.connect(host=os.environ['DB_HOST'], user=os.environ['DB_USER'], password=os.environ['DB_PASS'], database=os.environ['DB_DATABASE'])
		cur = db.cursor()
		cur.execute("INSERT INTO log (username, ip, event, result, date) VALUES (%s, %s, %s, %s, %s);", ("", raddr, "{}[{}]".format(fname, event), msg[:500], connix.now()))
		db.commit()
		db.close()
	except Exception as err:
		print(err)

# Validate if the web user is from the same site
def validate(loggedin=False):
	if "HTTP_REFERER" not in os.environ:
		return False
	if config('SERVER_HOST') not in os.environ['HTTP_REFERER']:
		return False
	if loggedin:
		if "HTTP_COOKIE" not in os.environ:
			return False
		if "dcpim_net_session" not in os.environ['HTTP_COOKIE']:
			return False
		try:
			db = pymysql.connect(host=os.environ['DB_HOST'], user=os.environ['DB_USER'], password=os.environ['DB_PASS'], database=os.environ['DB_DATABASE'])
			cur = db.cursor()
			cur.execute("SELECT username FROM sessions GROUP BY username;")
			user_found = False
			results = cur.fetchall()
			for result in results:
				if result[0] in os.environ['HTTP_COOKIE']:
					user_found = True
			db.close()
			if not user_found:
				return False
		except:
			error("Failed to connect to the database.")
	try:
		db = pymysql.connect(host=os.environ['DB_HOST'], user=os.environ['DB_USER'], password=os.environ['DB_PASS'], database=os.environ['DB_DATABASE'])
		cur = db.cursor()
		cur.execute("SELECT COUNT(*) FROM sessions WHERE valid_until > UNIX_TIMESTAMP() AND ip = %s;", (os.environ['REMOTE_ADDR']))
		results = cur.fetchall()
		db.close()
		if results[0][0] == 0 and os.environ['REMOTE_ADDR'] != config('MY_IP') and os.environ['REMOTE_ADDR'] != config('SERVER_IP') and os.environ['REMOTE_ADDR'] != "127.0.0.1":
			return False
	except:
		error("Failed to connect to the database.")
	if "HTTP_COOKIE" in os.environ and "dcpim_net_darkmode=0" not in os.environ['HTTP_COOKIE']:
		print("<style>html {background-color:#182025;color:#E0E0E0;} .run_msg, .run_msg b, .run_msg i, .run_msg span {color:#000000;} a {color:#3EAEEE;}</style>")
	return True

# Display an error message and quit
def error(msg):
	print("<div style='background-color:#E07070;padding:5px;margin:1px;' class='run_msg'><b>ERROR:</b> {} <span style='float:right;font-size:12px'>Go back <a href='/'>home</a>. {}.</span></div>".format(msg, timing()))
	log("error", str(msg))
	quit(1)

# Display a success message and quit
def done(redir=False):
	if redir:
		print("<meta http-equiv='refresh' content='0; URL=./' />")
	print("<div style='background-color:#70E070;padding:5px;margin:1px;' class='run_msg'><b>DONE!</b> <span style='float:right;font-size:12px'>Go back <a href='/'>home</a>. {}.</span></div>".format(timing()))
	quit(0)

# Run a shell command
def cmd(cmd):
	global pipeline
	pipeline += 1
	log("cmd", cmd)
	print("<div style='background-color:#A0A0A0;padding:5px;margin:1px' class='run_msg'><b>{}. CMD:</b> {} 2>&1</div>".format(pipeline, cmd))
	print("<pre style='margin-left:5px;max-width:99%;max-height:300px;overflow:auto'>")
	sys.stdout.flush()
	fd = os.popen(cmd + " 2>&1")
	print(fd.read().rstrip('\n'))
	print("</pre>")
	sys.stdout.flush()
	status = fd.close()
	if status and status != 0:
		error("Command returned status code: {}".format(status))

# Run a SQL command
def sql(q, *params):
	global pipeline
	pipeline += 1
	log("sql", "{} {}".format(q, params))
	print("<div style='background-color:#A0A0A0;padding:5px;margin:1px' class='run_msg'><b>{}. SQL:</b> {}<br>{}</div>".format(pipeline, q, params))
	print("<pre style='margin-left:5px;max-width:99%;max-height:300px;overflow:auto'>")
	sys.stdout.flush()
	try:
		db = pymysql.connect(host=os.environ['DB_HOST'], user=os.environ['DB_USER'], password=os.environ['DB_PASS'], database=os.environ['DB_DATABASE'])
		cur = db.cursor()
		print(cur.execute(q, (params)))
		db.commit()
		db.close()
	except Exception as err:
		print("</pre>")
		error(err)
	print("</pre>")
	sys.stdout.flush()

# Run a SQL query and return results
def query(q, *params):
	global pipeline
	pipeline += 1
	results = []
	print("<div style='background-color:#A0A0A0;padding:5px;margin:1px' class='run_msg'><b>{}. SQL:</b> {}<br>{}</div>".format(pipeline, q, params))
	print("<pre style='margin-left:5px;max-width:99%;max-height:300px;overflow:auto'>")
	sys.stdout.flush()
	try:
		db = pymysql.connect(host=os.environ['DB_HOST'], user=os.environ['DB_USER'], password=os.environ['DB_PASS'], database=os.environ['DB_DATABASE'])
		cur = db.cursor()
		print(cur.execute(q, (params)))
		results = cur.fetchall()
		db.close()
	except Exception as err:
		print("</pre>")
		error(err)
	print("</pre>")
	sys.stdout.flush()
	return results

# Fetch a config value
def config(key):
	results = []
	value = ""
	try:
		db = pymysql.connect(host=os.environ['DB_HOST'], user=os.environ['DB_USER'], password=os.environ['DB_PASS'], database=os.environ['DB_DATABASE'])
		cur = db.cursor()
		cur.execute("SELECT v FROM config WHERE k = %s;", (key))
		results = cur.fetchall()
		for result in results:
			value = result[0]
		db.close()
	except Exception as err:
		error(err)
	return value

# Identify a file type and rename the file with proper extension
def filetype(localfile):
	filetype = connix.cmd("file /tmp/{}".format(localfile))
	if "JPEG" in filetype:
		filename = "{}.jpg".format(localfile)
	elif "GIF" in filetype:
		filename = "{}.gif".format(localfile)
	elif "PNG" in filetype:
		filename = "{}.png".format(localfile)
	elif "PDF" in filetype:
		filename = "{}.pdf".format(localfile)
	elif "Java serialization" in filetype:
		filename = "{}.pdf".format(localfile)
	elif "Zip" in filetype:
		filename = "{}.zip".format(localfile)
	elif "ASCII" in filetype:
		filename = "{}.txt".format(localfile)
	elif "executable" in filetype:
		filename = "{}.exe".format(localfile)
	elif "tar archive" in filetype:
		filename = "{}.tar".format(localfile)
	elif "gzip" in filetype:
		filename = "{}.gz".format(localfile)
	elif "Audio file" in filetype:
		filename = "{}.mp3".format(localfile)
	elif "MPEG ADTS" in filetype:
		filename = "{}.mp3".format(localfile)
	elif "QuickTime movie" in filetype:
		filename = "{}.mp4".format(localfile)
	elif "Video file" in filetype:
		filename = "{}.mp4".format(localfile)
	elif "MPEG v4" in filetype:
		filename = "{}.mp4".format(localfile)
	elif "Paint.NET" in filetype:
		filename = "{}.pdn".format(localfile)
	elif "text" in filetype:
		filename = "{}.txt".format(localfile)
	else:
		error("Error: Invalid file type, aborting. Must be one of: jpg, gif, png, pdf, zip, txt, mp3, mp4, exe.<br>{}".format(filetype))
	cmd("mv /tmp/{} /tmp/{}".format(localfile, filename))
	return filename

# Send a notification to site owner
def notify(msg):
	cmd("aws sns publish --region '{}' --topic-arn '{}' --message \"{}\"".format(config('AWS_REGION'), config('SNS_ARN'), msg.replace('"',"'")))
