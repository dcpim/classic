#!/usr/bin/python3
# Export a table as CSV.

import os
import run
import connix
import pymysql

# Manual headers because of CSV format type
print("Content-Disposition: attachment; filename=export.csv")
print("Content-Type: text/csv; charset=utf-8")
print()

form = connix.form()

# Manual validate
if "HTTP_REFERER" not in os.environ:
	run.error("Unauthorized.")
if config('SERVER_HOST') not in os.environ['HTTP_REFERER']:
	run.error("Unauthorized.")
db = pymysql.connect(host=os.environ['DB_HOST'], user=os.environ['DB_USER'], password=os.environ['DB_PASS'], database=os.environ['DB_DATABASE'])
cur = db.cursor()
cur.execute("SELECT COUNT(*) FROM sessions WHERE valid_until > UNIX_TIMESTAMP() AND ip = %s;", (os.environ['REMOTE_ADDR']))
results = cur.fetchall()
db.close()
if results[0][0] == 0 and os.environ['REMOTE_ADDR'] != config('MY_IP') and os.environ['REMOTE_ADDR'] != config('SERVER_IP') and os.environ['REMOTE_ADDR'] != "127.0.0.1":
	run.error("Unauthorized.")

if 'data' not in form:
	run.error("Missing input.")

data = form['data']
bucket_col = -1

# Parse request
if data == "health":
	cols = "id,date,distance,stairs,steps,weight,diastolic,systolic,heart,fat"
	query = "SELECT {} FROM health ORDER BY id;".format(cols)
elif data == "nutrition":
	cols = "id,date,food,calories,fiber,sugar"
	query = "SELECT nutrition.id,nutrition.date,foods.name,foods.calories,foods.fiber,foods.sugar FROM nutrition INNER JOIN foods ON nutrition.food = foods.id;"
elif data == "medical":
	cols = "id,date,name,url,size"
	query = "SELECT {} FROM medical_files ORDER BY id;".format(cols)
	bucket_name = run.config('BUCKET_FILES')
	bucket_col = 3
elif data == "music":
	cols = "id,date,title,artist,duration,url,size"
	query = "SELECT {} FROM music ORDER BY id;".format(cols)
	bucket_name = run.config('BUCKET_MUSIC')
	bucket_col = 5
elif data == "files":
	cols = "id,date,name,type,url,size"
	query = "SELECT {} FROM utils ORDER BY id;".format(cols)
	bucket_name = run.config('BUCKET_FILES')
	bucket_col = 4
elif data == "photos":
	cols = "id,date,name,event,year,url,size"
	query = "SELECT {} FROM photos ORDER BY id;".format(cols)
	bucket_name = run.config('BUCKET_IMAGES')
	bucket_col = 5
elif data == "videos":
	cols = "id,date,name,type,duration,url,size"
	query = "SELECT {} FROM videos ORDER BY id;".format(cols)
	bucket_name = run.config('BUCKET_VIDEOS')
	bucket_col = 5
elif data == "education":
	cols = "id,date,name,url,size"
	query = "SELECT {} FROM education_files ORDER BY id;".format(cols)
	bucket_name = run.config('BUCKET_FILES')
	bucket_col = 3
elif data == "project_files":
	cols = "id,date,name,project,url,size"
	query = "SELECT project_files.id,project_files.date,project_files.name,projects.name,project_files.url,project_files.size FROM project_files INNER JOIN projects ON project_files.prjid = projects.id ORDER BY project_files.id;".format(cols)
	bucket_name = run.config('BUCKET_FILES')
	bucket_col = 4
elif data == "items":
	cols = "id,date,name,project,serial,price,notes"
	query = "SELECT inventory.id,inventory.date,inventory.name,projects.name,inventory.serial,inventory.price,inventory.notes FROM inventory INNER JOIN projects ON inventory.prjid = projects.id WHERE inventory.sold < 1 ORDER BY inventory.id;".format(cols)
