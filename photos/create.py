#!/usr/bin/python3
# Add a photo

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

if 'year' not in form or 'filename' not in form:
	run.error("Missing input.")

name = ""
if 'name' in form:
	name = form['name']
event = form['event']
year = int(form['year'])
localfile = connix.guid()

# Local file upload
print(str(len(form['filename'])) + " bytes uploaded.<br>")
with open("/tmp/{}".format(localfile), 'wb') as fd:
	fd.write(form['filename'])

# Get file type
filename = run.filetype(localfile)

# Get EXIF data
device = connix.cmd('identify -verbose /tmp/{} | grep "exif:Model:"'.format(filename))
if ": " in device:
	device = device.split(': ')[-1]
else:
	device = "Unknown"
date = connix.cmd('identify -verbose /tmp/{} | grep "exif:DateTime:"'.format(filename))
if ": " in date:
	date = date.split(': ')[-1]
	(d0, d1) = date.split(' ')
	date = "{} {}".format(d0.replace(':','-'), d1)
else:
	date = connix.now()

# Process image
run.cmd("convert -auto-orient /tmp/{} -resize 2048\> -quality 80 /tmp/{}".format(filename, filename))

# Upload image file to S3
run.cmd("aws s3 cp /tmp/{} s3://{}/Photos/{}/{}".format(filename, run.config('BUCKET_IMAGES'), year, filename))

# Creation of thumb
run.cmd("convert /tmp/{} -resize 150 /tmp/thumb-{}".format(filename, filename))

# Upload thumb file to S3
run.cmd("aws s3 cp /tmp/thumb-{} s3://{}/Photos/Thumbs/{}".format(filename, run.config('BUCKET_IMAGES'), filename))

# Insert metadata into database
run.sql("INSERT INTO photos (name, event, url, thumb, size, year, date, device) VALUES (%s, %s, %s, %s, %s, %s, %s, %s);", name, event, "Photos/{}/{}".format(year, filename), "Photos/Thumbs/{}".format(filename), Path("/tmp/{}".format(filename)).stat().st_size, year, date, device)

# Delete local files
run.cmd("rm -f \"/tmp/{}\"".format(filename))
run.cmd("rm -f \"/tmp/thumb-{}\"".format(filename))

run.done(True)
