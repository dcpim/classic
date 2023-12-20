#!/usr/bin/python3
# Deploy files across the system. Must be run as root.

import os
import json
import connix

if os.geteuid() != 0:
	print("Must be run as root.")
	exit(1)

with open("deploy.json", "r") as fd:
	files = json.loads(fd.read())

for k,v in files.items():
	print("* {} -> {}".format(k, v))
	connix.cmd("cp \"{}\" \"{}\"".format(k, v))
	if ".py" in v or ".php" in v:
		connix.cmd("chmod +x \"{}\"".format(v))

print("Done.")
