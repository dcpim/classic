#!/usr/bin/python3
# Hide a game entry

import connix
import sys
import os
import run

print(connix.header())
form = connix.form()

if not run.validate(True):
	run.error("Unauthorized.")

if 'id' not in form:
	run.error("Missing input.")

id = int(form['id'])

# Update file in the database
run.sql("UPDATE steam SET hidden = %s WHERE id = %s;", 1, id)

run.done()
