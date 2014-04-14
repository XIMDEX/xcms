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


SCRIPT_PATH=$(cd $(dirname $0) && pwd -P)

. $SCRIPT_PATH/lib/functions.sh

REMOVE_DATANODES=${REMOVE_DATANODES:-''}
PROJECT_DEMO=${PROJECT_DEMO:-''}
DEFAULT_MODULES=${DEFAULT_MODULES:-''}
IN_CRONTAB=${IN_CRONTAB:-''}

# ##################################################### FUNCTIONS ##########################################
function usage
{
  echo -e "Usage: ximinitialize [-options]"
  echo -e "\nwhere options include:"
  echo -e "    -r 1|0               Remove previous data/nodes files"
  echo -e "    -p 0-3	            Project demo(0=>none, 1=>AddressBook, 2=>Picasso, 3=>The Hobbit"
  echo -e "    -m 1|0	            Install default modules"
  echo -e "    -i 1|0					Install automatic & scheduler into crontab file"
  echo -e "    -x <path>	Install Path"
  echo -e "\nExamples:"
  echo -e "   ./ximinitialize "
  echo -e "   ./ximinitialize -r 1 -p 2 -m 0"
}


# ################################### GET PARAMS #################################
while getopts 'r:p:m:i:x:c:' OPTION;
do
  case $OPTION in
  	r) #remove data/nodes files
		REMOVE_DATANODES="$OPTARG";;

	p) #install a project demo
		PROJECT_DEMO="$OPTARG";;

	m) #install default modules
		DEFAULT_MODULES=$OPTARG;;

	c) #Generate crontab file
		CRONTAB_FILE=$OPTARG;;

	i) #install in crontab
		IN_CRONTAB=$OPTARG;;

	x) #install final path
		XIMDEX_PARAMS_PATH=$OPTARG;;

	*)	usage
		exit 1;
  esac
done

echo ""
echo "* Ximdex initialize *"
echo ""

echo "Using $XIMDEX_PATH as working directory"


if [ -z "$DEFAULT_MODULES" ];
then
	#install modules
	echo ""
	io.question "Install our recommended modules xnews, xtags and xtour? (recommended) "
	DEFAULT_MODULES=$(io.getOption)
fi

if [ "$DEFAULT_MODULES" = '1' ];
then
  #install recommended modules  
  ($PHP_CMD $ADD_MEMORY $SCRIPT_PATH/lib/modules.php install -r  2>>$LOG)  
fi

if [ -z $XIMDEX_PARAMS_PATH ]; then
    XIMDEX_PARAMS_PATH=$XIMDEX_PATH
fi

echo "MODULES-INSTALLED" > $XIMDEX_PATH/install/_STATUSFILE

SCRIPT_USER=${SCRIPT_USER:-$USER}
instance_in_crontab=$(crontab -u $SCRIPT_USER -l|grep "$XIMDEX_PATH")

if [ -z "$instance_in_crontab" ]; then
    if [ -z "$IN_CRONTAB" ]; then
        echo "If you want to publish into remote servers in the cloud, ..."
        echo "Ximdex decoupled publishing system has to be periodically launched ..."
        io.question "Do you want to add Scheduler tasks to your user ($SCRIPT_USER) crontab? "
        IN_CRONTAB=$(io.getOption)
    fi

    scheduler="* * * * * (php $XIMDEX_PARAMS_PATH/modules/ximSYNC/scripts/scheduler/scheduler.php) >> $XIMDEX_PARAMS_PATH/logs/scheduler.log 2>&1"
    automatic="* * * * * (php $XIMDEX_PARAMS_PATH/modules/ximNEWS/actions/generatecolector/automatic.php) >> $XIMDEX_PARAMS_PATH/logs/automatic.log 2>&1"

    if [ "$IN_CRONTAB" = '1' ]; then
        echo "Adding scheduler y automatic scripts at $XIMDEX_PARAMS_PATH into crontab for $SCRIPT_USER user"
        echo ""
        (crontab -u $SCRIPT_USER -l 2>/dev/null; echo -e "\n#**** Ximdex $XIMDEX_PARAMS_PATH ****\n$automatic \n$scheduler"; ) | crontab -u $SCRIPT_USER  -
    else 
	if [ -z $CRONTAB_FILE ]; then
	        echo "Please, add the following lines to your crontab:"
       		echo -e "\n#**** Ximdex $XIMDEX_PARAMS_PATH ****\n$automatic \n$scheduler\n"	
	else
       		echo -e "\n#**** Ximdex $XIMDEX_PARAMS_PATH ****\n$automatic \n$scheduler\n" > $CRONTAB_FILE	
	fi
    fi
else
    echo "It seems the instance has already scripts declared in your crontab"
    echo ""
fi

#perms to states files
$(chmod 660 $XIMDEX_PATH/data/.[xw]* 2>/dev/null)
exit 0
