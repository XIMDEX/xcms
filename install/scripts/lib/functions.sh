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


VERBOSE=0
INTERACTIVE=0
AUTOMATIC=0
SCRIPT_USER=`whoami`
if [ -n "$SUDO_USER" ]
then
 export SCRIPT_USER=${SUDO_USER:-$USERNAME}
else
 export SCRIPT_USER=${USERNAME:-$SUDO_USER}
fi
XIMDEX_PATH=${SCRIPT_PATH/\/install\/scripts/}
TEMPLATE="${XIMDEX_PATH}/install/templates/install-params.conf.php"
INSTALL_PARAMS="${XIMDEX_PATH}/conf/install-params.conf.php"
PHP_CMD=`which php`
LOG=${XIMDEX_PATH}/install/install.log
####### DB_PARAMS #####
ERROR_DB=0
ADD_DATA=1
QUERY_DB=${QUERY_DB:-""}
USER_ADMIN=${USER_ADMIN:-""}
PASSWD_ADMIN=${PASSWD_ADMIN:-""}
USER_DB=${USER_DB:-""}
PASSWD_DB=${PASSWD_DB:-""}
SERVER_DB=${SERVER_DB:-""}
DATABASE=${DATABASE:-""}
PORT_DB=${PORT_DB:-""}

#perms to conf
$(chmod u+w $XIMDEX_PATH/conf)
$(chmod u+x $XIMDEX_PATH/install/install.sh)


function println()
{
 if [ $VERBOSE == 1 ]
 then
  echo -e "$1"
 #else
   #echo -e "$1" >>  $LOG
 fi
# DEBUG into LOG FILE
 echo -e "$1" >>  $LOG
}


function next_step
{
STEP=`expr $STEP + 1`
}

function prev_step
{
STEP=`expr $STEP - 1`
}

function assign
{
  element=$1
  value=${2//\//\\\/}
 INSTALL_PARAMS_TEMP=$INSTALL_PARAMS\.$RANDOM 

 $(sed "s/##${element}##/${value}/g" $INSTALL_PARAMS > $INSTALL_PARAMS_TEMP)
 $(mv $INSTALL_PARAMS_TEMP $INSTALL_PARAMS)
  println "sed s/##${element}##/${value}/g   $INSTALL_PARAMS"
}

function getDBParams
{
	data=$(cat $INSTALL_PARAMS|grep  "\$DB")
	data=${data//[\$\;\ \"]/}
	println "Load database params"
	for line in ${data}
	do
		declare -x "$line"
	done

	DATABASE=$DBNAME
	SERVER_DB=$DBHOST
	USER_ADMIN=$DBUSER
	PASSWD_ADMIN=$DBPASSWD
	PORT_DB=$DBPORT
	println "DB PARAMS READ --> HOST: $DBHOST | PORT: $DBPORT | DBUSER: $DBUSER | DBPASSWD: $DBPASSWD | DBNAME: $DBNAME "
}


function sql
{
  sql=$1;
  mysql_query=`(mysql --skip-column-names  $DATABASE -u $USER_ADMIN -p$PASSWD_ADMIN -h $SERVER_DB -P $PORT_DB -e "$sql") 2>&1`
  println "Conection:  -u $USER_ADMIN -p$PASSWD_ADMIN -h $SERVER_DB  "
  QUERY_DB=$mysql_query
  ERROR_DB=""
  if [[ $QUERY_DB =~  ^ERROR ]]; then
    ERROR_DB=$(echo "$QUERY_DB"|cut -d ' ' -f 2,1);
  fi
	
  println "Connectiong to mysql --skip-column-names  $DATABASE -u $USER_ADMIN -p$PASSWD_ADMIN -h $SERVER_DB -P $PORT_DB" 
  #println "Running query: $sql)" 
  #println "QUERY: $QUERY_DB"
  println "SQL: $sql | ERROR: $ERROR_DB"
  #println "SQL: $sql | RESULT: $QUERY_DB | ERROR: $ERROR_DB "
}

function checkDBconn {
  sql="status;"
  mysql_query=`(mysql --skip-column-names  $DATABASE -u $USER_ADMIN -p$PASSWD_ADMIN -h $SERVER_DB -P $PORT_DB -e "$sql") 2>&1`
  QUERY_DB=$mysql_query
  #println "SQL: $sql | RESULT: $QUERY_DB | ERROR: $ERROR_DB "
  if [[ $QUERY_DB =~  ^ERROR ]]; then
    	ERROR_DB=$(echo "$QUERY_DB"|cut -d ' ' -f 2,1);
        echo "Database returned error code $ERROR_DB"
  	echo "$QUERY_DB"
        echo ""
        echo "CAN NOT ACCESS THE DATABASE. ENDING!"
        
        exit 1
  else
        echo "Database connection stablished!"  
  fi

}

function sql_user
{

  sql=$1;
  mysql_query=`(mysql $DATABASE -u $USER_DB -p$PASSWD_DB -h $SERVER_DB --port $PORT_DB -e "$sql") 2>&1`
  #println "Conection:  -u $USER_ADMIN -p$PASSWD_ADMIN -h $SERVER_DB  "
  QUERY_DB=$mysql_query
  ERROR_DB=$(echo "$QUERY_DB"|cut -d ' ' -f 2,1);
  println ""

}

function withroot
{
  if [ "$XIMDEX_INSTALL" = 1 ];
  then
	 return 0;
  fi

	SCRIPT_USER=`whoami`
	if [ $SCRIPT_USER != 'root' ]
	then
		 $(sudo -v 2>/dev/null)
		 if [ 0 = $? ]
		 then
		   exec sudo  -p "Setting perms needs root user.Root password:" $0 $*  # Call this prog as root
		 else
		   echo "Setting perms needs root user"
		   exec su -c "$0 $*"
		 fi
	 exit ${?}
	else
	  println "Using root user..."
	fi


	#¿in sudo?
	if [ -n "$SUDO_USER" ]
	then
	 SCRIPT_USER=${SUDO_USER:-$USERNAME}
  else
	 SCRIPT_USER=${USERNAME:-$SUDO_USER}
	fi

}

IO_OPTION=0
function io.question()
{
 question="$1"

  option='x'
  while [ "$option" != 'Y' ] && [ "$option" != 'y' ] && [ "$option" != 'n' ] && [ "$option" != 'N' ]
  do
  echo -n  "$question [y/n]: "
  read option
  done

  if  [ "$option" == 'Y' ] || [ "$option" == 'y' ];
  then
	 IO_OPTION=1
  else
	 IO_OPTION=0
  fi

}

function io.getOption()
{
  echo $IO_OPTION
}
