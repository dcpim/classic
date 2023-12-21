#!/usr/bin/python3
# Create a new collectible entry

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

if 'name' not in form or 'type' not in form or 'subtype' not in form or 'brand' not in form:
	run.error("Missing input.")

name = form['name']
brand = form['brand']
subtype = form['subtype']
localfile = connix.guid()
type = connix.alphanum(form['type'])

# Get bucket name
if type == "Book":
	bucket = "Books"
elif type == "Figure":
	bucket = "Figures"
elif type == "Misc":
	bucket = "Misc"
else:
	run.error("Unknown type.")

# Local file upload
print(str(len(form['filename'])) + " bytes uploaded.<br>")
with open("/tmp/{}".format(localfile), 'wb') as fd:
	fd.write(form['filename'])

# Get file type
filename = run.filetype(localfile)

# Process image
run.cmd("convert -auto-orient /tmp/{} -resize 2048\> -quality 80 /tmp/{}".format(filename, filename))

# Upload image file to S3
run.cmd("aws s3 cp /tmp/{} s3://{}/Collection/{}/{}".format(filename, run.config('BUCKET_IMAGES'), bucket, filename))

# Creation of thumb
run.cmd("convert /tmp/{} -resize 150 /tmp/thumb-{}".format(filename, filename))

# Upload thumb file to S3
run.cmd("aws s3 cp /tmp/thumb-{} s3://{}/Collection/Thumbs/{}".format(filename, run.config('BUCKET_IMAGES'), filename))

# Insert metadata into database
run.sql("INSERT INTO collection (name, brand, url, thumb, size, type, date, sold, stars, subtype) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s);", name, brand, "Collection/{}/{}".format(bucket, filename), "Collection/Thumbs/{}".format(filename), Path("/tmp/{}".format(filename)).stat().st_size, type, connix.now().split(' ')[0], 0, 0, subtype)

# Delete local files
run.cmd("rm -f \"/tmp/{}\"".format(filename))
run.cmd("rm -f \"/tmp/thumb-{}\"".format(filename))

run.done(True)
