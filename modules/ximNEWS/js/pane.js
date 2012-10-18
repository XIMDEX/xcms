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


var textPadding = 3; 
var strictDocType = false;	
var tabObj;
var activeTabIndex = -1;
var MSIE = navigator.userAgent.indexOf('MSIE')>=0?true:false;
var navigatorVersion = navigator.appVersion.replace(/.*?MSIE (\d\.\d).*/g,'$1')/1;
var activarTab = new Array();

function setPadding(obj,padding){
	var span = obj.getElementsByTagName('SPAN')[0];
	span.style.paddingLeft = padding + 'px';	
	span.style.paddingRight = padding + 'px';	
}
function  hideActualTab()
{	
	var obj = document.getElementById('tabTab'+activeTabIndex);
	obj.className='tabInactive';
	document.getElementById('tabView' + activeTabIndex).style.display='none';
}
function showTab(tabIndex)
{
     	//If we want to activated an already activated one
	if(activeTabIndex==tabIndex)return;	
	
	//If there is a language form previously activated, we disactivate it
	if(activeTabIndex>=0){
       	   hideActualTab();
	}   
		
	//Activating the selected language tab
	var thisObj = document.getElementById('tabTab'+tabIndex);		
	thisObj.className='tabActive';
	document.getElementById('tabView' + tabIndex).style.display='block';
	activeTabIndex = tabIndex;
}
function tabClick()
{
	showTab(this.id.replace(/[^\d]/g,''));
}
function rolloverTab()
{
	if(this.className.indexOf('tabInactive')>=0){
	   this.className='inactiveTabOver';
	}
}
function rolloutTab()
{
	if(this.className ==  'inactiveTabOver'){
	   this.className='tabInactive';
	}
}

//Tabs for the news separation by languages
function initTabs(activeTab,width,height)
{
	tabObj = document.getElementById('ximnews_TabView');		
        if(tabObj){
		var tabDiv = document.createElement('DIV');
		var firstDiv = tabObj.getElementsByTagName('DIV')[0];
		tabObj.insertBefore(tabDiv,firstDiv);
		
		tabDiv.className = 'ximnews_tabPane';
	
        	//Creating a control layer for flaps
		tablas = tabObj.getElementsByTagName("table");
		for(var t=0; t < tablas.length; t++){
             		if(tablas[t].className == "tabla"){
                		var aTab = createTab(t,tablas[t].getAttribute("name"));
	        		tabDiv.appendChild(aTab);
             		}
		}
		width = width + '';
		if(width.indexOf('%')<0)width= width + 'px';
		//tabObj.style.width = width;
		height = height + '';
		if(height.length>0){
	   		if(height.indexOf('%')<0)height= height + 'px';
	   		//tabObj.style.height = height;
		}
 		var tabs = tabObj.getElementsByTagName('DIV');
		var divCounter = 0;
		for(var no=0;no<tabs.length;no++){
	     		if(tabs[no].className=='ximnews_aTab'){
				//if(height.length>0)tabs[no].style.height = height;
				tabs[no].style.display='none';
				tabs[no].id = 'tabView' + divCounter;
				divCounter++;			
	     		}
        	} 
		activeTabIndex = -1;
		//alert(tabObj.innerHTML); 
		showTab(activeTab);
       }
}
function createTab(no,tabTitle)
{
	activarTab[tabTitle] = no;
	var aTab = document.createElement('DIV');
	aTab.id = 'tabTab' + no;
	aTab.onmouseover = rolloverTab;
	aTab.onmouseout = rolloutTab;
	aTab.onclick = tabClick;
	aTab.className='tabInactive';
	var span = document.createElement('SPAN');
	span.innerHTML = tabTitle;
	aTab.appendChild(span);
        return aTab;
}
