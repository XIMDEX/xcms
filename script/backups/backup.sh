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

SCRIPT=$0;
SCRIPT_VARS=$*
SCRIPT_NUM_VARS=$#
REL_PATH="/script/backups"

#includes 
. ../lib/io.sh
io.include /script/lib/db.sh
io.include /script/lib/util.sh
io.include /script/lib/date.sh
io.include /script/lib/backup.sh


new.backup
backup.setTarget $XIMDEX_PATH   #Ximdex backup
backup.setExcludeDirs  $XIMDEX_PATH/data/backup  $XIMDEX_PATH/data/cache  $XIMDEX_PATH/data/tmp  $XIMDEX_PATH/data/trash
backup.setExcludeDirs  $XIMDEX_PATH/data/previos  $XIMDEX_PATH/data/creator  $XIMDEX_PATH/data/sync  $XIMDEX_PATH/logs
backup.setCompress bzip2
DAY_TODAY=$(date.today)
TODAY=$(date.get)

if [ "$DAY_TODAY" = "$SATURDAY" ]; then
 #FULL
 #borrado del full anterior
  (rm $XIMDEX_PATH/data/backup/*)
  backup.doit
  db.databaseBackup "$XIMDEX_PATH/data/backup/db_$TODAY.sql"
elif [ "$DAY_TODAY" != "$SUNDAY" ]; then
 #INCREMENTAL
  backup.doitIncremental
  db.databaseBackup "$XIMDEX_PATH/data/backup/db_$TODAY.sql"
else
  io.log  "BACKUP: there isnt backup today" "info"
  exit 0
fi


name=$(backup.getName)

io.log  "BACKUP: Backup "$name" completed" "info"