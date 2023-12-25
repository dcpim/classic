#!/usr/bin/python3
# Set the cache value for all images in S3

import connix
import run

print(connix.header())
form = connix.form()

if not run.validate(False):
	run.error("Unauthorized.")

run.cmd("aws s3 cp s3://{}/ s3://{}/ --recursive --expires 2034-01-01T00:00:00Z --cache-control max-age=2592000,private".format(run.config('BUCKET_IMAGES'), run.config('BUCKET_IMAGES')))

run.done()
