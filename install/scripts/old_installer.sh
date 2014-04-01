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
# /* Changelog
#  * ---------- Install.sh -------------
#  * - Moved and adapted from ximtest.sh the tests to check whether apache is running. 
#  *    Needed here because if apache is not running, the subsequent commands to get the apache group will fail
#  * - Changed the command to obtain the apache group with which apache is running
#  * - Changed some commands to be compatible with other unix distributions
#  *
#  * ---------- scripts/ximtest.sh -------------
#  *
#  * ---------- scripts/ximdb.sh ---------------
#  * - Changed some commands to be compatible with other unix distributions
#  * - Added server port (setPort function)
#  * - Changed usage function and case flow to fix some issues
#  * - Changed user grant sentences in checkUser function, from user @'localhost' to user@'%' because @'localhost' doesn't make sense of the mysql server is an external server and not localhost
#  *   because of if you are going to connect with the external server from a given host, the connection will fail as the user has only permissions if connects from 'localhost'
#  * - Changed checkUserError and loadData functions to be aware of the mysql port
#  * - Changed setServer function to remove the protocol and trailing slashes (if exist) from the server given by the user before make the connection test
#  *
#  * ---------- scripts/lib/functions.sh -------
#  * - Added PORT_DB var
#  * - Changed some commands to be compatible with other unix distributions
#  * - Changed getDBParams to be aware of server port
#  * - Changed sql and sql_user functions to use the --port parameter in mysql command
#  *
#  * ---------- scripts/ximparams.sh ----------
#  * - Changed some commands to be compatible with other unix distributions
#  *
#  * ---------- scripts/ximinitialize.sh ------
#  * - Changed some commands to be compatible with other unix distributions
#  *
#  * ---------- templates/install-params.conf.php -----
#  * - Added  port to conf template
#  *
#  * ---------- templates/setup.conf ------------------
#  * - Added port (3309 by default)
#  *
#  *
#  * ---------- IMPROVEMENTS TO DO --------------------
#  * - Improve the web server checking according to the operating system where the script is running
#  * - Improve the grant command according to the server which the user is going to connect from (a new option in the ximdb script? )
#  *
#  *
#  */




XIMDEX_INSTALL=1
SCRIPT_PATH=$(cd $(dirname $0) && pwd -P)
XIMDEX_PATH=${SCRIPT_PATH/\/install/}
SCRIPT_USER=`whoami`
STEP=1
export CONFIG_FILE=''
export AUTOMATIC_INSTALL=0

function usage()
{
  echo -e "Usage (how to launch): ./install.sh  [-options]"
  echo -e "\nwhere options include:"
  echo -e "    -h                   Show usage"
  echo -e "    -a 						Automatic mode (Using file: install/templates/setup.conf )"
  echo -e "    -i <file>            Ximdex file config (with absolute path)"
  echo -e "\nExamples:"
  echo -e "   ./install.sh "
  echo -e "   ./install.sh -a"
  echo -e "   ./install.sh  -i /home/ximdex/template_config.conf"

}




function waiting()
{
	echo ""
	echo ""
	echo  "Installation in course, please do not abort the process. If you abort the installation while running, it may cause problems for future attempts."
	echo -n "Are you sure you want to abort the installation? [y/n]:"
	read option
	if [ "$option" = "y" ] || [ "$option" = "Y" ]
	then
		stty echo
		#perms to /install
		exit 1;
	else
		echo ""
		STEP=`expr $STEP - 1`
	fi
}

if [ $SCRIPT_USER != 'root' ]
then
	 $(sudo -v 2>/dev/null)
	 if [ 0 = $? ]
	 then
	   exec sudo  -p "Install.sh needs root user. Root password:" $0 $*  # Call this prog as root
	 else
	   echo "Setting perms needs root user... "
	   exec su -c "$0 $*"
	 fi
 exit ${?}
else
  echo "Using root user... "
fi


# ################################### GET PARAMS #################################
while getopts 'hi:a' OPTION;
do
  case $OPTION in
	i) #file config
		CONFIG_FILE="$OPTARG"
		AUTOMATIC_INSTALL=1
		;;

	a) #automatic mode ( Using file: install/templates/setup.conf )
		CONFIG_FILE="$SCRIPT_PATH/templates/setup.conf"
		AUTOMATIC_INSTALL=1
		;;

	*)
		usage
		exit 0
		;;
  esac
done


#loading vars from config file
if [ "$AUTOMATIC_INSTALL" = 1 ] && [ -f "$CONFIG_FILE" ];
then
	. $SCRIPT_PATH/scripts/ximdex_installer_LoadAutomaticParams.sh "$CONFIG_FILE"
	result="$?"
	if [ "$result" != 0 ];
	then
		exit $result
	fi
