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


//addEvent(window, "load", sortables_init);

//addEvent(window, "load",load_news);

//addEvent(window, "load",show_tree());
var SORT_COLUMN_INDEX;
var http1 = new createRequestObject1();
var virtualContain = document.createElement("div");
var vContain1 = document.createElement("div");
var vContain2 = document.createElement("div");
function createRequestObject1(){
	var xmlhttp1 = null;
	try {
		xmlhttp1 = new XMLHttpRequest();
	} 
	catch (e) {
		var tiposIE = ['MSXML2.XMLHTTP', 'Microsoft.XMLHTTP', 'MSXML2.XMLHTTP.5.0', 'MSXML2.XMLHTTP.4.0', 'MSXML2.XMLHTTP.3.0'];
		var exito = false;
		for (var i = 0; (i < tiposIE.length) && ( ! exito); i ++) {
			try {
				xmlhttp1 = new ActiveXObject(tiposIE[i]);
				exito = true;
			} 
			catch (e) {}
		}
		if ( ! exito ) {
			return null;
		}
	}
	return xmlhttp1;
}

//Bulletin viewer
var ultima = 0;
var itemsLevel1;
var tabContainer;
function reset_row()
{
    var obj = document.getElementById("receptor");
		var cuerpo = obj.firstChild;
		var ultima_fila = cuerpo.lastChild;
		ultima_fila.removeNode(true);
}
function show_bulletins()
{
   var nodeid = document.getElementById("nodoIDF").value;
	 //for(var j=0; j < ultima; j++){
		 //    reset_row();
	 //}
	 
	 http1.open('get','../../inc/BRWX_Adapter.php?tipo=boletines&nodeid='+nodeid);
	 http1.onreadystatechange = presenta_bulletins;
	 http1.send(null);		 
}

