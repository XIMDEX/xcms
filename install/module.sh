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
MODULES_PHP_SCRIPT=$SCRIPT_PATH"/scripts/lib/modules.php"
PHP_CMD=`which php`
ADD_MEMORY="-d memory_limit=-1"
MODULE=${2}
OP=${1}



function usage
{
  echo ""
  echo -e "Usage: module.sh [install|uninstall|reinstall|list] [module_name]"
  echo -e "\nExamples:"
  echo -e "   module.sh install ximIO"
  echo -e "   module.sh uninstall ximSYNC"
}

if [ $# != 1 ] && [ $# != 2 ]
then
	usage
	exit 0
fi

if [ $# = 1 ] && [ $OP != "list" ]
then
	usage
	exit 0
fi

function install() {

 if [ $MODULE != "ximLOADER" ] && [ $MODULE != "ximLOADERDEVEL" ]
 then
	$PHP_CMD $ADD_MEMORY $MODULES_PHP_SCRIPT install $MODULE
   $PHP_CMD $ADD_MEMORY  $MODULES_PHP_SCRIPT enable $MODULE
 else
	$PHP_CMD $ADD_MEMORY $MODULES_PHP_SCRIPT install $MODULE $PROJECT_DEMO
 fi

}

function uninstall() {

 if [ $2 != "ximLOADER" ] && [ $2 != "ximLOADERDEVEL" ]
 then
	 $PHP_CMD $ADD_MEMORY  $MODULES_PHP_SCRIPT  disable $MODULE
 fi
    $PHP_CMD $ADD_MEMORY  $MODULES_PHP_SCRIPT  uninstall $MODULE

}


 case $OP in
   install)
   		install $1 $2;;
   uninstall)
   		uninstall $1 $2;;

   reinstall)
		uninstall $1 $2
   		install $1 $2
		;;
   list)
		   modules=(`$PHP_CMD $ADD_MEMORY $SCRIPT_PATH/scripts/getAvaliableModules.php -l`)
		   for mod in ${modules[@]}
		   do
		   	echo "$mod"
		   done;;
   *)
   	usage;;
 esac