elif data == "bookmarks":
	cols = "id,date,name,project,type,section,notes,url"
	query = "SELECT bookmarks.id,bookmarks.date,bookmarks.name,projects.name,bookmarks.type,bookmarks.section,bookmarks.notes,bookmarks.url FROM bookmarks INNER JOIN projects ON bookmarks.prjid = projects.id ORDER BY bookmarks.id;".format(cols)
elif data == "transactions":
	cols = "id,date,note,debit,credit,invoice_links,expense_links,paid"
	query = "SELECT {} FROM balance ORDER BY id;".format(cols)
elif data == "invoices":
	cols = "id,date,invoice,url,client,project,currency,subtotal,qst,gst,total,paid"
	query = "SELECT {} FROM invoices ORDER BY id;".format(cols)
	bucket_name = run.config('BUCKET_ACCOUNTING')
	bucket_col = 3
elif data == "statements":
	cols = "id,date,name,type,scope,url,size"
	query = "SELECT {} FROM statements ORDER BY id;".format(cols)
	bucket_name = run.config('BUCKET_ACCOUNTING')
	bucket_col = 5
elif data == "expenses":
	cols = "id,date,invoice,url,supplier,description,currency,subtotal,qst,gst,total,paid"
	query = "SELECT {} FROM expenses ORDER BY id;".format(cols)
	bucket_name = run.config('BUCKET_ACCOUNTING')
	bucket_col = 3
elif data == "art":
	cols = "id,date,name,genre,url,size"
	query = "SELECT {} FROM renders ORDER BY id;".format(cols)
	bucket_name = run.config('BUCKET_IMAGES')
	bucket_col = 4
elif data == "wallpapers":
	cols = "id,date,name,url,size"
	query = "SELECT {} FROM wallpapers ORDER BY id;".format(cols)
	bucket_name = run.config('BUCKET_IMAGES')
	bucket_col = 3
elif data == "screenshots":
	cols = "id,date,game,url,size"
	query = "SELECT {} FROM games ORDER BY id;".format(cols)
	bucket_name = run.config('BUCKET_IMAGES')
	bucket_col = 3
elif data == "payroll":
	cols = "id,date,pay,ca_tax,qc_tax,ei,corp_ei,qpp,corp_qpp,qpip,corp_qpip,corp_health,name"
	query = "SELECT {} FROM payroll ORDER BY id;".format(cols)
elif data == "income":
	cols = "id,date,note,credit,debit,is_saving"
	query = "SELECT {} FROM income ORDER BY id;".format(cols)
elif data == "networth":
	cols = "id,date,banks,investments,assets,equity,cc,mortgage,loans,biz"
	query = "SELECT {} FROM networth ORDER BY id;".format(cols)
elif data == "anime":
	cols = "id,date,title,stars,review"
	query = "SELECT {} FROM series ORDER BY id;".format(cols)
elif data == "games":
	cols = "id,date,game_name,genre,release_date,appid,played_time,stars,review"
	query = "SELECT {} FROM steam WHERE hidden != 1 AND played_time > 0 ORDER BY id;".format(cols)
elif data == "projects":
	cols = "id,date,name,client,address,end_date,contact_name,contact_email,contact_phone"
	query = "SELECT {} FROM projects ORDER BY id;".format(cols)
else:
	run.error("Invalid table.")

# Download data
db = pymysql.connect(host=os.environ['DB_HOST'], user=os.environ['DB_USER'], password=os.environ['DB_PASS'], database=os.environ['DB_DATABASE'])
cur = db.cursor()
cur.execute(query)
data = cur.fetchall()
print(cols)
for row in data:
	output = ""
	num = 0;
	for col in row:
		if bucket_col == num:
			output += "\"https://{}/{}\",".format(run.config('STORAGE_HOST').replace('[bucket]',bucket_name),col)
		elif connix.is_int(col):
			output += "{},".format(col)
		else:
			output += "\"{}\",".format(col)
		num += 1
	output = output[:-1]
	print(output)

# Done
db.close()
