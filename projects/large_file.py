#!/usr/bin/python3
# Add an existing file to the database

import connix
import sys
import os
import run

print(connix.header())
form = connix.form()

if not run.validate(True):
	run.error("Unauthorized.")

if 'name' not in form or 'size' not in form or 'url' not in form or 'id' not in form:
	run.error("Missing input.")

id = int(form['id'])
name = form['name']
url = form['url']
date = connix.now().split(' ')[0]
size = form['size']

# Add file to database
run.sql("INSERT INTO project_files (name, url, size, date, prjid, notes) VALUES (%s, %s, %s, %s, %s, %s);", name, url, size, date, id, "")

# Update last updated date
run.sql("UPDATE projects SET last_update = %s WHERE id = %s;", connix.now(), id)

print("<meta http-equiv='refresh' content='0; URL=./files.php?id={}' />".format(id))
run.done()
