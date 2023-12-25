#!/usr/bin/python3
# Search utility for the main page

import connix
import sys
import os
import run

print(connix.header())
form = connix.form()

if not run.validate(True):
    run.error("Unauthorized.")

if 'q' not in form:
	print("|||<p>Missing query.</p>")
	exit(0)

q = connix.alphanum(form['q'], spaces=True)
output = "|||"

# Search projects
results = run.query("SELECT name,client,id FROM projects WHERE name LIKE '%%{}%%' OR client LIKE '%%{}%%';".format(q,q))

for result in results:
	output += "<p><b>Project:</b> <a href='/projects/?id={}'>{} ({})</a></p>".format(result[2], result[0], result[1])

# Search music
results = run.query("SELECT title,artist,url FROM music WHERE title LIKE '%%{}%%' OR artist LIKE '%%{}%%';".format(q,q))

for result in results:
	output += "<p><b>Music:</b> <a href='https://{}/{}'>{} ({})</a></p>".format(run.config('STORAGE_HOST').replace('[bucket]',run.config('BUCKET_MUSIC')), result[2], result[0], result[1])

# Search statements
results = run.query("SELECT name,type,scope,url FROM statements WHERE name LIKE '%%{}%%';".format(q))

for result in results:
	output += "<p><b>Statement:</b> <a href='https://{}/{}'>{} - {} ({})</a></p>".format(run.config('STORAGE_HOST').replace('[bucket]',run.config('BUCKET_ACCOUNTING')), result[3], result[2], result[0], result[1])

# Search videos
results = run.query("SELECT COUNT(*) FROM videos WHERE name LIKE '%%{}%%';".format(q))

if int(results[0][0]) > 0:
	output += "<p><b>Videos:</b><br>"
	results = run.query("SELECT name,url,type,thumb FROM videos WHERE name LIKE '%%{}%%';".format(q))

	for result in results:
		output += "<a href='https://{}/{}'><img src='https://{}/{}'><br>{} ({})</a><br>".format(run.config('STORAGE_HOST').replace('[bucket]',run.config('BUCKET_VIDEOS')), result[1], run.config('STORAGE_HOST').replace('[bucket]',run.config('BUCKET_VIDEOS')), result[3], result[0], result[2])
	output += "</p>"

# Search codex
results = run.query("SELECT description,content,id,prjid FROM code WHERE description LIKE '%%{}%%' OR content LIKE '%%{}%%';".format(q,q))

for result in results:
	results2 = run.query("SELECT name,client FROM projects WHERE id = {};".format(result[3]))
	if len(results2) > 0:
		output += "<p><b>Codex:</b> <a href='/codex/?id={}&prjid={}'>{}</a> {} ({})</p>".format(result[2], result[3], result[0], results2[0][0], results2[0][1])

# Search bookmarks
results = run.query("SELECT name,url,prjid,section FROM bookmarks WHERE name LIKE '%%{}%%' OR notes LIKE '%%{}%%';".format(q,q))

for result in results:
	results2 = run.query("SELECT name,client FROM projects WHERE id = {};".format(result[2]))
	if len(results2) > 0:
		output += "<p><b>Bookmark:</b> <a target=_new href='{}'>{} / {}</a> {} ({})</p>".format(result[1], result[3], result[0], results2[0][0], results2[0][1])

# Search secrets
results = run.query("SELECT site,prjid FROM secrets WHERE site LIKE '%%{}%%' OR note LIKE '%%{}%%';".format(q,q))

for result in results:
	results2 = run.query("SELECT name,client FROM projects WHERE id = {};".format(result[1]))
	if len(results2) > 0:
		output += "<p><b>Secret:</b> <a target=_new href='/secrets/?id={}'>{}</a> {} ({})</p>".format(result[1], result[0], results2[0][0], results2[0][1])

# Search renders
count = run.query("SELECT COUNT(*) FROM renders WHERE name LIKE '%%{}%%';".format(q))
if int(count[0][0]) > 0:
	output += "<p><b>Art:</b><br>"
	results = run.query("SELECT name,url,thumb FROM renders WHERE name LIKE '%%{}%%';".format(q))

	for result in results:
		output += "<a href='https://{}/{}' target=_blank><img src='https://{}/{}'></a> ".format(run.config('STORAGE_HOST').replace('[bucket]',run.config('BUCKET_IMAGES')), result[1], run.config('STORAGE_HOST').replace('[bucket]',run.config('BUCKET_IMAGES')), result[2])

	output += "</p>"

# Search wallpapers
count = run.query("SELECT COUNT(*) FROM wallpapers WHERE name LIKE '%%{}%%';".format(q))
if int(count[0][0]) > 0:
	output += "<p><b>Wallpapers:</b><br>"
	results = run.query("SELECT name,url,thumb FROM wallpapers WHERE name LIKE '%%{}%%';".format(q))

	for result in results:
		output += "<a href='https://{}/{}' target=_blank><img src='https://{}/{}'></a> ".format(run.config('STORAGE_HOST').replace('[bucket]',run.config('BUCKET_IMAGES')), result[1], run.config('STORAGE_HOST').replace('[bucket]',run.config('BUCKET_IMAGES')), result[2])

	output += "</p>"

