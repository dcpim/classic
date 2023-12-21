#!/usr/bin/python3
# Upload a game screenshot

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

name = ""
if 'game' in form:
	name = form['game']
localfile = connix.guid()

# Local file upload
print(str(len(form['filename'])) + " bytes uploaded.<br>")
with open("/tmp/{}".format(localfile), 'wb') as fd:
	fd.write(form['filename'])

# Get file type
filename = run.filetype(localfile)

# Process image
run.cmd("convert -auto-orient /tmp/{} -resize 1920\> -quality 80 /tmp/{}".format(filename, filename))

# Upload image file to S3
run.cmd("aws s3 cp /tmp/{} s3://{}/Games/{}".format(filename, run.config('BUCKET_IMAGES'), filename))

# Creation of thumb
run.cmd("convert /tmp/{} -resize 150 /tmp/thumb-{}".format(filename, filename))

# Upload thumb file to S3
run.cmd("aws s3 cp /tmp/thumb-{} s3://{}/Games/Thumbs/{}".format(filename, run.config('BUCKET_IMAGES'), filename))

# Insert metadata into database
run.sql("INSERT INTO games (game, url, thumb, size, date) VALUES (%s, %s, %s, %s, %s);", name, "Games/{}".format(filename), "Games/Thumbs/{}".format(filename), Path("/tmp/{}".format(filename)).stat().st_size, connix.now())

# Delete local files
run.cmd("rm -f \"/tmp/{}\"".format(filename))
run.cmd("rm -f \"/tmp/thumb-{}\"".format(filename))

run.done(True)