else
	AUTOMATIC_INSTALL=0
fi



#in sudo?
if [ -n "$SUDO_USER" ]
then
 SCRIPT_USER=${SUDO_USER:-$USERNAME}
else
 SCRIPT_USER=${USERNAME:-$SUDO_USER}
fi

#Moved from ximtest.sh. Needed here because if apache is not running, the subsequent commands to obtain the apache group will fail.
# #################### Apache  #######################
#Review because in Mac the /etc/apache2 directory exists but the web server is called httpd and not apache2
operating_system=`uname`
if [ -d "/etc/apache2" ];
then
	if [ "$operating_system" = "Darwin" ] 
	then
 		web_server="httpd" #Darwin (Mac)
 	else
 		web_server="apache2" #Debian
 	fi
else
 web_server="httpd"   #RedHat
fi

echo -n "Apache running: "
apache_cmd=$(which $web_server)
running=$(ps aux|grep $apache_cmd|grep -v grep)
if [ $? -ne 0 ] || [ -z "$running" ];
then
   echo " Fail! You should start your Apache server in order to continue with the installation process."
	exit 1;
else
	echo " OK "
fi
#End moved

USER_APACHE=$SCRIPT_USER
GROUP_APACHE="`ps -eo 'group args'|grep 'httpd\|apache' |grep 'start\|bin'|grep -v grep|grep -v root|grep -v USER|awk 'NR<=1 {print $1; }'|cut -d ' ' -f 1,1 `"
GROUP_APACHE=${GROUP_APACHE:-$USER_APACHE}
$(chown -R ${USER_APACHE}:${GROUP_APACHE} ${XIMDEX_PATH} 2>/dev/null) #setting user & group to ximdex /dev/null to avoid problems with .svn from other machines
$(chmod -R u+x $SCRIPT_PATH/*sh)


# trap keyboard interrupt (control-c)
trap waiting SIGINT
trap waiting SIGKILL

$((rm -f ${SCRIPT_PATH}/install.log && echo "---- Installing Ximdex in `date +%F-%k:%m:%S` ----" >  ${SCRIPT_PATH}/install.log ) )

while [ $STEP != "END" ]
do
	case $STEP in
		#lauch ximtest
		1) $(chmod +x $SCRIPT_PATH/scripts/ximdex_installer_CheckDependencies.sh)
		( $SCRIPT_PATH/scripts/ximdex_installer_CheckDependencies.sh )
		result="$?"
		if [ "$result" != 0 ];
		then
		  exit $result
		fi
		;;
		2)
		#lauch ximdb
		$(chmod +x $SCRIPT_PATH/scripts/ximdex_installer_CreateDatabase.sh)
		( $SCRIPT_PATH/scripts/ximdex_installer_CreateDatabase.sh -i )

		result="$?"
		if [ "$result" != 0 ];
		then
		exit $result
		fi
		;;
		3)
		#launch ximparams
		$(chmod +x $SCRIPT_PATH/scripts/ximdex_installer_Configurator.sh)
		( $SCRIPT_PATH/scripts/ximdex_installer_Configurator.sh -i -n )
		result="$?"
		if [ "$result" != 0 ];
		then
		  exit $result
		fi
		;;

		4)
		#maintenance_tasks
		$(chmod +x $SCRIPT_PATH/scripts/ximdex_installer_MaintenanceTasks.sh)
		( $SCRIPT_PATH/scripts/ximdex_installer_MaintenanceTasks.sh )
		result="$?"
		if [ "$result" != 0 ];
		then
		  exit $result
		fi
		;;

		5)
		  #launch ximinitialize
		  $(chmod +x $SCRIPT_PATH/scripts/ximdex_installer_InitializeInstance.sh)
		  ( $SCRIPT_PATH/scripts/ximdex_installer_InitializeInstance.sh )
		;;


		6)
		#waiting for settings perms
		echo -n "Setting permissions as ${USER_APACHE}:${GROUP_APACHE} in directory ${XIMDEX_PATH}... "
		$(chown -R ${USER_APACHE}:${GROUP_APACHE} ${XIMDEX_PATH})
		$(chmod -R 2770 ${XIMDEX_PATH}/data)
		$(chmod -R 2770 ${XIMDEX_PATH}/logs)
		echo "OK";;

		7)
		#removing config file
		if [ -n "$CONFIG_FILE" ];
		then
			rm $CONFIG_FILE
		fi

		echo ""
		echo ""
		echo "Installation completed successfully. Thanks for installing Ximdex. "
		exit 0;;

		*)
			exit 1;;
	 esac

	STEP=`expr $STEP + 1`

done

