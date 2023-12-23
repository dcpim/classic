#!/usr/bin/python3
# Update a game entry

import connix
import sys
import os
import run

print(connix.header())
form = connix.form()

if not run.validate(True):
	run.error("Unauthorized.")

if 'id' not in form or 'stars' not in form or 'name' not in form:
	run.error("Missing input.")

id = int(form['id'])
name = form['name']
stars = int(form['stars'])
review = ""
if 'review' in form:
	review = form['review'].replace('\n',' ')

# Update file in the database
run.sql("UPDATE steam SET game_name = %s, stars = %s, review = %s WHERE id = %s;", name, stars, review, id)

run.done()
