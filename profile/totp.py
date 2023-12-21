#!/usr/bin/python3
# Add TOTP token to a user

import connix
import sys
import os
import run
import datetime
from hashlib import sha1
from dateutil.relativedelta import relativedelta
import base64
import hmac
import struct
import sys
import time

print(connix.header())
form = connix.form()

if not run.validate(False):
	run.error("Unauthorized.")

if 'key' not in form or 'token' not in form:
	run.error("Missing input.")

mkey = form['key']
mtoken = int(form['token'])
username = ""
passwd = ""

def hotp(key, counter, digits=6, digest='sha1'):
	key = base64.b32decode(key.upper() + '=' * ((8 - len(key)) % 8))
	counter = struct.pack('>Q', counter)
	mac = hmac.new(key, counter, digest).digest()
	offset = mac[-1] & 0x0f
	binary = struct.unpack('>L', mac[offset:offset+4])[0] & 0x7fffffff
	return str(binary)[-digits:].zfill(digits)

def totp(key, time_step=30, digits=6, digest='sha1'):
	return hotp(key, int(time.time() / time_step), digits, digest)

# Parse cookies
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

# Check if TOTP token is correct
token = int(totp(mkey))
if token != mtoken:
	run.error("Token is invalid. Please try registering your device again.")

# Update database
run.sql("UPDATE users SET totp = %s WHERE id = %s;", mkey, user[0])

run.done(True)
