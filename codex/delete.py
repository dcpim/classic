#!/usr/bin/python3
# Delete a codex entry

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
run.sql("DELETE FROM code WHERE id = %s;", id)

run.done()
