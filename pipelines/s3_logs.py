#!/usr/bin/python3
# Process S3 access logs and save to database

import os
import run
import sys
import time
import json
import connix
from ipwhois import IPWhois

print(connix.header())
form = connix.form()

if not run.validate(False):
	run.error("Unauthorized.")

buckets = [
	run.config('BUCKET_ACCOUNTING'),
	run.config('BUCKET_FILES'),
	run.config('BUCKET_BACKUPS'),
	run.config('BUCKET_MUSIC'),
	run.config('BUCKET_VIDEOS'),
	run.config('BUCKET_IMAGES')
]
months = {'Jan':'01', 'Feb':'02', 'Mar':'03', 'Apr':'04', 'May':'05', 'Jun':'06', 'Jul':'07', 'Aug':'08', 'Sep':'09', 'Oct':'10', 'Nov':'11', 'Dec':'12'}

for bucket in buckets:
	print(bucket)
	data = connix.cmd("aws s3 ls s3://{}/{}/".format(run.config('BUCKET_LOGS'), bucket))
	for line in data.split('\n'):
		if len(line.split(' ')) > 3:
			file = line.split(' ')[-1]
			tmp = "/tmp/{}.txt".format(connix.guid())
			run.cmd("aws s3 cp s3://{}/{}/{} {}".format(run.config('BUCKET_LOGS'), bucket, file, tmp))
			with open(tmp, 'r') as fd:
				raw = fd.read()
			run.cmd("rm -f {}".format(tmp))
			for line in raw.split('\n'):
				if len(line.split(' ')) > 7:
					ip = line.split(' ')[4]
					date = connix.in_tag(line, "[", "]")
					date2 = "{}-{}-{} {}:{}:{}".format(date.split('/')[2].split(':')[0], months[date.split('/')[1]], date.split('/')[0], date.split(':')[1], date.split(':')[2], date.split(':')[3].split(' ')[0])
					query = line.split(' ')[7]
					object = line.split(' ')[8][:99]
					try:
						code = int(line.split('" ')[1].split()[0])
					except:
						code = 0
					if ip == "-":
						pass
					elif query == "REST.PUT.BUCKETPOLICY" and code == 204:
						pass
					else:
						try:
							obj = IPWhois(ip)
							res=obj.lookup_whois()
							country = res["nets"][0]['country']
							orgname = res["nets"][0]['name'][:99]
						except:
							country = "??"
							orgname = "Unknown"
						run.sql("INSERT IGNORE INTO s3_logs (date, ip, query, object, code, orgname, country, bucket) VALUES (%s, %s, %s, %s, %s, %s, %s, %s);", date2, ip, query, object, code, orgname, country, bucket)
			run.cmd("aws s3 rm s3://{}/{}/{}".format(run.config('BUCKET_LOGS'), bucket, file))

run.done()
