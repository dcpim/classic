#!/usr/bin/python3
# Import an Apple healthkit export

import xml.etree.ElementTree
from pathlib import Path
import datetime
import pymysql
import connix
import sys
import os
import run

print(connix.header())
form = connix.form()

if not run.validate(False):
	run.error("Unauthorized.")

if 'filename' not in form:
	run.error("Missing input.")

# Local file upload
print(str(len(form['filename'])) + " bytes uploaded.<br>")
tmpfile = connix.guid()
with open("/tmp/" + tmpfile, 'wb') as fd:
	fd.write(form['filename'])

# Unzip package
run.cmd("unzip -o /tmp/" + tmpfile + " -d /tmp")
try:
	records = xml.etree.ElementTree.parse("/tmp/apple_health_export/export.xml").getroot()
except:
	records = xml.etree.ElementTree.parse("/tmp/apple_health_export/書き出したデータ.xml").getroot()

# Process records
print("<div style='background-color:#AAFFAA;padding:5px'>Processing " + str(len(records)) + " records...</div><pre>")
sys.stdout.flush()
results = {'steps': {}, 'distance': {}, 'stairs': {}, 'weight': {}, 'systolic': {}, 'diastolic': {}, 'heart': {}, 'fat': {}}
for record in records:
	result = {}
	for key, value in record.attrib.items():
		if key == 'type' or key == 'value' or key == 'startDate':
			result[key] = value
	if "type" in result:
		day = result['startDate'].split(' ')[0]
		if result['type'] == "HKQuantityTypeIdentifierDistanceWalkingRunning":
			if day in results['distance']:
				results['distance'][day] += float(result['value'])
			else:
				results['distance'][day] = float(result['value'])
		elif result['type'] == "HKQuantityTypeIdentifierFlightsClimbed":
			if day in results['stairs']:
				results['stairs'][day] += int(result['value'])
			else:
				results['stairs'][day] = int(result['value'])
		elif result['type'] == "HKQuantityTypeIdentifierStepCount":
			if day in results['steps']:
				results['steps'][day] += int(result['value'])
			else:
				results['steps'][day] = int(result['value'])
		elif result['type'] == "HKQuantityTypeIdentifierBodyMass":
			results['weight'][day] = int(float(result['value']))
		elif result['type'] == "HKQuantityTypeIdentifierBodyFatPercentage":
			results['fat'][day] = int(float(result['value'])*1000)
		elif result['type'] == "HKQuantityTypeIdentifierBloodPressureSystolic":
			if day in results['systolic']:
				results['systolic'][day] += int(result['value'])
			else:
				results['systolic'][day] = int(result['value'])
		elif result['type'] == "HKQuantityTypeIdentifierBloodPressureDiastolic":
			if day in results['diastolic']:
				results['diastolic'][day] += int(result['value'])
			else:
				results['diastolic'][day] = int(result['value'])
		elif result['type'] == "HKQuantityTypeIdentifierHeartRate":
			if day in results['heart']:
				results['heart'][day] += int(result['value'])
			else:
				results['heart'][day] = int(result['value'])
		elif result['type'] == "HKQuantityTypeIdentifierWalkingAsymmetryPercentage":
			pass
		elif result['type'] == "HKQuantityTypeIdentifierWalkingDoubleSupportPercentage":
			pass
		elif result['type'] == "HKQuantityTypeIdentifierWalkingStepLength":
			pass
		elif result['type'] == "HKQuantityTypeIdentifierHeight":
			pass
		elif result['type'] == "HKQuantityTypeIdentifierBodyMassIndex":
			pass
		elif result['type'] == "HKQuantityTypeIdentifierAppleWalkingSteadiness":
			pass
		elif result['type'] == "HKQuantityTypeIdentifierLeanBodyMass":
			pass
		elif result['type'] == "HKQuantityTypeIdentifierWalkingSpeed":
			pass
		elif result['type'] == "HKQuantityTypeIdentifierOxygenSaturation":
			pass
		elif result['type'] == "HKQuantityTypeIdentifierActiveEnergyBurned":
			pass
		elif result['type'] == "HKQuantityTypeIdentifierBasalEnergyBurned":
			pass
		elif result['type'] == "HKQuantityTypeIdentifierHeadphoneAudioExposure":
			pass
		else:
			if 'value' in result:
				print("Unknown data point: " + result['type'] + " - " + str(result['value']) + "<br>")
days = []
for result in results['distance'].keys():
	day = {'date': result, 'distance': 0, 'stairs': 0, 'steps': 0, 'weight': 0, 'systolic': 0, 'diastolic': 0, 'heart': 0, 'fat': 0}
	if result in results['distance']:
		day['distance'] = round(results['distance'][result], 2)
	if result in results['stairs']:
		day['stairs'] = results['stairs'][result]
	if result in results['steps']:
		day['steps'] = results['steps'][result]
	if result in results['weight']:
		day['weight'] = results['weight'][result]
	if result in results['fat']:
		day['fat'] = results['fat'][result]
	if result in results['systolic']:
		day['systolic'] = results['systolic'][result]
	if result in results['diastolic']:
		day['diastolic'] = results['diastolic'][result]
	if result in results['heart']:
		day['heart'] = results['heart'][result]
	if not connix.in_list(days, 'date', result) and result != connix.now().split(' ')[0]:
		days.append(day)
days.sort(key=lambda x: datetime.datetime.strptime(x['date'], '%Y-%m-%d'))
print("</pre><br>")
sys.stdout.flush()

# Insert metadata into database
q = "INSERT IGNORE INTO health (date, distance, stairs, steps, weight, diastolic, systolic, heart, fat) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s);"
added = 0

for day in days:
	try:
		run.sql(q, day['date'], day['distance'], day['stairs'], day['steps'], day['weight'], day['diastolic'], day['systolic'], day['heart'], day['fat'])
		added += 1
	except:
		pass

# Delete local file
run.cmd("rm -f \"/tmp/{}\"".format(tmpfile))

print("Added " + str(added) + " new records.<br>")

run.done(True)

