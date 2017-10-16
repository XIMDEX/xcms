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


/*var colorCeldaActiva = "#dddddd";
var colorCeldaInactiva = "#FAFAFA";*/
var colorCeldaActiva = "#c0c0c0";
var colorCeldaOver = "#dddddd";
var colorCeldaInactiva = "#FAFAFA";

var elementoActivo;
var visor = new Array();
var lastImgAct = "desactivado";
var flagCondition = "Ver";
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
function show_LoteImagenes()
{
   	var nodeid = document.getElementById("nodoIDF").value;
     	var menu = "LoteImagenes";
	http1.open('get','../modules/ximNEWS/inc/BRWN_Adapter.php?tipo='+menu+'&nodeid='+nodeid);
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
function call_lote()
{
   	var menu = "imagenes";
   	var nodeID = document.getElementById("nodoIDF").value;
   	http1.open('get','../modules/ximNEWS/inc/BRWN_Adapter.php?tipo='+menu+'&nodeid='+nodeID+'&activaImg=2');
   	http1.onreadystatechange = presenta_images;
   	http1.send(null); 
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
			    }
			}		
		}
		http1.open('get','../modules/ximNEWS/inc/BRWN_Adapter.php?tipo='+menu+'&nodeid='+nodeID+'&activaImg=2');
		http1.onreadystatechange = presenta_images;
		http1.send(null);
}
function show_lotes2(nodeID,name)
{
		var menu = "imagenes";
		
		http1.open('get','../modules/ximNEWS/inc/BRWN_Adapter.php?tipo='+menu+'&nodeid='+nodeID);
		http1.onreadystatechange = presenta_images;
		http1.send(null);
}	
function presenta_images(){ 
 	if(http1.readyState == 4){			    
	   var respuesta = http1.responseText;
	   virtualContain.innerHTML = respuesta;
	   var recep =  document.getElementById('area2');
	   if(recep.hasChildNodes()){
	      //recep.firstChild.removeNode(true);
		  recep.removeChild(recep.firstChild);
	   }
	   //second table with the batch image list
	   vContain2.appendChild(virtualContain.lastChild);
	   recep.appendChild(vContain2.firstChild);		
           //tables with the thumbnails//////
	   //vContain1.appendChild(virtualContain.firstChild);
	   vContain1.innerHTML = virtualContain.innerHTML;
	}
}			
function getByName(escala){
    	var tablas = vContain1.getElementsByTagName("table");
    	for(var t=0; t < tablas.length; t++){
	     tablaT = tablas.item(t);
	     name = tablaT.getAttribute("name");
	     if(name == escala){
		return tablaT;
	     }
	}
}
function toggle_view(obj){
   var recep =  document.getElementById('area2');
	 var primo = obj.parentNode.nextSibling.firstChild;
	 var escala = primo.options[primo.selectedIndex].value;
	 var tabla = getByName(escala)
	 
   if(obj.value == "Imagen"){
	    obj.value = "Lista";
			vContain2.appendChild(recep.firstChild);
			//recep.appendChild(vContain1.firstChild);
			recep.appendChild(tabla);
	 }
	 else{
	   obj.value = "Imagen";
		 vContain1.appendChild(recep.firstChild);
		 recep.appendChild(vContain2.firstChild);
	 }		 
}
function changeView(obj)
{
	var recep =  document.getElementById('area2');
	var primo = obj.parentNode.nextSibling.firstChild;
	var escala = primo.options[primo.selectedIndex].value;
	var tabla = getByName(escala)
	var tipo = obj.options[obj.selectedIndex].value 
        if(tipo == "miniaturas"){
	   vContain2.appendChild(recep.firstChild);
	   //recep.appendChild(vContain1.firstChild);
	   recep.appendChild(tabla);
	}
	else{
	   vContain1.appendChild(recep.firstChild);
	   recep.appendChild(vContain2.firstChild);
	}		 
}