function presenta_bulletins()
{
    if(http1.readyState == 4){		
		   var respuesta = http1.responseXML;
			 
			 tabContainer = document.createElement('<div id="ximnews_tabView" >');				
			 itemsLevel1 = respuesta.getElementsByTagName("category");
			 var plugin;
			 if(itemsLevel1.length > 1){
			    //If there is more than one news category, a view is created, grouped per category.
				// Different views can be accessed through an index (configurable via tpye:toc or tab)
					var type = "tab";
					var index_obj = make_index();
					if(type == "toc"){
					   var tabla = document.createElement('<table>');
	           var cuerpo = document.createElement("<tbody>");
					   var row = document.createElement("<tr>");
				     var col = document.createElement("<td>");
					   var empty = document.createElement("<td>");
					   empty.setAttribute("id","contactoEmpty");
					   col.appendChild(index_obj);
					   row.appendChild(col);
					   row.appendChild(empty);
					   cuerpo.appendChild(row);
					   tabla.appendChild(cuerpo);
						 plugin = tabla;
				  }		
					else{
					   plugin = tabContainer;
					}
			 }		
			 var area = document.getElementById("area1");
			 area.appendChild(plugin);
			 initTabs(0,572,0); 
			 //traverseTree(respuesta.documentElement);
			 
		}
}
function show_bulletin_info(){
   var enlaceArea2 = document.getElementById("area2");
	 if(enlaceArea2.firstChild){
	    enlaceArea2.removeChild(enlaceArea2.firstChild);
	 }
	 var idref = this.getAttribute("name");
	 var boletin = ximGetElementsByAttribute("bulletinLanguage","id",idref);
	 var tabla = document.createElement('<table>');
	 var tablaH = document.createElement("<thead>");
   var headerRow = document.createElement("<tr class='cabeceratabla' >");
	 var header1 = document.createElement("<th width='60'>");
	 var header2 = document.createElement("<th width='140'>");
	 var prop = document.createTextNode("PROPIEDAD");
	 var val = document.createTextNode("VALOR");
	 header1.appendChild(prop);
	 header2.appendChild(val);
	 headerRow.appendChild(header1);
	 headerRow.appendChild(header2);
	 tablaH.appendChild(headerRow);
	 tabla.appendChild(tablaH);
	 var cuerpo = document.createElement("<tbody>");
	 var cabecera = boletin.firstChild.firstChild;
	 var childsCab = cabecera.childNodes;
	 for(var f=0; f < childsCab.length; f++){
				var row = document.createElement("<tr class='filaoscuranegrita' >");
				var col1 = document.createElement("<td>");
				var col2 = document.createElement("<td>");
				var itemA = childsCab.item(f);
				var clave = itemA.getAttribute("name");
				var claveObj = document.createTextNode(clave);
				col1.appendChild(claveObj);
				var valor = getText(itemA);
				var valorObj = document.createTextNode(valor);
				col2.appendChild(valorObj);
				row.appendChild(col1);
				row.appendChild(col2);
				cuerpo.appendChild(row);
	}			
	tabla.appendChild(cuerpo);			
	enlaceArea2.appendChild(tabla);
}
function show_news_info(){
   var enlaceArea3 = document.getElementById("area3");
	 if(enlaceArea3.firstChild){
	    enlaceArea3.removeChild(enlaceArea3.firstChild);
	 }
	 var idref = this.getAttribute("name");
	 var boletin = ximGetElementsByAttribute("bulletinLanguage","id",idref);
	 var tabla = news_table(boletin);
	 enlaceArea3.appendChild(tabla);
}
//Creating a table with all the news from a bulletin
//obj references the bulletin object
function news_table(obj){
	
   var noticias = obj.getElementsByTagName("noticia");
 
   //tabla with the list of news class="sortable"
   var tabla = document.createElement('<table width="560" >');
	 
   //primary header information
   var tablaH = document.createElement("<thead>");
   var headerRow = document.createElement("<tr class='cabeceratabla' >");
	 header = document.createElement("<th width='100'>");
	 header.setAttribute("id","thnombreN");
	 enlace = document.createElement("<a href='#' class='sortheader' onclick='ts_resortTable(this);return false;'>");
   contenido = document.createTextNode("nombre");
	 enlace.appendChild(contenido);
	 header.appendChild(enlace);
	 headerRow.appendChild(header);
   //accessing to the news body
	 if(noticias.length > 0){
      var headers = noticias.item(0).firstChild.childNodes;
	    var fraccion = parseInt(headers.length);
	    var ratio = 500/(fraccion);
	    ratio = parseInt(ratio);
      for(var h=0; h < headers.length; h++){
            var etiqueta = headers.item(h).getAttribute("label");  
			      header = document.createElement("<th>");
						header.setAttribute("width",ratio);
				    header.setAttribute("id","th"+etiqueta);
				    enlace = document.createElement("<a href='#' class='sortheader' onclick='ts_resortTable(this);return false;'>");
            contenido = document.createTextNode(etiqueta);
				    enlace.appendChild(contenido);
				    header.appendChild(enlace);
				    headerRow.appendChild(header);
		 }
  }				 
  tablaH.appendChild(headerRow);
  tabla.appendChild(tablaH);
  //table body
  var cuerpo = document.createElement("<tbody>");

  for(var i=0; i < noticias.length; i++){
       var row = document.createElement("<tr class='filaoscuranegrita'>");
		   var notName = noticias.item(i).getAttribute("name");
			 var content = document.createTextNode(notName);
			 var span = document.createElement("<span style='font-size:14px;' >");
			 span.appendChild(content);
       var col = document.createElement("<td>");
			 col.appendChild(span);
			 row.appendChild(col);
		   var childs = noticias.item(i).firstChild.childNodes;
		   for(var j=0; j < childs.length; j++){ 
		            var ItemJ = childs.item(j);
							  var texto = getText(ItemJ);
							  content = document.createTextNode(texto);
					      span = document.createElement("<span style='font-size:14px; ' >");
					      span.appendChild(content);
                col = document.createElement("<td>");
					      col.appendChild(span);
					      row.appendChild(col);
		  } 			 
		  cuerpo.appendChild(row);		
  }
  tabla.appendChild(cuerpo); 
	return tabla;
}
function ximGetElementsByAttribute(tag,attr,value)
{
  var a, list, found ;
  list = http1.responseXML.getElementsByTagName(tag);
  for (var i = 0; i < list.length; ++i) {
         a = list[i].getAttribute(attr);
         if (a == value) {
             found = list[i];
             break;
				 }
  }
  return found;
}

