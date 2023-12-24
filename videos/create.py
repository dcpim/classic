#!/usr/bin/python3
# Upload a video or download from YouTube

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

if 'name' not in form or 'type' not in form:
	run.error("Missing input.")

name = connix.alphanum(form['name'], spaces=True).replace(' ', '_')

# Local file upload
if 'filename' in form and form['filename'] != "" and len(form['filename']) > 0:
	print(str(len(form['filename'])) + " bytes uploaded.<br>")
	with open("/tmp/{}.mp4".format(name), 'wb') as fd:
	    fd.write(form['filename'])

# Download video file from YouTube
if 'url' in form and form['url'] != "":
	url = str(form['url']).replace('\"', '&quot;').replace('`', '&#768;').replace('$', '&#36;')
	run.cmd("/usr/local/bin/yt-dlp -f bestvideo[ext=mp4][vcodec^=avc]+bestaudio[ext=m4a]/best[ext=mp4]/best --embed-subs --no-playlist --merge-output-format mp4 \"{}\" -o \"/tmp/{}.mp4\"".format(url, name))

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

# Upload video file to S3
run.cmd("aws s3 cp \"/tmp/{}.mp4\" s3://{}/{}/".format(name, run.config('BUCKET_VIDEOS'), bucket))

# Get size
size = Path("/tmp/{}.mp4".format(name)).stat().st_size

# Get duration
duration = connix.cmd('ffmpeg -i \"/tmp/{}.mp4\" 2>&1 | grep "Duration:"'.format(name))
if " 00:" in duration:
    duration = duration.split(' 00:')[1].split('.')[0]
else:
    duration = "99:99"

# Make thumbnail
thumb = connix.guid() + ".jpg"
run.cmd("ffmpeg -ss 00:00:02.00 -i \"/tmp/{}.mp4\" -vf 'scale=320:320:force_original_aspect_ratio=decrease' -vframes 1 /tmp/{}".format(name, thumb))

# Upload thumbnail
run.cmd("aws s3 cp \"/tmp/{}\" s3://{}/Thumbs/".format(thumb, run.config('BUCKET_VIDEOS')))

# Insert metadata into database
run.sql("INSERT INTO videos (name, url, thumb, type, size, date, duration) VALUES (%s, %s, %s, %s, %s, %s, %s);", form['name'], "{}/{}.mp4".format(bucket, name), "Thumbs/{}".format(thumb), form['type'], size, connix.now(), duration)

# Delete local files
run.cmd("rm -f \"/tmp/{}.mp4\"".format(name))
run.cmd("rm -f \"/tmp/{}\"".format(thumb))

run.done(True)
