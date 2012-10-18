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

if [ "$LIB_DB" = "1" ];
then
exit 0;
fi


LIB_DB=1
DBNAME=''
DBHOST=''
DBUSER=''
DBPASSWD=''
DB_RESULT=''
DB_ERROR=''
DB_NUM_ERROR=0
MYSQL_CMD=$(which mysql)
MYSQL_DUMP=$(which mysqldump)

function db.init()
{
  io.initVarFromFile $INSTALL_PARAMS "DB"
}


function db.sql()
{
  if [ -z "$1"]; then
	 return 1;
  fi

  sql=$1;
  mysql_query=`($MYSQL_CMD --skip-column-names   $DBNAME -u $DBUSER -p$DBPASSWD -h $DBHOST -e "$sql") 2>&1`
  #io.println "Conection:  -u $USER_ADMIN -p$PASSWD_ADMIN -h $SERVER_DB  "
  DB_RESULT=$mysql_query
  DB_NUM_ERROR=$?
  if [ $DB_NUM_ERROR != "0" ]; then
	 DB_ERROR=$(echo "$DB_RESULT"|cut -d ' ' -f 2,1);
  fi
  #io.println ""
  #io.println "SQL: $sql | RESULT: $QUERY_DB | ERROR: $ERROR_DB "
  if [ -n "$2" ]; then
		eval $1="$DB_RESULT"
  fi

  if [ -n "$3" ]; then
		eval $2="$DB_ERROR"
  fi
}

function db.tables()
{
  db.sql "show tables;"

  if [ -n "$1" ]; then
		eval $1=$DB_RESULT
  fi
}

function db.checkConnection()
{

db.tables
if [ -z "$DB_ERROR" ]
then
	 return 0;
else
	 return $?;
fi

}


function db.select()
{

  if [ -z "$1" ] || [ -z "$2" ]; then
	 return 1;
  fi

  if [ -n "$1" ]; then
	 select_fields="select $1"
  else
	 select_fields="select *"
  fi

  from="from $2"

  if [ -n "$3" ]; then
	 where= "where $3"
  else
	 where=''
  fi


  if [ -n "$4" ]; then
	 limit= "limit $4"
  else
	 limit=''
  fi

  if [ -n "$5" ]; then
	 orderby= "order by $5"
  else
	 orderby=''
  fi


  db.sql "$select_fields $from $where $orderby $limit"
}

function db.update()
{
  if [ -z "$1" ] || [ -z "$2" ]; then
	 return 1;
  fi

  table=$1
  $set "set $2"

  if [ -n "$3" ]; then
	 where= "where $3"
  else
	 where=''
  fi

  db.sql "update $table $set $where"
}

function db.delete()
{
  db.sql "delete from $1 where $2"
}


#------------- BACKUPS -----------------
function db.databaseBackup() {
  if [ -z "$1" ]; then
	 return 1;
  fi

  backup_file=$1
  options="$2"

  $($MYSQL_DUMP $options --opt --host=$DBHOST --user=$DBUSER --password=$DBPASSWD $DBNAME > $backup_file)
}

function db.tablesBackup() {
  if [ -z "$1" ] || [ -z "$2" ]; then
	 return 1;
  fi

  tables=$1
  backup_file=$2
  options="$3"

  $($MYSQL_DUMP  $options --opt --host=$DBHOST --user=$DBUSER --password=$DBPASSWD $DBNAME $tables > $backup_file)
}


#------------- RESTORE BACKUPS -----------------
function db.restoreBackup() {
  if [ -z "$1" ]; then
	 return 1;
  fi

  backup_file=$1
  options="$2"

  $(cat $1|$MYSQL_CMD $options -h $DBHOST -u $DBUSER -p$DBPASSWD $DBNAME)

}


db.init