#!/usr/bin/python3
# Remove all active sessions for a user

import connix
import run
import os
import datetime
from hashlib import sha1

print(connix.header())
form = connix.form()

if not run.validate(True):
	run.error("Unauthorized.")

if 'HTTP_COOKIE' in os.environ:
	cookies = os.environ['HTTP_COOKIE']
	cookies = cookies.split('; ')

	for cookie in cookies:
		cookie = cookie.split('=')
		if cookie[0] == "dcpim_net_session":
			username = cookie[1]
		if cookie[0] == "dcpim_net_token":
			passwd = cookie[1]

# Gather user from database
user = None
users = run.query("SELECT id,username,password,last_reset FROM users;")
for row in users:
	if row[1] == username and sha1(row[2].encode('utf-8')).hexdigest() == passwd:
		user = row

# User was not found
if not user:
	run.error("User not found in database!")

# Clear session table
run.sql("DELETE FROM sessions WHERE username = %s", user[1])

print("<meta http-equiv='refresh' content='1; URL=/' />")
run.done()

