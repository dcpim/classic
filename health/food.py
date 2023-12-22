#!/usr/bin/python3
# Add a nutrition item

import connix
import sys
import os
import run

print(connix.header())
form = connix.form()

if not run.validate(True):
	run.error("Unauthorized.")

if 'food' not in form or 'date' not in form:
	run.error("Missing input.")

food = int(form['food'])
date = form['date']

# Add food to the database
run.sql("INSERT INTO nutrition (food, date) VALUES (%s, %s);", food, date)

run.done(True)
