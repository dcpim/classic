#!/usr/bin/python3
# This script creates a token to be used by the login process.

import connix
import random
import run

print(connix.header())
form = connix.form()

if not run.validate(False):
	run.error("Unauthorized.")

# Create a random token
run.sql("DELETE FROM token;")
run.sql("INSERT INTO token (curtoken) VALUES (%s);", random.randrange(100000,999999))

run.done()
