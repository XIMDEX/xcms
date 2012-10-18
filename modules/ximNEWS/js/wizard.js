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


var array_activos = new Array();
var enlaces = new Array();
var grupo;
var plantillaSelected;
var titulosEnlaces = new Array();
titulosEnlaces["images"] = "a_enlaceid_noticia_imagen_asociada";
titulosEnlaces["enlace"] = "a_enlaceid_noticia_enlace_asociado";
titulosEnlaces["archivos"] = "a_enlaceid_noticia_archivo_asociado";
titulosEnlaces["video"] = "a_enlaceid_noticia_video_asociado";
var orden = 0;
var virtualContain4 = document.createElement("div");
var vContain41 = document.createElement("div");
var vContain42 = document.createElement("div");
var conexion;
var handler;
var recep;
var lastNodeID;
var traduccion = new Array();
traduccion["ind10000"] = "HOLA";
var tablaIdiomas = new Array();

function idiomizarDato(entradas,titulo)
{
		 for(var e=0; e < entradas.length; e++){
			var entrada = entradas.item(e);
			var name = entrada.getAttribute("name");
			var value = entrada.getAttribute("value");
			entrada.setAttribute("name",titulo+"_" + name);
			name = entrada.getAttribute("name");
			// alert(name);
         }
}
function agrupaIdiomas(objID)
{
	var obj = document.getElementById(objID);
        var tablas = obj.getElementsByTagName("table");
        for(var t=0; t < tablas.length; t++){
             var tabla = tablas.item(t);
             var titulo = tabla.getAttribute("grupo"); 
	     tablaIdiomas[titulo] = tabla.getAttribute("name");
             var entradas1 = tabla.getElementsByTagName("input");
             idiomizarDato(entradas1,titulo); 
             var entradas2 = tabla.getElementsByTagName("textarea");
             idiomizarDato(entradas2,titulo); 
             var entradas3 = tabla.getElementsByTagName("select");
             idiomizarDato(entradas3,titulo); 
        } 

} 
function rolloverSolapa()
{
	if(this.className.indexOf('tabInactive2')>=0){
	   this.className='inactiveTabOver2';
	}
}
function rolloutSolapa()
{
	if(this.className ==  'inactiveTabOver2'){
	   this.className='tabInactive2';
	}
}
function createSolapa(Title)
{
	var span = document.createElement('DIV');
	span.onclick="mostrarEnlaces(this);" ;
	// width="100%" onclick="mostrarEnlaces(this);" 
        span.onmouseover = rolloverSolapa;
        span.onmouseout = rolloutSolapa;
        span.className='tabInactive2';
	span.innerHTML = Title;
        return span;
}	
							
function createEnlaceItem(nodo,k)
{
	var span = document.createElement('DIV');
	//width="100%"
        var ind = "ind"+nodo; 
        var name = traduccion[ind];
        var input = document.createElement('INPUT');
        input.setAttribute("type","hidden");
        input.setAttribute("name",titulosEnlaces[k]+"[]");
        input.value = nodo;
        var span1 = document.createElement('span');
        span1.innerHTML = traduccion[ind];
        span.appendChild(input);
        span.appendChild(span1);
        var img = document.createElement('IMG');
		img.setAttribute("src","../xmd/images/botones/limpiar.gif");
		img.setAttribute("border","0");
		img.setAttribute("name",k);
		img.onclick = function(){
			var name = this.getAttribute("name");
			enlaces[grupo][name][0] = 0;
			actualizaEnlaces();
		};
        span.appendChild(img);
        return span;
}

