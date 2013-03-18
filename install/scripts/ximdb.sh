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



SCRIPT_PATH=$(cd ${0%/*} && pwd -P)
DO_BACKUP=1

. $SCRIPT_PATH/lib/functions.sh



function checkInstallParams
{
	if [ ! -e "$INSTALL_PARAMS" ]
	then
		 println "Creating install params: $INSTALL_PARAMS "
		 $(cp  $TEMPLATE $INSTALL_PARAMS)
	else
		#########  Restore Database params #########################

		if [ -z "$BACKUP" ] && [ $DO_BACKUP = 1 ]
		then
			datenow=$(date +%N)
			BACKUP="${XIMDEX_PATH}/conf/install-params.conf_bk_$datenow.php"
			println  "Backup $INSTALL_PARAMS to $BACKUP "
		   $(cp  $INSTALL_PARAMS $BACKUP)
		fi

		#restore template
		LINE_SPLIT=$(grep -n "XIMDEX_PARAMS" $TEMPLATE|cut -d ":" -f 1,1)
		TOTAL_LINE=$(wc -l $TEMPLATE|sed 's/^ *//g' | cut -d " " -f 1,1)
		FOOT=$(expr $TOTAL_LINE - $LINE_SPLIT + 1)
		HEAD=$(expr $LINE_SPLIT - 1)
		#println "Line split: $LINE_SPLIT | Total Line. $TOTAL_LINE | Foot: $FOOT | Head: $HEAD"
		FILE_HEAD=$(head -n $HEAD $TEMPLATE)
		FILE_TAIL=$(tail -n $FOOT $INSTALL_PARAMS)
		$(echo "$FILE_HEAD"  > $INSTALL_PARAMS)
		$(echo  -e "\n\n"  >> $INSTALL_PARAMS)
		$(echo "$FILE_TAIL"  >> $INSTALL_PARAMS)
	fi

  DO_BACKUP=0
	next_step
}

# ##################################################### FUNCTIONS ##########################################
function usage
{
  echo -e "Usage: ximdb [-options]"
  echo -e "\nwhere options include:"
  echo -e "    -v                   verbose mode"
  echo -e "    -i                   interactive mode"
  echo -e "    -u <user>            user Mysql"
  echo -e "    -p <password>        Mysql user password"
  echo -e "    -r <admin user>      Mysql Admin user"
  echo -e "    -w <password>        Mysql Admin user password"
  echo -e "    -h <host>            Mysql server host"
  echo -e "	   -k <port>			Mysql server port"
  echo -e "    -d <database>        Mysql database"
  echo -e "    -s <sql>             sql sentence"
  echo -e "    -n	            Not backup of install-params"

  echo -e "\nExamples:"
  echo -e "   ximdb -v -u ximdex_user -p ximdex_user -d ximdex \n"
}

################# SET PARAMS #################################

function setServer
{


	if [ -z $SERVER_DB ]
	then
	  SERVER_DB='localhost';
	  if [ $INTERACTIVE = 1 ]
	 then
	  echo -n "Database server [$SERVER_DB]: "
	  read option
	  SERVER_DB=${option:-$SERVER_DB}
	  fi
	fi

	#Remove protocol if exists
	server_db_t=${SERVER_DB/#*:\/\//}
	#Remove trailing slashes
	server_db_t=${server_db_t/%\/*}
	$(ping -q -c 1 ${server_db_t} >/dev/null 2>/dev/null)
	result=$?
	if [ $result == 0 ]
	then
		SERVER_DB=${server_db_t:-SERVER_DB}
		assign "DB_HOST" $SERVER_DB
		next_step
	else
		echo "Server not found. "
	        SERVER_DB=''
	fi
}

function setPort
{
	if [ -z $PORT_DB ]
	then
		PORT_DB=3306
		if [ $INTERACTIVE = 1 ]
		then
			echo -n "Database port [$PORT_DB]: "
			read option
			PORT_DB=${option:-$PORT_DB}
		fi
	fi
	
	echo -n "Checking connection to server $SERVER_DB and port $PORT_DB (wait a few seconds, please): "
	connection=`telnet $SERVER_DB $PORT_DB 2>&1 | grep 'Connected to'`
	if [ -n "$connection" ]
	then
		assign "DB_PORT" $PORT_DB
		echo "OK"
		next_step
	else
		echo "Couldn't connect to the specified port. "
		PORT_DB=''
	fi
}


function setAdminUser
{

	if [ -z "$USER_ADMIN" ]
	then
	  USER_ADMIN='root';
	  if [ $INTERACTIVE = 1 ]
		 then
	  echo -n "Admin database user [$USER_ADMIN]: "
	  read option
	  USER_ADMIN=${option:-$USER_ADMIN}
	  fi
	fi

	if [ -n "$USER_ADMIN" ]
	then
		next_step
	else
	   echo "Admin database user not found. "
	   USER_ADMIN=''

	fi
}

function setAdminPass
{

	if [ -z "$PASSWD_ADMIN" ]
	then
	  PASSWD_ADMIN='root';
	  if [ $INTERACTIVE = 1 ]
	    then
	    stty -echo
		  echo -n "Admin database password: "
 	 	 read option
	  	  echo ""
		  PASSWD_ADMIN=${option:-''}
		  echo -n "Admin database password (repeat): "
		  read option
 	 	  echo ""
		  PASSWD_ADMIN2=${option:-''}
		  stty echo
	  fi
	else
		PASSWD_ADMIN2="$PASSWD_ADMIN"
	fi

	if [ -n "$PASSWD_ADMIN" ] && [ -n "$PASSWD_ADMIN2" ] && [ "$PASSWD_ADMIN" = "$PASSWD_ADMIN2" ]
	then
		checkConnection

		  if [ "$ERROR_DB" = 'ERROR 1045' ]
		  then
			  	println "$mysql_query"
			  	echo "Connection failed! Connection data errors. ";
				STEP=0
				USER_ADMIN=''
				PASSWD_ADMIN=''
				SERVER_DB=''
				if [ "$AUTOMATIC_INSTALL" = 1 ]
				then
					echo "Please, check config file: $CONFIG_FILE. ";
					STEP="99"
				fi
				if [ $INTERACTIVE != 1 ]
				then
					STEP="99"
				fi
			else
			 		next_step
		  fi

	else
	    echo "Error in admin database password. "
	    PASSWD_ADMIN=''
	fi
}

function setUser
{

	if [ -z "$USER_DB" ]
	then
          USER_DB=${DATABASE:0:15}
	  if [ $INTERACTIVE = "1" ]
	 then
		  echo -n "Database user [$USER_DB]: "
		  read option
		  USER_DB=${option:-$USER_DB}
	  fi
	fi

	declare -i NAME_SIZE
	NAME_SIZE=${#USER_DB}

 	if [ $NAME_SIZE -gt 16 ]
 	then
 	   echo "User name is very long [max 16 characters]. "
 	    USER_DB=''
 	else

		if [ -n "$USER_DB" ]
		then
			assign "DB_USER" $USER_DB
			next_step
		else
		   echo "Database user not found."
		   USER_DB=''
		fi
	fi

}

function setUserPass
{

	 if [ "$USER_ADMIN" = "$USER_DB" ]
	 then
	 	PASSWD_DB=$PASSWD_ADMIN
	 	PASSWD_DB2=$PASSWD_ADMIN
	 fi

	if [ -z "$PASSWD_DB" ]
	then
	  PASSWD_DB="";
	  if [ $INTERACTIVE = 1 ]
	    then
     	  stty -echo
		  echo -n "User database password: "
 	 	  read option
 	 	  echo ""
		  PASSWD_DB=${option:-''}
		  echo -n "User database password (repeat): "
		  read option
 	 	  echo ""
		  PASSWD_DB2=${option:-''}
		  stty echo
	  fi
	else
		PASSWD_DB2="$PASSWD_DB"
	fi

	if [ -n "$PASSWD_DB" ] && [ -n "$PASSWD_DB2" ]  && [ "$PASSWD_DB" = "$PASSWD_DB2" ]
	then
		assign "DB_PASSWD" $PASSWD_DB
		next_step
	else
	    echo "User database password not found."
	    PASSWD_DB=''
	fi
}

#Added port and changed step to 6 instead of 5
function checkUserError
{
  checkInstallParams
  assign "DB_HOST" $SERVER_DB
  assign "DB_PORT" $PORT_DB
  assign "DB_NAME" $DATABASE

  USER_DB=''
  PASSWD_DB=''
  CREATE_USER=''
  STEP=6
}

function checkUser
{
  if [ "$USER_ADMIN" = "$USER_DB" ]
  then
     next_step
     return 0
  fi

  sql "SELECT user FROM mysql.user WHERE user='$USER_DB';"

  if [ -z "$QUERY_DB" ]
  then
		if [ -z "$CREATE_USER"  ];
		then
			if [ $INTERACTIVE = "1" ]
			then
				io.question "User '$USER_DB' does not exist. Do you want to create it? "
				CREATE_USER=$(io.getOption)
			else
				CREATE_USER="1"
			fi
		fi

		if [ "$CREATE_USER" = "1" ]
		then
				  	#Changed to @'%'. Previously, it was localhost but this didn't make sense if the server is an external server and not localhost. It's necessary twice time(localhost and all ). See #2583
					sql "GRANT ALL PRIVILEGES  ON $DATABASE.* TO '$USER_DB'@'localhost' IDENTIFIED BY '$PASSWD_DB'; FLUSH privileges; " 
					sql "GRANT ALL PRIVILEGES  ON $DATABASE.* TO '$USER_DB'@'%' IDENTIFIED BY '$PASSWD_DB'; FLUSH privileges; " 
					println "Database user does not exist. Creating it... "
		else
			checkUserError
			return 0
		fi
  else
     	println "Database user already exists."
  	#privilege to user
  	#Changed to @'%'. Previously, it was localhost but this didn't make sense if the server is an external server and not localhost.  It's neccesary twice time ( localhost y all ). See #2583 
      	sql "GRANT ALL PRIVILEGES  ON $DATABASE.* TO '$USER_DB'@'localhost' IDENTIFIED BY '$PASSWD_DB' WITH GRANT OPTION;"
      	sql "GRANT ALL PRIVILEGES  ON $DATABASE.* TO '$USER_DB'@'%' IDENTIFIED BY '$PASSWD_DB' WITH GRANT OPTION;"
   fi

	sql_user "show tables;"
	if [ "$ERROR_DB" = 'ERROR 1045' ]
	then
		echo "Connection error. "
		checkUserError
	else
	next_step
	fi
}


function checkConnection
{
 sql "show tables;"
 println "Checking connection... "
}

function setDB
{
  	if [ -z "$DATABASE" ]
	then
	  DATABASE=$(echo $XIMDEX_PATH|sed -e "s/.*\///g"|sed -e "s/\///g")
	  if [ $INTERACTIVE = 1 ]
		 then
	  echo -n "Database name [$DATABASE]: "
	  read option
	  DATABASE=${option:-$DATABASE}
	  fi
	fi

	if [ -n "$DATABASE" ]
	then
		checkConnection
		if [ "$ERROR_DB" = "ERROR 1049" ]
		then
		  DATABASE_T=$DATABASE
		  DATABASE=''
		  sql "create database $DATABASE_T;"
		  println "Creating database $DATABASE_T... "
		  DATABASE=$DATABASE_T
		  if [ "$ERROR_DB" = "ERROR 1064" ];
		  then
				echo "Invalid database name";
				DATABASE=''
				return 0
		  fi
		else
			if [ -z "$OVERWRITE_DATABASE" ];
			then
				io.question "Database already exists. Do you want to overwrite it? "
				OVERWRITE_DATABASE=$(io.getOption)
			fi

			if [ "$OVERWRITE_DATABASE" = '1' ]
			then
		   		sql "drop database $DATABASE;"
				println "Dropping database $DATABASE... "
				DATABASE_T=$DATABASE
				DATABASE=''
				sql "create database $DATABASE_T;"
				println "Creating database $DATABASE_T... "
				DATABASE=$DATABASE_T
			else
				io.question "Do you want to use the existing one? "
				option2=$(io.getOption)

				if [ "$option2" = '1' ]
				then
				   	println "Using $DATABASE... "
					ADD_DATA=0
				else
					DATABASE=''
					OVERWRITE_DATABASE=''
					return 0
				fi
			fi
		fi
		assign "DB_NAME" $DATABASE
		next_step
	else
	   echo "DATABASE name not found. "
	   DATABASE=''
	fi
}


function loadDATA
{
 if [ $ADD_DATA  = 1  ]
 then
  datain=$(mysql $DATABASE -u $USER_ADMIN -p$PASSWD_ADMIN -h $SERVER_DB --port $PORT_DB < $XIMDEX_PATH/install/ximdex_data/ximdex.sql)
  println "Importing ximdex data....mysql $DATABASE -u $USER_ADMIN -p$PASSWD_ADMIN -h $SERVER_DB --port $PORT_DB < $XIMDEX_PATH/install/ximdex_data/ximdex.sql"
 fi

   next_step
}


# ################################### GET PARAMS #################################
while getopts 'viu:p:r:w:h:dn' OPTION;
do
  case $OPTION in
  	v) #mode verbose ON
		VERBOSE=1;;

	i) #mode interative ON
		INTERACTIVE=1;;

	u) #user Mysql
		USER_DB=$OPTARG;;

	p) #passwd user Mysql
		PASSWD_DB=$OPTARG;;

	r) #Admin user Mysql
		USER_ADMIN=$OPTARG;;

	w) #passwd admin user Mysql
		PASSWD_ADMIN=$OPTARG;;

	h) #mysql server
		SERVER_DB=$OPTARG;;
	
	k) #mysql port
		PORT_DB=$OPTARG;;
	
	d) #database
		DATABASE=$OPTARG;;

	n) #not backup install-params
		DO_BACKUP=0;;

	*)	usage
		exit 1;
  esac
done

if [ "$#" = "0" ]
then
 usage
 exit 1;
fi


#for future options
shift $(($OPTIND - 1))

################# SET PARAMS #################################
echo ""
echo ""
echo "**************************"
echo "* Ximdex database config *"
echo "**************************"
echo ""
echo ""

#- ask server
STEP=0
while [ $STEP != "END" ]
do
	case $STEP in
	0) checkInstallParams;; #restore database_params
	1) setServer;;  #ask server
	2) setPort;;	#ask port
	3) setAdminUser;; #admin user
	4) setAdminPass;; #admin pass
	5) setDB;; #database
	6) setUser;; #normal user
	7) setUserPass;; #normal user pass
	8) checkUser;;  #check if user exists
	9) loadDATA;;
	10)
		echo "Configuring Ximdex database... Success"
		exit 0;;
	*)
		exit 1;;
	esac
	println "STEP: $STEP";
done

