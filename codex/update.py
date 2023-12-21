#!/usr/bin/python3
# Update a codex entry

import connix
import sys
import os
import run

print(connix.header())
form = connix.form()

if not run.validate(True):
	run.error("Unauthorized.")

if 'id' not in form or 'description' not in form or 'language' not in form or 'prjid' not in form:
	run.error("Missing input.")

id = int(form['id'])
prjid = int(form['prjid'])
description = form['description']
language = form['language']
content = ""
if 'content' in form:
	content = form['content']
sync = ""
if 'sync' in form:
	sync = form['sync']
pub = int(form['pub'])

# Update file in the database
run.sql("UPDATE code SET pub = %s, sync = %s, date = %s, content = %s, description = %s, language = %s WHERE id = %s;", pub, sync, connix.now().split(' ')[0], content, description, language, id)

# Update last updated date
run.sql("UPDATE projects SET last_update = %s WHERE id = %s;", connix.now(), prjid)

run.done()
