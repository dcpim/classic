#!/usr/bin/python3
# Upload a file to the local filesystem

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

if 'filename' not in form:
	run.error("Missing input.")

# Local file upload
print(str(len(form['filename'])) + " bytes uploaded.<br>")
filename = connix.guid()
with open("/uploads/" + filename, 'wb') as fd:
	fd.write(form['filename'])

print("Saved locally to: /uploads/" + filename)

run.done()

