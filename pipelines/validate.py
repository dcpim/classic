#!/usr/bin/python3
# Check for unauthorized API calls in the past day

import connix
import sys
import os
import run
import json
import datetime

print(connix.header())
form = connix.form()

if not run.validate(False):
	run.error("Unauthorized.")

data = run.query('SELECT COUNT(*),ip FROM log WHERE result = "Unauthorized." AND date BETWEEN NOW() - INTERVAL 1 DAY AND NOW() GROUP BY ip;')

total = 0
for row in data:
	print("{}: {}<br>\n".format(row[1], row[0]))
	if int(row[0]) > 5:
		run.notify("There were [{}] unauthorized requests from [{}].".format(row[0], row[1]))
	total += int(row[0])

if total > 10:
	run.notify("There were [{}] unauthorized requests in the past day.".format(total))

run.done()
