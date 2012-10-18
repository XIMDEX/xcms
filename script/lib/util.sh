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

if [ "$LIB_UTIL" = "1" ];
then
exit 0;
fi

LIB_UTIL=1
GZIP=$(which gzip)
MKDIR=$(which mkdir)
FIND=$(which find)


function util.upper()
{
   string=$1
	echo $string | tr '[a-z]' '[A-Z]'
}

function util.lower()
{
   string=$1
	echo $string | tr  '[A-Z]' '[a-z]'
}


function  util.filename()
{
  file=$1

  echo ${file%.*}
}

function util.fileext()
{
  file=$1

  echo ${file/*./}
}

function util.gzip()
{
  file=$1
  $($GZIP -f $file)
}

#execute script as root
function util.withRoot
{

	if [ $SCRIPT_USER != 'root' ]
	then
		 $(sudo -v $SCRIPT $SCRIPT_VARS 2>/dev/null)
		 if [ 0 = $? ]
		 then
		   exec sudo  -p "Setting perms needs root user.Root password:" $SCRIPT $SCRIPT_VARS  # Call this prog as root
		 else
		   echo "Setting perms needs root user"
		   exec su -c "$SCRIPT $SCRIPT_VARS"
		 fi
	 exit ${?}
	else
	  io.println "Using root user..."
	fi


	#¿in sudo?
	if [ -n "$SUDO_USER" ]
	then
	 SCRIPT_USER=${SUDO_USER:-$USERNAME}
   else
	 SCRIPT_USER=${USERNAME:-$SUDO_USER}
	fi

}
