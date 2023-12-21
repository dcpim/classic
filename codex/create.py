#!/usr/bin/python3
# Create a new project codex entry

import connix
import sys
import os
import run

print(connix.header())
form = connix.form()

if not run.validate(True):
	run.error("Unauthorized.")

description = "  New entry"
language = "Python"
content = ""
prjid = 0
if "prjid" in form:
	prjid = int(form['prjid'])

# Insert notebook into database
run.sql("INSERT INTO code (description, language, content, prjid, sync, pub) VALUES (%s, %s, %s, %s, %s, %s);", description, language, content, prjid, "", 0)

print("<meta http-equiv='refresh' content='0; URL=./?prjid={}' />".format(prjid))
run.done()
