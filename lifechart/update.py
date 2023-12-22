#!/usr/bin/python3
# Update an education file

import connix
import sys
import os
import run

print(connix.header())
form = connix.form()

if not run.validate(True):
	run.error("Unauthorized.")

if 'id' not in form or 'name' not in form or 'date' not in form:
	run.error("Missing input.")

id = int(form['id'])
name = form['name']
date = form['date']

# Update file in the database
run.sql("UPDATE education_files SET date = %s, name = %s WHERE id = %s;", date, name, id)

run.done()
