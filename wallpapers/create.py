#!/usr/bin/python3
# Create a wallpaper

from pathlib import Path
import pymysql
import connix
import boto3
import sys
import os
import run

print(connix.header())
form = connix.form()

if not run.validate(True):
	run.error("Unauthorized.")

rek = boto3.client('rekognition', region_name = run.config('AWS_REGION'))

name = ""
if 'name' in form:
	name = form['name']
landscape = 0
if 'type' in form and form['type'] == "Desktop":
	landscape = 1
localfile = connix.guid()

def detect_labels(bucket, filename):
	data = {"S3Object": {"Bucket": bucket, "Name": filename}}
	results = {}
	labels = rek.detect_labels(Image=data, MinConfidence=45)

	for label in labels['Labels']:
		if 'Confidence' in label:
			results[label['Name']] = int(label['Confidence'])
	keys = sorted(results.keys(), key=lambda x:x.lower())

	labels = "{"
	for k in keys:
		labels = "{} '{}': {},".format(labels,k,results[k])
	labels = labels[:-1] + " }"

	return labels

# Local file upload
print(str(len(form['filename'])) + " bytes uploaded.<br>")
with open("/tmp/{}".format(localfile), 'wb') as fd:
	fd.write(form['filename'])

# Get file type
filename = run.filetype(localfile)

# Process image
run.cmd("convert -auto-orient /tmp/{} -resize 2048\> -quality 90 /tmp/{}".format(filename, filename))

# Upload image file to S3
run.cmd("aws s3 cp /tmp/{} s3://{}/Wallpapers/{}".format(filename, run.config('BUCKET_IMAGES'), filename))

# Get labels
labels = ""

# Creation of thumb
run.cmd("convert /tmp/{} -resize 150 /tmp/thumb-{}".format(filename, filename))

# Upload thumb file to S3
run.cmd("aws s3 cp /tmp/thumb-{} s3://{}/Wallpapers/Thumbs/{}".format(filename, run.config('BUCKET_IMAGES'), filename))

# Insert metadata into database
run.sql("INSERT INTO wallpapers (name, url, thumb, size, date, landscape, labels) VALUES (%s, %s, %s, %s, %s, %s, %s);", name, "Wallpapers/{}".format(filename), "Wallpapers/Thumbs/{}".format(filename), Path("/tmp/{}".format(filename)).stat().st_size, connix.now(), landscape, labels)

# Delete local files
run.cmd("rm -f \"/tmp/{}\"".format(filename))
run.cmd("rm -f \"/tmp/thumb-{}\"".format(filename))

run.done(True)
