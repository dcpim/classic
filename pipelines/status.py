#!/usr/bin/python3
# Fetch news and weather information for status screen.

from pathlib import Path
import feedparser
import connix
import json
import run
import os

folder = "/var/www/html/status"
top = "<!DOCTYPE html><html><head><link rel='preconnect' href='https://fonts.gstatic.com'><link href='https://fonts.googleapis.com/css2?family=Exo+2&display=swap' rel='stylesheet'><style>body{background-color:#000000;color:#FFFFFF;font-family:'Exo 2',monospace;}</style></head><body>"
bottom = "</body></html>"
newsfeeds = [
				"http://www.ctvnews.ca/rss/ctvnews-ca-world-public-rss-1.822289",
				"http://montreal.ctvnews.ca/rss/ctv-news-montreal-1.822366",
				"https://www.ctvnews.ca/rss/ctvnews-ca-sci-tech-public-rss-1.822295",
				"https://www.ctvnews.ca/rss/business/ctv-news-business-headlines-1.867648"
			]
weatherfeed = "https://weather.gc.ca/rss/city/qc-147_e.xml"
symbols = [
				{ 'symbol': "^GSPC", 'name': "S&P 500" },
				{ 'symbol': "^IXIC", 'name': "NASDAQ" },
				{ 'symbol': "CADJPY=X", 'name': "CAD/JPY" },
				{ 'symbol': "CADUSD=X", 'name': "CAD/USD" }
		]

print(connix.header())
form = connix.form()

if 'newsfeeds' in form:
	newsfeeds = []
	for a in form['newsfeeds']:
		newsfeeds.append(connix.remove_spaces(a))
if 'weatherfeed' in form:
	weatherfeed = form['weatherfeed']

# Update news panes

for i in [1, 2, 3, 4]:
	count = 0
	tmp = "/tmp/{}.html".format(connix.guid())
	with open(tmp, "w", encoding='UTF-8') as fd:
		rss = feedparser.parse(newsfeeds[i-1])
		fd.write("{}\n".format(top))
		for item in rss['items']:
			img = ""
			if 'links' in item and len(item['links']) > 1:
				img = item['links'][1]['url']
			elif 'media_content' in item:
				img = item['media_content'][0]['url']
			if len(img) > 0 and img[0] == '/':
				img = "http:" + img
			desc = item['description'].replace('\n',' ').replace('<br>',' ').replace('<p>',' ')
			desc = connix.max_len(desc, 260)
			title = item['title']
			if len(title) > 2 and img != "":
				print("[{}] {}".format(i, title))
				fd.write("<div style='height:270px;position:relative;'><font size=+3><b>{}</b></font><br><table><tr><td style='vertical-align:top'><img style='width:300px;height:169px' src=\"{}\"></td><td><font size=+2>{}</font></td></table></div>\n".format(title, img, desc))
				count = count + 1
			if count > 2:
				break
		fd.write("{}\n".format(bottom))
	size = Path(tmp).stat().st_size
	print(size)
	if int(size) > 1000:
		connix.cmd("mv {} {}/news{}.html".format(tmp, folder, i))
	else:
		connix.cmd("rm -f {}".format(tmp))

# Get current weather
weather = "?? °C"
try:
	rss = feedparser.parse(weatherfeed)
	for item in rss['items']:
		if "Current Conditions: " in item['title']:
			weather_array = item['title'].replace("Current Conditions: ", "").split(' ')
			for weather_part in weather_array:
				if "°C" in weather_part:
					weather = weather_part
	weather = weather[0:30]
except Exception as e:
	print(e)
with open("{}/weather.txt".format(folder), "w", encoding='UTF-8') as fd:
	fd.write(weather)

# Get stock market data
output = ""
for symbol in symbols:
	try:
		print(symbol)
		data = connix.curl("https://finance.yahoo.com/quote/{}/".format(symbol['symbol']))
		stock_price = connix.in_tag(data, 'data-symbol="{}"'.format(symbol['symbol']), "<")
		stock_price = stock_price.split('>')[1]
		print(stock_price)
		data2 = connix.in_tag(data, 'data-symbol="{}" data-field="regularMarketChange"'.format(symbol['symbol']), "</span>")
		if data2 == "":
			data2 = connix.in_tag(data, 'data-symbol="{}" data-test="qsp-price-change" data-field="regularMarketChange"'.format(symbol['symbol']), "</span>")
		stock_color = "white"
		if "negative" in data2:
			stock_color = "red"
		if "positive" in data2:
			stock_color = "green"
		output += "<font color='white'><b>{}:</b></font> <font color='{}'>{}</font> &nbsp; ".format(symbol['name'], stock_color, stock_price)
	except Exception as e:
		print(e)
with open("{}/stocks.txt".format(folder), 'w', encoding='UTF-8') as fd:
	fd.write(output)

run.done()
