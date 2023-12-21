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

if 'id' not in form or 'game' not in form:
	run.error("Missing input.")

id = int(form['id'])
game = form['game']

# Update file in the database
run.sql("UPDATE games SET game = %s WHERE id = %s;", game, id)

run.done()
