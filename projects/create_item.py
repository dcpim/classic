#!/usr/bin/python3
# Add an inventory item

import connix
import sys
import os
import run

print(connix.header())
form = connix.form()

if not run.validate(True):
	run.error("Unauthorized.")

if 'name' not in form or 'serial' not in form or 'price' not in form or 'prjid' not in form:
	run.error("Missing input.")

name = form['name']
serial = form['serial']
prjid = int(form['prjid'])
price = float(form['price'])

# Create bill in the database
run.sql("INSERT INTO inventory (prjid, name, serial, date, price, invoice, notes, link, sold) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s);", prjid, name, serial, connix.now().split(' ')[0], price, "", "", "", 0)

# Update last updated date
run.sql("UPDATE projects SET last_update = %s WHERE id = %s;", connix.now(), prjid)

print("<meta http-equiv='refresh' content='0; URL=./inventory.php?id={}' />".format(prjid))
run.done()
