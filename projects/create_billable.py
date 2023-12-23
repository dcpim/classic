#!/usr/bin/python3
# Create a new billable

import connix
import sys
import os
import run

print(connix.header())
form = connix.form()

if not run.validate(True):
	run.error("Unauthorized.")

if 'rate' not in form or 'note' not in form or 'qty' not in form or 'billid' not in form:
	run.error("Missing input.")

note = form['note']
qty = int(form['qty'])
rate = float(form['rate'])
billid = int(form['billid'])

# Create bill in the database
run.sql("INSERT INTO billables (billid, note, rate, qty) VALUES (%s, %s, %s, %s);", billid, note, rate, qty);

print("<meta http-equiv='refresh' content='0; URL=./billables.php?id={}' />".format(billid))
run.done()
