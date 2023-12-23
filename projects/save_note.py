#!/usr/bin/python3
# Update project notes

import connix
import sys
import os
import run

print(connix.header())
form = connix.form()

if not run.validate(True):
	run.error("Unauthorized.")

if 'id' not in form or 'notes' not in form or 'name' not in form:
	run.error("Missing input.")

id = int(form['id'])
notes = form['notes']
name = form['name']

# Insert project into database
if "default" in name.lower():
	run.sql("UPDATE projects SET notes = %s WHERE id = %s;", notes, id)
else:
	if connix.remove_tags(notes) == "":
		run.sql("DELETE FROM project_notes WHERE prjid = %s AND name = %s;", id, name)
	else:
		run.sql("INSERT INTO project_notes (prjid, name, notes) VALUES(%s, %s, %s) ON DUPLICATE KEY UPDATE notes = %s;", id, name, notes, notes)

# Update last updated date
run.sql("UPDATE projects SET last_update = %s WHERE id = %s;", connix.now(), id)

run.done()
