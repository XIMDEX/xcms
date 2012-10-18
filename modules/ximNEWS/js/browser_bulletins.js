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


//Bulleting viewer
var colorCeldaActiva = "#c0c0c0";
var colorCeldaOver = "#dddddd";
var colorCeldaInactiva = "#FAFAFA";
var ultima = 0;
var itemsLevel1;
var tabContainer;
var http1 = new createRequestObject1();

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
	http1.open('get','../modules/ximNEWS/inc/BRWX_Adapter.php?tipo=boletines&nodeid='+nodeid);
	http1.onreadystatechange = presentar_bulletins;
	http1.send(null);		 
}
function presentar_bulletins()
{
	if(http1.readyState == 4){		
	   var respuesta = http1.responseXML;
	   itemsLevel1 = respuesta.getElementsByTagName("category");
	   var area = document.getElementById("area1");
	   if(itemsLevel1.length > 0){
	      var tabla = document.createElement('<table width="100%">');
	      var cuerpo = document.createElement("<tbody>");
	      for(c=0; c < itemsLevel1.length; c++){
		   cat = itemsLevel1[c];
		   var row = document.createElement("<tr>");
		   var col = document.createElement("<td class='filaclara' onmouseover='cambiar_color_over(this)' onmouseout='cambiar_color_out(this)' >");
		   name = cat.getAttribute("name"); 
		   var content = document.createTextNode(name);
		   //var content = document.createTextNode(name+" »");
		   var span = document.createElement("<span style='cursor:hand;' onclick='mostrar_boletines_cat(this);'>");
		   span.setAttribute("name",name);
		   span.appendChild(content);
		   col.appendChild(span);
		   row.appendChild(col);
		   cuerpo.appendChild(row);
	      }			
	      tabla.appendChild(cuerpo);
	      area.appendChild(tabla);
	   }
	   else{
	      //No categories to visualize
              notify("No news collector existing"); 
	   }
	 }		 
}
function notify(text)
{
 		var recep = document.getElementById("area3");
		var hijo = document.createElement("<div>");
		hijo.style.marginTop="100px";
		hijo.style.textAlign="center";
		
		var cad="WARNING: "+text;
		var texto=document.createTextNode(cad);
		hijo.appendChild(texto);
		hijo.innerHTML=cad;
		recep.appendChild(hijo);
}

function cambiar_color_over(celda){
if(celda.style.backgroundColor != colorCeldaActiva)
  {
   celda.style.backgroundColor = colorCeldaOver; 
  } 
}

function cambiar_color_out(celda){
if(celda.style.backgroundColor != colorCeldaActiva)
  {
   celda.style.backgroundColor = colorCeldaInactiva; 
  } 
}

function cambiar_color_clic(celda){
   celda.style.backgroundColor = colorCeldaActiva;
   celda.firstChild.className="itemnegrita";
   //celda.firstChild.style.setAttribute("font-weight","bold");
   //The rest of cells are kept in blank
   var tabla=celda.parentNode.parentNode;
   var celdas=tabla.getElementsByTagName("td");
   for(var i=0;i<celdas.length;i++)
   {
    if (celdas[i] != celda) 
	{
	 celdas[i].style.backgroundColor=colorCeldaInactiva;
     //celdas[i].firstChild.style.setAttribute("font-weight","normal");
	}
   }
}

function cambiar_color_clic_row(fila){
   fila.style.backgroundColor = colorCeldaActiva;
   //The rest of cells are kept in blank
   var tabla=fila.parentNode;
   var filas=tabla.getElementsByTagName("tr");
   for(var i=0;i<filas.length;i++)
   {
    if (filas[i] != fila) filas[i].style.backgroundColor=colorCeldaInactiva;
   }
}


