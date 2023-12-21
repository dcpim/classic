#!/usr/bin/python3
# Make a PDF from an archive file

import xml.etree.ElementTree
from pathlib import Path
import datetime
import pymysql
import connix
import sys
import os
import run

print(connix.header())
form = connix.form()

if not run.validate(True):
	run.error("Unauthorized.")

if 'filename' not in form or 'type' not in form or 'name' not in form:
	run.error("Missing input.")
bucket = form['type']
name = form['name']

# Local file upload
print(str(len(form['filename'])) + " bytes uploaded.<br>")
tmpfile = connix.guid()
filename = "{}.pdf".format(tmpfile)
with open("/tmp/" + tmpfile, 'wb') as fd:
	fd.write(form['filename'])

# Unzip package
run.cmd("mkdir -p /tmp/makepdf")
run.cmd("unzip -o /tmp/" + tmpfile + " -d /tmp/makepdf")

# Make PDF
run.cmd("convert -compress jpeg -quality 85 /tmp/makepdf/*.JPG /tmp/{}".format(filename))

# Upload to S3
run.cmd("aws s3 cp /tmp/{} s3://{}/{}/{}".format(filename, run.config('BUCKET_FILES'), bucket, filename))

# Insert metadata into database
run.sql("INSERT INTO utils (name, url, size, type, date) VALUES (%s, %s, %s, %s, %s);", name, "{}/{}".format(bucket, filename), Path("/tmp/{}".format(filename)).stat().st_size, bucket, connix.now())

# Delete local file
run.cmd("rm -rf /tmp/makepdf")
run.cmd("rm -f \"/tmp/{}\"".format(tmpfile))

run.done(True)

