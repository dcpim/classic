#!/usr/bin/python3
# This script downloads available frames from Tapo camera then produces a video, adds the previously existing video, then updates the entry in the db

from pathlib import Path
import connix
import run

print(connix.header())
form = connix.form()

if not run.validate(False):
	run.error("Unauthorized.")

# Make temp folder
run.cmd("mkdir -p /tmp/tapo")

# Download current frames
run.cmd("/usr/local/bin/aws s3 mv s3://{}/tapo-frames/ /tmp/tapo --recursive".format(run.config('BUCKET_DATA')))

# Validate the number of frames
num = connix.cmd("ls -l /tmp/tapo | wc -l 2>&1")

# Make video from frames
run.cmd("/usr/bin/ffmpeg -framerate 24 -pattern_type glob -i '/tmp/tapo/*.jpg' -c:v libx264 /tmp/tapo/2.mp4")

# Download existing video
run.cmd("/usr/local/bin/aws s3 cp s3://{}/Local/tapo_current.mp4 /tmp/tapo/1.mp4".format(run.config('BUCKET_VIDEOS')))

# Concatenate the old and new videos
run.cmd("/usr/bin/ffmpeg -f concat -safe 0 -i /var/www/html/pipelines/process_tapo.lst -c copy /tmp/tapo/3.mp4")

# Upload the new video to S3
run.cmd("/usr/local/bin/aws s3 cp /tmp/tapo/3.mp4 s3://{}/Local/tapo_current.mp4".format(run.config('BUCKET_VIDEOS')))

# Get size
size = Path("/tmp/tapo/3.mp4").stat().st_size

# Get duration
duration = connix.cmd('ffmpeg -i /tmp/tapo/3.mp4 2>&1 | grep "Duration:"')
if " 00:" in duration:
	duration = duration.split(' 00:')[1].split('.')[0]
else:
	duration = "99:99"

# Update metadata
run.sql("UPDATE videos SET size = %s, duration = %s WHERE url = %s;", size, duration, "Local/tapo_current.mp4")

# Delete temp folder
run.cmd("rm -rf /tmp/tapo")

# Send notification if too few frames
if int(num) < 20:
	run.error("Counted [{}] TAPO images from the past day. Expected 24.".format(int(num)))

run.done()

