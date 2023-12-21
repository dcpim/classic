#!/usr/bin/python3
# Update existing collectible

import connix
import sys
import os
import run

print(connix.header())
form = connix.form()

if not run.validate(True):
	run.error("Unauthorized.")

if 'id' not in form or 'brand' not in form or 'type' not in form or 'sold' not in form or 'stars' not in form or 'subtype' not in form:
	run.error("Missing input.")

id = int(form['id'])
type = form['type']
subtype = form['subtype']
brand = form['brand']
date = ""
if 'date' in form:
	date = form['date']
name = ""
if 'name' in form:
	name = form['name']
procurement = ""
if 'procurement' in form:
	procurement = form['procurement']
currency = ""
if 'currency' in form:
	currency = form['currency']
price = 0
if 'price' in form:
	price = int(form['price'])
sold = int(form['sold'])
stars = int(form['stars'])

# Update file in the database
run.sql("UPDATE collection SET subtype = %s, stars = %s, procurement = %s, date = %s, type = %s, brand = %s, name = %s, sold = %s, price = %s, currency = %s WHERE id = %s;", subtype, stars, procurement, date, type, brand, name, sold, price, currency, id)

run.done()
