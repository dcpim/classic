#!/usr/bin/python3
# Process CSV files from local wifi scans, speedtest and AIS scans.

import re
import os
import run
import connix
from pyais.stream import FileReaderStream

print(connix.header())
form = connix.form()

if not run.validate(False):
	run.error("Unauthorized.")

# Scans from AIS and wifi scans
scans = connix.cmd("aws s3 ls s3://{}/scans/".format(run.config('BUCKET_DATA')))
vendors = run.query("SELECT * FROM mac_lookup;")

for scan in scans.split('\n'):
	if "PRE" in scan and len(scan.split(' ')) > 1:
		id = scan.split(' ')[-1].split('/')[0]
		data = run.query("SELECT id FROM mon_scan WHERE id = %s;", id)
		if len(data) == 0:
			files = connix.cmd("aws s3 ls s3://{}/scans/{}/".format(run.config('BUCKET_DATA'), id))
			for file in files.split('\n'):
				if "csv" in file and "kismet" not in file and len(file.split(' ')) > 3:
					filename = file.split(' ')[-1]
					tmp = "/tmp/{}.csv".format(connix.guid())
					run.cmd("aws s3 cp s3://{}/scans/{}/{} {}".format(run.config('BUCKET_DATA'), id, filename, tmp))
					missing_ships = []
					mships = run.query("SELECT mmsi FROM ais_scan WHERE pos_lon = \"\" AND date = %s;", connix.now().split(' ')[0])
					for srow in mships:
						missing_ships.append(str(srow[0]))
					if filename == "rtl_ais.csv": # AIS scan file
						for entry in FileReaderStream(tmp):
							msg = entry.decode()
							if hasattr(msg, 'callsign'):
								msg_call_sign = msg.callsign
								msg_lon = ""
								if hasattr(msg, 'lon'):
									msg_lon = msg.lon
								msg_lat = ""
								if hasattr(msg, 'lat'):
									msg_lat = msg.lat
								msg_mmsi = ""
								if hasattr(msg, 'mmsi'):
									msg_mmsi = msg.mmsi
								msg_imo = ""
								if hasattr(msg, 'imo'):
									msg_imo = msg.imo
								msg_destination = ""
								if hasattr(msg, 'destination'):
									msg_destination = msg.destination
								msg_ship_name = ""
								if hasattr(msg, 'shipname'):
									msg_ship_name = msg.shipname
								msg_speed = ""
								if hasattr(msg, 'speed'):
									msg_speed = msg.speed
								msg_ship_type = ""
								if hasattr(msg, 'ship_type'):
									if len(str(msg.ship_type).split('.')) > 1:
										msg_ship_type = str(msg.ship_type).split('.')[1]
									else:
										msg_ship_type = str(msg.ship_type)
								run.sql("INSERT IGNORE INTO ais_scan (id, date, call_sign, pos_lon, pos_lat, mmsi, imo, destination, ship_name, ship_type, speed) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s);", id, connix.now().split(' ')[0], msg_call_sign, msg_lon, msg_lat, msg_mmsi, msg_imo, msg_destination, msg_ship_name, msg_ship_type, msg_speed)
								if msg_lat == "" or msg_speed == "":
									missing_ships.append(str(msg_mmsi))
							else:
								if hasattr(msg, 'mmsi') and hasattr(msg, 'lat') and hasattr(msg, 'lon') and hasattr(msg, 'speed'):
									if str(msg.mmsi) in missing_ships:
										missing_ships.remove(str(msg.mmsi))
										run.sql("UPDATE ais_scan SET pos_lat = %s, pos_lon = %s, speed = %s WHERE date = %s AND mmsi = %s;", msg.lat, msg.lon, msg.speed, connix.now().split(' ')[0], msg.mmsi)
					else: # Wifi scan file
						with open(tmp, "r") as fd:
							lines = fd.read()
						mode = 0
						for line in lines.split('\n'):
							if len(line.split(' ')) < 2:
								mode = 0
							if mode == 1 or mode == 2: # For wifi APs and devices
								vendor = "Unknown"
								for v in vendors:
									if line.split(', ')[0].startswith(v[1]):
										vendor = v[2]
							if mode == 1: # AP line
								run.sql("INSERT IGNORE INTO mon_scan (id, mac, date, vendor, channel, security, description, type) VALUES (%s, %s, %s, %s, %s, %s, %s, %s);", id, line.split(', ')[0], line.split(', ')[1], vendor, int(line.split(', ')[3]), line.split(', ')[5], line.split(',')[-1], 1)
							if mode == 2: # Device line
								run.sql("INSERT IGNORE INTO mon_scan (id, mac, date, vendor, description, type, station) VALUES (%s, %s, %s, %s, %s, %s, %s);", id, line.split(', ')[0], line.split(', ')[1], vendor, line.split(',')[-1], 2, line.split(',')[5])
							if "ID-length" in line: # The next section in the file lists access points
								mode = 1
							if "Station MAC" in line: # The next section in the file lists wifi devices
								mode = 2
					run.cmd("rm -f {}".format(tmp))

# Speed test results
scans = connix.cmd("aws s3 ls s3://{}/speedtest/".format(run.config('BUCKET_DATA')))

for scan in scans.split('\n'):
	if len(scan.split(' ')) > 1:
		id = scan.split(' ')[-1]
		tmp = "/tmp/{}.txt".format(connix.guid())
		run.cmd("aws s3 cp s3://{}/speedtest/{} {}".format(run.config('BUCKET_DATA'), id, tmp))
		with open(tmp, "r") as fd:
			rows = fd.read().split('\n')
		download = None
		upload = None
		for row in rows:
			row = re.sub(' +', ' ', row) # This annoys me so much
			if "Download:" in row:
				print(row)
				download = row.split(' ')[2]
			if "Upload:" in row:
				print(row)
				upload = row.split(' ')[2]
		if download and upload:
			run.sql("INSERT IGNORE INTO speedtest (date, download, upload) VALUES (%s, %s, %s);", connix.unixtime2datetime(id), download, upload)
		run.cmd("aws s3 rm s3://{}/speedtest/{}".format(run.config('BUCKET_DATA'), id))
		run.cmd("rm -f {}".format(tmp))

run.done()

