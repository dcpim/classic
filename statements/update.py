#!/usr/bin/python3
# Update a statement

import connix
import sys
import os
import run

print(connix.header())
form = connix.form()

if not run.validate(True):
	run.error("Unauthorized.")

if 'id' not in form or 'name' not in form or 'type' not in form or 'date' not in form or 'scope' not in form:
	run.error("Missing input.")

id = int(form['id'])
name = form['name']
type = form['type']
date = form['date']
scope = form['scope']

# Update file in the database
run.sql("UPDATE statements SET name = %s, type = %s, scope = %s, date = %s WHERE id = %s;", name, type, scope, date, id)

run.done()
