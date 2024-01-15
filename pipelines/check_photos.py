#!/usr/bin/python3
# This script checks photos that have no description uploaded in the current year.
# Input value: year, the year to start looking

import pymysql
import connix
import sys
import os
import run
import datetime

print(connix.header())
form = connix.form()

if not run.validate(False):
	run.error("Unauthorized.")

year = datetime.date.today().year
if 'year' in form:
	year = int(form['year'])

# List photos without names
results = run.query("SELECT id,event FROM photos WHERE name = %s AND year >= %s", "", year)

if len(results) > 0:
	cats = []
	for result in results:
		if result[1] not in cats:
			cats.append(result[1])

	print(cats)

	task = "There are {} photos without names for events {}".format(len(results), cats)

	count = run.query("SELECT COUNT(*) FROM tasks WHERE prjid = 1 AND task = '" + task + "';")
	if int(count[0][0]) == 0:
		run.sql("INSERT INTO tasks (task, date, prjid, repeatset) VALUES (%s, %s, %s, %s);", task, connix.now().split(' ')[0], 1, "Never")

run.done()
