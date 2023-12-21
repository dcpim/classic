#!/usr/bin/python3
# Update a music entry

import connix
import sys
import os
import run

print(connix.header())
form = connix.form()

if not run.validate(True):
	run.error("Unauthorized.")

if 'id' not in form or 'name' not in form or 'artist' not in form:
	run.error("Missing input.")

id = int(form['id'])
name = form['name']
artist = form['artist']

# Update file in the database
run.sql("UPDATE music SET title = %s, artist = %s WHERE id = %s;", name, artist, id)

run.done()
