#!/bin/sh

cd "$(dirname "$(readlink -f "$0")")"
source ./env.sh

usage() {
    cat << EOF
Usage: $(basename $0) [-m|-c] [-rxhd]
where:
    -r refresh database prior to testing
    -m test only models
    -c test only controllers
    -x immediately stop tests if any fail
    -d phpunit debug mode
    -h show usage
EOF
    exit "${1:-0}"
}
fail() {
    printf -- '\n%s\n\n' "$1" >&2
    usage "${2:-1}"
}

M=
C=
REFRESH_DB=
TERMINATE=
DEBUG=
while getopts ":mcrxdh" opt; do
    case $opt in
        r) REFRESH_DB=1
            ;;
        m) M=1
        [ $C ] && fail "Can only choose one of [-m|-c]"
            ;;
        c) C=1
        [ $M ] && fail "Can only choose one of [-m|-c]"
            ;;
        x) TERMINATE=1
            ;;
        d) DEBUG='--debug'
            ;;
        h) usage
            ;;
        *) fail "You used flag that doesn't exist."
            ;;
    esac
done

cd $SCRIPTS
[ $REFRESH_DB ] &&
    printf -- "Refreshing database..." &&
    ./db.sh -p &&
    printf -- "\nDatabase refreshed\n"


cd $FAZA_5

if [ -z $M ] && [ -z $C ]; then
    $PHPUNIT $DEBUG
    [ "$?" -eq 1 ] && [ $TERMINATE ] && exit 1
fi

[ $M ] &&
    DIR="$MODEL_TESTS" ||
    DIR="$CONTROLLER_TESTS"

for file in $DIR/*.php; do
    $PHPUNIT $DEBUG $file
    [ "$?" -eq 1 ] && [ $TERMINATE ] && exit 1
done
