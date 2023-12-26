#!/usr/bin/python3
# Update notes and hostname entries for found LAN devices.

import pymysql
import connix
import sys
import os
import run

hosted_id = run.config('DNS_ZONE')

print(connix.header())
form = connix.form()

if not run.validate(True):
	run.error("Unauthorized.")

if 'id' not in form:
	run.error("Missing fields.")

mac = form['id']

# Update notes
if "notes" in form and form['notes'] != "":
	notes = form['notes']
	run.sql("UPDATE wlan_scan SET notes = %s WHERE mac = %s;", notes, mac)

# Update hostname
if "dns" in form and form['dns'] != "":
	dns = form['dns']
	run.sql("UPDATE wlan_scan SET dns = %s WHERE mac = %s;", dns, mac)

	data = run.query("SELECT ip FROM wlan_scan WHERE mac = %s;", mac)
	ip = ""
	for entry in data:
		ip = entry[0]
	if ip != "":
		record_set = '{ "Comment": "Automatically set by set_wlan.py","Changes": [{ "Action": "UPSERT", "ResourceRecordSet": { "Name": "' + dns + '.' + run.config('DNS_DOMAIN') + '", "Type": "A", "TTL": 300, "ResourceRecords": [{ "Value": "' + ip + '"}]}}]}'
		tmp = "/tmp/{}.json".format(connix.guid())
		with open(tmp, "w") as fd:
			fd.write(record_set)
		run.cmd("aws route53 change-resource-record-sets --hosted-zone-id {} --change-batch file://{}".format(hosted_id, tmp))
		run.cmd("rm -f {}".format(tmp))

# Done
run.done(True)
