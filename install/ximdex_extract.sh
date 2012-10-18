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


## Notes:
##	1) The database file name should be the same than the one containing the compressed ximdex.
##	2) It recognise the compression format extension (gz|tgz|bz2).
##

## TODO:
##   · Add option --automatic.
##   · Checkr bash
##   · Try to extract all in one (ignoring the first directory)

### ------------------------------------------------------------
### Constants
### ------------------------------------------------------------
VERSION=25d

DEFAULT_DIR=$PWD
INSTALL_DIR="install/"
INSTALL_BIN="installer.sh"
SQL_INSTALL_DIR=$INSTALL_DIR"ximdex_data/sql/"
EXTRACT_LOG="ximdex_extract.log"

SYSTEM_CONFIG="system.config"
XIMDEX_CONFIG="ximdex.config"


EXIT_SUCCESS=1
EXIT_FAILURE=0
true=1
false=0

### ------------------------------------------------------------
### Main Script.
### ------------------------------------------------------------

if [ ! -n "$1" ]; then
	echo "Sintaxis: $(basename $0) <ximdex_package> <dest dir>"
	echo 
	echo "<ximdex_package> : Ximdex package"
	echo "<dest_dir>       : Source directory where decompress the package (Optional, by default './')"
	echo
	exit $EXIT_FAILURE
fi

## Information about the compressed file which contains Ximdex
EXTRACT_DIR=${2:-${DEFAULT_DIR}}
EXTRACT_FILE=$1
EXTRACT_FILE_EXT=${EXTRACT_FILE:$(expr $EXTRACT_FILE : ".*\.")}

## TODO: It is depending on the name of the file which contains Ximdex
##       (deletes extensions and add sql.gz) 
##       It is not robust.
SQL_FILE_WITHOUT_EXT=${EXTRACT_FILE:0:$(expr index $EXTRACT_FILE ".")}
SQL_FILE=$SQL_FILE_WITHOUT_EXT"sql.gz"


echo "$(basename $0) - version $VERSION"
echo
echo "Running jobs:"

## Print information in log.
echo > $EXTRACT_LOG
echo "------------------------------------------------------------" >> $EXTRACT_LOG
echo "$(date): $(basename $0) - version $VERSION" >> $EXTRACT_LOG
echo "------------------------------------------------------------" >> $EXTRACT_LOG

## Ximdex file test
##
echo -n "    · Checking compressed Ximdex file [$EXTRACT_FILE]... "
if [ ! -f $EXTRACT_FILE ]; then
	echo "not found (exiting)"
	exit $EXIT_FAILURE;
else
	echo "found!";
fi

## TODO: It is depending on the name of the file which contains Ximdex
##       (deletes extensions and add sql.gz) 
##       It is not robust.
SQL_FILE_WITHOUT_EXT=${EXTRACT_FILE:0:$(expr index $EXTRACT_FILE ".") - 1}
SQL_FILE=$SQL_FILE_WITHOUT_EXT".sql.gz"

## Ximdex file extension test
##
echo -n "    . Checking compressed Ximdex file format... "
case $EXTRACT_FILE_EXT in
	"tgz" | "gz" )
		EXTRACT_CMD="xvzf $EXTRACT_FILE"
		TEST_CMD="tzf $EXTRACT_FILE"
		echo "gzip"
	;;
	"bz2" )
		EXTRACT_CMD="xvjf $EXTRACT_FILE"
		TEST_CMD="tjf $EXTRACT_FILE"
		echo "bzip2"
	;;
	* )
		echo "extension not recognized"
		exit $EXIT_FAILURE
	;;
esac


## Ximdex database file test
##
echo -n "    · Checking compressed Ximdex database file [$SQL_FILE]... "
if [ ! -f $SQL_FILE ]; then
	echo "not found"
#	exit $EXIT_FAILURE;
else
	SQL_FILE_EXISTS=1
	echo "found!"
fi

## Target directory test
##
echo -n "    · Checking target directory [$EXTRACT_DIR]... "
if [ ! -d $EXTRACT_DIR ]; then
	mkdir $EXTRACT_DIR
	echo "not found ($EXTRACT_DIR created)."
else
	echo "found!"
fi

