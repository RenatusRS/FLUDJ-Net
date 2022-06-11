#!/bin/sh

cd "$(dirname "$(readlink -f "$0")")"
source ./env.sh

usage() {
    cat << EOF
Usage: $(basename $0) [-r] [-m|-c|-a]
where:
    -r refresh database prior to testing
    -m test only models
    -c test only controllers
    -a test all
EOF
    exit 0
}

M=1
C=1
REFRESH_DB=
while getopts ":mcahr" opt; do
    case $opt in
        r) REFRESH_DB=1
            ;;
        m) M=1; C=;
            ;;
        c) M=; C=1;
            ;;
        a) M=1; C=1;
            ;;
        h) usage
            ;;
    esac
done

cd $SCRIPTS
[ $REFRESH_DB ] &&
    printf -- "Refreshing database..." &&
    ./db.sh &&
    printf -- "\nDatabase refreshed\n"


cd $FAZA_5

if [ $M ]; then
    for file in $MODEL_TESTS/*.php; do
        $PHPUNIT $file || exit
    done
fi

if [ $C ]; then
    for file in $CONTROLLER_TESTS/*.php; do
        $PHPUNIT $file || exit
    done
fi
