#!/usr/bin/python3
# Update software on server host

import connix
import run

print(connix.header())
form = connix.form()

if not run.validate(False):
	run.error("Unauthorized.")

# Delete cache
run.cmd("rm -rf /root/.cache")
run.cmd("rm -rf /home/{}/.cache".format(run.config('LOCAL_USER')))

# System updates
run.cmd("apt-get -y update")
run.cmd("apt-get -y upgrade")

# Python modules
run.cmd("pip3 install connix awscli yt-dlp boto3 openai --upgrade")

# Done
run.done()
