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

GROUP_APACHE=''
USER_APACHE=''
SCRIPT_PATH=$(cd ${0%/*} && pwd -P)
FILE=''

. $SCRIPT_PATH/lib/functions.sh


# ##################################################### FUNCTIONS ##########################################
function usage
{
  echo -e "Usage: perm [-options]"
  echo -e "\nwhere options include:"
  echo -e "    -v          verbose mode"
  echo -e "    -i          interactive mode"
  echo -e "    -a          automatic mode"
  echo -e "    -p <path>   ximdex path"
  echo -e "    -u <user>   user developer/apache"
  echo -e "    -g <group>  group apache"
  echo -e "    -f <file>   add user/group to file"
  echo -e "\nExamples:"
  echo -e "    perm -v -p /var/www/ximdex\n"
}

if [ 0 == $# ]
then
  usage
  exit 1
fi



#--- ROOT USER, PLEASE ----
withroot $*


function setperms()
{
dir="$1"
permdir=${2:-'---'}
permfile=${3:-'---'}
params=${4:-''}
directory="${XIMDEX_PATH:=''}/${dir}"

 if [ ! -d ${directory} ]
 then
   println "'$directory' directory not found";
 else
	 println "Setting perms to '${directory}' -R ( files: $permfile | dir:  $permdir ) ... "
	 option="1"
	 if [ $INTERACTIVE = 1 ]
	 then
		io.question "Do you want to set perms in '${directory}'? (recommended)"
		option=$(io.getOption)
    fi

	 if [ "$option" = '1' ]
	 then

		if [ $permfile != "---" ]
		then
		  $(find ${directory} ${params} -type f -exec chmod  ${permfile} {} \;)
		fi

		if [ $permdir != "---" ]
		then
		  $(find ${directory} ${params} -type d -exec chmod  ${permdir} {} \;)
		fi
   fi
 fi

}

function setexecperms() {

 println "Setting executable perm to script files.... "
 option="1"
 if [ $INTERACTIVE = 1 ]
 then
    io.question "Do you want to set executable perm to script files? (recommended) "
    option=$(io.getOption)
 fi

 if [ "$option" = '1' ]
 then
	 files=$(grep  -H -R -e  "^#\!.*/bin/" ${XIMDEX_PATH}|grep -v '.svn'|cut -d ':' -f 1,1)
	 for file in ${files[@]}; do
		chmod +x ${file}
	 done
  fi

}


function setown() {
perm_user=${1}
perm_group=${2}
perm_dir=${3}

  option="1"
  if [ $INTERACTIVE = 1 ]
  then
	 io.question "Do you want to set user/group to $perm_dir files? (recommended) "
    option=$(io.getOption)
  fi
 
  if [ "$option" = '1' ]
  then
    println "Setting user/group to $perm_dir files... "
    chown -R $perm_user:$perm_group $perm_dir
  fi
}

# ################################### GET PARAMS #################################
while getopts 'viu:g:p:af:' OPTION;
do
  case $OPTION in
  	v) #mode verbose ON
		VERBOSE=1;;

	i) #mode interative ON
		INTERACTIVE=1
		AUTOMATIC=0;;

	u) #user developer/apache
		USER_APACHE=$OPTARG;;

	g) #group apache
		GROUP_APACHE=$OPTARG;;

	p) #XIMPDEX Path
		XIMDEX_PATH=$OPTARG;;

	a) #Automatic mode ON
		INTERACTIVE=0
		AUTOMATIC=1;;

	f) 	FILE=$OPTARG;;
		
	*) 	usage
		exit 1;
  esac
done

if [ "$#" = "0" ] && [ "$AUTOMATIC" != "1" ]
then
 usage
 exit 1;
fi


#for future options
shift $(($OPTIND - 1))

################# SET PARAMS ################################# 
if [ -z $XIMDEX_PATH ]
then
 XIMDEX_PATH=${SCRIPT_PATH/\/install\/scripts/}

 if [ $INTERACTIVE = 1 ]
 then
  echo -n "Ximdex Path [$XIMDEX_PATH]: "
  read option;
  XIMDEX_PATH=${option:-$XIMDEX_PATH}
 fi
fi


if [ ! -d $XIMDEX_PATH ]
then
  echo "Directory '$XIMDEX_PATH' does not exist"
  exit 1
fi

if [ ! -d ${XIMDEX_PATH}/data -o  ! -d ${XIMDEX_PATH}/modules  -o ! -d ${XIMDEX_PATH}/xmd -o ! -f ${XIMDEX_PATH}/install/install.sh ]
then
  echo "${XIMDEX_PATH} is not a Ximdex Path"
  exit 1
fi

if [ -z $USER_APACHE]
then
 USER_APACHE=$SCRIPT_USER
 if [ $INTERACTIVE = 1 ]
 then
  echo -n "Developer/apache user: [$USER_APACHE]: "
  read option;
  USER_APACHE=${option:-$USER_APACHE}
 fi
fi


if [ -z $GROUP_APACHE]
then
 GROUP_APACHE="`ps -eo '%G %a'|grep apache2|grep 'start\|bin'|grep -v grep|grep -v root|grep -v USER|awk 'NR<=1 {print $1; }'|cut -d '' -f 1,1 `"
 if [ $INTERACTIVE = 1 ]
 then
  echo -n "Apache Group: [$GROUP_APACHE]: "
  read option;
  GROUP_APACHE=${option:-$GROUP_APACHE}
 fi
  GROUP_APACHE=${GROUP_APACHE:-$USER_APACHE}
fi

if [ -n "$FILE" ]
then
   chown ${USER_APACHE}:${GROUP_APACHE} $FILE
   exit 0;
fi

# ########################### APACHE #######################################a
println "Ximdex path: ${XIMDEX_PATH}";
println "User: ${USER_APACHE}";
println "Group: ${GROUP_APACHE}";

# #######################  SET PARAMS ################################################
println "Starting to set perms to Ximdex "
setown ${USER_APACHE} ${GROUP_APACHE} ${XIMDEX_PATH} #setting user & group to ximdex


#Setting perms to ximdex directory(0750) and files(0640)
setperms '' "0750" "0640"

lista=( data logs)
#permfile=460 # r--rw---- | permdir=570 # r-xrwx---
for dir in ${lista[@]}; do
 setperms ${dir} "2770" "0660"
done

lista=(modules/dexT modules/ximSYNC/scripts)
#permfile=660 # rw-rw---- | permdir=770 # rwxrwx---
for dir in ${lista[@]}; do
 setperms ${dir} "0770" "0770"
done

#set executables perms
setexecperms

#chown to config file
chown ${USER_APACHE}:${GROUP_APACHE} ${XIMDEX_PATH}/conf -R
