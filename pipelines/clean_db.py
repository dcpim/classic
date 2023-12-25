#!/usr/bin/python3
# This script cleans the database of old data
# Input value: days, the number of days to delete data before

import connix
import random
import run

print(connix.header())
form = connix.form()

if not run.validate(False):
	run.error("Unauthorized.")

days = 90
if 'days' in form:
	days = int(form['days'])

if days < 7:
	run.error("Minimum value is 7 days.")

# Delete old automate runs
run.sql("DELETE FROM automate_runs WHERE date <= (NOW() - INTERVAL %s DAY);", days)
run.sql("OPTIMIZE TABLE automate_runs;")

# Delete old event logs
run.sql("DELETE FROM log WHERE date <= (NOW() - INTERVAL %s DAY);", days)
run.sql("OPTIMIZE TABLE log;")

# Delete old S3 access logs
run.sql("DELETE FROM s3_logs WHERE date <= (NOW() - INTERVAL %s DAY);", days)
run.sql("OPTIMIZE TABLE s3_logs;")

# Delete old web access logs
run.sql("DELETE FROM wwwlogs WHERE date <= (NOW() - INTERVAL %s DAY);", days)
run.sql("OPTIMIZE TABLE wwwlogs;")

run.done()
