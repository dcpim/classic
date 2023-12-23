#!/usr/bin/python3
# Update a file

import connix
import sys
import os
import run

print(connix.header())
form = connix.form()

if not run.validate(True):
	run.error("Unauthorized.")

if 'id' not in form or 'name' not in form or 'date' not in form or 'prjid' not in form:
	run.error("Missing input.")

id = int(form['id'])
prjid = int(form['prjid'])
name = form['name']
date = form['date']
notes = ""
if "notes" in form:
	notes = form['notes']

# Update file in the database
run.sql("UPDATE project_files SET notes = %s, prjid = %s, date = %s, name = %s WHERE id = %s;", notes, prjid, date, name, id)

# Update last updated date
run.sql("UPDATE projects SET last_update = %s WHERE id = %s;", connix.now(), prjid)

run.done()
