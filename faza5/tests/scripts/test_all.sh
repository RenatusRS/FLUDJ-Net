#!/bin/sh

MODELS="tests/app/Models"
CONTROLLERS="tests/app/Controllers"
PHPUNIT="./vendor/bin/phpunit"
cd "$(dirname "$(readlink -f "$0")")"

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


cd "../../"
echo $PWD

if [ $M ]; then
    for file in $MODELS/*.php; do
        $PHPUNIT $file || exit
    done
fi

if [ $C ]; then
    for file in $CONTROLLERS/*.php; do
        $PHPUNIT $file || exit
    done
fi
