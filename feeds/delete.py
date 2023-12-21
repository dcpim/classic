#!/usr/bin/python3
# Delete a feed

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

if 'id' not in form:
	run.error("Missing input.")

id = form['id']

# Delete feed from database
run.sql("DELETE FROM rss_feeds WHERE id = %s;", id)
run.sql("DELETE FROM rss WHERE feed = %s;", id)

run.done(True)
