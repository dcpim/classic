#!/usr/bin/python3
# This script adds task entries in specific cases

import connix
import run

print(connix.header())
form = connix.form()

if not run.validate(False):
	run.error("Unauthorized.")

# Check for 2 days without food entries
count = run.query("SELECT COUNT(*) FROM nutrition WHERE date >= CURRENT_DATE - INTERVAL 2 DAY;")

if int(count[0][0]) == 0:
	count = run.query("SELECT COUNT(*) FROM tasks WHERE prjid = 1 AND task = 'No food entry detected in the past 2 days.';")
	if int(count[0][0]) == 0:
		run.sql("INSERT INTO tasks (task, date, prjid, repeatset) VALUES (%s, %s, %s, %s);", "No food entry detected in the past 2 days.", connix.now().split(' ')[0], 1, "Never")

# Check for 7 days without journal entries
count = run.query("SELECT COUNT(*) FROM journal WHERE date >= CURRENT_DATE - INTERVAL 7 DAY AND prjid = 17;")

if int(count[0][0]) == 0:
	count = run.query("SELECT COUNT(*) FROM tasks WHERE prjid = 17 AND task = 'No journal entry detected in the past 7 days.';")
	if int(count[0][0]) == 0:
		run.sql("INSERT INTO tasks (task, date, prjid, repeatset) VALUES (%s, %s, %s, %s);", "No journal entry detected in the past 7 days.", connix.now().split(' ')[0], 17, "Never")

run.done()
