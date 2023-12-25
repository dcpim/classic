#!/usr/bin/python3
# Backup local files to S3 and call device stats script

import connix
import run

print(connix.header())
form = connix.form()

if not run.validate(False):
	run.error("Unauthorized.")

# Making temp folders
run.cmd("mkdir -p /tmp/backups")

# Backing up local files
run.cmd("tar -cvzf /tmp/backups/www.tgz  --absolute-names /var/www")
run.cmd("tar -cvzf /tmp/backups/scripts.tgz  --absolute-names /home/{}/scripts".format(run.config('BUCKET_BACKUPS')))
run.cmd("tar -cvzf /tmp/backups/git.tgz  --absolute-names /home/{}/git".format(run.config('BUCKET_BACKUPS')))
run.cmd("tar -cvzf /tmp/backups/apache2_config.tgz  --absolute-names /etc/apache2")
run.cmd("tar -cvzf /tmp/backups/postfix_config.tgz  --absolute-names /etc/postfix")
run.cmd("mysqldump -h localhost  --quick --compress  --skip-lock-tables {} > /tmp/backups/{}.sql".format(os.environ['DB_DATABASE'], os.environ['DB_DATABASE']))
run.cmd("gzip /tmp/backups/{}.sql".format(os.environ['DB_DATABASE']))
run.cmd("crontab -l > /tmp/backups/crontab.txt")

# Backing up logs
run.cmd("tar czf /tmp/logs.tgz /var/log || exit 0") # Tar exits 1 if log files are being modified during archival

# Uploading to S3
run.cmd("aws s3 cp /tmp/backups s3://{}/{}/ --recursive".format(run.config('BUCKET_BACKUPS'), run.config('SERVER_HOST')))
run.cmd("aws s3 cp /tmp/logs.tgz s3://{}/{}/logs/{}.tgz".format(run.config('BUCKET_BACKUPS'), run.config('SERVER_HOST'), connix.now().split(' ')[0]))

# Removing temp folders
run.cmd("rm -rf /tmp/backups")
run.cmd("rm -f /tmp/logs.tgz")

# Sending local device stats
run.cmd("""
uptime=$(uptime | awk -F [p,] '{print $2}' | xargs | tr -s ' ' '+')
update=$(stat /var/log/apt/history.log | grep Modify | awk '{print $2}')
usage=$(df -h | grep 'nvme0n1p1 '| awk '{print $5}' | rev | cut -c 2- | rev)
curl "https://""" + run.config('SERVER_HOST') + """/pipelines/device_stats.py?token=""" + run.config('DEVICE_TOKEN') + """&device=Erza&disk=20GB&usage=$usage&update=$update&uptime=$uptime"
""")

# Done
run.done()
