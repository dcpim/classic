#!/usr/bin/python3
# This script is called from remote devices to insert stats into the database for the Devices page charts.

import pymysql
import connix
import base64
import sys
import os
import run

print(connix.header())
form = connix.form()

if 'token' not in form or 'device' not in form or 'disk' not in form or 'update' not in form or 'uptime' not in form:
	run.error("Missing field.")

if form['token'] != run.config('DEVICE_TOKEN'):
	run.error("Unauthorized.")

device = connix.alphanum(form['device'])
disk = form['disk']
update = form['update']
uptime = str(form['uptime']).strip()
usage = int(form['usage'])

# Remove existing entries for device
run.sql("DELETE FROM device_stats WHERE device = %s AND disk = %s;", device, disk)

# Add stats to database
run.sql("INSERT INTO device_stats (date, device, disk, diskusage, updatedate, uptime) VALUES (%s, %s, %s, %s, %s, %s);", connix.now().split(' ')[0], device, disk, usage, update, uptime)

# Alert if > 90%
if usage > 90:
	run.notify("Disk space on [{}] was reported at [{}%].".format(device, usage))

run.done()