function setDynamicData()
{	
	for (var name in enlaces[grupo]){
	      value = enlaces[grupo][name][0];
              name = grupo+"_"+name;
              setCookie(name, value, 0, 0, 0, 0);	  
            //  alert("COOKIE " + name + "VALUE " + value);              
 	}
        var grupos = "";
        for(var g in enlaces){
             grupos +=g+"/";
        }
        setCookie("enlaces", 1, 0, 0, 0, 0); 
	setCookie("grupos", grupos, 0, 0, 0, 0);
}
function actualizaEnlaces2()
{
	var fila = document.createElement("TR");
 	fila.setAttribute("name","filaEnlaces");
        var col1 = document.createElement("TD");
	col1.setAttribute("class","filaoscuranegrita");
        var col2 = document.createElement("TD");
	col2.setAttribute("class","filaclara");
	for (var k in enlaces[grupo]){
              var span = createSolapa(k);
              col1.appendChild(span);
              var cont = document.createElement("DIV");
              cont.setAttribute("id",k+grupo+"_idf");
              for(var v=0; v < enlaces[grupo][k].length; v++){
                   var span1 = createEnlaceItem(enlaces[grupo][k][v],k);
                   cont.appendChild(span1);
              } 
              if( plantillaSelected == k){
                  span.className='tabActive2';
                  cont.style.display = "block";
              }
	      else{
                  span.className='tabInactive2'; 
 		  cont.style.display = "none";
              }
              col2.appendChild(cont);
        }
        fila.appendChild(col1);
 	fila.appendChild(col2);
        //handler.appendChild(fila);
        if(conexion.nextSibling){
           if(conexion.nextSibling.getAttribute("name") == 'filaEnlaces'){
              handler.removeChild(conexion.nextSibling);
           } 
           handler.insertBefore(fila,conexion.nextSibling);
        } 
	else{
           if(handler.lastChild.getAttribute("name") == 'filaEnlaces'){
              handler.removeChild(handler.lastChild);
           } 
           handler.appendChild(fila); 
        }
}
function actualizaEnlaces3()
{
	if(conexion.nextSibling.getAttribute("name") == 'filaEnlaces'){
           var filas = GetElementsByAttribute(handler,"tr","name",'filaEnlaces');
           for(var t=0; t < filas.length; t++){ 
                handler.removeChild(conexion.nextSibling);
           }
	} 
	for (var k in enlaces[grupo]){
              if(enlaces[grupo][k] > 0){
	      var fila = document.createElement("TR");
	      fila.name = 'filaEnlaces';
              var col1 = document.createElement("TD");
	      col1.className = "filaoscuranegrita";
              var col2 = document.createElement("TD");
	      col2.className = "filaoscuranegrita";
              var span = createSolapa(k);
              col1.appendChild(span);
              var cont = document.createElement('DIV');
	      //width="100%"	
              cont.setAttribute("id",k+grupo+"_idf");
              var span1 = createEnlaceItem(enlaces[grupo][k][0],k);
              cont.appendChild(span1);
              //span.className='tabActive2';
              cont.style.display = "block";
              col2.appendChild(cont);
              fila.appendChild(col1);
 	      fila.appendChild(col2);
	      handler.insertBefore(fila,conexion.nextSibling);
              }	
        }
}
	
function actualizaEnlaces()
{
	var estilo = conexion.getAttribute("simple");
        if(estilo == "1"){
           actualizaEnlaces3();
        }
        else{
           actualizaEnlaces2();
        } 
	
}
function mostrarEnlaces(obj)
{
        var padre = obj.parentNode;
        var hijos = padre.getElementsByTagName("div");
        for(var h=0; h < hijos.length; h++){
             var hijo = hijos.item(h);
             hijo.className='tabInactive2'; 
             var puntero = getText(hijo);
             var capa = document.getElementById(puntero+grupo+"_idf");
             capa.style.display = "none";
        }
        obj.className='tabActive2';
        puntero = getText(obj);
        capa = document.getElementById(puntero+grupo+"_idf");
        capa.style.display = "block";
}
function frameTree(tipo,obj)
{
	var WindowObjectReference = new Array();
	WindowObjectReference["ID"] = 0;
 	//adding idemxime
 	var args = new Array();
	arr = null;
	//end adding idemxime
 
 	if(ie)
 	{
  	//adding idemxime
		if (obj.value != "") args["url"] = obj.value;
		    //args["nodeid"] =Xnodeparent;
		    if (args["nodeid"] = '') args["nodeid"]= '10001'; 
                    if(tipo == "video") tipo = "archivos";
		        WindowObjectReference = showModalDialog( "../actions/xmleditor/inc/"+tipo+".php","args","font-family:Verdana; dialogWidth:630px; dialogHeight:420px;");
       //end adding idemxime
       }
       	
       if (typeof WindowObjectReference != "undefined"){
           return WindowObjectReference["ID"];
       }
       else{
           return 0;
       }	
       
}
function select2(obj,tipo)
{
	//icon span
	var padre = obj.parentNode;
        //toolbar column
        padre = padre.parentNode;
        //toolbar row
        padre = padre.parentNode
        conexion = padre;
	//table body
	handler = padre.parentNode;
        grupo = handler.parentNode.getAttribute("grupo"); 
        var nodeID = frameTree(tipo,obj);
	if(nodeID > 0){
		lastNodeID = "ind"+nodeID;    
		selectNodeID2(nodeID);
        	getDataNode(nodeID);
	}
        //setDynamicData();
}
function selectLink(obj)
{
        plantillaSelected = "enlace";
	select2(obj,plantillaSelected);
}
function selectFile(obj)
{
        plantillaSelected = "archivos";
	select2(obj,plantillaSelected);
}
function selectVideo(obj)
{
        plantillaSelected = "video";
	select2(obj,plantillaSelected);
}
function selectImage(obj)
{
	plantillaSelected = "images";
        select2(obj,plantillaSelected);
}

