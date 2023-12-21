#!/usr/bin/python3
# Create a new music entry, download mp3 from YouTube

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

if 'url' not in form or 'title' not in form:
	run.error("Missing input.")

url = str(form['url']).replace('\"', '&quot;').replace('`', '&#768;').replace('$', '&#36;')
title = form['title']
artist = ""
if 'artist' in form:
	artist = form['artist']
s3name = "{}_{}".format(connix.alphanum(title), connix.alphanum(artist))

# Download music file from YouTube
run.cmd("/usr/local/bin/yt-dlp --embed-thumbnail --no-playlist --extract-audio --audio-format mp3 \"{}\" -o \"/tmp/{}.%(ext)s\"".format(url, s3name))

# Upload music file to S3
run.cmd("aws s3 cp \"/tmp/{}.mp3\" \"s3://{}/{}.mp3\"".format(s3name, run.config('BUCKET_MUSIC'), s3name))

# Get size
size = Path("/tmp/{}.mp3".format(s3name)).stat().st_size

# Get duration
duration = connix.cmd('ffmpeg -i \"/tmp/{}.mp3\" 2>&1 | grep "Duration:"'.format(s3name))
if " 00:" in duration:
	duration = duration.split(' 00:')[1].split('.')[0]
else:
	duration = "99:99"

# Insert metadata into database
run.sql("INSERT INTO music (title, artist, url, size, date, duration) VALUES (%s, %s, %s, %s, %s, %s);", title, artist, "{}.mp3".format(s3name), size, connix.now(), duration)

# Delete local video file
run.cmd("rm -f \"/tmp/{}.mp3\"".format(s3name))

run.done(True)