function show_table(){
   var orden = this.getAttribute("name");
	 orden = parseInt(orden);
   var contacto = document.getElementById("contactoEmpty");
   if(contacto.firstChild ){
	    contacto.removeChild(contacto.firstChild);
	 }
	 var obj = itemsLevel1.item(orden);
	 var tabla = bulletins_table(obj);
	 contacto.appendChild(tabla);
}
//creating a table with all the bulletins of a category
//obj references the category object
function bulletins_table(obj){
	
   var bulletins = obj.getElementsByTagName("bulletin");
 
   //table with the list of bulletins class="sortable"
   var tabla = document.createElement('<table width="560" >');
	 var name = obj.getAttribute("name");
	 tabla.setAttribute("name",name);
   //primary header information
   var tablaH = document.createElement("<thead>");
   var headerRow = document.createElement("<tr class='cabeceratabla' >");
	 header = document.createElement("<th width='100'>");
	 header.setAttribute("id","thnombre");
	 enlace = document.createElement("<a href='#' class='sortheader' onclick='ts_resortTable(this);return false;'>");
   contenido = document.createTextNode("nombre");
	 enlace.appendChild(contenido);
	 header.appendChild(enlace);
	 headerRow.appendChild(header);
   //accessing to the first bulletin header 
   //For the case in which all the bulletins are going to be organized in the same way
   var headers = obj.getElementsByTagName("cabecera_boletin");
   var muestraH = headers[0];
   var childsH = muestraH.childNodes;
	 var fraccion = parseInt(childsH.length);
	 var ratio = 500/(fraccion);
	 ratio = parseInt(ratio);
   for(var h=0; h < childsH.length; h++){
         if(childsH[h].getAttribute("level") == "primary"){ 
			      header = document.createElement("<th>");
						header.setAttribute("width",ratio);
				    name = childsH[h].getAttribute("name");
				    header.setAttribute("id","th"+name);
				    enlace = document.createElement("<a href='#' class='sortheader' onclick='ts_resortTable(this);return false;'>");
            contenido = document.createTextNode(name);
				    enlace.appendChild(contenido);
				    header.appendChild(enlace);
				    headerRow.appendChild(header);
			  }
  }				 
  tablaH.appendChild(headerRow);
  tabla.appendChild(tablaH);
  //table body
  var cuerpo = document.createElement("<tbody>");

  for(var i=0; i < headers.length; i++){
       var row = document.createElement("<tr class='filaclara'>");
		   var head = headers.item(i);
			 var abuelo = head.parentNode.parentNode;
			 var bullName = abuelo.getAttribute("name");
			 var idref = abuelo.getAttribute("id");
			 var content = document.createTextNode(bullName);
			 var span = document.createElement("<span style='font-size:14px;cursor:hand;' >");
			 span.setAttribute("name",idref);
			 span.onmouseover = show_bulletin_info;
			 span.onclick = show_news_info;
			 span.appendChild(content);
       var col = document.createElement("<td>");
			 col.appendChild(span);
			 row.appendChild(col);
		   var childsHead = head.childNodes;
		   for(var j=0; j < childsHead.length; j++){
		         if(childsHead[j].getAttribute("level") == "primary"){ 
		            var ItemJ = childsHead.item(j);
							  var texto = getText(ItemJ);
							  content = document.createTextNode(texto);
					      span = document.createElement("<span style='font-size:14px; ' >");
					      span.appendChild(content);
                col = document.createElement("<td>");
					      col.appendChild(span);
					      row.appendChild(col);
					 }
		  } 			 
		  cuerpo.appendChild(row);		
  }
  tabla.appendChild(cuerpo); 
	return tabla;
}

