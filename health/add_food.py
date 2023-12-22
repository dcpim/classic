#!/usr/bin/python3
# Add a new food item

import pymysql
import connix
import sys
import os
import run

print(connix.header())
form = connix.form()

if not run.validate(True):
	run.error("Unauthorized.")

if 'name' not in form or 'sugar' not in form or 'fiber' not in form or 'calories' not in form or 'type' not in form or 'year' not in form:
	run.error("Missing field.")

sugar = int(form['sugar'])
fiber = int(form['fiber'])
calories = int(form['calories'])
name = "{} - {} - {}".format(form['year'], form['type'], form['name'])
id = -1
if "id" in form and form['id'] != "":
	id = int(form['id'])

# Add food to database
if id != -1:
	run.sql("UPDATE foods SET name = %s, sugar = %s, fiber = %s, calories = %s WHERE id = %s;", name, sugar, fiber, calories, id)
else:
	run.sql("INSERT INTO foods (name, sugar, fiber, calories, date) VALUES (%s, %s, %s, %s, %s);", name, sugar, fiber, calories, connix.now().split(' ')[0])

run.done(True)
