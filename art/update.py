#!/usr/bin/python3
# Update an art entry

import connix
import sys
import os
import run

print(connix.header())
form = connix.form()

if not run.validate(True):
	run.error("Unauthorized.")

if 'id' not in form or 'genre' not in form:
	run.error("Missing input.")

id = int(form['id'])
genre = form['genre']
name = ""
if 'name' in form:
	name = form['name']
desc = ""
if 'desc' in form:
	desc = form['desc']
date = ""
if 'date' in form:
	date = form['date']

# Update file in the database
run.sql("UPDATE renders SET genre = %s, name = %s, description = %s, date = %s WHERE id = %s;", genre, name, desc, date, id)

run.done()
