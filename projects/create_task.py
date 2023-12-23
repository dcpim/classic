#!/usr/bin/python3
# Add a new task

import connix
import sys
import os
import run

print(connix.header())
form = connix.form()

if not run.validate(True):
	run.error("Unauthorized.")

if 'prjid' not in form:
	run.error("Missing input.")

prjid = int(form['prjid'])
title = "New task"
if 'title' in form and form['title'] != "":
	title = form['title']

# Update file in the database
run.sql("INSERT INTO tasks (task, date, prjid, repeatset) VALUES (%s, %s, %s, %s);", title, connix.now().split(' ')[0], prjid, "Never")

# Update last updated date
run.sql("UPDATE projects SET last_update = %s WHERE id = %s;", connix.now(), prjid)

# Update journal
run.sql("INSERT INTO journal (prjid, title, entry, date, mood, type) VALUES (%s, %s, %s, %s, %s, %s);", prjid, "New task created:", title, connix.now(), "tasks", 1)

print("<meta http-equiv='refresh' content='0; URL=/projects/?id={}' />".format(prjid))
run.done()
