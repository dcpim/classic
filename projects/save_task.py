#!/usr/bin/python3
# Update a task

import connix
import sys
import os
import run

print(connix.header())
form = connix.form()

if not run.validate(True):
	run.error("Unauthorized.")

if 'id' not in form or 'task' not in form or 'repeat' not in form or 'prjid' not in form:
	run.error("Missing input.")

id = int(form['id'])
prjid = int(form['prjid'])
task = form['task']
repeat = form['repeat']
date = ""
if 'date' in form:
	date = form['date']
details = ""
if 'details' in form:
	details = str(form['details']).replace('"', "'")
url = ""
if 'url' in form:
	url = form['url']

# Update task in the database
run.sql("UPDATE tasks SET url = %s, details = %s, task = %s, date = %s, repeatset = %s WHERE id = %s;", url, details, task, date, repeat, id)

# Update journal
run.sql("INSERT INTO journal (prjid, title, entry, date, mood, type) VALUES (%s, %s, %s, %s, %s, %s);", prjid, "Task updated:", task, connix.now(), "tasks", 1)

run.done()
