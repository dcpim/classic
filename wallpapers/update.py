#!/usr/bin/python3
# Update a wallpaper

import connix
import sys
import os
import run

print(connix.header())
form = connix.form()

if not run.validate(True):
	run.error("Unauthorized.")

if 'id' not in form or 'landscape' not in form:
	run.error("Missing input.")

id = int(form['id'])
name = ""
if 'name' in form:
	name = form['name']
landscape = int(form['landscape'])

tag_action = int(form['tag_action'])
tag_bondage = int(form['tag_bondage'])
tag_casual = int(form['tag_casual'])
tag_jk = int(form['tag_jk'])
tag_loli = int(form['tag_loli'])
tag_muscles = int(form['tag_muscles'])
tag_nsfw = int(form['tag_nsfw'])
tag_yuri = int(form['tag_yuri'])
tag_ai = int(form['tag_ai'])

# Update file in the database
run.sql("UPDATE wallpapers SET tag_action = %s, tag_bondage = %s, tag_casual = %s, tag_jk = %s, tag_loli = %s, tag_muscles = %s, tag_nsfw = %s, tag_yuri = %s, tag_ai = %s, name = %s, landscape = %s WHERE id = %s;", tag_action, tag_bondage, tag_casual, tag_jk, tag_loli, tag_muscles, tag_nsfw, tag_yuri, tag_ai, name, landscape, id)

run.done()