function make_index(type){
	 if(type == "toc"){
      var tabla = document.createElement('<table>');
	    var cuerpo = document.createElement("<tbody>");
	    for(var i=0; i < itemsLevel1.length; i++){
			     var itemI = itemsLevel1.item(i);
	         var name = itemI.getAttribute("name");
				   var row = document.createElement("<tr class='cabeceratabla' >");
				   var col = document.createElement("<td>");
				   var control = document.createElement("<input type='button' style='text-align: center; font-size:14px; width: 100%; border: 1px solid #B8429B; border-color: #B8429B; background-color: #B8429B;>");
					 control.setAttribute("value",name);
					 control.setAttribute("name",i);
					 control.onclick = show_table;
					 control.onmouseover = function(){
					     this.style.color = "white"; return false
					 }
					 control.onmouseout = function(){
					     this.style.color = "black"; return false
					 }
					 col.appendChild(control);
				   row.appendChild(col);
				   cuerpo.appendChild(row);
	   }
		 tabla.appendChild(cuerpo);
		 return tabla;
	 }
	 else{	   
	    for(var i=0; i < itemsLevel1.length; i++){
				   var tabTable = generate_tabTable(itemsLevel1[i]);
				   tabContainer.appendChild(tabTable);
	    }
	 }		  
}
function generate_tabTable(obj){
var container = document.createElement('<div class="ximnews_aTab">');
var tabla = bulletins_table(obj);
container.appendChild(tabla);
return container;
}
function getText(node)
{
  var children = node.childNodes,
    text = "";
  for(i = 0;i < children.length;i++)
  {
   var n = children.item(i);
   if(n.nodeType == 3)
     text += n.data;
  }
  return text;
}
function traverseTree(objNode)
{
  if(objNode){
     if(objNode.nodeType == 1){
        processNode(objNode);
     }
     for(var c = objNode.firstChild; c; c = c.nextSibling){
         traverseTree(c);
     }
  }
}
function processNode(obj)
{
/*
var hoja = document.createElement("div");
//var control = document.createElement("img");
//var icono = document.createElement("img");
var titulo = document.createElement("span");
var content = obj.getText();
var contentElement = document.createTextElement(content); 
titulo.appendChild(contentElement);
hoja.appendChild(titulo);
contenedor.appendChild(hoja);
*/
}	
function xGetElementsByAttribute(sTag, sAtt, sRE, fn)
{
  var a, list, found = new Array(), re = new RegExp(sRE, 'i');
	var list ;
  list = xGetElementsByTagName(sTag);
  for (var i = 0; i < list.length; ++i) {
         a = list[i].getAttribute(sAtt);
         if (!a) {
				    a = list[i][sAtt];
				 }
         if (typeof(a)=='string' && a.search(re) != -1) {
      found[found.length] = list[i];
      if (fn) fn(list[i]);
    }
  }
  return found;
}
/*


function show_bulletins()
{
   var nodeid = document.getElementById("nodoIDF").value;
	 for(var j=0; j < ultima; j++){
		     reset_row();
	 }
	 http1.open('get','../../inc/BRWN_Adapter.php?tipo=boletines&nodeid='+nodeid);
	 http1.onreadystatechange = presenta_bulletins;
	 http1.send(null);		 
}

function presenta_bulletins()
{
    if(http1.readyState == 4){		
		   ultima = 1;
			 var respuesta = http1.responseText;
			 var area = document.getElementById("area1"); 
			 area.innerHTML = respuesta;
			 var lista = area.lastChild;
			 var cadena_lista = lista.value;
			 var array_lista = cadena_lista.split(",");
			 lista.removeNode(true);
			
       initTabs(array_lista,0,420,0);
			 
		}
}
function ver_boletin(boletin)
{
    document.getElementById("area2").innerHTML = boletin;
		http1.open('get','../../inc/BRWN_Adapter.php?tipo=noticiasB&nodeid='+boletin);
	  http1.onreadystatechange = presenta_news;
	  http1.send(null);		
}
*/
function presenta_news()
{
    if(http1.readyState == 4){		
		   var respuesta = http1.responseText;
       document.getElementById("area3").innerHTML = respuesta;
		}	  
}
//News viewer
function load_news()
{

   var nodeid = document.getElementById("nodoIDF").value;
	 http1.open('get','../../inc/BRWX_Adapter.php?tipo=noticias&nodeid='+nodeid);
	 http1.onreadystatechange = list_news;
	 http1.send(null);
 
}
function list_news()
{
   if(http1.readyState == 4){		
			var respuesta = http1.responseXML;
			var noticias = respuesta.getElementsByTagName("noticia");
			if(noticias.length > 0){
			   var tabla = news_table2(noticias);
				 var plugin = document.getElementById("area1");
         plugin.appendChild(tabla);
			}	 
	 }		
}
function ximGetChildsByAttribute(refer,attr,value)
{
  var a, list, found = new Array();
  list = refer.childNodes;
  for (var i = 0; i < list.length; ++i) {
         a = list[i].getAttribute(attr);
         if (a == value) {
             found[found.length] = list[i];
				 } 			
  }
  return found;
}
function news_table2(noticias){
	
   //table with the list of news class="sortable"
   var tabla = document.createElement('<table width="560" >');
	 
   //primary header information
   var tablaH = document.createElement("<thead>");
   var headerRow = document.createElement("<tr class='cabeceratabla' >");
	 header = document.createElement("<th width='100'>");
	 header.setAttribute("id","thnombreN");
	 enlace = document.createElement("<a href='#' class='sortheader' onclick='ts_resortTable(this);return false;'>");
   contenido = document.createTextNode("nombre");
	 enlace.appendChild(contenido);
	 header.appendChild(enlace);
	 headerRow.appendChild(header);
   //accessing to the news body
   var ref = noticias.item(0).firstChild;
	 var headers = ximGetChildsByAttribute(ref,"summary","yes");			   
	 var fraccion = parseInt(headers.length);
	 var ratio = 500/(fraccion);
	 ratio = parseInt(ratio);
   for(var h=0; h < headers.length; h++){
	       var itemH = headers[h];
         var etiqueta = itemH.getAttribute("label");  
			   header = document.createElement("<th>");
				 header.setAttribute("width",ratio);
				 header.setAttribute("id","th"+etiqueta);
				 enlace = document.createElement("<a href='#' class='sortheader' onclick='ts_resortTable(this);return false;'>");
         contenido = document.createTextNode(etiqueta);
				 enlace.appendChild(contenido);
				 header.appendChild(enlace);
				 headerRow.appendChild(header);
	 }				 
   tablaH.appendChild(headerRow);
   tabla.appendChild(tablaH);
   //table body
   var cuerpo = document.createElement("<tbody>");

   for(var i=0; i < noticias.length; i++){
        var row = document.createElement("<tr class='filaoscuranegrita'>");
		    var notName = noticias.item(i).getAttribute("name");
			  var content = document.createTextNode(notName);
			  var span = document.createElement("<span style='font-size:14px;' >");
			  span.appendChild(content);
        var col = document.createElement("<td>");
			  col.appendChild(span);
			  row.appendChild(col);
				var refer = noticias.item(i).firstChild;
				var childs = ximGetChildsByAttribute(refer,"summary","yes");	
		    for(var j=0; j < childs.length; j++){ 
		          var ItemJ = childs[j];
							var texto = getText(ItemJ);
							content = document.createTextNode(texto);
					    span = document.createElement("<span style='font-size:14px; ' >");
					    span.appendChild(content);
              col = document.createElement("<td>");
					    col.appendChild(span);
					    row.appendChild(col);
		   } 			 
		   cuerpo.appendChild(row);		
    }
    tabla.appendChild(cuerpo); 
	return tabla;
}
//Images viewer

