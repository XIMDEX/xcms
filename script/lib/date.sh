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


if [ "$LIB_DATE" = "1" ];
then
exit 0;
fi

LIB_DATE=1
DATE=$(which date)
declare -a DAYS
DAYS=("MONDAY", "TUESDAY", "WEDNESDAY","THURSDAY","FRIDAY","SATURDAY","SUNDAY")
MONDAY="1"
TUESDAY="2"
WEDNESDAY="3"
THURSDAY="4"
FRIDAY="5"
SATURDAY="6"
SUNDAY="7"

function date.get()
{
  echo $($DATE -I)
}
function date.timeStamp()
{
  echo $($DATE +%N)
}
function date.day()
{
  echo $($DATE +%d)
}
function date.today()
{
  echo $(date.day|sed -e "s/0//g")
}
function date.yesterday()
{
  DAY_TODAY=$(date.today)
  if [ $DAY_TODAY = 1 ];
  then
	 echo 7
  else
	 echo $(expr "$DAY_TODAY - 1")
  fi
}
function date.month()
{
  echo $($DATE +%m)
}
function date.year()
{
  echo $($DATE +%Y)
}
function date.dayOfWeek()
{
  echo $($DATE +%u)
}