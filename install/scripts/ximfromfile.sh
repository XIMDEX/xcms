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



echo ""
echo "*** Ximdex config from file *"
echo ""



if [ -z "$1" ] || [ ! -f "$1" ]
then
	echo "file not found";
	exit 1;
fi;

# #################### LOADING VARS #######################
echo  "Getting installation params from file $1:"

#vars witch
data=$(cat $1|sed -e "s/#.*$//g"|grep  "\$"|grep -v "^#")
data=${data%#*}
data=${data//[\$\;\ \"]/}

for line in ${data}
do
	#XIMDEX_DEMOsplit
	variable=${line%=*}
	value=${line#*=}
	if [ -n "$value" ];
	then
		#only declare -x doesnt work
		echo "Read $variable --> $value"
		eval $variable=$value
		declare -x "$variable=$value"
	fi
done