# Search tasks
results = run.query("SELECT task,prjid FROM tasks WHERE task LIKE '%%{}%%';".format(q))

for result in results:
	results2 = run.query("SELECT name,client FROM projects WHERE id = {};".format(result[1]))
	if len(results2) > 0:
		output += "<p><b>Project task:</b> <a href='/projects/?id={}'>{}</a> {} ({})</p>".format(result[1], result[0], results2[0][0], results2[0][1])

# Search inventory
results = run.query("SELECT name,prjid FROM inventory WHERE name LIKE '%%{}%%' OR notes LIKE '%%{}%%';".format(q,q))

for result in results:
	results2 = run.query("SELECT name,client FROM projects WHERE id = {};".format(result[1]))
	if len(results2) > 0:
		output += "<p><b>Project item:</b> <a href='/projects/inventory.php?id={}&search={}'>{}</a> {} ({})</p>".format(result[1], result[0], result[0], results2[0][0], results2[0][1])

# Search notes
results = run.query("SELECT notes,name,client,id FROM projects WHERE notes LIKE '%%{}%%';".format(q))

for result in results:
	output += "<p><b>Note:</b> <a href='/projects/?id={}'>Default</a> {} ({})</p>".format(result[3], result[1], result[2])

# Search games
results = run.query("SELECT game_name FROM steam WHERE (game_name LIKE '%%{}%%' OR review LIKE '%%{}%%') AND hidden != 1 AND played_time != 0;".format(q,q))

for result in results:
	output += "<p><b>Game:</b> <a href='/steam/?search={}'>{}</a></p>".format(result[0],result[0])

# Search anime
results = run.query("SELECT title FROM series WHERE title LIKE '%%{}%%' OR review LIKE '%%{}%%';".format(q,q))

for result in results:
	output += "<p><b>Anime:</b> <a href='/anime/?search={}'>{}</a></p>".format(result[0],result[0])

# Search files
results = run.query("SELECT name,url,type FROM utils WHERE name LIKE '%%{}%%';".format(q))

for result in results:
	output += "<p><b>File:</b> <a href='https://{}/{}' target=_blank>{} ({})</a></p>".format(run.config('STORAGE_HOST').replace('[bucket]',run.config('BUCKET_FILES')), result[1], result[0], result[2])

results = run.query("SELECT name,url FROM medical_files WHERE name LIKE '%%{}%%';".format(q))

for result in results:
	output += "<p><b>File:</b> <a href='https://{}/{}' target=_blank>{} (Medical)</a></p>".format(run.config('STORAGE_HOST').replace('[bucket]',run.config('BUCKET_FILES')), result[1], result[0])

results = run.query("SELECT name,url FROM education_files WHERE name LIKE '%%{}%%';".format(q))

for result in results:
	output += "<p><b>File:</b> <a href='https://{}/{}' target=_blank>{} (Education)</a></p>".format(run.config('STORAGE_HOST').replace('[bucket]',run.config('BUCKET_FILES')), result[1], result[0])

results = run.query("SELECT name,prjid,url FROM project_files WHERE name LIKE '%%{}%%' OR notes LIKE '%%{}%%';".format(q,q))

for result in results:
	results2 = run.query("SELECT name,client FROM projects WHERE id = {};".format(result[1]))
	if len(results2) > 0:
		output += "<p><b>File:</b> <a href='https://{}/{}' target=_blank>{}</a> {} ({})</p>".format(run.config('STORAGE_HOST').replace('[bucket]',run.config('BUCKET_FILES')), result[2], result[0], results2[0][0], results2[0][1])

# Search photos
count = run.query("SELECT COUNT(*) FROM photos WHERE name LIKE '%%{}%%' OR event LIKE '%%{}%%';".format(q,q))
if int(count[0][0]) > 0:
	output += "<p><b>Photos:</b><br>"
	results = run.query("SELECT year,event,url,thumb FROM photos WHERE name LIKE '%%{}%%' OR event LIKE '%%{}%%';".format(q,q))

	curevent = ""
	for result in results:
		if curevent != "{} - {}".format(result[0], result[1]):
			curevent = "{} - {}".format(result[0], result[1])
			output += "<br>{}<br>".format(curevent)
		output += "<a href='https://{}/{}' target=_blank><img src='https://{}/{}'></a> ".format(run.config('STORAGE_HOST').replace('[bucket]',run.config('BUCKET_IMAGES')), result[2], run.config('STORAGE_HOST').replace('[bucket]',run.config('BUCKET_IMAGES')), result[3])

	output += "</p>"

# Search collection
results = run.query("SELECT name,brand FROM collection WHERE (name LIKE '%%{}%%' OR brand LIKE '%%{}%%') AND sold != 1;".format(q,q))

for result in results:
	output += "<p><b>Collectible:</b> <a href='/collection/?search={}'>{} ({})</a></p>".format(q,result[0],result[1])

# Search journal
results = run.query("SELECT title,entry,date FROM journal WHERE (title LIKE '%%{}%%' OR entry LIKE '%%{}%%') AND type = 0;".format(q,q))

for result in results:
	output += "<p><b>Journal: {}</b> <i>({})</i><br>{}</p>".format(result[0],result[2],result[1])

# No result
if output == "|||":
	output += "<p>No result.</p>";

print(output)
