#!/usr/bin/python3
# Update file metadata

import connix
import sys
import os
import run

print(connix.header())
form = connix.form()

if not run.validate(True):
	run.error("Unauthorized.")

if 'id' not in form or 'name' not in form or 'type' not in form:
	run.error("Missing input.")

id = int(form['id'])
name = form['name']
type = form['type']

# Update file in the database
run.sql("UPDATE utils SET name = %s, type = %s WHERE id = %s;", name, type, id)

run.done()
