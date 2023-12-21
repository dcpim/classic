#!/usr/bin/python3
# This script sends a token to an SNS endpoint.

import connix
import sys
import os
import run

print(connix.header())
form = connix.form()

if not run.validate(False):
	run.error("Unauthorized.")

if 'username' not in form:
	run.error("Missing input.")

username = form['username']

# Get current token
results = run.query("SELECT curtoken FROM token ORDER BY id DESC LIMIT 1;")
token = results[0][0]

# Get SNS ARN for user
results = run.query("SELECT sms FROM users WHERE username = %s;", username)

if len(results[0][0]) < 5:
	run.error("No contact found.")

run.cmd("aws sns publish --region '{}' --topic-arn '{}' --subject 'Login token' --message 'This is your login token: {}'".format(run.config('AWS_REGION'), results[0][0], token))

run.done()
