#!/usr/bin/python3
# Setup mail blacklist and update hits counter, process mail delivery failures.

import os
import run
import sys
import boto3
import time
import json
import connix
from datetime import datetime, timedelta

print(connix.header())
form = connix.form()

if not run.validate(False):
	run.error("Unauthorized.")

emails = run.query("SELECT * FROM mail_blacklist;")
blacklist = ""

# Go through emails and get hits counter
for email in emails:
	blacklist += "{} REJECT Emails from your address are not allowed on this server.\n".format(email[1])
	hits = int(connix.cmd(" grep \"{}\" /var/log/mail.log | grep \"from=\" | wc -l".format(email[1])))
	if hits < int(email[3]): # mail log file was rotated
		hits += int(email[3])
	run.sql("UPDATE mail_blacklist SET hits = %s WHERE id = %s", hits, email[0])

# Save blacklist
print(blacklist)
with open("/etc/postfix/blacklist", "w") as fd:
	fd.write(blacklist)

# Update running config
run.cmd("/usr/sbin/postmap /etc/postfix/blacklist 2>&1")
run.cmd("systemctl reload postfix 2>&1")

# Get delivery failures
failures = connix.cmd("grep \"NOQUEUE\" /var/log/mail.log").split('\n')
for failure in failures:
	if "T" in failure and "NOQUEUE" in failure:
		date1 = failure.split('.')[0]
		date = "{} {}".format(date1.split('T')[0], date1.split('T')[1])
		msg = failure.split('NOQUEUE: ')[1]
		run.sql("INSERT IGNORE INTO mail_failures (date, message) VALUES (%s, %s);", date, msg)

# Done
run.done()

