#!/usr/bin/python3
# Add video already in S3

from pathlib import Path
import pymysql
import connix
import sys
import os
import run
import datetime
import dateutil.relativedelta

print(connix.header())
form = connix.form()

if not run.validate(True):
	run.error("Unauthorized.")

if 'name' not in form or 'url' not in form or 'size' not in form or 'duration' not in form:
	run.error("Missing field")

# Download video
run.cmd("wget \"{}\" -O /tmp/a.mp4".format(form['url']))

# Make thumbnail
thumb = connix.guid() + ".jpg"
run.cmd("ffmpeg -ss 00:00:02.00 -i \"/tmp/a.mp4\" -vf 'scale=320:320:force_original_aspect_ratio=decrease' -vframes 1 /tmp/{}".format(thumb))

# Upload thumbnail
run.cmd("aws s3 cp \"/tmp/{}\" s3://{}/Thumbs/".format(run.config('BUCKET_VIDEOS'), thumb))

# Add to videos table
run.sql("INSERT INTO videos (name, url, type, size, date, duration, thumb) VALUES (%s, %s, %s, %s, %s, %s, %s);", form['name'], form['url'], "Anime", int(form['size']), connix.now(), form['duration'], "Thumbs/{}".format(thumb))

# Delete temp files
run.cmd("rm -f /tmp/a.mp4")
run.cmd("rm -f /tmp/{}".format(thumb))

run.done(True)
