#!/usr/bin/python3
# Create a new education file

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

if 'name' not in form or 'filename' not in form:
	run.error("Missing input.")

name = form['name']
localfile = connix.guid()

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
run.cmd("aws s3 cp /tmp/{} s3://{}/Education/{}".format(filename, run.config('BUCKET_FILES'), filename))

# Insert metadata into database
run.sql("INSERT INTO education_files (name, url, size, date) VALUES (%s, %s, %s, %s);", name, "Education/{}".format(filename), Path("/tmp/{}".format(filename)).stat().st_size, connix.now())

# Delete local file
run.cmd("rm -f \"/tmp/{}\"".format(filename))

run.done(True)
