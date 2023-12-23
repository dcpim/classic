#!/usr/bin/python3
# Update a journal entry

import connix
import sys
import os
import run

print(connix.header())
form = connix.form()

if not run.validate(True):
	run.error("Unauthorized.")

if 'title' not in form or 'entry' not in form or 'id' not in form or 'prjid' not in form or 'mood' not in form:
	run.error("Missing input.")

title = form['title']
entry = form['entry'].replace('\n','<br>')
id = int(form['id'])
prjid = int(form['prjid'])
mood = form['mood']

# Update bill in the database
run.sql("UPDATE journal SET mood = %s, entry = %s, title = %s WHERE id = %s;", mood, entry, title, id);

# Update last updated date
run.sql("UPDATE projects SET last_update = %s WHERE id = %s;", connix.now(), prjid)

run.done()