function selectNodeID2(nodeID)
{
        if(enlaces[grupo] == undefined){
           enlaces[grupo] =  new Array();
           enlaces[grupo][plantillaSelected] = new Array();
        }
        if(enlaces[grupo][plantillaSelected] == undefined){
           enlaces[grupo][plantillaSelected] = new Array();
        }
        //enlaces[grupo][plantillaSelected].push(nodeID);
 	enlaces[grupo][plantillaSelected][0] = nodeID;
}
	
function getDataNode(nodeID)
{
	var url = "../modules/ximNEWS/inc/BRWN_Adapter.php";
	var pars = "tipo=dataNodeID&nodeid="+nodeID;
	var ajax = new Ajax.Request( url, {parameters: pars,method:"get",onComplete: getDataParser});
}	
function getDataParser(resp){ 			    
	var respuesta = resp.responseText;
	traduccion[lastNodeID] = respuesta;
	actualizaEnlaces();
}
function insertMore(obj)
{
		var clase = obj.getAttribute("name");
		var myClase = new String(clase);
		name = myClase.replace("_MULTIPLICITY","");
	  var inc = obj.getAttribute("inc");
		var padre = obj.parentNode;
		var abuelo = padre.parentNode;
		var clonAbuelo = abuelo.cloneNode(true);
		clonAbuelo.lastChild.firstChild.innerHTML = "";
		var base = abuelo.parentNode;
		
		var tioAbuelo = abuelo.nextSibling;
		if(tioAbuelo != "undefined"){
		   base.insertBefore(clonAbuelo,tioAbuelo);
		}
		else{
		  base.appendChild(clonAbuelo);
		}
		padre.removeChild(obj);
		
		padre.firstChild.setAttribute("name",inc+name);
		inc++;
		clonAbuelo.lastChild.firstChild.setAttribute("name",inc+name);
		clonAbuelo.lastChild.lastChild.setAttribute("inc",inc);
}
function HasAttribute(elemento,attr){
	var found = 0;
	attrs = elemento.attributes;
	for(var a=0; a < attrs.length; a++){
	     nombre = attrs[a].name;
	     if(nombre == attr){
                found  = 1;
		break;
	     }		
	}
	return found;
}
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
function mostrarCookies()
{
	   
        /*
        if(getCookie("enlaces")){
           var cadGrupos = getCookie("grupos");      
           var grupos = cadGrupos.split("/");
           for(var g=0; g < grupos.length -1 ; g++){  
                for(var k in titulosEnlaces){
                     name = grupos[g]+"_"+k; 
                     anterior = getCookie(name);
                     if(anterior > 0){
                        enlaces[grupo][k] = anterior;
                        //alert("GRUPO " + grupos[g] + " K " + k + " VALOR " + anterior);
                     } 
                }
           }
           //actualizaEnlaces3();
        } 
	*/
 	
}
/**
 * Sets a Cookie with the given name and value.
 *
 * name       Name of the cookie
 * value      Value of the cookie
 * [expires]  Expiration date of the cookie (default: end of current session)
 * [path]     Path where the cookie is valid (default: path of calling document)
 * [domain]   Domain where the cookie is valid
 *              (default: domain of calling document)
 * [secure]   Boolean value indicating if the cookie transmission requires a
 *              secure transmission
 */
function setCookie(name, value, expires, path, domain, secure) {
    document.cookie= name + "=" + escape(value) +
        ((expires) ? "; expires=" + expires.toGMTString() : "") +
        ((path) ? "; path=" + path : "") +
        ((domain) ? "; domain=" + domain : "") +
        ((secure) ? "; secure" : "");
}

/**
 * Gets the value of the specified cookie.
 *
 * name  Name of the desired cookie.
 *
 * Returns a string containing value of specified cookie,
 *   or null if cookie does not exist.
 */
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

/**
 * Deletes the specified cookie.
 *
 * name      name of the cookie
 * [path]    path of the cookie (must be same as path used to create cookie)
 * [domain]  domain of the cookie (must be same as domain used to create cookie)
 */
function deleteCookie(name, path, domain) {
    if (getCookie(name)) {
        document.cookie = name + "=" +
            ((path) ? "; path=" + path : "") +
            ((domain) ? "; domain=" + domain : "") +
            "; expires=Thu, 01-Jan-70 00:00:01 GMT";
    }
}


	
