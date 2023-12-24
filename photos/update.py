#!/usr/bin/python3
# Update photo

import connix
import sys
import os
import run

print(connix.header())
form = connix.form()

if not run.validate(True):
	run.error("Unauthorized.")

if 'id' not in form or 'event' not in form or 'year' not in form:
	run.error("Missing input.")

id = int(form['id'])
year = int(form['year'])
event = form['event']
name = ""
if 'name' in form:
	name = form['name']

# Update file in the database
run.sql("UPDATE photos SET year = %s, event = %s, name = %s WHERE id = %s;", year, event, name, id)

run.done()
