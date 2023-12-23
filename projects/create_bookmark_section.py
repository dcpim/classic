#!/usr/bin/python3
# Add a bookmark section

import connix
import sys
import os
import run
import random

print(connix.header())
form = connix.form()

if not run.validate(True):
	run.error("Unauthorized.")

if 'name' not in form or 'prjid' not in form:
	run.error("Missing input.")

name = form['name']
prjid = int(form['prjid'])
c = ['A', 'B', 'C', 'D', 'E', 'F']
color = "#{}{}{}{}{}{}".format(random.choice(c), random.choice(c), random.choice(c), random.choice(c), random.choice(c), random.choice(c))
if name == "None":
	color = "#FFFFFF"

# Create bill in the database
run.sql("INSERT INTO bookmark_sections (prjid, name, color) VALUES (%s, %s, %s);", prjid, name, color)

# Update last updated date
run.sql("UPDATE projects SET last_update = %s WHERE id = %s;", connix.now(), prjid)

print("<meta http-equiv='refresh' content='0; URL=./bookmarks.php?id={}' />".format(prjid))
run.done()
