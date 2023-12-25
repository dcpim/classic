#!/usr/bin/python3
# Process syslog, apache log and save to database

import os
import run
import sys
import time
import json
import connix
import hashlib
from ipwhois import IPWhois

months = {'Jan':'01', 'Feb':'02', 'Mar':'03', 'Apr':'04', 'May':'05', 'Jun':'06', 'Jul':'07', 'Aug':'08', 'Sep':'09', 'Oct':'10', 'Nov':'11', 'Dec':'12'}

print(connix.header())
form = connix.form()

if not run.validate(False):
	run.error("Unauthorized.")

# Go through syslog and save to database
data = connix.cmd("grep error /var/log/syslog")

for line in data.split('\n'):
	if len(line.split(' ')) > 2:
		id = hashlib.sha1(line.encode('utf-8')).hexdigest()
		date = "{} {}".format(line.split('T')[0], connix.in_tag(line, 'T', '.'))
		process = line.split(' ')[2].split('[')[0].split('/')[0].split(':')[0]
		message = line.split(' ', 3)[3]
		run.sql("INSERT IGNORE INTO syslog (id, date, process, message) VALUES (%s, %s, %s, %s);", id, date, process, message)

# Go through auth logs
data = connix.cmd("grep invalid /var/log/auth.log")

for line in data.split('\n'):
	if len(line.split(' ')) > 2:
		id = hashlib.sha1(line.encode('utf-8')).hexdigest()
		date = "{} {}".format(line.split('T')[0], connix.in_tag(line, 'T', '.'))
		process = line.split(' ')[2].split('[')[0]
		message = line.split(' ', 3)[3]
		run.sql("INSERT IGNORE INTO syslog (id, date, process, message) VALUES (%s, %s, %s, %s);", id, date, process, message)

# Go through apache logs and save to database
data = connix.cmd("grep -v \" 200 \" /var/log/apache2/other_vhosts_access.log | grep -v \" 304 \"")

for line in data.split('\n'):
	if len(line.split(' ')) > 1:
		date = connix.in_tag(line, "[", "]")
		date2 = "{}-{}-{} {}:{}:{}".format(date.split('/')[2].split(':')[0], months[date.split('/')[1]], date.split('/')[0], date.split(':')[1], date.split(':')[2], date.split(':')[3].split(' ')[0])
		ip = line.split(' ')[1]
		code = int(line.split('" ')[1].split()[0])
		url = line.split('"')[1][:99]
		if url != "-" and url != "":
			run.sql("INSERT IGNORE INTO wwwlogs (date, code, ip, url) VALUES (%s, %s, %s, %s);", date2, code, ip, url)

# Scan www logs for unknown countries
rows = run.query("SELECT ip FROM wwwlogs WHERE orgname IS NULL GROUP BY ip;")

for row in rows:
	obj = IPWhois(row[0])
	try:
		res=obj.lookup_whois()
		country = res["nets"][0]['country']
		orgname = res["nets"][0]['name'][:99]
	except:
		country = "??"
		orgname = "Unknown"
	run.sql("UPDATE wwwlogs SET country = %s, orgname = %s WHERE ip = %s;", country, orgname, row[0])

# Done
run.done()

