#!/usr/bin/python3
# Update an anime series

import connix
import sys
import os
import run

print(connix.header())
form = connix.form()

if not run.validate(True):
	run.error("Unauthorized.")

if 'id' not in form or 'stars' not in form or 'url' not in form:
	run.error("Missing input.")

id = int(form['id'])
title = form['title']
stars = int(form['stars'])
url = form['url']
review = ""
if 'review' in form:
	review = form['review'].replace('\n',' ')

# Update file in the database
run.sql("UPDATE series SET title = %s, stars = %s, url = %s, review = %s WHERE id = %s;", title, stars, url, review, id)

run.done()
