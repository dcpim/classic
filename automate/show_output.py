#!/usr/bin/python3
# Show the output of a specific historical run

import connix
import time
import sys
import os
import run

print(connix.header())
form = connix.form()

if not run.validate(True):
	run.error("Unauthorized.")

if 'run' in form:
	runid = int(form['run'])
	result = run.query("SELECT output,date,run FROM automate_runs WHERE run = %s;", runid)
	print("<div><h3>Output produced on {}:</h3><pre>{}</pre></div>".format(result[0][1],result[0][0]))
elif 'id' in form:
	id = int(form['id'])
	result = run.query("SELECT output,last_run,history FROM automate WHERE id = %s;", id)
	print("<div><h3>Output produced on {}:</h3><pre>{}</pre></div>".format(result[0][1],result[0][0]))
	if result[0][2] == 1:
		results = run.query("SELECT run,date,result FROM automate_runs WHERE id = %s ORDER BY run DESC LIMIT 30;", id)
		print("<h3>Last 30 runs:</h3><ul>")
		for result in results:
			status = "Success"
			if result[2] != 1:
				status = "Failed"
			print("<li><a href='./show_output.py?run={}'>{}</a> [{}]</li>".format(result[0],result[1],status))
		print("</ul>")
else:
	run.error("Missing input.")

run.done()
