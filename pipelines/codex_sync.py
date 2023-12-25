#!/usr/bin/python3
# Synchronize codex entries with actual files

import connix
import sys
import os
import run

print(connix.header())
form = connix.form()

if not run.validate(False):
	run.error("Unauthorized.")

entries = run.query('SELECT id,sync,description,pub FROM code WHERE sync IS NOT NULL AND sync != "";')

for entry in entries:
	if len(entry[1]) > 1:
		print("* {}".format(entry[2]))
		content = ""
		run.cmd("mkdir -p /tmp/codex_sync")
		filename = entry[1]
		pubfile = entry[2].replace(' ', '_').replace('"', "'").replace('..', '').replace('/', '').replace('\\', '').replace('$', '').replace('{', '')
		pub = int(entry[3])
		if "$" in filename:
			filename = entry[1].split('$')[0]
		if "https://" in filename:
			run.cmd("wget {} -O /tmp/codex_sync/a".format(filename))
		elif "s3://" in filename:
			run.cmd("aws s3 cp {} /tmp/codex_sync/a".format(filename))
		else:
			run.cmd("cp {} /tmp/codex_sync/a".format(filename))
		if "$" in entry[1]:
			subfilename = entry[1].split('$')[1]
			if "tgz" in filename:
				run.cmd("cd /tmp/codex_sync && tar xzvf a")
			elif "zip" in filename:
				run.cmd("cd /tmp/codex_sync && unzip a")
			else:
				run.error("Unknown archive type: {}".format(filename))
			with open("/tmp/codex_sync" + subfilename, 'r') as fd:
				content = fd.read()
			if pub == 1:
				run.cmd("cp /tmp/codex_sync{} \"/var/www/html/share/{}\"".format(subfilename, pubfile))
		else:
			with open("/tmp/codex_sync/a", 'r') as fd:
				content = fd.read()
			if pub == 1:
				run.cmd("cp /tmp/codex_sync/a \"/var/www/html/share/{}\"".format(pubfile))
		if content != "":
			run.sql("UPDATE code SET content = %s WHERE id = %s;", content, entry[0])
		run.cmd("rm -rf /tmp/codex_sync")

run.done()
