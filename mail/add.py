#!/usr/bin/python3
# Add a blocked domain

import connix
import time
import sys
import os
import run

print(connix.header())
form = connix.form()

if not run.validate(True):
	run.error("Unauthorized.")

if 'email' not in form:
	run.error("Missing input.")

email = form['email']

# Add entry to the database
run.sql("INSERT INTO mail_blacklist (email, hits, date) VALUES (%s, %s, %s);", email, 0, connix.now().split(' ')[0])

run.done(True)
