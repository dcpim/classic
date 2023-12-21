#!/usr/bin/python3
# Update a feed

import connix
import sys
import os
import run

print(connix.header())
form = connix.form()

if not run.validate(True):
	run.error("Unauthorized.")

if 'id' not in form or 'name' not in form or 'url' not in form:
	run.error("Missing input.")

id = int(form['id'])
name = form['name']
url = form['url']
filter = ""
if 'filter' in form:
	filter = form['filter']

# Update feed in the database
run.sql("UPDATE rss_feeds SET name = %s, url = %s, filter = %s WHERE id = %s;", name, url, filter, id)

run.done(True)
