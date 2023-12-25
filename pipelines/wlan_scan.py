#!/usr/bin/python3
# This is called by Gura to input the list of local WiFi devices in the database

import pymysql
import connix
import base64
import sys
import os
import run

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

for entry in data_decoded.split('Nmap scan'):
	mac = connix.in_tag(entry, "MAC Address: ", " ")
	ip = connix.in_tag(entry, "report for ", "\n")
	name = connix.in_tag(connix.in_tag(entry, "MAC", "\n"), "(", ")")
	if len(mac) > 1 and len(ip) > 1:
		results = run.query("SELECT * FROM wlan_scan WHERE mac = %s;", mac)
		if len(results) == 0:
			print("* New unique device found found: {} {} ({})".format(mac, ip, name))
			run.notify("New unique device detected by wlan_scan: {} {} ({})".format(mac, ip, name))
			run.sql("INSERT INTO wlan_scan (mac, ip, first_seen, last_seen, notes, dns) VALUES (%s, %s, %s, %s, %s, %s);", mac, ip, connix.now().split(' ')[0], connix.now().split(' ')[0], name, "")
		else:
			print("* Device already in database: {} {} ({})".format(mac, ip, results[0][5]))
			if results[0][4] != connix.now().split(' ')[0]:
				print("Last seen was: {}, updating value.".format(results[0][4]))
				run.sql("UPDATE wlan_scan SET last_seen = %s WHERE mac = %s;", connix.now().split(' ')[0], mac)
			if results[0][2] != ip:
				print("IP changed from {} to {}, updating value.".format(results[0][2], ip))
				run.sql("UPDATE wlan_scan SET ip = %s WHERE mac = %s;", ip, mac)
				data = run.query("SELECT dns FROM wlan_scan WHERE mac = %s;", mac)
				dns = ""
				for entry in data:
					dns = entry[0]
				if dns is not None and dns != "":
					record_set = '{ "Comment": "Automatically set by set_wlan.py","Changes": [{ "Action": "UPSERT", "ResourceRecordSet": { "Name": "' + dns + '.' + run.config('DNS_DOMAIN') + ', "Type": "A", "TTL": 300, "ResourceRecords": [{ "Value": "' + ip + '"}]}}]}'
					tmp = "/tmp/{}.json".format(connix.guid())
					with open(tmp, "w") as fd:
						fd.write(record_set)
					run.cmd("aws route53 change-resource-record-sets --hosted-zone-id {} --change-batch file://{}".format(run.config('DNS_ZONE'), tmp))
					run.cmd("rm -f {}".format(tmp))

run.done()
