#!/usr/bin/python3
# Update an inventory item

import connix
import sys
import os
import run

print(connix.header())
form = connix.form()

if not run.validate(True):
	run.error("Unauthorized.")

if 'price' not in form or 'serial' not in form or 'date' not in form or 'id' not in form or 'name' not in form or 'sold' not in form:
	run.error("Missing fields.")

name = form['name']
serial = form['serial']
date = form['date']
id = int(form['id'])
sold = int(form['sold'])
price = float(form['price'])
invoice = ""
if 'invoice' in form:
	invoice = form['invoice']
statement = ""
if 'statement' in form:
	statement = form['statement']
notes = ""
if 'notes' in form:
	notes = form['notes']
link = ""
if 'link' in form:
	link = form['link']

# Update item in the database
run.sql("UPDATE inventory SET statement = %s, name = %s, serial = %s, date = %s, price = %s, invoice = %s, sold = %s, notes = %s, link = %s WHERE id = %s;", statement, name, serial, date, price, invoice, sold, notes, link, id);

run.done()
