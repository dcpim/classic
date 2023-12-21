#!/usr/bin/python3
# Set a pipeline to be run ASAP

import connix
import time
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
run.sql("UPDATE automate SET next_run = %s WHERE id = %s;", int(time.time()), id)

run.done()
