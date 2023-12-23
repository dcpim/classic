#!/usr/bin/python3
# Share a note publicly

import connix
import sys
import os
import run
from datetime import datetime
from dateutil.relativedelta import relativedelta

print(connix.header())
form = connix.form()

if 'name' not in form or 'notes' not in form:
	run.error("Missing input.")

filename = "{}.html".format(connix.guid())
title = form['name']
content = form['notes']
url = "https://{}/Share/{}".format(run.config('STORAGE_HOST').replace('[bucket]',run.config('BUCKET_FILES')), filename)

# Craft output
output = """
<html>
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>{}</title>
        <link href="https://{}/bootstrap.min.css" rel="stylesheet" />
        <link href="https://{}/font-awesome-4.7.0/css/font-awesome.min.css" rel="stylesheet" />
        <link rel="stylesheet" type="text/css" href="https://{}/jquery.dataTables.min.css">
    </head>
    <body>
        <div class="container">
			<p><center><img style="width:100%" src="https://{}/images/toptitle.jpg"></center></p>
{}
		</div>
	</body>
</html>
""".format(title, run.config('SERVER_HOST'), run.config('SERVER_HOST'), run.config('SERVER_HOST'), run.config('SERVER_HOST'), content.replace("<img", "<img style='max-width:90%'"))

with open("/tmp/{}".format(filename), 'w') as fd:
	fd.write(output)

# Upload file to S3
run.cmd("aws s3 cp /tmp/{} s3://{}/Share/{}".format(filename, run.config('BUCKET_FILES'), filename))

# Delete local file
run.cmd("rm -f \"/tmp/{}\"".format(filename))

print("<URL>{}</URL>".format(url))
run.done()
