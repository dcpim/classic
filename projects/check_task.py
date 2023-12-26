#!/usr/bin/python3
# Mark a task as checked

import connix
import sys
import os
import run
from datetime import datetime
from dateutil.relativedelta import relativedelta

print(connix.header())
form = connix.form()

if not run.validate(True):
	run.error("Unauthorized.")

if 'id' not in form or 'prjid' not in form:
	run.error("Missing input.")

id = int(form['id'])
prjid = int(form['prjid'])

# Get task information
tasks = run.query("SELECT task,date,repeatset,prjid FROM tasks WHERE id = %s", id)
try:
	task = tasks[0][0]
	date = tasks[0][1]
	repeat = tasks[0][2]
	prjid = tasks[0][3]
except:
	print("<meta http-equiv='refresh' content='0; URL={}' />".format(os.environ['HTTP_REFERER']))
	run.done()
date_datetime = datetime.strptime(date, "%Y-%m-%d")
if "Next day" in repeat:
	new_date_datetime = date_datetime + relativedelta(days=1)
	new_date = new_date_datetime.strftime('%Y-%m-%d')
elif "Next workday" in repeat:
	if date_datetime.weekday() == 4:
		new_date_datetime = date_datetime + relativedelta(days=3)
	else:
		new_date_datetime = date_datetime + relativedelta(days=1)
	new_date = new_date_datetime.strftime('%Y-%m-%d')
elif "Next week" in repeat:
	new_date_datetime = date_datetime + relativedelta(days=7)
	new_date = new_date_datetime.strftime('%Y-%m-%d')
elif "Next month" in repeat:
	new_date_datetime = date_datetime + relativedelta(months=1)
	new_date = new_date_datetime.strftime('%Y-%m-%d')
elif "Next year" in repeat:
	new_date_datetime = date_datetime + relativedelta(years=1)
	new_date = new_date_datetime.strftime('%Y-%m-%d')
else:
	new_date = "Delete"

# Update task in the database
if new_date == "Delete":
	run.sql("DELETE FROM tasks WHERE id = %s;", id)
else:
	run.sql("UPDATE tasks SET date = %s WHERE id = %s;", new_date, id)

# Update last updated date
run.sql("UPDATE projects SET last_update = %s WHERE id = %s;", connix.now(), prjid)

# Add a journal entry
run.sql("INSERT INTO journal (prjid, title, entry, date, mood, type) VALUES (%s, %s, %s, %s, %s, %s);", prjid, "Task completed!", task, connix.now(), "tasks", 1)

print("<meta http-equiv='refresh' content='0; URL={}' />".format(os.environ['HTTP_REFERER']))
run.done()
