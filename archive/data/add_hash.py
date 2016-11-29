#!/usr/bin/env python3

import sys
import csv
import ctypes
import crypt

salt = crypt.mksalt(crypt.METHOD_MD5).split('$')[2]

reader = csv.reader(open(sys.argv[1], 'r'))
writer = csv.writer(sys.stdout)

title_row = next(reader)

try:

	indexes_to_hash_oblast = {
		'partner1': title_row.index('PARTNER'),
		'partner2': title_row.index('PARTNER2'),
		'agency': title_row.index('AGENCY'),
	}
except Exception:
	pass

try:
	indexes_to_hash_index = {
		'partner1': title_row.index('partner #1_name'),
		'partner2': title_row.index('partner #2_name'),
		'agency': title_row.index('org_name'),
	}
except Exception:
	pass

indexes_to_hash = indexes_to_hash_index



writer.writerow(title_row)

for row in reader:
	for key,index in indexes_to_hash.items():
		row[index] = row[index] and ( "X%X" % ctypes.c_ulonglong(hash(row[index] + salt)).value ) or ''
	writer.writerow(row)
