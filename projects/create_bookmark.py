#!/usr/bin/python3
# Add a new bookmark

import connix
import sys
import os
import run

print(connix.header())
form = connix.form()

if not run.validate(True):
	run.error("Unauthorized.")

if 'name' not in form or 'url' not in form or 'prjid' not in form or 'section' not in form:
	run.error("Missing input.")

name = form['name']
section = form['section']
url = form['url']
prjid = int(form['prjid'])
type = "globe"

# Create bill in the database
run.sql("INSERT INTO bookmarks (section, prjid, name, url, type, date) VALUES (%s, %s, %s, %s, %s, %s);", section, prjid, name, url, type, connix.now())

# Update last updated date
run.sql("UPDATE projects SET last_update = %s WHERE id = %s;", connix.now(), prjid)

print("<meta http-equiv='refresh' content='0; URL=./bookmarks.php?id={}' />".format(prjid))
run.done()
