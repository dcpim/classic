#!/usr/bin/python3
# Update a bill

import connix
import sys
import os
import run

print(connix.header())
form = connix.form()

if not run.validate(True):
	run.error("Unauthorized.")

note = form['note']
tax_location = form['tax_location']
date = form['date']
id = int(form['id'])
rate = int(form['rate'])
hours = int(form['hours'])
discount = 0
if 'discount' in form:
	discount = float(form['discount'])

# Update bill in the database
run.sql("UPDATE bills SET hours = %s, discount = %s, note = %s, tax_location = %s, date = %s, rate = %s WHERE id = %s;", hours, discount, note, tax_location, date, rate, id);

run.done()
