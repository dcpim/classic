#!/usr/bin/python3
# This is called by Gura to input the list of SSID access points nearby

import pymysql
import connix
import base64
import sys
import os
import run
import json
import cgi

print(connix.header())
form = connix.form()

if 'token' not in form or 'data' not in form:
	run.error("Missing field.")

if form['token'] != run.config('DATA_TOKEN'):
	run.error("Unauthorized.")

data = form['data'].replace(' ','+')
data_bytes = data.encode("ascii")
data_decoded_bytes = base64.b64decode(data_bytes)
data_decoded = data_decoded_bytes.decode("ascii")
entries = json.loads(data_decoded)

for entry in entries:
	rows = run.query("SELECT last_seen FROM wlan_ssid WHERE mac = %s", entry['mac'])
	if len(rows) == 0:
		run.sql("INSERT INTO wlan_ssid (mac, channel, frequency, encryption, quality, name, vendor, first_seen, last_seen) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s);", entry['mac'], entry['channel'], entry['frequency'], entry['encryption'], entry['quality'], entry['name'], entry['vendor'], connix.now().split()[0], connix.now().split()[0])
	else:
		run.sql("UPDATE wlan_ssid SET channel = %s, frequency = %s, encryption = %s, quality = %s, name = %s, vendor = %s, last_seen = %s WHERE mac = %s;", entry['channel'], entry['frequency'], entry['encryption'], entry['quality'], entry['name'], entry['vendor'], connix.now().split()[0], entry['mac'])

run.done()
