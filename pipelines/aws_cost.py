#!/usr/bin/python3
# This script checks the current AWS billing amount for the mount and sends an alert if
# it seems to high compared with the base cost.
# Input values: base

import connix
import sys
import os
import run
import json
import datetime

print(connix.header())
form = connix.form()

if not run.validate(False):
	run.error("Unauthorized.")

now = datetime.datetime.now()
base = 30
if 'base' in form:
	base = int(form['base'])
maxcost = base + (1.5 * int(now.strftime("%d")))

# Get cost data
if now.strftime("%d") == "01":
	print("First of the month... skipping.")
	run.done()

cmd = "aws ce get-cost-and-usage --time-period Start={}-01,End={} --metrics BlendedCost --granularity MONTHLY".format(now.strftime("%Y-%m"),now.strftime("%Y-%m-%d"))
run.cmd("echo \"{}\"".format(cmd))
data = connix.cmd(cmd)
costdata = json.loads(data)
cost = float(costdata['ResultsByTime'][0]['Total']['BlendedCost']['Amount'])

print("<p>Maximum cost for the current date is ${} USD.<br>".format(maxcost))
print("Currently monthly cost is ${:.2f} USD.</p>".format(cost))

if cost > maxcost:
	run.error("Cost estimate is currently very high.")

run.done()
