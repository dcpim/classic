#!/usr/bin/python3
# Add a new art entry

from pathlib import Path
import pymysql
import connix
import boto3
import sys
import os
import run

print(connix.header())
form = connix.form()

if not run.validate(True):
	run.error("Unauthorized.")

name = form['name']
genre = form['genre']
localfile = connix.guid()

# Local file upload
print(str(len(form['filename'])) + " bytes uploaded.<br>")
with open("/tmp/{}".format(localfile), 'wb') as fd:
	fd.write(form['filename'])

# Get file type
filename = run.filetype(localfile)

# Upload image file to S3
run.cmd("aws s3 cp /tmp/{} s3://{}/Renders/{}/{}".format(filename, run.config('BUCKET_IMAGES'), genre, filename))

# Creation of thumb
run.cmd("convert /tmp/{} -resize 150 /tmp/thumb-{}".format(filename, filename))

# Upload thumb file to S3
run.cmd("aws s3 cp /tmp/thumb-{} s3://{}/Renders/Thumbs/{}".format(filename, run.config('BUCKET_IMAGES'), filename))

# Insert metadata into database
run.sql("INSERT INTO renders (name, url, thumb, size, date, genre, description) VALUES (%s, %s, %s, %s, %s, %s, %s);", name, "Renders/{}/{}".format(genre, filename), "Renders/Thumbs/{}".format(filename), Path("/tmp/{}".format(filename)).stat().st_size, connix.now().split(' ')[0], genre, "")

# Delete local files
run.cmd("rm -f \"/tmp/{}\"".format(filename))
run.cmd("rm -f \"/tmp/thumb-{}\"".format(filename))

run.done(True)
