#!/bin/sh

cd "$(dirname "$(readlink -f "$0")")"
source ./env.sh

usage() {
    cat << EOF
Usage: $(basename $0) [-m|-c|-a]
where:
    -m test only models
    -c test only controllers
    -a test all
EOF
    exit 0
}

M=1
C=1
while getopts ":mcah" opt; do
    case $opt in
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
