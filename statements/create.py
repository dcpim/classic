#!/usr/bin/python3
# Upload a statement

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

if 'name' not in form or 'type' not in form or 'date' not in form or 'scope' not in form:
	run.error("Missing input.")

name = form['name']
localfile = connix.guid()
type = form['type']
scope = form['scope']
date = form['date']

# Local file upload
print(str(len(form['filename'])) + " bytes uploaded.<br>")
with open("/tmp/{}".format(localfile), 'wb') as fd:
	fd.write(form['filename'])

# Get file type
filename = run.filetype(localfile)

# Process image if needed
if ".jpg" in filename or ".gif" in filename or ".png" in filename:
	run.cmd("convert -auto-orient /tmp/{} -resize 2048\> -quality 80 /tmp/{}".format(filename, filename))

# Upload file to S3
run.cmd("aws s3 cp /tmp/{} s3://{}/Statements/{}".format(filename, run.config('BUCKET_ACCOUNTING'), filename))

# Insert metadata into database
run.sql("INSERT INTO statements (name, url, size, type, date, scope) VALUES (%s, %s, %s, %s, %s, %s);", name, "Statements/{}".format(filename), Path("/tmp/{}".format(filename)).stat().st_size, type, date, scope)

# Delete local file
run.cmd("rm -f \"/tmp/{}\"".format(filename))

run.done(True)
