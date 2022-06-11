#!/bin/sh

export WAMP_ROOT="/c/wamp64"
export FLUDJ_ROOT="$WAMP_ROOT/www/FLUDJ-Net"
export FAZA_5="$FLUDJ_ROOT/faza5"

export SCRIPTS="$FAZA_5/tests/scripts"

export MODEL_TESTS="$FAZA_5/tests/app/Models"
export CONTROLLER_TESTS="$FAZA_5/tests/app/Controllers"
export PHPUNIT="$FAZA_5/vendor/bin/phpunit"

export DB="$FLUDJ_ROOT/faza4/database_filled.sql"
for file in $WAMP_ROOT/bin/mysql/mysql*; do
    export MYSQL_DIR="$file/bin"
done

export PATH="$PATH:$MYSQL_DIR:$SCRIPTS"
