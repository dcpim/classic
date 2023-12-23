#!/usr/bin/python3
# Update a bookmark

import connix
import sys
import os
import run

print(connix.header())
form = connix.form()

if not run.validate(True):
	run.error("Unauthorized.")

if 'name' not in form or 'url' not in form or 'type' not in form or 'id' not in form or 'prjid' not in form or 'section' not in form:
	run.error("Missing input.")

name = form['name']
type = form['type']
section = form['section']
url = form['url']
id = int(form['id'])
prjid = int(form['prjid'])
notes = ""
if 'notes' in form:
	notes = form['notes']

# Update bill in the database
run.sql("UPDATE bookmarks SET notes = %s, section = %s, name = %s, type = %s, url = %s WHERE id = %s;", notes, section, name, type, url, id);

# Update last updated date
run.sql("UPDATE projects SET last_update = %s WHERE id = %s;", connix.now(), prjid)

run.done()
