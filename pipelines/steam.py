#!/usr/bin/python3
# This script fetches Steam play time for the Steam page

import datetime
import connix
import json
import run
import os

print(connix.header())
form = connix.form()

if not run.validate(False):
	run.error("Unauthorized.")

# Get JSON file
data = connix.curl("https://api.steampowered.com/IPlayerService/GetOwnedGames/v0001/?key={}&steamid={}&format=json".format(run.config('STEAM_KEY'), run.config('STEAM_ID')))
games = json.loads(data)

for game in games['response']['games']:
	run.sql("INSERT INTO steam (appid, played_time, date, stars, hidden) VALUES (%s, %s, %s, %s, %s) ON DUPLICATE KEY UPDATE played_time = %s, date = %s", game['appid'], game['playtime_forever'], connix.now(), 1, 0, game['playtime_forever'], connix.now())

# Update game names and get header images
entries = run.query("SELECT appid FROM steam WHERE game_name IS NULL OR game_name = \"\" OR release_date IS NULL OR release_date = \"\";")

for entry in entries:
	id = str(entry[0])
	data = connix.curl("http://store.steampowered.com/api/appdetails/?appids={}".format(id))
	metadata = json.loads(data)
	game_name = ""
	try:
		game_name = connix.alphanum(metadata[id]['data']['name'], symbols=True, spaces=True)[0:49]
	except:
		print("Could not get data for this game. Received: {}".format(metadata))
	release_date = "Unknown"
	try:
		release_date = metadata[id]['data']['release_date']['date']
	except:
		pass
	genre = "Unknown"
	try:
		genre = metadata[id]['data']['genres'][0]['description']
	except:
		pass
	if game_name != "":
		tmp1 = "/tmp/{}.jpg".format(connix.guid())
		tmp2 = "/tmp/{}.jpg".format(connix.guid())
		run.sql("UPDATE steam set game_name = %s, release_date = %s, genre = %s WHERE appid = %s;", game_name, release_date, genre, id)
		run.cmd("wget https://cdn.cloudflare.steamstatic.com/steam/apps/{}/header.jpg -O {}".format(id, tmp1))
		run.cmd("convert -auto-orient {} -resize 160\> -quality 80 {}".format(tmp1, tmp2))
		run.cmd("aws s3 cp {} s3://{}/SteamHeaders/{}.jpg".format(tmp2, run.config('BUCKET_IMAGES'), id))
		run.cmd("rm -f {}".format(tmp1))
		run.cmd("rm -f {}".format(tmp2))

# Convert release dates as needed
entries = run.query("SELECT appid,release_date FROM steam WHERE release_date LIKE '%%,%%';")

for entry in entries:
	try:
		run.sql("UPDATE steam SET release_date = %s WHERE appid = %s;", datetime.datetime.strptime(entry[1], '%b %d, %Y').strftime('%Y-%m-%d'), entry[0])
	except:
		run.sql("UPDATE steam SET release_date = %s WHERE appid = %s;", datetime.datetime.strptime(entry[1], '%d %b, %Y').strftime('%Y-%m-%d'), entry[0])

# Delete entries that still couldn't be matched
run.sql("DELETE FROM steam WHERE game_name IS NULL;")

run.done()

