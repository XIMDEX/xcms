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


function notify(text,recep)
{
	var hijo = document.createElement("<div>");
	hijo.style.marginTop="100px";
	hijo.style.textAlign="center";
	var cad="ATENCIÓN: "+text;
	var texto=document.createTextNode(cad);
	hijo.appendChild(texto);
	hijo.innerHTML=cad;
	recep.appendChild(hijo);
}

function show_colectores()
{
  	var nodeID = document.getElementById("nodoIDF").value;
 	var nodetype = document.getElementById("typeIDF").value;
	var name = document.getElementById("nameIDF").value;

	if(nodetype == "XimNewsColector"){
           presentar_colector(name);
           obj = document.createElement("span");
           obj.setAttribute("id",nodeID);
           show_bulletins(obj);
        }
        else{
           var url = "../modules/ximNEWS/inc/Browser_Adapter.php";
	   var cacheP =  '&' + Math.random();
	   var pars =  "&flag=0&tipo=colectores&nodeid="+nodeID+cacheP;
	   var ajax = new Ajax.Request( url, {parameters: pars,method:"get",onComplete: presentar_colectores});         
        } 		 
}
function presentar_colector(name)
{
	var area = document.getElementById("areaCategorias");
	area.innerHTML = name;
}	

function presentar_colectores(resp)
{	
	var respuesta = resp.responseText;
	if(respuesta != ""){ 
	   var area = document.getElementById("areaCategorias");
	   area.innerHTML = respuesta;
	}
	else{
           //no hay ninguna categoria que visualizar
	   var recep = document.getElementById("areaBoletines");
           notify(_("No news collector existing"),recep);   	   
	}    
}	

function show_bulletins(obj)
{ 
        var colectorID = obj.getAttribute("id");
	var url = "../modules/ximNEWS/inc/Browser_Adapter.php";
	var cacheP =  '&' + Math.random();
	var pars =  "&tipo=boletines&nodeid="+colectorID+cacheP;
	var ajax = new Ajax.Request( url, {parameters: pars,method:"get",onComplete: presentar_boletines});  
}
function presentar_boletines(resp)
{
	var area = document.getElementById("areaBoletines");
	if(area.firstChild){
	   area.removeChild(area.firstChild);
	}
   	var respuesta = resp.responseText;
   	if(respuesta != ""){
	   area.innerHTML = respuesta;
	   //activar_boletines(area);
	}
	else{
          //The category has not bulletins
          /*
          notify("La categori­a <b>"+getText(obj)+"</b> no contiene boletines");
	  var cab_noticia= document.getElementById("cab_tabla_noticias");
		var area_noticia=document.getElementById("area5");
		cab_noticia.style.setAttribute("visibility","hidden");
   	 area5.style.setAttribute("visibility","hidden");
         */	   
	} 	
}

function show_bulletin_info(obj)
{
        var bulletinID = obj.getAttribute("id"); 
	var url = "../modules/ximNEWS/inc/Browser_Adapter.php";
	var cacheP =  '&' + Math.random();
	var pars =  "&tipo=boletin&nodeid="+bulletinID+cacheP;
	var ajax = new Ajax.Request( url, {parameters: pars,method:"get",onComplete: presentar_boletin});          		 
}
function presentar_boletin(resp){
   	var area = document.getElementById("areaBoletin");
	if(area.firstChild){
	   area.removeChild(area.firstChild);
	}
	var respuesta = resp.responseText; 
	area.innerHTML = respuesta;
}
function show_news(obj)
{
        var bulletinID = obj.getAttribute("id");  
	var url = "../modules/ximNEWS/inc/Browser_Adapter.php";
	var cacheP =  '&' + Math.random();
	var pars =  "&tipo=noticias&nodeid="+bulletinID+cacheP;
	var ajax = new Ajax.Request( url, {parameters: pars,method:"get",onComplete:  presentar_noticias});              		 
}

function load_news()
{
	var nodeID = document.getElementById("nodoIDF").value;
        var from = document.getElementById("fromTypeIDF").value;
        var append = "&subtipo=noticias"; 
        if(from == "news"){
           var limite = "&limite="+ document.getElementById("limiteIDF").value;
           var criterio = "&criterio=ultimas";
           append += limite+criterio;
        }
        else if(from == "boletin"){
           append = "&subtipo=boletin"; 
        }
	var url = "../modules/ximNEWS/inc/Browser_Adapter.php";
	var cacheP =  '&' + Math.random();
	var pars =  " &tipo=noticias&nodeid="+nodeID+append+cacheP;
	var ajax = new Ajax.Request( url, {parameters: pars,method:"get",onComplete: presentar_noticias});
                      		 
}

function presentar_noticias(resp){ 
	var area = document.getElementById("areaNoticias");
	if(area.firstChild){
	   area.removeChild(area.firstChild);
	}
	var respuesta = resp.responseText; 	
	if(respuesta != ""){ 
	   area.innerHTML = respuesta;
           area.style.visibility = "visible";
	   area.parentNode.parentNode.previousSibling.firstChild.style.visibility="visible";
	}
	else{
           var recep = document.getElementById("areaNoticias");
   	   notify("The bulletin has not associated news");
	}		
}
function show_new(obj)
{
        var newsID = obj.getAttribute("id");
	var url = "../modules/ximNEWS/inc/Browser_Adapter.php";
	var cacheP =  '&' + Math.random();
	var pars =  "&tipo=noticia&nodeid="+newsID+cacheP;
	var ajax = new Ajax.Request( url, {parameters: pars,method:"get",onComplete: presentar_noticia});         		 
}
function presentar_noticia(resp){ 
	var area = document.getElementById("areaNoticia");
	if(area.firstChild){
	   area.removeChild(area.firstChild);
	}
	var respuesta = resp.responseText;	
	area.innerHTML = respuesta;
        area.style.visibility = "visible";
}


