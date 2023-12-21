#!/usr/bin/python3
# Delete existing collectible entry

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

id = int(form['id'])

# Delete S3 file
data = run.query("SELECT url FROM collection WHERE id = %s;", id)
for entry in data:
	run.cmd("aws s3 rm \"s3://{}/{}\"".format(run.config('BUCKET_IMAGES'), entry[0]))

# Delete entry from the database
run.sql("DELETE FROM collection WHERE id = %s;", id)

run.done()
