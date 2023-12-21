#!/usr/bin/python3
# Create a new automation pipeline

import connix
import time
import sys
import os
import run

print(connix.header())
form = connix.form()

if not run.validate(True):
	run.error("Unauthorized.")

if 'name' not in form or 'pipeline' not in form or 'repeats' not in form:
	run.error("Missing input.")

name = form['name']
pipeline = connix.remove_spaces(form['pipeline']).replace('&','').replace(';','').replace('/','').replace('\\','').replace(' ','')
repeats = int(form['repeats'])
params = ""
nextrun = int(time.time())

# Add entry to the database
run.sql("INSERT INTO automate (name, repeats, params, pipeline, next_run, notify, history, node) VALUES (%s, %s, %s, %s, %s, %s, %s, %s);", name, repeats, params, pipeline, nextrun, 1, 1, "local")

run.done(True)
