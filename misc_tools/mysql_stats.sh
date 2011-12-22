#!/bin/sh

#
# Really simple script to gather some basic stats for a ubuntu/debian mysql server.
# If you have databases somewhere else than /var/lib/mysql, please change DB_DIR
# If you don't use a debian derivative, please change MYSQL_COMMAND
#

DB_DIR="/var/lib/mysql"
MYSQL_COMMAND="mysql --defaults-file=/etc/mysql/debian.cnf information_schema"

echo "database statistics:"
find "$DB_DIR" -mindepth 1 -type d -exec du -sb '{}' ';' | awk '{ count++; sum += $1; if ($1 > max) max = $1 } END { printf "dbcount=%d maxsize=%g avgsize=%g totsize=%g\n", count, max, sum/count, sum; }'
echo

echo "global server index stats:"
$MYSQL_COMMAND <<EOF
show status like 'key%';
show variables like 'key%';
EOF
echo

echo "summary of different file types:"
find "$DB_DIR" -mindepth 2 -type f | awk -F . '{ type[$NF]++ } END { for (t in type) print t ": " type[t] }'
echo

echo "index stats for first 1000 indexes:"
find "$DB_DIR" -mindepth 2 -type f -name "*.MYI" | head -n 1000 | xargs stat -c "%s" | awk '
	{ count++; sum += $NF; if ($NF > max) max = $NF }
	END { printf "myisamcount=%d max_index_size=%g avg_index_size=%g tot_index_size=%g\n", count, max, sum/count, sum; }'
echo

echo "table stats for first 1000 tables:"
find "$DB_DIR" -mindepth 2 -type f -name "*.MYD" | head -n 1000 | xargs stat -c "%s" | awk '
	{ count++; sum += $NF; if ($NF > max) max = $NF }
	END { printf "myisamcount=%d max_table_size=%g avg_table_size=%g tot_table_size=%g\n", count, max, sum/count, sum; }'
