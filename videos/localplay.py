#!/usr/bin/python3
# Localplay a specific video

import connix
import sys
import os
import run

print(connix.header())
form = connix.form()

if not run.validate(True):
	run.error("Unauthorized.")

if 'url' not in form:
	run.error("Missing parameters.")

url = form['url']

# Add url to file
with open("../localplay/play.txt", 'w') as fd:
	fd.write(url)

run.done(True)
