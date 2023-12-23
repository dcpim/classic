#!/usr/bin/python3
# Upload a file

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

if 'name' not in form or 'id' not in form or 'filename' not in form:
	run.error("Missing input.")

id = int(form['id'])
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
run.cmd("aws s3 cp /tmp/{} s3://{}/Projects/{}".format(filename, run.config('BUCKET_FILES'), filename))

# Insert metadata into database
size = Path("/tmp/{}".format(filename)).stat().st_size
run.sql("INSERT INTO project_files (prjid, name, url, size, date) VALUES (%s, %s, %s, %s, %s);", id, name, "Projects/{}".format(filename), size, connix.now())

# Update last updated date
run.sql("UPDATE projects SET last_update = %s WHERE id = %s;", connix.now(), id)

# Delete local file
run.cmd("rm -f \"/tmp/{}\"".format(filename))

print("<meta http-equiv='refresh' content='0; URL=./files.php?id={}' />".format(id))
run.done()
