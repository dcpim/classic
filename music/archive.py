#!/usr/bin/python3

import pymysql
import connix
import sys
import os
import run
import datetime
import json

print(connix.header())
form = connix.form()

if not run.validate(True):
	run.error("Unauthorized.")

if 'songs' not in form:
	run.error("Missing fields.")

# Make folder
run.cmd("mkdir -p /tmp/music")

# List songs
scount = run.query("SELECT COUNT(*) FROM music;")
songs = run.query("SELECT * FROM music ORDER BY id DESC LIMIT %s;", (scount[0][0]-int(form['songs'])))

for song in songs:
	filename = "{} - {}.{}".format(connix.alphanum(song[2],spaces=True), connix.alphanum(song[1],spaces=True), song[3][-3:])
	run.cmd("aws s3 cp \"s3://{}/{}\" \"/tmp/music/{}\"".format(run.config('BUCKET_MUSIC'), song[3], filename))

# Make archive
run.cmd("zip -j -r /tmp/music.zip /tmp/music")

# Upload to S3
run.cmd("aws s3 cp /tmp/music.zip s3://{}/Share/music.zip".format(run.config('BUCKET_FILES')))
print("<p>Archive: <a href='https://{}/Share/music.zip'>https://{}/Share/music.zip</a></p>".format(run.config('STORAGE_HOST').replace('[bucket]',run.config('BUCKET_FILES')), run.config('STORAGE_HOST').replace('[bucket]',run.config('BUCKET_FILES'))))

# Delete folder
run.cmd("rm -f /tmp/music.zip")
run.cmd("rm -rf /tmp/music")

# Done
run.done(True)
