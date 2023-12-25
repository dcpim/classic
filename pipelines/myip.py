#!/usr/bin/python3
# This is called by Adusa to check if my home IP changed

import connix
import run
import os

print(connix.header())
form = connix.form()

sg = "sg-0faf49743406338cc"

# No validate since MY_IP would change if this is needed

if 'token' not in form:
	run.error("Missing field.")

if form['token'] != run.config('DEVICE_TOKEN'):
	run.error("Unauthorized.")

if os.environ['REMOTE_ADDR'] != run.config('MY_IP'):
	run.notify("Warning: MY IP changed from [{}] to [{}].".format(run.config('MY_IP'), os.environ['REMOTE_ADDR']))
	run.sql("UPDATE config SET v = %s WHERE k = %s;", os.environ['REMOTE_ADDR'], "MY_IP")
	run.cmd("aws ec2 authorize-security-group-ingress --group-id {} --ip-permissions IpProtocol=tcp,FromPort=22,ToPort=22,IpRanges=\"[{CidrIp={}/32,Description=MYIP change script}]\"".format(sg, os.environ['REMOTE_ADDR']))
else:
	print("No change.")

run.done()
