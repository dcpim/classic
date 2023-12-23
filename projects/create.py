#!/usr/bin/python3
# Create a new project

import connix
import sys
import os
import run

print(connix.header())
form = connix.form()

if not run.validate(True):
	run.error("Unauthorized.")

if 'name' not in form or 'client' not in form:
	run.error("Missing input.")

name = form['name']
client = form['client']
date = connix.now().split(' ')[0]
if 'date' in form:
	date = form['date']

# Insert project into database
run.sql("INSERT INTO projects (default_rate, default_hours, name, client, date, end_date, last_update) VALUES (%s, %s, %s, %s, %s, %s, %s);", 85, 0, name, client, date, "", connix.now())

# Add preset if needed
run.sql("INSERT IGNORE INTO presets (type, name, address) VALUES (%s, %s, %s);", "client", client, "")

print("<meta http-equiv='refresh' content='0; URL=./list.php' />")
run.done()
