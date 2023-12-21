#!/usr/bin/python3
# Add an RSS feed

from pathlib import Path
import pymysql
import connix
import sys
import os
import run

print(connix.header())
form = connix.form()

if not run.validate(True):
	run.error("Unauthorized.")

if 'name' not in form or 'url' not in form:
	run.error("Missing input.")

name = form['name']
url = form['url']
filter = ""
if 'filter' in form:
	filter = form['filter']

# Insert feed into database
run.sql("INSERT INTO rss_feeds (name, url, filter) VALUES (%s, %s, %s);", name, url, filter)

run.done(True)