if [ $EXTRACT_DIR = $DEFAULT_DIR ]; then

	EXTRACT_CMD=$EXTRACT_CMD" -C $EXTRACT_DIR"

	## exec tar
	##
	echo -n "    · Uncompressing Ximdex file [tar $EXTRACT_CMD]... "
	if (tar $EXTRACT_CMD) >> $EXTRACT_LOG 2>&1
	then
		echo "Ok"
	else
		echo "with errors (exiting)"
		exit $EXIT_FAILURE
	fi
else

	EXTRACT_CMD=$EXTRACT_CMD" -C /tmp/"
	 echo -n "    · Uncompressing Ximdex file [tar $EXTRACT_CMD]... "
        if (tar $EXTRACT_CMD) >> $EXTRACT_LOG 2>&1
        then
                echo "Ok"
        else
                echo "with errors (exiting)"
                exit $EXIT_FAILURE
        fi
fi

## Test tar
##
## Posibles valores devueltos "dir/" o "./"
XIMDEX_DIR_PKG=$(tar $TEST_CMD | head -n1)
if [ $XIMDEX_DIR_PKG == "./" ]; then
	XIMDEX_DIR=$EXTRACT_DIR/
else
	XIMDEX_DIR=$EXTRACT_DIR/$XIMDEX_DIR_PKG
fi

if [ $EXTRACT_DIR != $DEFAULT_DIR ]; then
	
	echo -n "    · Copying /tmp/$XIMDEX_DIR_PKG/* a $EXTRACT_DIR ..."
	cp -a -r /tmp/$XIMDEX_DIR_PKG/* $EXTRACT_DIR
	echo "Ok"
	
	if [ ! -z $XIMDEX_DIR_PKG ]; then
		echo -n "    · Eliminando /tmp/$XIMDEX_DIR_PKG ..."
		rm -rf /tmp/$XIMDEX_DIR_PKG
		echo "ok"
	fi
	
	XIMDEX_DIR=$EXTRACT_DIR"/"
fi

## TODO: Verify the ximdex hierarchical structure

## Copy Ximdex database file to install folder
##
## Due to it is assumed that the sql file is compressed with gzip, decompressing.
if [ $SQL_FILE_EXISTS ]; then

    if [ ! -d $XIMDEX_DIR$SQL_INSTALL_DIR ]; then
	    mkdir -p $XIMDEX_DIR$SQL_INSTALL_DIR
    fi

    echo -n "    · Extracting $SQL_FILE to $XIMDEX_DIR$SQL_INSTALL_DIR... "
    zcat $SQL_FILE > $XIMDEX_DIR$SQL_INSTALL_DIR"ximdex.sql"
    echo "Ok"
fi

if [ -e $XIMDEX_CONFIG ]; then
	XIMDEX_CONFIG_EXISTS="1"
	cp $XIMDEX_CONFIG $XIMDEX_DIR$INSTALL_DIR$XIMDEX_CONFIG
	echo "    · Copied $XIMDEX_CONFIG in $XIMDEX_DIR$INSTALL_DIR$XIMDEX_CONFIG."
fi

if [ -e $SYSTEM_CONFIG ]; then
	SYSTEM_CONFIG_EXISTS="1"
	cp $SYSTEM_CONFIG $XIMDEX_DIR$INSTALL_DIR$SYSTEM_CONFIG
	echo "    · Copied $SYSTEM_CONFIG in $XIMDEX_DIR$INSTALL_DIR$SYSTEM_CONFIG."
fi

## End.
echo
echo "Extraction finished."
echo "    * Ximdex dir: $XIMDEX_DIR"
echo "    * Installer location: $XIMDEX_DIR$INSTALL_DIR$INSTALL_BIN"
echo

## Check installer presence.
if [ ! -f $XIMDEX_DIR$INSTALL_DIR$INSTALL_BIN ]; then
	echo " *ERROR: $XIMDEX_DIR$INSTALL_DIR$INSTALL_BIN not found."
	exit $EXIT_FAILURE
fi

if [ $XIMDEX_CONFIG_EXISTS -a $SYSTEM_CONFIG_EXISTS ]; then
	echo "Detected system configurations, running installer in automatic mode."
	echo
	cd $XIMDEX_DIR$INSTALL_DIR
	./$INSTALL_BIN --automatic
else
	echo "./installer.sh --help"
	cd $XIMDEX_DIR$INSTALL_DIR
	./$INSTALL_BIN --help
	echo
fi