function changeViewR(obj) //chageView para RadioButtons
{
	var recep =  document.getElementById('area2');
	var oDivPreview = document.getElementById('area3');
	var oDivTitPreview = document.getElementById('tit_area3');
	var oDivTitPreview2 = document.getElementById('tit_area2');
	var primo = document.getElementById('selectScale');
    var escala="0.75";
    var primohijos=primo.getElementsByTagName('input');
	
    for(var i=0;i< primohijos.length ; i++)
	{
	 if(primohijos[i].checked ==true) escala=primohijos[i].value; 
	}
	var tabla = getByName(escala);
	var tipo = obj.value; 

    if(tipo == "miniaturas"){
	   if(recep.hasChildNodes())
	   {
	    vContain2.appendChild(recep.firstChild);
	    //recep.appendChild(vContain1.firstChild);
	    recep.appendChild(tabla);
	   }
	   oDivPreview.style.visibility="hidden";
       oDivTitPreview.style.visibility="hidden";
       oDivTitPreview2.style.visibility="hidden";
	}
	else{
	   if(recep.hasChildNodes())
	   {
	    vContain1.appendChild(recep.firstChild);
	    recep.appendChild(vContain2.firstChild);
	   }	
	   oDivPreview.style.visibility="visible";
  	   oDivTitPreview.style.visibility="visible";
  	   oDivTitPreview2.style.visibility="visible";
	}		 
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
   //the rest of cells are kept in blank
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

function view_image(obj){
   cambiar_color_clic(obj.parentNode);
  	var escalaObj = document.getElementById("selectScale");
    var escala='0.75';
	var escalahijos=escalaObj.getElementsByTagName('input');
    for(var i=0;i< escalahijos.length ; i++)
	{
	 if(escalahijos[i].checked ==true) escala=escalahijos[i].value; 
	}

	//var escala = escalaObj.options[escalaObj.selectedIndex].value;
	var tabla = getByName(escala);
        var texto = obj.innerHTML;
	var target = document.getElementById("area3");
	if(target.hasChildNodes()){
	   //target.firstChild.removeNode(true);
	  target.removeChild(target.firstChild);
	}
        var clon = getImageByName(tabla,texto);
	target.appendChild(clon);
}

function changeScale(obj){
   var primo = obj.parentNode.previousSibling.firstChild;
	 if(primo.value == "Imagen"){
	    var target = document.getElementById("area3");
			if(target.hasChildNodes()){
			   var texto =  target.firstChild.getAttribute("name");
			   target.firstChild.removeNode(true);
				 var escalaObj = document.getElementById("selectScale");
	       var escala = escalaObj.options[escalaObj.selectedIndex].value;
	       var tabla = getByName(escala);	
			   var images = tabla.getElementsByTagName("img");
	       for(var i=0; i < images.length; i++){
	            var image = images.item(i);
				      if(image.getAttribute("name") == texto){
				         var clon = image.cloneNode(true);
				         target.appendChild(clon);
					       break;
				      }	 
	      }
		  }			
	 }
}

function changeScale2(obj){
 	var primo = obj.parentNode.previousSibling.firstChild;
        var tipo = primo.options[primo.selectedIndex].value;
	var escala = obj.options[obj.selectedIndex].value;
	var tabla = getByName(escala);
	if(tipo == "miniaturas"){
           var recep =  document.getElementById('area2');  
	   vContain1.appendChild(recep.firstChild);
	   recep.appendChild(tabla);
	}
        else{
           var target = document.getElementById("area3");
	   if(target.hasChildNodes()){
              var texto = target.firstChild.getAttribute("name");
              target.firstChild.removeNode(true);
              var clon = getImageByName(tabla,texto);
	      target.appendChild(clon);
           }	 
     	}
}

function changeScaleR(obj){ //change of scale for RadioButtons
	var primo = document.getElementById('typeView');
    var primohijos=primo.getElementsByTagName('input');
    for(var i=0;i< primohijos.length ; i++)
	{
	 if(primohijos[i].checked ==true) tipo=primohijos[i].value; 
	}

    var escala=obj.value;

	var tabla = getByName(escala);
	if(tipo == "miniaturas"){
           var recep =  document.getElementById('area2');  
	   vContain1.appendChild(recep.firstChild);
	   recep.appendChild(tabla);
	}
        else{
           var target = document.getElementById("area3");
	   if(target.hasChildNodes()){
              var texto = target.firstChild.getAttribute("name");
				target.removeChild(target.firstChild);
              var clon = getImageByName(tabla,texto);
	      target.appendChild(clon);
           }	 
     	}
}


function getImageByName(tabla,name){
	var images = tabla.getElementsByTagName("img");
	for(var i=0; i < images.length; i++){
	     var image = images.item(i);
	     if(image.getAttribute("name") == name){
		return image.cloneNode(true);
	     }
        }
}
function initSelectImages()
{
	var imgs = document.getElementsByTagName('IMG');
	for(var no=0;no<imgs.length;no++){
		if(imgs[no].className=='thumb'){
			 imgs[no].setAttribute("onclick",'selectImage(this)');
		}		
	}	
}
/*
function selectImage(obj)
{
	var ultimo = point.lastChild;
	var clon = obj.firstChild.cloneNode();
	var valor = obj.lastChild.value;
	if(ultimo.tagName == "A" || ultimo.tagName == "IMG" ){
	   ultimo.removeNode(true);
	}
  point.appendChild(clon);	 
	point.firstChild.value = valor;
}
*/
function call_lotes(nodeid,flag)
{  
     var extension = "";
     if(flag == 1){
		    var extension = '&activa=1';
		 }
		 else if(flag == 2){
		    var extension = '&wizard=1';
		 }
     var menu = "LoteImagenes";
		 http1.open('get','../modules/ximNEWS/inc/BRWN_Adapter.php?tipo='+menu+'&nodeid='+nodeid+extension);
		 http1.onreadystatechange = presenta_LoteImg;
		 http1.send(null);
}

function search_image(obj){
  var padre = obj.parentNode;
	var abuelo = padre.parentNode;
	var siguiente = abuelo.nextSibling;
	var cuerpo = abuelo.parentNode;
  var fila = document.createElement("tr");
  var colspan="colspan='2'"
  var oriImg = document.getElementById("origen");
	if(obj.getAttribute("id") != lastImgAct){
	  lastImgAct = obj.getAttribute("id");
	  var columna1 = document.createElement("td");
	  var capa1 = document.createElement("<div>");
		capa1.setAttribute("id","area1");
		capa1.style.border="2px inset #cccccc";
		capa1.style.background="white";
		capa1.style.overflow="auto";
		capa1.style.width="150px";
		capa1.style.height="200px";
    capa1.innerHTML ='SUBARBOL';
	  columna1.appendChild(capa1);
	  fila.appendChild(columna1);
	  colspan = "";	
	  nodeid = oriImg.value;
	  call_lotes(nodeid,0);	
	
	  var columna2 = document.createElement("<td "+colspan+" >");
		var capa2 = document.createElement("<div>");
		capa2.setAttribute("id","area2");
		capa2.style.border="2px inset #cccccc";
		capa2.style.background="white";
		capa2.style.overflow="auto";
		capa2.style.width="320px";
		capa2.style.height="200px";

	  columna2.appendChild(capa2);		
	  fila.appendChild(columna2);
		
    cuerpo.insertBefore(fila,siguiente);	 
	}
	else{	
		siguiente.removeNode(true);
		lastImgAct = "desactivado";
  }
}


function activa_lote(obj){
     var texto = obj.innerHTML;
		 for(var o=0; o < elementoActivo.options.length; o++){
		      opcion = elementoActivo.options[o];
					name = opcion.innerHTML;
					if(texto == name){
					   elementoActivo.selectedIndex = o;
					}
		 }
}

function add_column1(fila){
     var columna1 = document.createElement("td");
		var capa1 = document.createElement("<div>");
		capa1.setAttribute("id","area1");
		capa1.style.border="2px inset #cccccc";
		capa1.style.background="white";
		capa1.style.overflow="auto";
		capa1.style.width="150px";
		capa1.style.height="200px";
		 capa1.innerHTML ='SUBARBOL';
		 columna1.appendChild(capa1);
		 fila.appendChild(columna1);
}
function add_column2(fila,colspan){
     var columna2 = document.createElement("<td " +colspan+" >");
		var capa1 = document.createElement("<div>");
		capa2.setAttribute("id","area2");
		capa2.style.border="2px inset #cccccc";
		capa2.style.background="white";
		capa2.style.overflow="auto";
		capa2.style.width="320px";
		capa2.style.height="200px";

		 columna2.appendChild(capa2);		
		 fila.appendChild(columna2);
}		 		
function asociar_img(obj){

     if(flagCondition == "Ver"){
		    flagCondition = "Ocultar";
		    var oriImg = document.getElementById("origen");
        var padre = obj.parentNode;
	      var abuelo = padre.parentNode;
	      var siguiente = abuelo.nextSibling;
	      var cuerpo = abuelo.parentNode;
        var colspan = "";
		    var fila = document.createElement("tr");
		    add_column1(fila);		
		    add_column2(fila,colspan); 
		    cuerpo.insertBefore(fila,siguiente);
		    visor[2] = fila;
		    nodeid = oriImg.value;
		    call_lotes(nodeid,2);
		 }
		 else{
		   flagCondition = "Ver";
			 visor[2].removeNode(true);
		 }

}
function select_visor(obj,orden){
     
     var oriImg = document.getElementById("origen");
     var padre = obj.parentNode;
		 var combo = padre.firstChild;
		 elementoActivo = combo;
		 var indice = combo.selectedIndex;
		 var valor = combo.options[indice].value;
		
		 if(obj.value == "Ver"){
	      obj.value = "Ocultar";
				 
				switch(orden){
				    case 0:
						  resultado = img_nav0(valor);
				      break;
				    case 1:
						  resultado = img_nav1(obj,valor);
				      break;
						case 2:
						  resultado = img_nav2(obj);
				      break;	
						case 3:
						  resultado = img_nav3(obj);
				      break;	
				}
		    visor[orden] = resultado;
		    nodeid = oriImg.value;
				if(valor == 0){
		       call_lotes(nodeid,1);
				}	 	
		    if(valor > 0){
		       show_lotes1(valor,"anulado");
		    }	 
		 }
		 else{
	         obj.value = "Ver"; 
				   //deleting created line
				   visor[orden].removeNode(true);
		 }		
}
function img_nav2(obj){
     var oriImg = document.getElementById("origen");
     var padre = obj.parentNode;
	   var abuelo = padre.parentNode;
	   var siguiente = abuelo.nextSibling;
	   var cuerpo = abuelo.parentNode;
     var colspan = "";
		 var fila = document.createElement("tr");
		 add_column1(fila);		
		 add_column2(fila,colspan); 
		 cuerpo.insertBefore(fila,cuerpo.lastChild);
		 return fila;
}
function img_nav0(valor){
     var recep = document.getElementById("receptor");
     var oriImg = document.getElementById("origen");
     var cuerpo = recep.firstChild;

		 var fila = document.createElement("tr");
		 var colspan="colspan='2'"
		 if(valor == 0){ 
		    add_column1(fila);
			  colspan = "";	
		 }		
		 add_column2(fila,colspan); 
		 cuerpo.insertBefore(fila,cuerpo.lastChild);
		 return fila;
}

function wrapp_fila(fila){
     //table of one row with two cols inside a colum in a row of the receptor table
		 //la tabla receptora		
		 var filaC = document.createElement("tr");
		 var columnaC = document.createElement("td");
		 var tablaC = document.createElement("<table align='center'>");
		 var cuerpoC = document.createElement("tbody");
		 cuerpoC.appendChild(fila);
		 tablaC.appendChild(cuerpoC);
		 columnaC.appendChild(tablaC);
		 filaC.appendChild(columnaC);
		 return filaC;
}
function img_nav1(obj,valor){
     
     //access to the table body, where the viewer is going to be added in a line
     var recep = document.getElementById("receptor");
		 var cuerpo = recep.firstChild;
     var fila = document.createElement("tr");
		 var colspan="colspan='2'"
		 if(valor == 0){ 
		    add_column1(fila);
			  colspan = "";	
		 }		
		 add_column2(fila,colspan); 
		 //transforms a row of two columns in one colum by a table
		 var filaN = wrapp_fila(fila);
		//Inserting the viewer before the next line
		 cuerpo.insertBefore(filaN,cuerpo.lastChild);
		 return filaN;
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
		var ref = document.getElementById("refer");
		var abuelo = ref.parentNode.parentNode;
		var cuerpo = abuelo.parentNode;
		var hermano = abuelo.nextSibling;
		
		var fila = document.createElement("tr");
		var columna1 = document.createElement("<td valign='top' >");
		var capa1 = document.createElement("<div>");
		capa1.setAttribute("id","area1");
		capa1.style.border="2px inset #cccccc";
		capa1.style.background="white";
		capa1.style.overflow="auto";
		capa1.style.width="100px";
		capa1.style.height="80px";

		capa1.appendChild(tabla);
		columna1.appendChild(capa1);
		fila.appendChild(columna1);
		
		var columna2 = document.createElement("<td >");
		var capa2 = document.createElement("<div>");
		capa2.setAttribute("id","area2");
		capa2.style.border="2px inset #cccccc";
		capa2.style.background="white";
		capa2.style.overflow="auto";
		capa2.style.width="520px";
		capa2.style.height="200px";

		columna2.appendChild(capa2);		
		fila.appendChild(columna2);		
		cuerpo.insertBefore(fila,hermano);
}							 

