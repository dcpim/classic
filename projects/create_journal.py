#!/usr/bin/python3
# Add a journal entry

import connix
import sys
import os
import run

print(connix.header())
form = connix.form()

if not run.validate(True):
	run.error("Unauthorized.")

if 'title' not in form or 'entry' not in form or 'prjid' not in form or 'mood' not in form:
	run.error("Missing input.")

title = form['title']
entry = form['entry'].replace('\n', '<br>')
mood = form['mood']
prjid = int(form['prjid'])

# Create journal in the database
run.sql("INSERT INTO journal (prjid, title, entry, mood, date, type) VALUES (%s, %s, %s, %s, %s, %s);", prjid, title, entry, mood, connix.now(), 0)

# Update last updated date
run.sql("UPDATE projects SET last_update = %s WHERE id = %s;", connix.now(), prjid)

print("<meta http-equiv='refresh' content='0; URL=./journal.php?prjid={}' />".format(prjid))
run.done()
