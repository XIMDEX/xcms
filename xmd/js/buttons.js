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


document.onmouseover=doOver;
document.onmouseout=doOut;
document.onmousedown=doDown;
document.onmouseup=doUp;
function doOver(){
{
	if(window.event) {
		var toEl=getReal(window.event.toElement,"className","coolButton");
		var fromEl=getReal(window.event.fromElement,"className","coolButton");
	}else {
		var toEl=getReal(null,"className","coolButton");
		var fromEl=getReal(null,"className","coolButton");
	}

	if(toEl==fromEl)return;
	var el=toEl;
	var cDisabled=el.cDisabled;cDisabled=(cDisabled!=null);
	if(el.className=="coolButton")
		el.onselectstart=new Function("return false");
	if((el.className=="coolButton")&&!cDisabled)
			{
			makeRaised(el);
			makeGray(el,false);
			}
	}
function doOut()
{
	if(window.event) {
		var toEl=getReal(window.event.toElement,"className","coolButton");
		var fromEl=getReal(window.event.fromElement,"className","coolButton");
	}else {
		var toEl=getReal(null,"className","coolButton");
		var fromEl=getReal(null,"className","coolButton");
	}
	if(toEl==fromEl)return;
	var el=fromEl;
	var cDisabled=el.cDisabled;
	cDisabled=(cDisabled!=null);
	var cToggle=el.cToggle;
	toggle_disabled=(cToggle!=null);
	if(cToggle&&el.value)
		{
		makePressed(el);
		makeGray(el,true);
		}
	else if((el.className=="coolButton")&&!cDisabled)
		{
		makeFlat(el);
		makeGray(el,true);
		}
	}
function doDown()
	{
	el=getReal(window.event.srcElement,"className","coolButton");
	var cDisabled=el.cDisabled;
	cDisabled=(cDisabled!=null);
	if((el.className=="coolButton")&&!cDisabled){
		makePressed(el)
		}
	}
function doUp()
	{
	el=getReal(window.event.srcElement,"className","coolButton");
	var cDisabled=el.cDisabled;cDisabled=(cDisabled!=null);
	if((el.className=="coolButton")&&!cDisabled)
		{
		makeRaised(el);
		}
	}
function getReal(el,type,value)
	{
	temp=el;
	while((temp!=null)&&(temp.tagName!="BODY"))
		{
		if(eval("temp."+type)==value)
			{
			el=temp;
			return el;
			}
		temp=temp.parentElement;
		}
	return el;
	}
function findChildren(el,type,value)
	{
	var children=el.children;
	var tmp=new Array();
	var j=0;
	for(var i=0;i<children.length;i++)
		{
		if(eval("children[i]."+type+"==\""+value+"\""))
			{
			tmp[tmp.length]=children[i];
			}
		tmp=tmp.concat(findChildren(children[i],type,value));
		}
	return tmp;
	}
function disable(el)
	{
	if(document.readyState!="complete")
		{
		window.setTimeout("disable("+el.id+")",100);
		return;
		}
	var cDisabled=el.cDisabled;
	cDisabled=(cDisabled!=null);
	if(!cDisabled){
		el.cDisabled=true;
		if(document.getElementsByTagName)
			{
			el.innerHTML="<span style='background: buttonshadow; height: 100%;'>"+"<span style=' height: 100%;'>"+el.innerHTML+"</span>"+"</span>";}
			else
				{
				el.innerHTML='<span style="background: buttonshadow; width: 100%; height: 100%; text-align: center;">'+'<span style=" height: 100%; width: 100%%; text-align: center;">'+el.innerHTML+'</span>'+'</span>';}
				if(el.onclick!=null)
					{
					el.cDisabled_onclick=el.onclick;el.onclick=null;
					}
				}
			}

function enable(el)
	{
	var cDisabled=el.cDisabled;
	cDisabled=(cDisabled!=null);
	if(cDisabled)
		{
		el.cDisabled=null;
		el.innerHTML=el.children[0].children[0].innerHTML;
		if(el.cDisabled_onclick!=null)
			{
			el.onclick=el.cDisabled_onclick;
			el.cDisabled_onclick=null;
			}
		}
	}

function addToggle(el)
	{
	var cDisabled=el.cDisabled;
	cDisabled=(cDisabled!=null);
	var cToggle=el.cToggle;
	cToggle=(cToggle!=null);
	if(!cToggle&&!cDisabled)
		{
		el.cToggle=true;
		if(el.value==null)

		el.value=0;
	if(el.onclick!=null)
		el.cToggle_onclick=el.onclick;
		else el.cToggle_onclick="";
		el.onclick=new Function("toggle(document.getElementById('"+el.id+"')); document.getElementById('"+el.id+"').cToggle_onclick();");
		}
	}
	
function removeToggle(el)
	{
	var cDisabled=el.cDisabled;
	cDisabled=(cDisabled!=null);
	var cToggle=el.cToggle;
	cToggle=(cToggle!=null);
	if(cToggle&&!cDisabled){
		el.cToggle=null;
		if(el.value){
			toggle(el);
		}
	makeFlat(el);
	if(el.cToggle_onclick!=null){
		el.onclick=el.cToggle_onclick;
		el.cToggle_onclick=null;
		}
	}
}
function toggle(el)
	{
	el.value=!el.value;
	if(el.value)
		el.style.background="URL(./images/tileback.gif)";
	else
		el.style.backgroundImage="";
	}

function makeFlat(el)
	{
	with(el.style){
		background="";
		border="1px solid buttonface";
		padding="1px";
		}
	}

function makeRaised(el)
	{
	with(el.style)
		{
		borderLeft="1px solid buttonhighlight";
		borderRight="1px solid buttonshadow";
		borderTop="1px solid buttonhighlight";
		borderBottom="1px solid buttonshadow";
		padding="1px";
		}
	}
function makePressed(el)
	{
	with(el.style)
		{
		borderLeft="1px solid buttonshadow";
		borderRight="1px solid buttonhighlight";
		borderTop="1px solid buttonshadow";
		borderBottom="1px solid buttonhighlight";
		paddingTop="2px";
		paddingLeft="2px";
		paddingBottom="0px";
		paddingRight="0px";
		}
	}
function makeGray(el,b)
	{
	var filtval;
	if(b) 
		filtval="gray()";
	else
		filtval="";
	var imgs=findChildren(el,"tagName","IMG");
	for(var i=0;i<imgs.length;i++)
		{
		//imgs[i].style.filter=filtval;
		}
	}
	document.write("<style>");
	document.write(".coolBar	{background: buttonface; padding: 1px; font: menu;}");
	document.write(".coolButton {border: 1px solid buttonface; padding: 1px; text-align: center; cursor: default;}");
	document.write(".coolButton IMG	{ }");
	document.write("</style>");
		