function show_LoteImagenes()
{
     var nodeid = document.getElementById("nodoIDF").value;
     var menu = "LoteImagenes";
		 http1.open('get','../../inc/BRWN_Adapter.php?tipo='+menu+'&nodeid='+nodeid);
		 http1.onreadystatechange = presenta_LoteImg;
		 http1.send(null);
}
function presenta_LoteImg()
{
     if(http1.readyState == 4){		
		    var respuesta = http1.responseText;
				var recep =  document.getElementById('area1');
				recep.innerHTML = respuesta;
		 }		
				
}
function show_tree(nodeid)
{
    if(nodeid == 0){
		   var nodeid = document.getElementById("nodoIDF").value;
		}	 
		var menu = "imagenes";
		http1.open('get','../../inc/BRWI_Adapter.php?tipo='+menu+'&nodeid='+nodeid);
		http1.onreadystatechange = presenta_tree;
		http1.send(null);
}
	
function presenta_tree(){ 
    if(http1.readyState == 4){		
		  var respuesta = http1.responseXML;
			d = new dTree('d');
			var items = respuesta.getElementsByTagName("node");
			var atributos = new Array();
			for(var i=0; i < items.length; i++){
			      var accion = "";
						var itemi = items[i];
						var name = itemi.getAttribute("name");
						var icon = itemi.getAttribute("icon");
						var abrir = itemi.getAttribute("abrir");
						id = itemi.getAttribute("id");
						accion = "javascript:show_lotes1('"+id+"','"+name+"');";
						itemi.setAttribute("sec",i);
						if(i==0){
						   d.add(0,-1,name,accion);
						}
						else{
						   padre = itemi.parentNode;
							 sec = padre.getAttribute('sec');
							 d.add(i,sec,name,accion);
						}	 
			}
      document.getElementById("area1").innerHTML = d.toString();
		}
}			 
function show_lotes1(nodeID,name)
{
		var menu = "imagenes";
		if(document.getElementById("nodoIDF") != "undefined"){
		   var combo = document.getElementById("nodoIDF");
			 if(combo.tagName == "select"){ 
			    for(var i=0; i < combo.options.length; i++){
			          if(combo.options[i].innerHTML == name){
						       combo.selectedIndex = i;
								   break;
						    }
			    //var indice = combo.selectedIndex;
		      //var valor = combo.options[indice].value;
			    }
			}		
		}
		http1.open('get','../../inc/BRWN_Adapter.php?tipo='+menu+'&nodeid='+nodeID);
		http1.onreadystatechange = presenta_images1;
		http1.send(null);
}
	
function presenta_images1(){ 
    if(http1.readyState == 4){			    
			 var respuesta = http1.responseText;
			 virtualContain.innerHTML = respuesta;
			 var recep =  document.getElementById('area2');
			 if(recep.hasChildNodes()){
			    recep.firstChild.removeNode(true);
			 }		
       //pushing the two tables of the virtual container
			 vContain1.appendChild(virtualContain.firstChild);
			 vContain2.appendChild(virtualContain.firstChild);
			 recep.appendChild(vContain2.firstChild);
			 //initSelectImages();
		}
}			
function show_lotesA(nodeID)
{
		var menu = "imagenes";
		http1.open('get','../../inc/BRWN_Adapter.php?tipo='+menu+'&nodeid='+nodeID);
		http1.onreadystatechange = presenta_imagesA;
		http1.send(null);
}
	
function presenta_imagesA(){ 
    if(http1.readyState == 4){			    
			 var respuesta = http1.responseText;
			 virtualContain.innerHTML = respuesta;
			 var recep =  document.getElementById('area2');
			 if(recep.hasChildNodes()){
			    recep.firstChild.removeNode(true);
			 }		
			 recep.appendChild(virtualContain.firstChild);
			 //initSelectImages();
		}
}			
function toggle_view(obj){
   var recep =  document.getElementById('area2');
   if(obj.value == "Imagen"){
	    obj.value = "Lista";
			vContain2.appendChild(recep.firstChild);
			recep.appendChild(vContain1.firstChild);
	 }
	 else{
	   obj.value = "Imagen";
		 vContain1.appendChild(recep.firstChild);
		 recep.appendChild(vContain2.firstChild);
	 }		 
}
function cambiar_color_over(celda){
   celda.style.backgroundColor="#66ff33" 
}

