#!/usr/bin/python3
# Create a new income entry

from pathlib import Path
import pymysql
import connix
import sys
import os
import run

print(connix.header())
form = connix.form()

if not run.validate(True):
	run.error("Unauthorized.")

if 'date' not in form or 'note' not in form:
	run.error("Missing input.")

note = form['note']
date = form['date']
debit = float(form['debit'])
credit = float(form['credit'])

# Add transaction to database
run.sql("INSERT INTO income (note, date, credit, debit) VALUES (%s, %s, %s, %s);", note, date, credit, debit)

# Get last id
id = -1
results = run.query("SELECT id FROM income ORDER BY id DESC LIMIT 1;")
for result in results:
	id = result[0]

if id == -1:
	run.error("Could not get last id!")

# Is a saving
if 'is_saving' in form:
	run.sql("UPDATE income SET is_saving = 1 WHERE id = %s;", id)

# Add preset if needed
run.sql("INSERT IGNORE INTO presets (type, name) VALUES (%s, %s);", "note", note)

run.done(True)
