#!/bin/sh

usage() {
	cat << EOF
Usage: $(basename $0) [-rRph]
where:
	-r removes dump file
	-R restore original database
	-h show this message
	-p purges test db
EOF
	exit 0
}

cd "$(dirname "$(readlink -f "$0")")"
source ./env.sh

REFRESH_DUMP=
REMOVE_DUMP=
RESTORE_DB=
PURGE_TEST_DB=
while getopts ":rRph" opt; do
	case $opt in
        r) REMOVE_DUMP=1
            ;;
		R) RESTORE_DB=1
			;;
		p) PURGE_TEST_DB=1
			;;
		*|h) usage
			;;
	esac
done

DUMPFILE="./dumpfile.sql"
TABLE="fludj"
TEST_TABLE="fludj_test"

if [ $PURGE_TEST_DB ]; then
	mysql -u root -e "DROP DATABASE IF EXISTS $TEST_TABLE"
fi

if [ $RESTORE_DB ]; then
	mysql -u root -e "DROP DATABASE IF EXISTS $TABLE;"
	mysql -u root -e "CREATE DATABASE $TABLE;"
	mysql -u root $TABLE < $DB
fi

mysqldump --add-drop-table -u root $TABLE > $DUMPFILE

mysql -u 'root' -e "CREATE DATABASE IF NOT EXISTS $TEST_TABLE;"
mysql -u root $TEST_TABLE < $DUMPFILE

[ $REMOVE_DUMP ] && [ -f "$DUMPFILE" ] && rm $DUMPFILE