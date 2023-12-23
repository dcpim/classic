#!/usr/bin/python3
# Update a project

import connix
import sys
import os
import run

print(connix.header())
form = connix.form()

if not run.validate(True):
	run.error("Unauthorized.")

if 'name' not in form or 'client' not in form or 'date' not in form or 'id' not in form:
	run.error("Missing input.")

id = form['id']
name = form['name']
client = form['client']
date = form['date']
address = ""
if 'address' in form:
	address = form['address']
end_date = ""
if 'end_date' in form:
	end_date = form['end_date']
contact_name = ""
if 'contact_name' in form:
	contact_name = form['contact_name']
contact_email = ""
if 'contact_email' in form:
	contact_email = form['contact_email']
contact_phone = ""
if 'contact_phone' in form:
	contact_phone = form['contact_phone']
default_rate = int(form['default_rate'])
default_hours = int(form['default_hours'])
reason = ""
if 'reason' in form:
	reason = form['reason']

# Insert project into database
run.sql("UPDATE projects SET reason = %s, default_rate = %s, default_hours = %s, name = %s, client = %s, date = %s, end_date = %s, contact_name = %s, contact_email = %s, contact_phone = %s, address = %s WHERE id = %s;", reason, default_rate, default_hours, name, client, date, end_date, contact_name, contact_email, contact_phone, address, id)

# Add preset if needed
run.sql("INSERT IGNORE INTO presets (type, name, address) VALUES (%s, %s, %s);", "client", client, "")
run.sql("UPDATE presets SET address = %s WHERE name = %s;", address, client)

# Update last updated date
run.sql("UPDATE projects SET last_update = %s WHERE id = %s;", connix.now(), id)

print("<meta http-equiv='refresh' content='0; URL=./?id={}' />".format(id))
run.done()
