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

if [ "$LIB_IO" = "1" ];
then
exit 0;
fi

LIB_IO=1
VERBOSE=0
INTERACTIVE=0
AUTOMATIC=0
PHP_CMD=`which php`
SCRIPT_PATH=''
XIMDEX_PATH=''
INSTALL_PARAMS=''
IORESULT=''
IO_OPTION=''

function io.init()
{
   SCRIPT_PATH=$(cd ${SCRIPT%/*} && pwd -P)
	REL_PATH=${REL_PATH//[\\/\;\\/\/\"]/}
   XIMDEX_PATH=${SCRIPT_PATH/$REL_PATH/}
   INSTALL_PARAMS="${XIMDEX_PATH}/conf/install-params.conf.php"
}

function io.initVarFromFile()
{
	data=$(cat $1|grep  "\$$2")
	data=${data//[\$\;\ \"]/}
	#io.println "Load params"

	for line in ${data}
	do
		#split
	   variable=${line%=*}
		value=${line#*=}
		#only declare -x doesnt work
		eval $variable=$value
		declare -x "$variable=$value"
	done
}


#TODO: INCLUDE_ONCE
function io.include()
{
  file=$1

  . ${XIMDEX_PATH}${file}
}

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


function io.println()
{
 if [ $VERBOSE == 1 ]
 then
  echo -e "$1"
 fi
}

function io.next_step()
{
 STEP=`expr $STEP + 1`
}

function io.prev_step()
{
 STEP=`expr $STEP - 1`
}

#replace text in file
function io.replace
{
  element=$1
  value=${2//\//\\\/}
  value=${value//\./\\\./}
  file=$3

 $(sudo sed -i "s/${element}/${value}/g" $file)

  io.println "sed -i s/${element}/${value}/g   $file"
}



#execute code php for ximdex
function io.php()
{
  code=$1

  IORESULT=$(${PHP_CMD} -d memory_limit=-1 -r "include_once('${XIMDEX_PATH}/inc/utils.inc'); ${code}");
}

#log to xmd_log
function io.log()
{
  msg=$1
  #types=debug, info,warning, error, fatal, display | default: debug
  type=${2:-debug}

  io.php " XMD_LOG::$type('$msg'); "
}


io.init