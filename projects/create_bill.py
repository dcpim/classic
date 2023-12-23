#!/usr/bin/python3
# Create a new bill

import connix
import sys
import os
import run

print(connix.header())
form = connix.form()

if not run.validate(True):
	run.error("Unauthorized.")

if 'tax_location' not in form or 'note' not in form or 'date' not in form or 'prjid' not in form or 'rate' not in form or 'hours' not in form:
	run.error("Missing input.")

note = form['note']
tax_location = form['tax_location']
date = form['date']
prjid = int(form['prjid'])
rate = int(form['rate'])
hours = int(form['hours'])

# Create bill in the database
run.sql("INSERT INTO bills (rate, prjid, note, tax_location, date, discount, hours) VALUES (%s, %s, %s, %s, %s, %s, %s);", rate, prjid, note, tax_location, date, 0, hours)

# Update last updated date
run.sql("UPDATE projects SET last_update = %s WHERE id = %s;", connix.now(), prjid)

print("<meta http-equiv='refresh' content='0; URL=./bills.php?id={}' />".format(prjid))
run.done()
