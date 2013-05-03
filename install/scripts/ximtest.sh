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


echo ""
echo ""
echo "***************************"
echo "* Ximdex dependency check *"
echo "***************************"
echo ""
echo ""


# #################### DISKSPACE #######################
echo -n "Disk space (more than 1G):"
space=$(df -aP  $XIMDEX_PATH|grep "/"  | awk '{ print $4 }')
space_human=$(df -aHP  $XIMDEX_PATH|grep "/"  | awk '{ print $4 }')
if [ "$space" -lt 1024 ];
then
  echo " Fail (available space $space_human)";
  exit 1;
else
  echo " OK (available space $space_human)"
fi


# #################### PHP VERSION #######################
echo -n "Php version (older than 5.2.5):"
php_cmd=$(which php)
error="0"
if [ -n "$php_cmd" ]
then
  version=$(php -r "echo (int) version_compare(PHP_VERSION, '5.2.5', '>='); ")


   if [ "$version" != "1" ]
   then
		error="1"
   fi
else
  error="1"
  version="0"
fi

version=$(php -r "echo phpversion(); "|cut -d "-" -f 1,1)
if [ "$error" = "1" ];
then
   echo " Fail (current version $version)"
   exit 1;
else
  echo " OK (current version $version)"
fi

# pear installed?

echo -n " - Php Pear installed: "
pear=$(which pear)
if [ -n "$pear" ];
then
  version=$(pear version|grep -i "pear"|cut -d ":" -f 2,2|sed "s/\ //g")
  echo "OK (current version $version)"
else
  echo "Fail (current version $version)"
  exit 1
fi

# gd installed?
echo -n " - Php GD installed: "
pear=$(php -r "if (extension_loaded('gd') && function_exists('gd_info')) { echo '1'; }")
if [ -n "$pear"   ];
then
  version=$(php -r '$gd=gd_info(); echo $gd["GD Version"];')
  echo "OK (current version $version)"
else
  echo "Fail (current version $version)"
  exit 1
fi

# php-xsl installed?

echo -n " - PHP XSL installed: "
xsltest=$(php -r "if (class_exists('XSLTProcessor')) { echo '1'; }")
if [ -n "$xsltest" ];
then
  echo "OK"
else
  echo "Fail"
  exit 1
fi
    

# #################### MYSQL  #######################
echo -n "MySQL version (older than 5.0):"
mysql_cmd=$(which mysql)
error="0"
if [ -n "$mysql_cmd" ]
then
  version=$($mysql_cmd -V|awk '{ print $5 }'|cut -d "." -f 1,2|sed "s/\.//g")

   if [ "$version" -lt "50" ]
   then
		error="1"
   fi
else
  error="1"
  version="0"
fi

version=$(mysql -V|awk '{ print $5 }'|sed "s/\,//g")
if [ "$error" = "1" ];
then
   echo " Fail (current version $version)"
   exit 1;
else
  echo " OK (current version $version)"
fi

echo -n " - MySQL running: "
running=$(ps aux|grep '$mysql_cmd')
if [ -z "$running" ];
then
   echo " Fail (start you mysql server, please)"
	exit 1;
else
	echo " OK "
fi

