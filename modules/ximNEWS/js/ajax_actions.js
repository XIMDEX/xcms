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

function GetElementsByAttribute(base,tag,attr,value)
{
  	var a, list;
	var found = new Array();
  	list = base.getElementsByTagName(tag);
	
  	for (var i = 0; i < list.length; ++i) { 
	      if(attr == "class"){
                 a = list[i].className;
	      }
              else{	
                 a = list[i].getAttribute(attr);
              }
              if (a == value) {
                  found.push(list[i]);
	      }
        }
        return found;
}
function checkAll(obj){
	var tabla = obj.parentNode.parentNode.parentNode.parentNode;
        var chks = GetElementsByAttribute(tabla,"input","type","checkbox");
	if(obj.checked){
		for (i = 0; i < chks.length; i++)
			chks[i].checked = true ;
	}
	else{
		for (i = 0; i < chks.length; i++)
			chks[i].checked = false ;
	}
}
function read_news(){
	var tabla = document.getElementById("grid");
	var chks = GetElementsByAttribute(tabla,"input","type","checkbox");
	var news = "";
	
	for (i = 0; i < chks.length; i++){
		if(chks[i].checked == true) {
			if(news != ""){
				news += "/";
			}
			news += chks[i].value;
		}
	}
	return news;
	
}
function delete_news(){
	var news = read_news();
	var url = "../../inc/Browser_Adapter.php";
	var cacheP =  '&' + Math.random();
	var pars =  "&tipo=deletenews&newsList="+news+cacheP;
	var ajax = new Ajax.Request( url, {parameters: pars,method:"get",onComplete: presentar_resultado2}); 
}
function link_news_bulletin(obj){
	if(obj.getAttribute("estado") == 0){
		var news = read_news();
		var aux = news.split("/");
		nodeID = aux[0];
		setCookie("news", news);
		var url = "../../inc/Browser_Adapter.php";
		var cacheP =  '&' + Math.random();
		var pars =  "&flag=1&tipo=colectores&nodeid="+nodeID+cacheP;
		var ajax = new Ajax.Request( url, {parameters: pars,method:"get",onComplete: presentar_colectores2}); 
		obj.setAttribute("estado",1);
	}
	else if(obj.getAttribute("estado") == 1){
		var area = document.getElementById("areaNoticia");
		var chks = GetElementsByAttribute(area,"input","type","checkbox");
		var colectors = "";
		var nodeID;
		for (i = 0; i < chks.length; i++){
			if(chks[i].checked == true) {
				if(colectors != ""){
					colectors += "/";
				}
				colectors += chks[i].value;
			}
		}
		news = getCookie("news");
		var url = "../../inc/Actions_Adapter.php";
		var cacheP =  '&' + Math.random();
		var pars =  "&tipo=addnewscolector&colectorList="+colectors+"&newsList="+news+cacheP;
		var ajax = new Ajax.Request( url, {parameters: pars,method:"get",onComplete: presentar_resultado2}); 
		obj.setAttribute("estado",0);
	}
}
function presentar_colectores2(resp){
      
	var area = document.getElementById("areaNoticia");
	if(area.firstChild){
	   area.removeChild(area.firstChild);
	}
	var respuesta = resp.responseText;
	area.innerHTML = respuesta;
        area.style.visibility = "visible";
	
}
function presentar_resultado2(resp){
	var area = document.getElementById("resultado");
	if(area.firstChild){
	   area.removeChild(area.firstChild);
	}
	var respuesta = resp.responseText;
	area.innerHTML = respuesta;
        area.style.visibility = "visible";
}
function setCookie(name, value, expires, path, domain, secure) {
    document.cookie= name + "=" + escape(value) +
        ((expires) ? "; expires=" + expires.toGMTString() : "") +
        ((path) ? "; path=" + path : "") +
        ((domain) ? "; domain=" + domain : "") +
        ((secure) ? "; secure" : "");
}
function getCookie(name) {
    var dc = document.cookie;
    var prefix = name + "=";
    var begin = dc.indexOf("; " + prefix);
    if (begin == -1) {
        begin = dc.indexOf(prefix);
        if (begin != 0) return null;
    } else {
        begin += 2;
    }
    var end = document.cookie.indexOf(";", begin);
    if (end == -1) {
        end = dc.length;
    }
    return unescape(dc.substring(begin + prefix.length, end));
}


