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

XIMDEX_PARAMS_HOST=${XIMDEX_PARAMS_HOST:-''}
XIMDEX_PARAMS_PATH=${XIMDEX_PARAMS_PATH:-''}
XIMDEX_USER=${XIMDEX_USER:-''}
XIMDEX_PASSWD=${XIMDEX_PASSWD:-''}
XIMDEX_LOCALE=${XIMDEX_LOCALE:-''}
XIMDEX_STATS=${XIMDEX_STATS:-''}
SCRIPT_PATH=$(cd ${0%/*} && pwd -P)
DO_BACKUP=1


. $SCRIPT_PATH/lib/functions.sh

function checkInstallParams
{
	if [ ! -e "$INSTALL_PARAMS" ]
	then
		 println "Creating install params: $INSTALL_PARAMS"
		 $(cp  $TEMPLATE $INSTALL_PARAMS)
	else
		#########  Restoring Database params #########################

		if [ -z "$BACKUP" ] && [ "$DO_BACKUP" = 1 ]
		then
			datenow=$(date +%N)
			BACKUP="${XIMDEX_PATH}/conf/install-params.conf_bk_$datenow.php"
			println  "Backup $INSTALL_PARAMS to $BACKUP "
		   $(cp  $INSTALL_PARAMS $BACKUP)
		fi

		#restoring template
		LINE_SPLIT=$(grep -n "XIMDEX_PARAMS" $TEMPLATE|cut -d ":" -f 1,1)
		TOTAL_LINE=$(wc -l $TEMPLATE|sed 's/^ *//g' | cut -d " " -f 1,1)
		FOOT=$(expr $TOTAL_LINE - $LINE_SPLIT + 1)
		HEAD=$(expr $LINE_SPLIT - 1)
		#println "Line split: $LINE_SPLIT | Total Line. $TOTAL_LINE | Foot: $FOOT | Head: $HEAD"
		FILE_HEAD=$(head -n $HEAD $INSTALL_PARAMS)
		FILE_TAIL=$(tail -n $FOOT $TEMPLATE)
		$(echo "$FILE_HEAD"  > $INSTALL_PARAMS)
		$(echo  -e "\n\n"  >> $INSTALL_PARAMS)
		$(echo "$FILE_TAIL"  >> $INSTALL_PARAMS)
	fi

  DO_BACKUP=0

	next_step
}

# ######################## FUNCTIONS ################################
function usage
{
  echo -e "Usage: ximparams [-options]"
  echo -e "\nwhere options include:"
  echo -e "    -v                   verbose mode"
  echo -e "    -i                   interactive mode"
  echo -e "    -h <host>            Ximdex host"
  echo -e "    -x <path>       	   Ximdex path"
  echo -e "    -u <user>       	   Ximdex admin user"
  echo -e "    -p <password>        Ximdex admin password"
  echo -e "    -l <locale>          Ximdex default locale"
  echo -e "    -s 1|0   	         Ximdex stasts, 1=true|0=false"
  echo -e "    -n	    			      Not backup of install-params"
  echo -e "\nExamples:"
  echo -e "   ximparams -v -h http://myximdex/lab -p /var/www/ximdex/lab -u ximdex \n"
}


################# SET PARAMS FUNCTIONS #################################
function setHost
{

   HOST=$(echo $XIMDEX_PATH|sed -e "s/.*\///g"|sed -e "s/\///g")

	if [ -z "$XIMDEX_PARAMS_HOST" ]
	then
	  XIMDEX_PARAMS_HOST="http://localhost/$HOST";
	  if [ $INTERACTIVE = 1 ]
	 then
	  echo -n "Ximdex Host [$XIMDEX_PARAMS_HOST]: "
	  read option
	  XIMDEX_PARAMS_HOST=${option:-$XIMDEX_PARAMS_HOST}
	  fi
	fi

  test=$(wget --spider $XIMDEX_PARAMS_HOST/README.md 2>/dev/null)

   if [ $? = 0 ] &&  [ -n "$XIMDEX_PARAMS_HOST" ]
	then
		sql "UPDATE Config SET ConfigValue='$XIMDEX_PARAMS_HOST' WHERE ConfigKEY='UrlRoot';"
		next_step
	else
	   echo "Ximdex host not found. "
	  XIMDEX_PARAMS_HOST=''
	fi
}

function setPath
{
	if [ -z "$XIMDEX_PARAMS_PATH" ]
	then
	  XIMDEX_PARAMS_PATH=$XIMDEX_PATH;
	  if [ $INTERACTIVE = 1 ]
	 then
	  echo -n "Ximdex Path [$XIMDEX_PARAMS_PATH]: "
	  read option
	  XIMDEX_PARAMS_PATH=${option:-$XIMDEX_PARAMS_PATH}
	  fi
	fi

	if [ -n "$XIMDEX_PARAMS_PATH " ] && [ -d "$XIMDEX_PARAMS_PATH" ] && [ -f "$XIMDEX_PARAMS_PATH/README" ]
	then
		assign "XIMDEX_PATH"  $XIMDEX_PARAMS_PATH
		sql "UPDATE Config SET ConfigValue='$XIMDEX_PARAMS_PATH' WHERE ConfigKEY='AppRoot';";
		next_step
	else
	   echo "Ximdex path not found. "
	   XIMDEX_PARAMS_PATH=''
	fi
}


function setAdminUser
{

	if [ -z "$XIMDEX_USER" ]
	then
	  XIMDEX_USER='ximdex';
	  if [ $INTERACTIVE = 1 ]
		 then
	  echo -n "Ximdex admin user [$XIMDEX_USER]: "
	  read option
	  XIMDEX_USER=${option:-$XIMDEX_USER}
	  fi
	fi

	if [ -n "$XIMDEX_USER" ]
	then
		sql "UPDATE Users SET Login='$XIMDEX_USER' where IdUser = '301'"
		sql "UPDATE Nodes SET Name='$XIMDEX_USER' where IdNode = '301'"
		next_step
	else
	   echo "Ximdex admin user not found. "
	   XIMDEX_USER=''
	fi
}


function setAdminPass
{


	if [ -z "$XIMDEX_PASSWD" ]
	then
	  XIMDEX_PASSWD="";
	  if [ $INTERACTIVE = 1 ]
	    then
     	  stty -echo
		  echo -n "Ximdex admin password: "
 	 	  read option
 	 	  echo ""
		  XIMDEX_PASSWD=${option:-''}
		  echo -n "Ximdex admin password (repeat): "
		  read option
 	 	  echo ""
		  XIMDEX_PASSWD2=${option:-''}
		  stty echo
	  fi
	else
		XIMDEX_PASSWD2="$XIMDEX_PASSWD"
	fi

	if [ -n "$XIMDEX_PASSWD" ] && [ -n "$XIMDEX_PASSWD2" ]  && [ "$XIMDEX_PASSWD" = "$XIMDEX_PASSWD2" ]
	then
		sql "UPDATE Users SET Pass=MD5('$XIMDEX_PASSWD') where IdUser = '301'"
		next_step
	else
  		 echo "Ximdex admin password not found. "
	    XIMDEX_PASSWD=''
	fi
}



function setLocale
{
	if [ -z "$XIMDEX_LOCALE" ]
	then
	  XIMDEX_LOCALE="en_US";
	  if [ $INTERACTIVE = 1 ]
	 then

		#Get available languages in ximdex
	  declare -a arr_langs=( `ls  -m  $XIMDEX_PATH/inc/i18n/locale/|sed -e "s/,//g"` )

   	if [ -z  "${#arr_langs[@]}" ]; then
			echo "Languages not found.";
			exit 1;
		fi

	  #Get names
	  languages=""
		for lang in  "${arr_langs[@]}"
		do
			sql "Select Name FROM Locales where Code = '$lang' ORDER BY Name ASC "
			if [ -n "$QUERY_DB" ] && [ "$ERROR_DB" != "ERROR 2005" ];
			then
				languages=$(echo "$languages $QUERY_DB")
				sql "UPDATE Locales SET Enabled='1' where Code = '$lang'"
			fi
		done

		if [ -z "$languages" ]; then
				echo "Languages not found";
				exit 1;
		fi


		out=0
		while [ $out != 1 ]; do
				echo  "Select your Ximdex default language choosing betweeen:"

				i=1;
				for option in $languages;
				do
					echo "        $i. $option";
					i=$(expr $i + 1)
				done

				echo -ne "\nXimdex default lenguage[1]: "
				read option;

				if [ -n "$option" ] && [ "$option" -ge 1 ] && [ "$option" -le ${#arr_langs[@]} ] ;
				then
					option=$(expr $option - 1 );
					XIMDEX_LOCALE=${arr_langs[$option]:-$XIMDEX_LOCALE}
					out=1
				elif [ -z "$option" ]
				then
					XIMDEX_LOCALE=${arr_langs[0]:-$XIMDEX_LOCALE}
					out=1
				else
					echo -e "\nError: Language does not exist";
				fi
			done
	  fi
	fi

	if [ -n "$XIMDEX_LOCALE " ] && [ -d "$XIMDEX_PATH/inc/i18n/locale/$XIMDEX_LOCALE/" ]
	then
		assign "XIMDEX_LOCALE"  $XIMDEX_LOCALE
		sql "UPDATE Config SET ConfigValue='$XIMDEX_LOCALE' WHERE ConfigKEY='locale';";
		#assign timezone
		if [ -f '/etc/timezone' ]
		then
			XIMDEX_TIMEZONE=$(cat /etc/timezone);
		fi

		XIMDEX_TIMEZONE=${XIMDEX_TIMEZONE:-"Europe/Madrid"}

		assign "XIMDEX_TIMEZONE" $XIMDEX_TIMEZONE
		next_step
	else
	   echo "Ximdex default lenguage not found. "
	   XIMDEX_LOCALE=''
	fi
}

function setXimdexUuid
{

  echo -n "Getting Ximdex identifier... "
  HOSTNAME=$(hostname)
  HOSTNAME=${HOSTNAME:-locahlost}
  XIMDEX_UUID=$(wget -T 3 -q -O - http://xid.ximdex.net/stats/getximid.php?host=$HOSTNAME);

  if [ $? != 0 ] ||  [ -z "$XIMDEX_UUID" ]
  then
		XIMDEX_UUID=$(php -r "echo uniqid("$HOSNAME"); ")
		XIMDEX_UUID="${HOSTNAME}_${XIMDEX_UUID}"
  fi

 if [ -z "$XIMDEX_UUID" ]
 then
	echo "Error: ximdex_uuid could not been obtained.";
 else
   echo "Success"
	sql "UPDATE Config SET ConfigValue='$XIMDEX_UUID' WHERE ConfigKEY='ximid';";
	next_step
 fi
}

function SetStatistics
{
	if [ -z "$XIMDEX_STATS" ];
	then
		io.question "Would you like to help us to improve sending information about Ximdex usage? (recommended) "
		XIMDEX_STATS=$(io.getOption)
	fi

	if [ "$XIMDEX_STATS" = '1' ]
	then
		sql "UPDATE Config SET ConfigValue='1' WHERE ConfigKEY='ActionsStats';";
	fi

	next_step
}


# ################################### GET PARAMS #################################
while getopts 'vih:x:u:p:nl:s:' OPTION;
do
  case $OPTION in
  	v) #mode verbose ON
		VERBOSE=1;;

	i) #mode interative ON
		INTERACTIVE=1;;

	h) #ximdex host
		XIMDEX_HOST=$OPTARG;;

	x) #ximdex path
		 XIMDEX_PARAMS_PATH=$OPTARG;;

	u) #ximdex user
		XIMDEX_USER=$OPTARG;;

	p) #ximdex password
		XIMDEX_PASSWD=$OPTARG;;

	n) #not backup install-params
		DO_BACKUP=0;;

	l) #ximdex locale
		XIMDEX_LOCALE=$OPTARG;;
	s) #ximdex stats
		XIMDEX_STATS=$OPTARG;;
	*) 	usage
		exit 1;
  esac
done

if [ "$#" = "0" ]
then
 usage
 exit 1;
fi

#for options future
shift $(($OPTIND - 1))

################# SET PARAMS #################################
echo ""
echo ""
echo "***********************"
echo "* Ximdex param config *"
echo "***********************"
echo ""
echo ""


getDBParams

#- ask server
STEP=0
while [ $STEP != "END" ]
do
	case $STEP in
	0) checkInstallParams;; #restore ximdex_params
	1) setHost;;  #ask host
	2) setPath;; #ask path
	3) setAdminUser;;
	4) setAdminPass;;
	5) setLocale;;
   6) setXimdexUuid;;
	7) SetStatistics;;
 	8)
		echo "Configuring Ximdex params... Success"
		exit 0;;
	*)
		exit 1;;
	esac
	println "STEP: $STEP";
done
