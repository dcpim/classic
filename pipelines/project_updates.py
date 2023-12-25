#!/usr/bin/python3
# This populates the project_updates stat table to speed up display

import os
import run
import sys
import boto3
import time
import json
import connix

print(connix.header())
form = connix.form()

if not run.validate(False):
	run.error("Unauthorized.")

# Delete previous values
run.sql("DELETE FROM project_updates;")

# Fetch values
data = run.query("SELECT date,COUNT(*) FROM log WHERE result LIKE '%%last_update%%' GROUP BY date ORDER BY date;")

curcount = 0
curdate = None
for row in data:
	d = row[0].split(' ')[0]
	curcount += row[1]
	if d != curdate:
		if curdate:
			run.sql("INSERT INTO project_updates (date, num) VALUES (%s, %s);", curdate, curcount)
		curdate = d
		curcount = 0

run.done()
