#!/usr/bin/python3
# Update a billable

import connix
import sys
import os
import run

print(connix.header())
form = connix.form()

if not run.validate(True):
	run.error("Unauthorized.")

if 'rate' not in form or 'note' not in form or 'qty' not in form or 'id' not in form:
	run.error("Missing input.")

note = form['note']
qty = int(form['qty'])
id = int(form['id'])
rate = float(form['rate'])

# Update bill in the database
run.sql("UPDATE billables SET qty = %s, note = %s, rate = %s WHERE id = %s;", qty, note, rate, id);

run.done()
