#!/usr/bin/python3
# Store DB size information

import os
import run
import connix

print(connix.header())
form = connix.form()

if not run.validate(False):
	run.error("Unauthorized.")

data = run.query("SELECT TABLE_NAME, TABLE_ROWS, DATA_LENGTH+INDEX_LENGTH AS SIZE FROM information_schema.TABLES WHERE TABLE_SCHEMA = %s;", os.environ['DB_DATABASE'])

tsize = 0
trows = 0
for row in data:
	tsize += int(row[2]/1000) # too big for int
	trows += row[1]

run.sql("INSERT IGNORE INTO db_storage (tsize, trows, date) VALUES (%s, %s, %s);", tsize, trows, connix.now().split(' ')[0])

run.done()
