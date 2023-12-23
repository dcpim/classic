#!/usr/bin/python3
# Add, update or delete a secret

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

if 'site' not in form or 'action' not in form or 'id' not in form or 'secret' not in form:
	run.error("Missing input.")

action = form['action']
site = form['site']
prjid = form['id']
secret = form['secret']
note = ""
if 'note' in form:
	note = form['note'].replace('<','[').replace('>',']')

# Add a new secret
if action == "create":
	run.sql("INSERT INTO secrets (site, secret, note, date, prjid) VALUES (%s, %s, %s, %s, %s);", site, secret, note, connix.now(), prjid)

# Update an existing secret
elif action == "update":
	run.sql("UPDATE secrets SET secret = %s, note = %s WHERE site = %s AND prjid = %s;", secret, note, site, prjid)

# Delete a secret
elif action == "delete":
	run.sql("DELETE FROM secrets WHERE site = %s AND prjid = %s;", site, prjid)

else:
	print("Unknown action.")

run.done()