function cambiar_color_out(celda){
  celda.style.backgroundColor="#EBEBEB" 
}
function view_image(obj){
   var texto = obj.innerHTML;
	 var target = document.getElementById("area3");
	 if(target.hasChildNodes()){
	    target.firstChild.removeNode(true);
	 }		
	 var images = vContain1.getElementsByTagName("img");
	 for(var i=0; i < images.length; i++){
	      var image = images.item(i);
				if(image.getAttribute("name") == texto){
				   var clon = image.cloneNode(true);
				   target.appendChild(clon);
					 break;
				}	 
	 }
}
function initSelectImages()
{
	var imgs = document.getElementsByTagName('IMG');
	for(var no=0;no<imgs.length;no++){
		if(imgs[no].className=='thumb'){
			 imgs[no].SetAttribute("onclick",'selectImage(this)');
		}		
	}	
}
function selectImage(obj)
{
  //var padre = point.parentNode;
  //var ultimo = padre.lastChild;
	var ultimo = point.lastChild;
	var clon = obj.firstChild.cloneNode();
	var valor = obj.lastChild.value;
	if(ultimo.tagName == "A" || ultimo.tagName == "IMG" ){
	   ultimo.removeNode(true);
	}
  point.appendChild(clon);	 
	point.firstChild.value = valor;
}

function img_nav(obj,flag){
 
 var recep = document.getElementById("receptor");
 var oriImg = document.getElementById("origen");
 
 var cuerpo = recep.firstChild;
 
 if(obj.value == "Ver"){
	  obj.value = "Ocultar";
	  /*	
	  var combo = document.getElementById("nodoIDF");
		var indice = combo.selectedIndex;
		var valor = combo.options[indice].value;
    */
		var padre = obj.parentNode;
		var combo = padre.firstChild;
		var indice = combo.selectedIndex;
		var valor = combo.options[indice].value;
		
		var fila = document.createElement("tr");
		var colspan="colspan='2'"
		if(valor == 0){ 
		   var columna1 = document.createElement("td");
	     var capa1 = document.createElement('<div id="area1" style="border:2px inset #cccccc;background:white;overflow:auto;width:150px;height:200px;">');
			 capa1.innerHTML ='SUBARBOL';
			 columna1.appendChild(capa1);
			 fila.appendChild(columna1);
			 colspan = "";	
			 nodeid = oriImg.value;
			 show_tree(nodeid);		
		}
		var columna2 = document.createElement("<td "+colspan+" >");
	  var capa2 = document.createElement('<div id="area2" style="border:2px inset #cccccc;background:white;overflow:auto;width:320px;height:200px;">');
		columna2.appendChild(capa2);		
		fila.appendChild(columna2);		
		
		if(flag == 1){
       //table of a row with two columns inside of column in a receptor table row
			 var filaC = document.createElement("tr");
			 var columnaC = document.createElement("td");
			 var tablaC = document.createElement("<table align='center'>");
			 var cuerpoC = document.createElement("tbody");
			 cuerpoC.appendChild(fila);
			 tablaC.appendChild(cuerpoC);
			 columnaC.appendChild(tablaC);
			 filaC.appendChild(columnaC);
			 cuerpo.insertBefore(filaC,cuerpo.lastChild);
			 //cuerpo.appendChild(filaC);	
		}
		else{		
		   //row with two columns inside the receptor table
		   cuerpo.insertBefore(fila,cuerpo.lastChild);
		}	 
		if(valor > 0){
		   show_lotes1(valor);
		}	 
 }
 else{
   obj.value = "Ver";
	 var ultimo = cuerpo.lastChild; 
	 ultimo.previousSibling.removeNode(true);
 }		
 
}
function selector_lotes()
{
    cadena = document.getElementById("punteroLotes").value;
		var lista = new Array();
		if(cadena.indexOf("&") > -1){
		   lista = cadena.split("&");
		}
		else{
		   lista[0] = cadena;
		}	 
		var tabla = document.createElement("table");
		var cuerpo = document.createElement("tbody");
		var fila = document.createElement("tr");
		var col = document.createElement("td");
		var label = document.createElement("<span class='cabeceratabla' >");
		label.innerHTML = "Lotes";
		col.appendChild(label);
		fila.appendChild(col);
		cuerpo.appendChild(fila);
		for(var i=0; i < lista.length; i++){
		     var listai = lista[i].split(",");
		     var filai = document.createElement("tr");
				 var coli = document.createElement("td");
				 var linki = document.createElement("<a href='javascript:show_lotesA("+listai[0]+");'>");
				 linki.innerHTML = listai[1];
				 coli.appendChild(linki);
				 filai.appendChild(coli);
				 cuerpo.appendChild(filai);
		}
		tabla.appendChild(cuerpo);
		alert(tabla.innerHTML);
		var ref = document.getElementById("refer");
		var abuelo = ref.parentNode.parentNode;
		var cuerpo = abuelo.parentNode;
		var hermano = abuelo.nextSibling;
		
		var fila = document.createElement("tr");
		var columna1 = document.createElement("<td valign='top' >");
	  var capa1 = document.createElement('<div id="area1" style="border:2px inset #cccccc;background:white;overflow:auto;width:100px;height:80px;">');
		capa1.appendChild(tabla);
		columna1.appendChild(capa1);
		fila.appendChild(columna1);
		
		var columna2 = document.createElement("<td >");
	  var capa2 = document.createElement('<div id="area2" style="border:2px inset #cccccc;background:white;overflow:auto;width:520px;height:200px;">');
		columna2.appendChild(capa2);		
		fila.appendChild(columna2);		
		cuerpo.insertBefore(fila,hermano);
}							 
///////END IMAGES VIEWER
 
