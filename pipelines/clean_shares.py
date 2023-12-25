#!/usr/bin/python3
# Remove files in the BUCKET_FILES/Share bucket not in the database.
# Input values: days

import connix
import boto3
import sys
import run
import os
from datetime import datetime, timedelta

print(connix.header())
form = connix.form()

if not run.validate(False):
	run.error("Unauthorized.")

s3 = boto3.client('s3', region_name = run.config('AWS_REGION'))
token = None
days = 30
if 'days' in form:
	days = int(form['days'])
print("Set days to keep at: {}<br>".format(days))

while True:
	if token:
		objects = s3.list_objects_v2(Bucket=run.config('BUCKET_FILES'), Prefix='Share', ContinuationToken=token)
	else:
		objects = s3.list_objects_v2(Bucket=run.config('BUCKET_FILES'), Prefix='Share')
	for object in objects['Contents']:
		timestamp = datetime.now() - object['LastModified'].replace(tzinfo=None)
		results = run.query("SELECT COUNT(*) FROM utils WHERE type = %s AND url = %s;", "Share", "{}".format(object['Key']))
		if results[0][0] == 0 and timestamp.days > days:
			print("Deleting [{}]...".format(object['Key']))
			s3.delete_object(Bucket=run.config('BUCKET_FILES'), Key=object['Key'])
	if objects['IsTruncated']:
		token = objects['NextContinuationToken']
	else:
		break

run.done()

