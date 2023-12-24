#!/usr/bin/python3
# Localplay a random video

import connix
import sys
import os
import run

print(connix.header())
form = connix.form()

if not run.validate(False):
	run.error("Unauthorized.")

# Select random video
url = run.query("SELECT url FROM videos WHERE type LIKE 'Music%%' ORDER BY RAND() LIMIT 1;")

# Add url to file
with open("../localplay/play.txt", 'w') as fd:
	fd.write("https://{}/{}".format(run.config('STORAGE_HOST').replace('[bucket]',run.config('BUCKET_VIDEOS')), url[0][0]))

print(url[0][0])

run.done(True)
