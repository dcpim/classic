#!/usr/bin/python3
# This script updates S3 bucket policies. It's called by automation and by the login process.
# Input values: buckets

import connix
import os
import run

print(connix.header())
form = connix.form()

buckets = [
	run.config('BUCKET_ACCOUNTING'),
	run.config('BUCKET_FILES'),
	run.config('BUCKET_MUSIC'),
	run.config('BUCKET_VIDEOS'),
	run.config('BUCKET_IMAGES')
]

if 'buckets' in form:
	buckets = []
	for bucket in form['buckets'].split(','):
		buckets.append(connix.remove_spaces(bucket))

rawips = run.query("SELECT ip FROM sessions;")
ips = ""
for ip in rawips:
	if ip[0] not in ips:
		ips += "\"{}/32\",".format(ip[0])
ips += "\"{}/32\"".format(run.config('MY_IP'))

for bucket in buckets:
	with open("/var/www/html/pipelines/bucket-policies/{}.json".format(bucket),'r') as fd:
		policy = fd.read()

	policy = policy.replace("####", ips)
	tmp = "/tmp/{}.json".format(connix.guid())
	with open(tmp,'w') as fd:
		fd.write(policy)
	run.cmd("cat {} && aws s3api put-bucket-policy --bucket {} --policy file://{}".format(tmp, bucket, tmp))
	run.cmd("rm -f {}".format(tmp))

run.done()
