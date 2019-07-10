#!/bin/bash
SOURCE="${BASH_SOURCE[0]}"

BASEDIR="$( dirname `dirname "$SOURCE"` )"
SCRIPT="php bootstrap.php modules/ximSYNC/scripts/scheduler/scheduler.php"
LOG="logs/scheduler.log"


while :
    do
        $BASEDIR/$SCRIPT  >> $BASEDIR/$LOG 2>&1
done
