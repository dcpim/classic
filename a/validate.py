#!/usr/bin/python3
# This script validates a TOTP token for a user.

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

if 'username' not in form or 'token' not in form:
	run.error("Missing input.")

username = connix.alphanum(form['username'])
token = int(form['token'])

def hotp(key, counter, digits=6, digest='sha1'):
	key = base64.b32decode(key.upper() + '=' * ((8 - len(key)) % 8))
	counter = struct.pack('>Q', counter)
	mac = hmac.new(key, counter, digest).digest()
	offset = mac[-1] & 0x0f
	binary = struct.unpack('>L', mac[offset:offset+4])[0] & 0x7fffffff
	return str(binary)[-digits:].zfill(digits)

def totp(key, time_step=30, digits=6, digest='sha1'):
	return hotp(key, int(time.time() / time_step), digits, digest)

# Get TOTP key
results = run.query("SELECT totp FROM users WHERE username = %s;", username)
try:
	key = results[0][0]
except:
	time.sleep(1)
	run.error("No contact found.")

# Check token
try:
	if int(totp(key)) != token:
		run.error("Invalid token.")
except:
	run.error("Invalid token")

# Get current token
results = run.query("SELECT curtoken FROM token ORDER BY id DESC LIMIT 1;")
token = results[0][0]

# Return token
print("###{}|||".format(token))

run.done()
