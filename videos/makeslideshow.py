#!/usr/bin/python3
# Make a video from images

from pathlib import Path
import datetime
import connix
import run
import sys
import os

print(connix.header())
form = connix.form()

if not run.validate(False):
	run.error("Unauthorized.")

if 'filename' not in form or 'type' not in form or 'name' not in form or 'fps' not in form:
	run.error("Missing input.")

filename = connix.alphanum(form['name'], spaces=True).replace(' ', '_') + ".mp4"
name = form['name']
fps = int(form['fps'])

# Set bucket based on type
if form['type'] == "Music video (Japanese)":
	bucket = "MVs/Japanese"
elif form['type'] == "Music video (English)":
	bucket = "MVs/English"
elif form['type'] == "Music video (French)":
	bucket = "MVs/French"
elif form['type'] == "Music video (Russian)":
	bucket = "MVs/Russian"
elif form['type'] == "Travel":
	bucket = "Travel"
elif form['type'] == "Local":
	bucket = "Local"
elif form['type'] == "Gaming":
	bucket = "Gaming"
elif form['type'] == "Funny video":
	bucket = "Funny"
else:
	bucket = form['type']

# Local file upload
print(str(len(form['filename'])) + " bytes uploaded.<br>")
tmpfile = connix.guid() + ".zip"
with open("/tmp/" + tmpfile, 'wb') as fd:
	fd.write(form['filename'])

# Unzip package
run.cmd("mkdir -p /tmp/makeslideshow")
run.cmd("unzip -o /tmp/" + tmpfile + " -d /tmp/makeslideshow")

# Make movie
run.cmd("ffmpeg -framerate {} -pattern_type glob -i '/tmp/makeslideshow/*.JPG' -c:v libx264 /tmp/{}".format(fps, filename))

# Upload to S3
run.cmd("aws s3 cp /tmp/{} s3://{}/{}/{}".format(filename, run.config('BUCKET_VIDEOS'), bucket, filename))

# Get size
size = Path("/tmp/{}".format(filename)).stat().st_size

# Get duration
duration = connix.cmd('ffmpeg -i "/tmp/{}" 2>&1 | grep "Duration:"'.format(filename))
if " 00:" in duration:
	duration = duration.split(' 00:')[1].split('.')[0]
else:
	duration = "99:99"

# Make thumbnail
thumb = connix.guid() + ".jpg"
run.cmd("ffmpeg -ss 00:00:02.00 -i \"/tmp/{}\" -vf 'scale=320:320:force_original_aspect_ratio=decrease' -vframes 1 /tmp/{}".format(filename, thumb))

# Upload thumbnail
run.cmd("aws s3 cp \"/tmp/{}\" s3://{}/Thumbs/".format(thumb, run.config('BUCKET_VIDEOS')))

# Insert metadata into database
run.sql("INSERT INTO videos (name, url, type, size, date, duration, thumb) VALUES (%s, %s, %s, %s, %s, %s, %s);", name, "{}/{}".format(bucket, filename), form['type'], size, connix.now(), duration, "Thumbs/{}".format(thumb))

# Delete local file
run.cmd("rm -rf /tmp/makeslideshow")
run.cmd("rm -f /tmp/{}".format(tmpfile))
run.cmd("rm -f /tmp/{}".format(filename))
run.cmd("rm -f /tmp/{}".format(thumb))

run.done(True)

