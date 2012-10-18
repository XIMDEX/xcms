#!/bin/bash
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

if [ "$LIB_BACKUP" = "1" ];
then
exit 0;
fi

#lib backup loaded
LIB_BACKUP=1
TAR=$(which tar)
# VARS
TARGET_BACKUP="$XIMDEX_PATH"
#name file of backup
TAR_OUT="backup"
#name extension of backup
TAR_EXT="tar"
#final directory of backup
TAR_OUT_DIR="$XIMDEX_PATH/data/backup"
#compress type: tar|gzip|bzip2
COMPRESS="tar"
#verbose in backup
BK_VERBOSE=""
#backup result
BK_RESULT=""
#backup error
BK_ERROR=0
#directories dont added to backup
EXCLUDE_DIRS=""
#incremental option
INCREMENTAL=""


function new.backup()
{
  TARGET_BACKUP="$XIMDEX_PATH"
  TAR_OUT="XIMDEX${XIMDEX_PATH//[\/]/_}"
  TAR_EXT="tar"
  COMPRESS=""
  VERBOSE=""
  BK_RESULT=""
  EXCLUDE_DIRS=""
  BK_ERROR=0
}

function backup.doit()
{
  if [ "$BK_ERROR" = 0 ]; then
	( $TAR --create --preserve-permissions $BK_VERBOSE $COMPRESS --file $TAR_OUT_DIR/$TAR_OUT.$TAR_EXT $INCREMENTAL  $TARGET_BACKUP  $EXCLUDE_DIRS )

   io.log "Backup: $TAR --create --preserve-permissions $BK_VERBOSE $COMPRESS --file $TAR_OUT_DIR/$TAR_OUT.$TAR_EXT $INCREMENTAL $TARGET_BACKUP  $EXCLUDE_DIRS" "info"

   BK_ERROR=$?
   BK_RESULT="$TAR_OUT.$TAR_EXT"
  fi
}

function backup.setTarget()
{
  TARGET_BACKUP=${1:-TARGET_BACKUP}

  if [ ! -d "$TARGET_BACKUP" ] && [ ! -f "$TARGET_BACKUP" ] ; then
	 io.log "Backup: TARGET_BACKUP[$TARGET_BACKUP] is not found" "fatal"
	 BK_ERROR=1
  fi
}

function backup.setName()
{
  TAR_OUT=${1:-$TAR_OUT}
}

function backup.setCompress()
{
	 mode=${1:-gzip}
    if [ $mode = "gzip" ]; then
		COMPRESS="--gzip"
		TAR_EXT="tgz"
	 elif [ $mode = "bzip2" ]; then
		COMPRESS="--bzip2"
		TAR_EXT="bz2"
	 else
		COMPRESS=""
		TAR_EXT="tar"
	 fi
}

function backup.setExcludeDirs()
{
  for i in $*
  do
	 EXCLUDE_DIRS="$EXCLUDE_DIRS --exclude=$i"
  done
}

function backup.setVerbose()
{
  BK_VERBOSE="--verbose"
}

#TODO:incremental
function backup.doitIncremental()
{
 #backup for today
 DAY_TODAY=$(date.today)
 #backup yestarday
 DAY_YESTERDAY=$(date.yesterday)
 TAR_OUT_YESTERDAY_INC="${TAR_OUT}_inc${DAY_YESTERDAY}.$TAR_EXT"

 INCREMENTAL=""
 #was there  a incremental yesterday?
 if [ -f "$TAR_OUT_DIR/$TAR_OUT_YESTERDAY_INC" ];
 then
	 INCREMENTAL=" --newer-mtime  $TAR_OUT_DIR/$TAR_OUT_YESTERDAY"
	 TAR_OUT="${TAR_OUT}_inc${DAY_TODAY}"
 elif [ -f "$TAR_OUT_DIR/$TAR_OUT.$TAR_EXT" ]; # full is the reference
 then
    INCREMENTAL=" --newer-mtime  $TAR_OUT_DIR/$TAR_OUT.$TAR_EXT"
	 TAR_OUT="${TAR_OUT}_inc${DAY_TODAY}"
 fi

 backup.doit
}

function backup.getName()
{
  echo  $BK_RESULT;
}

function backup.result()
{
  echo $BK_ERROR;
}
