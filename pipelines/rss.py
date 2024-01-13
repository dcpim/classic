#!/usr/bin/python3
# This script fetches RSS feeds for the Feeds page

import feedparser
import datetime
import connix
import json
import run
import os

print(connix.header())
form = connix.form()

if not run.validate(False):
	run.error("Unauthorized.")

# Get list of feeds
feeds = []
data = run.query("SELECT url,filter,id FROM rss_feeds;")
for feed in data:
	feeds.append({'url': feed[0], 'filter': feed[1], 'id': feed[2]})

# Parse feed items
for feed in feeds:
	print(feed)
	rss = feedparser.parse(feed['url'])
	count = 0
	for item in rss['items']:
		print("<li>{}: {}</li>".format(feed['url'], item['title']))
		try:
			date = item['updated'].rsplit(' ', 1)[0]
			title = item['title']
			title = connix.max_len(title, 245)
			url = item['link'][:499]
		except:
			continue
		img = ""
		if 'links' in item and len(item['links']) > 1 and 'url' in item['links'][1]:
			img = item['links'][1]['url']
		elif 'media_content' in item and len(item['media_content']) > 0 and 'url' in item['media_content'][0]:
			img = item['media_content'][0]['url']
		if len(img) > 0 and img[0] == '/':
			img = "http:" + img
		img = connix.max_len(img, 499)
		desc = ""
		if 'description' in item:
			desc = item['description']
		elif 'summary' in item:
			desc = item['summary']
		desc = desc.replace('<br>','\n')
		desc = desc.replace('\n\n', '\n')
		desc = desc.replace('\n\n', '\n')
		desc = connix.max_len(connix.remove_tags(desc), 995)
		desc = desc.replace('\n','<br>')
		desc = desc.replace('<br><br>', '<br>')
		desc = desc.replace('<br><br>', '<br>')
		desc = desc.replace('<br>', '<br><br>')
		if feed['filter'] and len(feed['filter']) > 1:
			if str(feed['filter']).lower() in str(title).lower() or str(feed['filter']).lower() in str(desc).lower():
				run.sql("INSERT IGNORE INTO rss (date, feed, url, title, description, image) VALUES (%s, %s, %s, %s, %s, %s);", date, feed['id'], url, title, desc, img)
		else:
			run.sql("INSERT IGNORE INTO rss (date, feed, url, title, description, image) VALUES (%s, %s, %s, %s, %s, %s);", date, feed['id'], url, title, desc, img)
		count += 1
	run.sql("UPDATE rss_feeds SET count = %s WHERE id = %s;", count, feed['id'])

run.done()