function addEvent(elm, evType, fn, useCapture)
// addEvent and removeEvent
// cross-browser event handling for IE5+,  NS6 and Mozilla
// By Scott Andrew
{
  if (elm.addEventListener){
    elm.addEventListener(evType, fn, useCapture);
    return true;
  } else if (elm.attachEvent){
    var r = elm.attachEvent("on"+evType, fn);
    return r;
  } else {
    alert("Handler could not be removed");
  }
} 

function sortables_init() {
    // Find all tables with class sortable and make them sortable
    if (!document.getElementsByTagName) return;
    tbls = document.getElementsByTagName("table");
    for (ti=0;ti<tbls.length;ti++) {
        thisTbl = tbls[ti];
        if (((' '+thisTbl.className+' ').indexOf("sortable") != -1) && (thisTbl.id)) {
            //initTable(thisTbl.id);
            ts_makeSortable(thisTbl);
        }
    }
}

function ts_makeSortable(table) {
    if (table.rows && table.rows.length > 0) {
        var firstRow = table.rows[0];
    }
    if (!firstRow) return;
    
    // We have a first row: assume it's the header, and make its contents clickable links
    for (var i=0;i<firstRow.cells.length;i++) {
        var cell = firstRow.cells[i];
        var txt = ts_getInnerText(cell);
        cell.innerHTML = '<a href="#" class="sortheader" onclick="ts_resortTable(this);return false;">'+txt+'<span class="sortarrow"></span></a>';
    }
}

function ts_getInnerText(el) {
	if (typeof el == "string") return el;
	if (typeof el == "undefined") { return el };
	if (el.innerText) return el.innerText;	//Not needed but it is faster
	var str = "";
	
	var cs = el.childNodes;
	var l = cs.length;
	for (var i = 0; i < l; i++) {
		switch (cs[i].nodeType) {
			case 1: //ELEMENT_NODE
				str += ts_getInnerText(cs[i]);
				break;
			case 3:	//TEXT_NODE
				str += cs[i].nodeValue;
				break;
		}
	}
	return str;
}

function ts_resortTable(lnk) {
    // get the span
    var span;
    for (var ci=0;ci<lnk.childNodes.length;ci++) {
        if (lnk.childNodes[ci].tagName && lnk.childNodes[ci].tagName.toLowerCase() == 'span') span = lnk.childNodes[ci];
    }
    var spantext = ts_getInnerText(span);
    var td = lnk.parentNode;
    var column = td.cellIndex;
    var table = getParent(td,'TABLE');
    
    // Work out a type for the column
    if (table.rows.length <= 1) return;
    var itm = ts_getInnerText(table.rows[1].cells[column]);
    sortfn = ts_sort_caseinsensitive;
    if (itm.match(/^\d\d[\/-]\d\d[\/-]\d\d\d\d$/)) sortfn = ts_sort_date;
    if (itm.match(/^\d\d[\/-]\d\d[\/-]\d\d$/)) sortfn = ts_sort_date;
    if (itm.match(/^[£$]/)) sortfn = ts_sort_currency;
    if (itm.match(/^[\d\.]+$/)) sortfn = ts_sort_numeric;
    SORT_COLUMN_INDEX = column;
    var firstRow = new Array();
    var newRows = new Array();
    for (i=0;i<table.rows[0].length;i++) { firstRow[i] = table.rows[0][i]; }
    for (j=1;j<table.rows.length;j++) { newRows[j-1] = table.rows[j]; }

    newRows.sort(sortfn);

    if (span.getAttribute("sortdir") == 'down') {
        ARROW = '&laquo;';
        newRows.reverse();
        span.setAttribute('sortdir','up');
    } else {
        ARROW = '&raquo;';
        span.setAttribute('sortdir','down');
    }
    
    // We appendChild rows that already exist to the tbody, so it moves them rather than creating new ones
    // don't do sortbottom rows
    for (i=0;i<newRows.length;i++) { if (!newRows[i].className || (newRows[i].className && (newRows[i].className.indexOf('sortbottom') == -1))) table.tBodies[0].appendChild(newRows[i]);}
    // do sortbottom rows only
    for (i=0;i<newRows.length;i++) { if (newRows[i].className && (newRows[i].className.indexOf('sortbottom') != -1)) table.tBodies[0].appendChild(newRows[i]);}
    
    // Delete any other arrows there may be showing
    var allspans = document.getElementsByTagName("span");
    for (var ci=0;ci<allspans.length;ci++) {
        if (allspans[ci].className == 'sortarrow') {
            if (getParent(allspans[ci],"table") == getParent(lnk,"table")) { // in the same table as us?
                allspans[ci].innerHTML = '';
            }
        }
    }
        
    span.innerHTML = ARROW;
}