function mostrar_boletines_cat(obj)
{
   cambiar_color_clic(obj.parentNode);
	/*var recep = document.getElementById("warning_msg");
	recep.innerHTML="";
	*/
	var resultado = 0;
	var area = document.getElementById("area3");
	if(area.firstChild){
	   area.removeChild(area.firstChild);
	}
      	var respuesta = http1.responseXML;
	itemsLevel1 = respuesta.getElementsByTagName("category");
      	for(c=0; c < itemsLevel1.length; c++){
	     name = itemsLevel1[c].getAttribute("name");
	     if(obj.name == name){
		if(itemsLevel1[c].hasChildNodes()){
		   var tabla = bulletins_table(itemsLevel1[c]);
		   area.appendChild(tabla);
		}
		else{
			//The category has not bulletins
                	notify(sprintf(_("The collector <b>%s</b> does not contain bulletins"), getText(obj));
			var cab_noticia= document.getElementById("cab_tabla_noticias");
			var area_noticia=document.getElementById("area5");
			cab_noticia.style.setAttribute("visibility","hidden");
   			area5.style.setAttribute("visibility","hidden");
		}
		break;
	    }
	}	
}
//Create a table with all the bulletins of a category
//obj references the category object
function bulletins_table(obj){
	var bulletins = obj.getElementsByTagName("bulletin");
 	//Primary header information
   	var headersData = new Array();
	var headersWidth = new Array();
	headersData[0] = "nombre";
	headersWidth[0] = "70";
   	//Accessing to the first bulleting header for the case in which all the bulletins are going to be organized in the same way. 
   	var headers = obj.getElementsByTagName("cabecera_boletin");
   	var muestraH = headers[0];
   	var childsH = muestraH.childNodes;
	var fraccion = parseInt(childsH.length);
	var ratio = 500/(fraccion);
	ratio = parseInt(ratio);
	var n = 1;
   	for(var h=0; h < childsH.length; h++){
             if(childsH[h].getAttribute("level") == "primary"){ 
	        headersData[n] = childsH[h].getAttribute("label");
		headersWidth[n] = ratio;
		n++;
   	    }
        }
	headersData[n] = "noticias";
	headersWidth[n] = "70";
 
    	//Table body
   	var rawData = new Array();
	var rawIdref = new Array();
   	for(var i=0; i < headers.length; i++){
	     rawData[i] = new Array();
	     var head = headers.item(i);
	     var abuelo = head.parentNode.parentNode;
	     rawData[i][0] = abuelo.getAttribute("name");
	     rawIdref[i] = abuelo.getAttribute("id");	
	     var childsHead = head.childNodes;
	     var p = 1;
	     for(var j=0; j < childsHead.length; j++){
		  if(childsHead[j].getAttribute("level") == "primary"){ 
		     var ItemJ = childsHead.item(j);
		     rawData[i][p++] = getText(ItemJ);
		  }
	     }
             var news = abuelo.getElementsByTagName("cuerpo_noticia");
	     if(news.length > 0){ 
	        rawData[i][p] = "ControlVer"; 
             }
	     else{
                rawData[i][p] = "Vacio"; 
             }
	}	
	 									 
	var tabla = create_table(headersData,headersWidth,rawData);
	var filas = tabla.getElementsByTagName("tr");
	for(var k=0;k<filas.length;k++)
	{
	 filas[k].setAttribute("onmouseover","cambiar_color_over(this)")
 	 filas[k].setAttribute("onmouseout","cambiar_color_out(this)")
	}
	var name = obj.getAttribute("name");
	tabla.setAttribute("name",name);
	tabla.setAttribute("width","100%");
	var cuerpo = tabla.getElementsByTagName("tbody");
	activate_row_table( rawIdref,cuerpo[0]);
	return tabla;
}

function create_table(headers, headerWidth,rawData){
 	var tabla = document.createElement('<table bgcolor="#ffffff">');
	var tablaH = document.createElement("<thead>");
	var fila = "<tr class='filaoscuranegrita'>";
   	var headerRow = document.createElement(fila);
	//headerRow.setAttribute("class","cabeceratabla");

	for(var h=0; h < headers.length; h++){
	     var header = document.createElement("<th>");
       	     header.setAttribute("width",headerWidth[h]);
	     var hcontent = document.createTextNode(headers[h]);
	     header.appendChild(hcontent);
	     headerRow.appendChild(header);
	}		 
	tablaH.appendChild(headerRow);
	tabla.appendChild(tablaH);
	var cuerpo = document.createElement("<tbody>");
	for(var i=0; i < rawData.length; i++){
	     var fila = "<tr class='filaclara'>";
	     var row = document.createElement(fila);
	     for(var j=0; j < rawData[i].length; j++){
		  var col = document.createElement("<td>");
		  var colContent = document.createTextNode(rawData[i][j]);
          col.appendChild(colContent);
		  row.appendChild(col);
	     }
	     cuerpo.appendChild(row);
	 }			
	 tabla.appendChild(cuerpo);
	 return tabla;
}
function show_bulletin_info(){
    cambiar_color_clic_row(this.parentNode.parentNode);
   var enlaceArea2 = document.getElementById("area4");
	 if(enlaceArea2.firstChild){
	    enlaceArea2.removeChild(enlaceArea2.firstChild);
	 }
	 
	 var idref = this.getAttribute("name");
	 var boletin = ximGetElementsByAttribute("bulletinLanguage","id",idref);
	 
	 var headers = new Array("PROPIEDAD","VALOR");
	 var headerWidth = new Array('30%','70%');
	 var rawData = new Array();
	 
	 var cabecera = boletin.firstChild.firstChild;
	 var childsCab = cabecera.childNodes;
	 for(var f=0; f < childsCab.length; f++){
	      rawData[f] = new Array();
				var itemA = childsCab.item(f);
				rawData[f][0] = itemA.getAttribute("name");
        rawData[f][1] = getText(itemA);
	}
	var tabla = create_table(headers, headerWidth,rawData);			
	enlaceArea2.appendChild(tabla);

}
function show_news_info(){ 
	/*var recep = document.getElementById("receptor");
        var cuerpo = recep.firstChild;
        var punto = cuerpo.firstChild.nextSibling;
	if(punto.getAttribute("name") == "warning"){
           cuerpo.removeChild(punto);
        } 
*/
   //Activating row from span
    /*cambiar_color_clic_row(this.parentNode.parentNode);*/

   	var enlaceArea5 = document.getElementById("area5");
        var titulo = enlaceArea5.parentNode.parentNode.previousSibling;
	if(enlaceArea5.firstChild){
	    enlaceArea5.removeChild(enlaceArea5.firstChild);
	}
	var idref = this.getAttribute("name");
	var boletin = ximGetElementsByAttribute("bulletinLanguage","id",idref);
	 
	//Accessing to all the bulletin news
	var noticias = boletin.getElementsByTagName("noticia");
	 
	//data for the news table header
	if(noticias.length > 0){
       var cabtablanoticias=document.getElementById("cab_tabla_noticias");
	   cabtablanoticias.innerHTML=_("&nbsp;News from bulletin: ")+boletin.getAttribute("name");

	   cabtablanoticias.style.setAttribute("visibility","visible");
	   var headersWidth = new Array();
	   headersWidth[0] = "100";
	   headersWidth[1] = "250";
	   headersWidth[2] = "250";
	   var headers = new Array();
	   headers[0] = "nombre";
	   headers[1] = "titulo";
	   headers[2] = "subtitulo";
           //data for news table content
           var rawData = new Array();
           for(var i=0; i < noticias.length; i++){
		rawData[i] = new Array();
		cuerpoNot = noticias.item(i).parentNode;
		rawData[i][0] = cuerpoNot.getAttribute("name");
		itemsA = cuerpoNot.getElementsByTagName("noticia_titular");
		rawData[i][1] = getText(itemsA[0]);
		itemsB = cuerpoNot.getElementsByTagName("noticia_entradilla_frase");
		rawData[i][2] = getText(itemsB[0]); 
           }
 	   var tabla = create_table(headers, headersWidth,rawData);	
	   tabla.setAttribute("width","100%");
       enlaceArea5.appendChild(tabla);
	   enlaceArea5.style.visibility = "visible";
	   titulo.style.visibility = "visible";
	}	
	else{
           notify(sprintf(_("The bulletin %s has not associated news"), boletin.getAttribute("name")));
	}	
}
 
function apply_styles(tabla){
   span = document.createElement("<span style='' >");
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


		      
//Each line of the table body is activated in order to activate in its first column the events onmouseover and onclick
// passing the object id information represented by the line. 

function activate_row_table(indices,cuerpo){
    	var rows = cuerpo.getElementsByTagName("tr");
    	for(var r=0; r < rows.length; r++){
	     var cols = rows[r].getElementsByTagName("td");
	     for(var c=0; c < cols.length; c++){
		  var content = getText(cols[c]);
		  var texto = document.createTextNode(content);
		  if(c > 0){
                     if(content == "ControlVer"){
                        span = document.createElement("<span style='font-size:10px;cursor:hand;' >");
		        span.setAttribute("name",indices[r]);
                        span.onclick = show_news_info;
                        texto = document.createTextNode("Ver »");
                     }
                     else{ 
		        span = document.createElement("<span style='font-size:10px; ' >");
                     }
		  }
		  else{
		       span = document.createElement("<span style='font-size:10px;cursor:hand;' >");
		       span.setAttribute("name",indices[r]);
		       span.onclick = show_bulletin_info;
		  }	 
		  span.appendChild(texto);
		  cols[c].removeChild(cols[c].firstChild);
		  cols[c].appendChild(span);
	      }			
	}
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

