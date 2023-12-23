#!/usr/bin/python3
# Delete a journal entry

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

# Delete bookmark from the database
run.sql("DELETE FROM journal WHERE id = %s;", id)

run.done()
