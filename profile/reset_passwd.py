#!/usr/bin/python3
# Reset a password provided through the reset password form

import connix
import sys
import os
import run
import datetime
from hashlib import sha1
from dateutil.relativedelta import relativedelta

print(connix.header())
form = connix.form()

if not run.validate(True):
	run.error("Unauthorized.")

if 'old_passwd' not in form or 'new_passwd1' not in form or 'new_passwd2' not in form:
	run.error("Missing input.")

old_passwd1 = form['old_passwd']
new_passwd1 = form['new_passwd1']
new_passwd2 = form['new_passwd2']
new_passwd = sha1(new_passwd1.encode('utf-8')).hexdigest()
old_passwd = sha1(old_passwd1.encode('utf-8')).hexdigest()
username = ""
token = ""

# Check if passwords match
if new_passwd1 != new_passwd2:
	run.error("New passwords do not match!")

# Check if new ans old password are the same
if new_passwd1 == old_passwd1:
	run.error("Please pick a new password!")

# Check if password is too short
if len(new_passwd1) < 8:
	run.error("Password is too short!")

# Parse cookies
if 'HTTP_COOKIE' in os.environ:
	cookies = os.environ['HTTP_COOKIE']
	cookies = cookies.split('; ')

	for cookie in cookies:
		cookie = cookie.split('=')
		if cookie[0] == "dcpim_net_session":
			username = cookie[1]
		if cookie[0] == "dcpim_net_token":
			token = cookie[1]

old_token = sha1(old_passwd.encode('utf-8')).hexdigest()

# Check cookie token with old password provided in the form
if token != old_token:
	run.error("Old password does not match session token!")

# Gather user from database
user = None
users = run.query("SELECT id,username,password,last_reset FROM users;")
for row in users:
	if row[1] == username and row[2] == old_passwd:
		user = row

# User was not found
if not user:
	run.error("User not found in database!")

# Check if password is due to be reset
one_year_ago = datetime.datetime.now() - relativedelta(years=1)
last_reset = datetime.datetime.strptime(user[3], "%Y-%m-%d")

if last_reset > one_year_ago:
	run.error("Password is not due to be reset.")

# Update database
run.sql("UPDATE users SET password = %s, last_reset = %s WHERE id = %s;", new_passwd, connix.now().split(' ')[0], user[0])

run.done(True)
