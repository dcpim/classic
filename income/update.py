#!/usr/bin/python3
# Update an income entry

import connix
import sys
import os
import run

print(connix.header())
form = connix.form()

if not run.validate(True):
	run.error("Unauthorized.")

if 'id' not in form or 'credit' not in form or 'date' not in form or 'debit' not in form:
	run.error("Missing input.")

id = int(form['id'])
note = form['note']
date = form['date']
credit = float(form['credit'])
debit = float(form['debit'])

# Update file in the database
run.sql("UPDATE income SET note = %s, date = %s, credit = %s, debit = %s WHERE id = %s;", note, date, credit, debit, id)

run.done()
