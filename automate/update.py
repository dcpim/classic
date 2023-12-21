#!/usr/bin/python3
# Update an automation pipeline

import connix
import sys
import os
import run

print(connix.header())
form = connix.form()

if not run.validate(True):
	run.error("Unauthorized.")

if 'id' not in form or 'name' not in form or 'pipeline' not in form or 'repeats' not in form or 'node' not in form:
	run.error("Missing input.")

id = int(form['id'])
notify = 0
if 'notify' in form:
	notify = int(form['notify'])
history = 0
if 'history' in form:
	history = int(form['history'])
name = form['name']
pipeline = form['pipeline']
node = form['node']
repeats = int(form['repeats'])
params = ""
if 'params' in form:
	params = str(form['params']).replace('"','\'')

# Update file in the database
run.sql("UPDATE automate SET history = %s, notify = %s, name = %s, repeats = %s, params = %s, pipeline = %s, node = %s WHERE id = %s;", history, notify, name, repeats, params, pipeline, node, id)

run.done()
