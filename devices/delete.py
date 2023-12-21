#!/usr/bin/python3
# Delete a device from the database

import pymysql
import connix
import sys
import os
import run

print(connix.header())
form = connix.form()

if not run.validate(True):
	run.error("Unauthorized.")

if 'mac' not in form:
	run.error("Missing fields.")

mac = form['mac']

# Delete from database
run.sql("DELETE FROM wlan_scan WHERE mac = %s;", mac)

run.done(True)

