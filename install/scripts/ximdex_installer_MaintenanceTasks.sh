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

ADD_MEMORY="-d memory_limit=-1"

. $SCRIPT_PATH/lib/functions.sh



echo ""
echo "*** Ximdex component configuration"
echo ""

while getopts 'x:' OPTION;
do
  case $OPTION in
	x) #target path
		XIMDEX_TARGET=$OPTARG;;
  esac
done

echo -n "Generating dynamic tables"
UPDATE_FT="$SCRIPT_PATH/lib/update_ft.php"

#$(chmod 775 $UPDATE_FT)
$(chmod +x $UPDATE_FT)
($PHP_CMD $ADD_MEMORY $UPDATE_FT  2>>$LOG)
ret_ft=$?

if [ $ret_ft != "0" ]; then
	echo " Fail"
	exit $ret_ft
else
	echo " Success"
fi

#TODO Asegurar que no hay rutas reales

echo -n "Updating node path info"
UPDATE_NP="$SCRIPT_PATH/lib/update_np.php"
$(chmod +x $UPDATE_NP)
($PHP_CMD $ADD_MEMORY $UPDATE_NP  2>>$LOG)
ret_np=$?

if [ $ret_np != "0" ]; then
	echo " Fail"
	exit $ret_np
else
	echo " Success"
fi

echo -n "Creating initial XSLT template... "
cd "$XIMDEX_PATH/install"
PHING_CMD="$XIMDEX_PATH/extensions/phing/bin/phing.php"
PHING_BUILD="$XIMDEX_PATH/build.xml"

# we expect XIMDEX_TARGET set previously with option -x
if [ -z $XIMDEX_TARGET ]; then
	XSLT_TARGET='${absolute_path_to_xslt}'
else
	XSLT_TARGET="$XIMDEX_TARGET/modules/dexT/xslt/"
#        while [[ $XSLT_TARGET =~ "//" ]]; do
#            XSLT_TARGET=$(echo $XSLT_TARGET | sed -e "s/\/\//\//g")
#        done
fi

if [ -f "$PHING_BUILD.base" ]; then 
	sed "s,##XIMDEX_TARGET##,$XSLT_TARGET," "$PHING_BUILD.base" > $PHING_BUILD
fi

$(chmod +x $PHING_CMD)
($PHP_CMD $ADD_MEMORY $PHING_CMD "-f" $PHING_BUILD >>$LOG )
ret_phing=$?

if [ $ret_ft != "0" ]; then
    echo "Fail"
	 exit $ret_phing
else
        echo "Success"
fi

echo -n "Generating configuration for modules as conf/install-modules.conf... "
$(chmod +x $SCRIPT_PATH/getAvailableModules.php)
($PHP_CMD $ADD_MEMORY $SCRIPT_PATH/getAvailableModules.php  2>>$LOG)

if [ -f $XIMDEX_PATH/conf/install-modules.conf ];
then
	$(chmod 775 $XIMDEX_PATH/conf/install-modules.conf)
	echo "Success"
else
	echo "Fail"
	exit 1
fi

#delete all states files
#moved to ximdb script
#$(rm -f $XIMDEX_PATH/data/.* 2>/dev/null)


#install cores modules
echo "Installing modules IO for the API..."
echo ""
bash $XIMDEX_PATH/install/module.sh install ximIO
echo "Installing modules XSYNC for dynamic publishing into the cloud... "
echo ""
bash $XIMDEX_PATH/install/module.sh install ximSYNC
