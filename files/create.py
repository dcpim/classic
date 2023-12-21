#!/usr/bin/python3
# Upload a file and add to database

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

if 'name' not in form or 'type' not in form:
	run.error("Missing input.")

name = form['name']
localfile = connix.guid()
type = form['type']

# Get bucket name
if type == "3D Asset":
	bucket = "3D/assets"
elif type == "3D Model":
	bucket = "3D/models"
elif type == "3D Texture":
	bucket = "3D/textures"
elif type == "Reference":
	bucket = "Reference"
elif type == "Game resource":
	bucket = "Games"
elif type == "Utility":
	bucket = "Utils"
elif type == "Education":
	bucket = "Education"
elif type == "Housing":
	bucket = "Housing"
elif type == "Medical":
	bucket = "Medical"
elif type == "Personal":
	bucket = "Personal"
elif type == "Share":
	bucket = "Share"
elif type == "Private":
	bucket = "Private"
else:
	run.error("Unknown folder.")

# Local file upload
print(str(len(form['filename'])) + " bytes uploaded.<br>")
with open("/tmp/{}".format(localfile), 'wb') as fd:
	fd.write(form['filename'])

# Get file type
filename = run.filetype(localfile)

# Upload file to S3
run.cmd("aws s3 cp /tmp/{} s3://{}/{}/{}".format(filename, run.config('BUCKET_FILES'), bucket, filename))

# Insert metadata into database
run.sql("INSERT INTO utils (name, url, size, type, date) VALUES (%s, %s, %s, %s, %s);", name, "{}/{}".format(bucket, filename), Path("/tmp/{}".format(filename)).stat().st_size, type, connix.now())

# Print URL
if bucket == "Share":
	print("<p>URL: <a target=_new href='https://{}/{}/{}'>https://{}/{}/{}</a></p>".format(run.config('STORAGE_HOST').replace('[bucket]',run.config('BUCKET_FILES')), bucket, filename, run.config('STORAGE_HOST').replace('[bucket]',run.config('BUCKET_FILES')), bucket, filename))

# Delete local file
run.cmd("rm -f \"/tmp/{}\"".format(filename))

run.done(True)
