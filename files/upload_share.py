#!/usr/bin/python3
# Upload a file to the share folder

import xml.etree.ElementTree
from pathlib import Path
import datetime
import pymysql
import connix
import sys
import os
import run

print(connix.header())
form = connix.form()

if not run.validate(True):
	run.error("Unauthorized.")

if 'filename' not in form or 'name' not in form:
	run.error("Missing input.")

filename = form['name'].replace(' ', '_').replace('"', "'").replace('/', '').replace('\\', '').replace('{', '').replace('$', '')

# Local file upload
print(str(len(form['filename'])) + " bytes uploaded.<br>")
with open("/var/www/html/share/" + filename, 'wb') as fd:
	fd.write(form['filename'])

print("Saved locally to: <a href='/share/'>/share</a>.")

run.done()

