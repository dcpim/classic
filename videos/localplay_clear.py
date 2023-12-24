#!/usr/bin/python3
# Clear the last played localplay entry

import connix
import sys
import os
import run

print(connix.header())
form = connix.form()

if not run.validate(False):
	run.error("Unauthorized.")

# Clear queue
with open("../localplay/play.txt", 'w') as fd:
	fd.write("")

run.done(True)
