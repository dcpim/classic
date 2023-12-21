#!/usr/bin/python3
# Add a new Anime series to the database

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

if 'title' not in form or 'stars' not in form or 'url' not in form:
	run.error("Missing input.")

title = form['title']
url = form['url']
stars = int(form['stars'])

# Insert into database
run.sql("INSERT INTO series (title, url, stars, date) VALUES (%s, %s, %s, %s);", title, url, stars, connix.now())

run.done(True)
