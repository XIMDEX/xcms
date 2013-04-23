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
  echo -e "\nExamples:"
  echo -e "   ./ximinitialize "
  echo -e "   ./ximinitialize -r 1 -p 2 -m 0"
}


# ################################### GET PARAMS #################################
while getopts 'r:p:m:i:' OPTION;
do
  case $OPTION in
  	r) #remove data/nodes files
		REMOVE_DATANODES="$OPTARG";;

	p) #install a project demo
		PROJECT_DEMO="$OPTARG";;

	m) #install default modules
		DEFAULT_MODULES=$OPTARG;;

	i) #install in crontab
		IN_CRONTAB=$OPTARG;;

	*)	usage
		exit 1;
  esac
done

echo ""
echo ""
echo "*********************"
echo "* Ximdex initialize *"
echo "*********************"
echo ""
echo ""


#install demo
datanodes=$(ls $XIMDEX_PATH/data/nodes/)

if [ -n "$datanodes" ]
then
  if [ -z "$REMOVE_DATANODES" ];
  then
		io.question "Do you want to remove your data/nodes files? "
		REMOVE_DATANODES=$(io.getOption)
	fi

  echo ""
  if [ $REMOVE_DATANODES = "1" ]
  then
	 $(rm -rf $XIMDEX_PATH/data/nodes/*)
  fi
fi


#install demo
echo ""
if [ -z "$PROJECT_DEMO" ];
then
	io.question "Do you want to install one of our demo projects? (recommended) "
	option=$(io.getOption)
	if [ "$option" = '1' ]
	then
		bash $XIMDEX_PATH/install/module.sh install ximLOADER


		install_demo="Y"
		while [ $install_demo = "Y" ]
		do
			echo ""
			io.question "Do you want to install other of our demo projects? "
			option=$(io.getOption)
			if [ "$option" = '1' ]
			then
				bash $XIMDEX_PATH/install/module.sh install ximLOADER
			else
				install_demo="N"
			fi
		done
	fi
else
	if [ $PROJECT_DEMO -ge 1 ];
	then
		bash $XIMDEX_PATH/install/module.sh install ximLOADER
	fi
fi

if [ -z "$DEFAULT_MODULES" ];
then
	#install modules
	echo ""
	io.question "Do you want to install our recommended modules? (recommended) "
	DEFAULT_MODULES=$(io.getOption)
fi

if [ "$DEFAULT_MODULES" = '1' ];
then
  bash $XIMDEX_PATH/install/module.sh install ximNEWS
  bash $XIMDEX_PATH/install/module.sh install ximTAGS
  bash $XIMDEX_PATH/install/module.sh install ximTOUR

  SCRIPT_USER=${SCRIPT_USER:-$USER}
  instance_in_crontab=$(crontab -u $SCRIPT_USER -l|grep "$XIMDEX_PATH")

  if [ -z "$instance_in_crontab" ];
  then
 	  if [ -z "$IN_CRONTAB" ];
	  then
			io.question "Do you want to add Automatic and Scheduler to your crontab? (recommended) "
 		   IN_CRONTAB=$(io.getOption)
		fi

	  if [ "$IN_CRONTAB" = '1' ]
	  then
		scheduler="* * * * * (php $XIMDEX_PATH/modules/ximSYNC/scripts/scheduler/scheduler.php) >> $XIMDEX_PATH/logs/scheduler.log 2>&1"
		automatic="* * * * * (php $XIMDEX_PATH/modules/ximNEWS/actions/generatecolector/automatic.php) >> $XIMDEX_PATH/logs/automatic.log 2>&1"

		(crontab -u $SCRIPT_USER -l 2>/dev/null; echo -e "\n#**** Ximdex $XIMDEX_PATH ****\n$automatic \n$scheduler"; ) | crontab -u $SCRIPT_USER  -

	  fi
   fi
fi

#perms to states files
$(chmod 660 $XIMDEX_PATH/data/.[xw]* 2>/dev/null)
exit 0
