#!/bin/sh
#/**
# *  \details &copy; 2011  Open Ximdex Evolution SL [http://www.ximdex.org]
# *
# *  Ximdex a Semantic Content Management System (CMS)
# *
# *  This program is free software: you can redistribute it and/or modify
# *  it under the terms of the GNU Affero General Public License as published
# *  by the Free Software Foundation, either version 3 of the License, or
# *  (at your option) any later version.
# *
# *  This program is distributed in the hope that it will be useful,
# *  but WITHOUT ANY WARRANTY; without even the implied warranty of
# *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# *  GNU Affero General Public License for more details.
# *
# *  See the Affero GNU General Public License for more details.
# *  You should have received a copy of the Affero GNU General Public License
# *  version 3 along with Ximdex (see LICENSE file).
# *
# *  If not, visit http://gnu.org/licenses/agpl-3.0.html.
# *
# *  @author Ximdex DevTeam <dev@ximdex.com>
# *  @version $Revision$
# */

##
## TODO:
##   · Module packages.

### ------------------------------------------------------------
### Constants
### ------------------------------------------------------------
VERSION=25d

DEFAULT_DIR=$PWD
FILE_LOG="ximdex_packager.log"


EXIT_SUCCESS=1
EXIT_FAILURE=0
true=1
false=0

### ------------------------------------------------------------
### Main Script.
### ------------------------------------------------------------

## Check syntax
if [ ! -n "$1" ]; then
	echo "Syntax: $(basename $0) <ximdex_location> <dest dir>"
	echo 
	echo "<ximdex_location> : Directory which contains the Ximdex instance to package."
	echo "<dest_dir>        : Destiny directory where package will be placed. (Optional, by default is './')"
	echo
	exit $EXIT_FAILURE
fi

echo
echo "+ Launching tasks:"
echo

## Test ximdex_location
echo -n "    · Checking ximdex_location [$1]... "
if [ ! -d "$1" ]; then
        echo "not found. (exiting)"
	exit $EXIT_FAILURE
else
	if [ ! -d "$1/inc" ]; then
		echo "does not look like a Ximdex directory. (exiting)"
		exit $EXIT_FAILURE
	else
        	echo "found."
		XIMDEX_DIR=$1
	fi
fi

## Test destination directory
DESTINATION_DIR=${2:-${DEFAULT_DIR}}

## Derive filename from ximdex_location
PACKAGE_FILENAME=${XIMDEX_DIR##$(dirname $XIMDEX_DIR)}
# last slash
PACKAGE_FILENAME=${PACKAGE_FILENAME%"/"}
# first slash
PACKAGE_FILENAME=${PACKAGE_FILENAME#"/"}
# extension
PACKAGE_FILENAME="$PACKAGE_FILENAME.tar.gz"

## Check tar
echo -n "    · Checking if tar exists... "
TAR=$(which tar)
ret_value=$?
if [ $ret_value -ne 0 ]; then
	echo "not found. (exiting)"
	exit $EXIT_FAILURE
else
	echo "found ($TAR)"	
fi

## Make package
echo -n "    · Creating the package [$DESTINATION_DIR/$PACKAGE_FILENAME]..." 
PARENT_DIR=$(dirname $XIMDEX_DIR)
CHILD_DIR=${XIMDEX_DIR##$(dirname $XIMDEX_DIR)}
CHILD_DIR=${CHILD_DIR%"/"}
CHILD_DIR=${CHILD_DIR#"/"}

PACKAGE_CMD="-C $PARENT_DIR -cvzf $DESTINATION_DIR/$PACKAGE_FILENAME --exclude=$CHILD_DIR/modules/xim* --exclude=$CHILD_DIR/data --exclude=$CHILD_DIR/*.svn --exclude=$CHILD_DIR/*.htaccess --exclude=$CHILD_DIR/devel --exclude=$CHILD_DIR/doc $CHILD_DIR" 

if (tar $PACKAGE_CMD) >> $FILE_LOG 2>&1
then
        echo "Ok"
else
        echo "with errors. (exiting)"
        exit $EXIT_FAILURE
fi

## Greets
echo
echo "+ Done [$DESTINATION_DIR/$PACKAGE_FILENAME]"
echo

