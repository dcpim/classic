#!/usr/bin/python3
# Make export archive

import connix
import run
import os

print(connix.header())
form = connix.form()

if not run.validate(False):
	run.error("Unauthorized.")

# Making temp folders
run.cmd("mkdir -p /tmp/export")

# Creating export archive
run.cmd("aws s3 cp s3://{}/{}/www.tgz /tmp/export/www.tgz".format(run.config('BUCKET_BACKUPS'), run.config('SERVER_HOST')))
run.cmd("aws s3 cp s3://{}/{}/{}.sql.gz /tmp/export/{}.sql.gz".format(run.config('BUCKET_BACKUPS'), run.config('SERVER_HOST'), os.environ['DB_DATABASE'], os.environ['DB_DATABASE']))
run.cmd("7za a -p{} -t7z /tmp/export.7z /tmp/export".format(run.config('EXPORT_PWD')))

# Uploading to S3
run.cmd("aws s3 cp /tmp/export.7z s3://{}/export.7z".format(run.config('BUCKET_BACKUPS')))

# Removing temp folders
run.cmd("rm -f /tmp/export.7z")
run.cmd("rm -rf /tmp/export")

# Done
run.done()

