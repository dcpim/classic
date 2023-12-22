#!/usr/bin/python3
# Remove a food item

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

# Add food to the database
run.sql("DELETE FROM nutrition WHERE id = %s;", id)

run.done(True)
