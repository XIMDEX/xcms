<?php
/**
 *  \details &copy; 2011  Open Ximdex Evolution SL [http://www.ximdex.org]
 *
 *  Ximdex a Semantic Content Management System (CMS)
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as published
 *  by the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  See the Affero GNU General Public License for more details.
 *  You should have received a copy of the Affero GNU General Public License
 *  version 3 along with Ximdex (see LICENSE file).
 *
 *  If not, visit http://gnu.org/licenses/agpl-3.0.html.
 *
 *  @author Ximdex DevTeam <dev@ximdex.com>
 *  @version $Revision$
 */

?>            <SCRIPT TYPE="text/javascript">
function loadPrev()
	{
	document.all["previewer"].src ="http://www.google.com";
	document.all["preview"].style.left = tempX;
	document.all["preview"].style.top = tempY;
	}
	
function isArray (array)
	{
	return ((array != null) && (typeof array == "object") && (array.constructor == Array));
	}
  		
function getById(element)
	{
	if (document.all)
		return document.all[element];
	else
		return document.getElementById(element);
	}

var is_ie = document.all?true:false;
if (!is_ie)
	document.captureEvents(Event.MOUSEMOVE)
document.onmousemove = getPosition;
var tempX = 0;
var tempY = 0;

function getPosition(e)
	{
	if (IE)
		{ 
		tempX = event.clientX + document.body.scrollLeft;
		tempY = event.clientY + document.body.scrollTop;
		}
	else
		{  
		tempX = e.pageX;
		tempY = e.pageY;
		}  

	if (tempX < 0)
		tempX = 0;

	if (tempY < 0)
		tempY = 0;

	return true;
	}
	
</script>

<style type="text/css">
#previewer
	{
	position: absolute;
	top: 30px;
	left: 85%;
	font: 12px verdana,arial,helvetica;
	color: #ffffff;
	background: #000000;
	padding: 10px;
	border: none;
	z-index: 5;
	}
</style>

<DIV ID="preview" STYLE="border:1px solid; display: block; position: absolute; left: 300; top: 300; z-index: 0; background-color:#ffffff; width:250; border: ;">
		<table width="100%" style="border:1px solid; font-size: xx-small; font-family: arial;">
			<tr><td> vista previa</td></tr>
		</table>
		<IFRAME SCROLLING=yes FRAMEBORDER=0 ID="previewer" name="previewer" STYLE="position: absolute; left: 0; top: 25; z-index: 0; border:0px;width: 275; height:200"></IFRAME>

</DIV>