function getParent(el, pTagName) {
	if (el == null) return null;
	else if (el.nodeType == 1 && el.tagName.toLowerCase() == pTagName.toLowerCase())	// Gecko bug, supposed to be uppercase
		return el;
	else
		return getParent(el.parentNode, pTagName);
}
function ts_sort_date(a,b) {
    // y2k notes: two digit years less than 50 are treated as 20XX, greater than 50 are treated as 19XX
    aa = ts_getInnerText(a.cells[SORT_COLUMN_INDEX]);
    bb = ts_getInnerText(b.cells[SORT_COLUMN_INDEX]);
    if (aa.length == 10) {
        dt1 = aa.substr(6,4)+aa.substr(3,2)+aa.substr(0,2);
    } else {
        yr = aa.substr(6,2);
        if (parseInt(yr) < 50) { yr = '20'+yr; } else { yr = '19'+yr; }
        dt1 = yr+aa.substr(3,2)+aa.substr(0,2);
    }
    if (bb.length == 10) {
        dt2 = bb.substr(6,4)+bb.substr(3,2)+bb.substr(0,2);
    } else {
        yr = bb.substr(6,2);
        if (parseInt(yr) < 50) { yr = '20'+yr; } else { yr = '19'+yr; }
        dt2 = yr+bb.substr(3,2)+bb.substr(0,2);
    }
    if (dt1==dt2) return 0;
    if (dt1<dt2) return -1;
    return 1;
}

function ts_sort_currency(a,b) { 
    aa = ts_getInnerText(a.cells[SORT_COLUMN_INDEX]).replace(/[^0-9.]/g,'');
    bb = ts_getInnerText(b.cells[SORT_COLUMN_INDEX]).replace(/[^0-9.]/g,'');
    return parseFloat(aa) - parseFloat(bb);
}

function ts_sort_numeric(a,b) { 
    aa = parseFloat(ts_getInnerText(a.cells[SORT_COLUMN_INDEX]));
    if (isNaN(aa)) aa = 0;
    bb = parseFloat(ts_getInnerText(b.cells[SORT_COLUMN_INDEX])); 
    if (isNaN(bb)) bb = 0;
    return aa-bb;
}

function ts_sort_caseinsensitive(a,b) {
    aa = ts_getInnerText(a.cells[SORT_COLUMN_INDEX]).toLowerCase();
    bb = ts_getInnerText(b.cells[SORT_COLUMN_INDEX]).toLowerCase();
    if (aa==bb) return 0;
    if (aa<bb) return -1;
    return 1;
}

function ts_sort_default(a,b) {
    aa = ts_getInnerText(a.cells[SORT_COLUMN_INDEX]);
    bb = ts_getInnerText(b.cells[SORT_COLUMN_INDEX]);
    if (aa==bb) return 0;
    if (aa<bb) return -1;
    return 1;
}

function set_rows_table(tablaId) {
	    var tabla = document.getElementById(tablaId);
			var tbodies = table.getElementsByTagName("tbody");
			for (var h = 0; h < tbodies.length; h++) {
				    var even = true;
				    var trs = tbodies[h].getElementsByTagName("tr");
				    for (var i = 0; i < trs.length; i++) {
					        trs[i].onmouseover=function(){
						            this.className += " ruled"; return false;
					        }
					        trs[i].onmouseout=function(){
						            this.className = this.className.replace("ruled", ""); return false;
					        }
					        if(even)
						         trs[i].className += " even";
					        even = !even;
						}			
			}
}
