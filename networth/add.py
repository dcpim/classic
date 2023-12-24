#!/usr/bin/python3
# Add a networth entry

import connix
import sys
import os
import run

print(connix.header())
form = connix.form()

if not run.validate(True):
	run.error("Unauthorized.")

if 'banks' not in form or 'investments' not in form or 'date' not in form or 'assets' not in form or 'equity' not in form or 'cc' not in form or 'mortgage' not in form or 'loans' not in form or 'biz' not in form:
	run.error("Missing input.")

date = form['date']
investments = float(form['investments'])
banks = float(form['banks'])
cc = float(form['cc'])
equity = float(form['equity'])
mortgage = float(form['mortgage'])
loans = float(form['loans'])
assets = float(form['assets'])
biz = float(form['biz'])

# Insert project into database
run.sql("INSERT INTO networth (date, biz, investments, banks, cc, equity, mortgage, loans, assets) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s);", date, biz, investments, banks, cc, equity, mortgage, loans, assets)

print("<meta http-equiv='refresh' content='0; URL=/networth' />")
run.done()
