#!/usr/bin/python3
# Process storage lens reports and add to DB

import os
import run
import csv
import sys
import time
import json
import connix

print(connix.header())
form = connix.form()

if not run.validate(False):
	run.error("Unauthorized.")

data = run.query("SELECT bucket, ObjectCount FROM s3_storage ORDER BY date DESC LIMIT 20;")
old_counts = {}
for row in data:
	if row[0] not in old_counts:
		old_counts[row[0]] = row[1]
data = run.query("SELECT bucket, DeleteMarkerObjectCount FROM s3_storage ORDER BY date DESC LIMIT 20;")
old_deletes = {}
for row in data:
	if row[0] not in old_deletes:
		old_deletes[row[0]] = row[1]

folders = connix.cmd("aws s3 ls {}".format(run.config('S3_REPORTS')))
for line in folders.split('\n'):
	if len(line.split()) > 1 and "dt=" in line:
		folder = line.split()[1]
		files = connix.cmd("aws s3 ls {}{}".format(run.config('S3_REPORTS'), folder))
		for line2 in files.split('\n'):
			if len(line2.split()) > 1 and ".csv" in line2:
				file = line2.split()[-1]
				print("* {}{}{}".format(run.config('S3_REPORTS'), folder, file))
				tmp = "/tmp/{}.csv".format(connix.guid())
				connix.cmd("aws s3 cp {}{}{} {}".format(run.config('S3_REPORTS'), folder, file, tmp))
				with open(tmp, 'r') as fd:
					data = csv.reader(fd, delimiter=',', quotechar='"')
					results = {}
					for row in data:
						if row[8] != "":
							if row[8] not in results:
								results[row[8]] = {'StorageBytes': 0, 'ObjectCount': 0, 'NonCurrentVersionObjectCount': 0, 'DeleteMarkerObjectCount': 0, 'date': ""}
				with open(tmp, 'r') as fd:
					data = csv.reader(fd, delimiter=',', quotechar='"')
					for row in data:
						if row[8] != "":
							if row[9] in results[row[8]]:
								results[row[8]][row[9]] = row[10]
								results[row[8]]['date'] = row[2]
				for k,v in results.items():
					if v['date'] != "":
						print("{}, {}<br>".format(k, v))
						if k in old_counts:
							a = int(v['ObjectCount'])
							b = int(old_counts[k])
							if a < 100:
								a = 100
							if b < 100:
								b = 100
							if int((abs(a-b)/b)*100) > 10 and k != run.config('BUCKET_LOGS') and k != run.config('BUCKET_DATA'):
								run.notify("Object count for [{}] changed by {}%.".format(k, int((abs(a-b)/b)*100)))
						if k in old_deletes:
							a = int(v['DeleteMarkerObjectCount'])
							b = int(old_deletes[k])
							if a < 100:
								a = 100
							if b < 100:
								b = 100
							if int((abs(a-b)/b)*100) > 10 and k != run.config('BUCKET_LOGS') and k != run.config('BUCKET_DATA'):
								run.notify("Delete count for [{}] changed by {}%.".format(k, int((abs(a-b)/b)*100)))
						run.sql("INSERT IGNORE INTO s3_storage (bucket, date, ObjectCount, DeleteMarkerObjectCount, NonCurrentVersionObjectCount, StorageBytes) VALUES (%s, %s, %s, %s, %s, %s);", k, v['date'], v['ObjectCount'], v['DeleteMarkerObjectCount'], v['NonCurrentVersionObjectCount'], v['StorageBytes'])
				run.cmd("rm -f {}".format(tmp))
				run.cmd("aws s3 rm {}{}{}".format(run.config('S3_REPORTS'), folder, file))
		run.cmd("aws s3 rm {}{}".format(run.config('S3_REPORTS'), folder))

run.done()
