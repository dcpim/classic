#!/usr/bin/python3
# Create a game entry manually

import connix
import sys
import os
import run
import random

print(connix.header())
form = connix.form()

if not run.validate(True):
	run.error("Unauthorized.")

if 'genre' not in form or 'stars' not in form or 'release' not in form or 'name' not in form or 'filename' not in form:
	run.error("Missing input.")

name = form['name']
release = form['release']
genre = form['genre']
stars = int(form['stars'])

# Figure out appid
rows = run.query("SELECT appid FROM steam ORDER BY appid ASC LIMIT 1;")
appid = -1
for row in rows:
	appid = int(row[0]) - 1

# Process image header
print(str(len(form['filename'])) + " bytes uploaded.<br>")
tmp1 = "/tmp/{}.jpg".format(connix.guid())
tmp2 = "/tmp/{}.jpg".format(connix.guid())
with open(tmp1, 'wb') as fd:
	fd.write(form['filename'])
run.cmd("convert -auto-orient {} -resize 160\> -quality 80 {}".format(tmp1, tmp2))
run.cmd("aws s3 cp {} s3://{}/SteamHeaders/{}.jpg".format(tmp2, run.config('BUCKET_IMAGES'), appid))
run.cmd("rm -f {}".format(tmp1))
run.cmd("rm -f {}".format(tmp2))

# Insert game in the database
run.sql("INSERT INTO steam (appid, game_name, stars, review, date, played_time, release_date, genre, hidden) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s);", appid, name, stars, "", connix.now(), -1, release, genre, 0)

run.done(True)
