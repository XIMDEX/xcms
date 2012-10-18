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

/*
  # - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -  
  # Framework: ximDEX
  #
  # Modulo: ximPORTA
  # Autor: Diego Gómez. (Javascript)
  # Tipo: módulo ajdunto a ximEDITOR.
  # - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -  
  # Tarea   : js. de equivalencias entre HTML y XML de ximDEX
  # - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -  

*/
  
var eqv = new Array();
var tabla = false;
var claseparrafo = "textojustificado";
var ximStyle = "clase";
var clase = "normal";
var f_estilo = 1;

function consulta_etiqueta(la_etiqueta){
	switch (la_etiqueta){
		case "hr":
			la_etiqueta = "separador";
			return la_etiqueta;
			break;		
		case "P":
			
			la_etiqueta = "parrafo";
			return la_etiqueta;
			break;
		case "TABLE":
			la_etiqueta = "tabla";
			tabla = true;
			return la_etiqueta;
			break;
		case "TBODY":
			la_etiqueta = "cuerpo-tabla";
			tabla = true;
			return la_etiqueta;
			break;
		case "THEAD":
			la_etiqueta = "cabecera-tabla";
			tabla = true;
			return la_etiqueta;
			break;
		case "TH":
			la_etiqueta = "elemento";
			tabla = true;
			return la_etiqueta;
			break;
		case "TR":
			la_etiqueta = "fila";
			return la_etiqueta;
			break;
		case "TD":
			la_etiqueta = "elemento";
			return la_etiqueta;
			break;
		case "LI":
			la_etiqueta = "item";
			return la_etiqueta;
			break;
		case "UL":
			la_etiqueta = "lista";
			return la_etiqueta;
			break;
		case "OL":
			la_etiqueta = "lista";
			return la_etiqueta;
			break;
		case "SPAN":
			la_etiqueta = "estilo";
			return la_etiqueta;
			break;
		case "FONT":
			la_etiqueta = "estilo";
			return la_etiqueta;
			break;
		case "B":
			la_etiqueta = "estilo";
			return la_etiqueta;
			break;
		case "STRONG":
			la_etiqueta = "estilo";
			return la_etiqueta;
			break;
		case "EM":
			la_etiqueta = "estilo";
			return la_etiqueta;
			break;
		case "DIV":
			la_etiqueta = "parrafo";
			return la_etiqueta;
			break;
		case "I":
			la_etiqueta = "estilo";
			return la_etiqueta;
			break;
		case "U":
			la_etiqueta = "estilo";
			return la_etiqueta;
			break;
		case "H1":
			la_etiqueta = "subtitulo";
			return la_etiqueta;
			break;
		case "H2":
			la_etiqueta = "subtitulo";
			return la_etiqueta;
			break;
		case "H3":
			la_etiqueta = "subtitulo";
			return la_etiqueta;
			break;
		case "H4":
			la_etiqueta = "subtitulo";
			return la_etiqueta;
			break;
		case "H5":
			la_etiqueta = "subtitulo";
			return la_etiqueta;
			break;
		case "BR":
			la_etiqueta = "salto_parrafo";
			return la_etiqueta;
			break;
		case "A":
			la_etiqueta = "enlace";
			return la_etiqueta;
			break;
		case "IMG":
			la_etiqueta = "imagen";
			return la_etiqueta;
			break;
		case "shape":
			la_etiqueta = "estilo";
			return la_etiqueta;
			break;
		case "CENTER":
			la_etiqueta = "parrafo";
			return la_etiqueta;
			break;
		case "BLOCKQUOTE":
			la_etiqueta = "bloque_sangria";
			return la_etiqueta;
			break;
		}

		
		la_etiqueta = "estilo";
		return la_etiqueta;
}

function define_clase(el_objeto){
var clase_def = new Array();
clase_def[0] = "";
clase_def[1] = "negrita";
clase_def[2] = "cursiva";
clase_def[3] = "subrayado";

var cs3 = el_objeto.childNodes;

if (cs3[0]){
if (cs3[0].tagName){
	var cs4 = cs3[0].childNodes;
	f_estilo = 2;
	if(cs4[0]){
	if (cs4[0].tagName) { f_estilo = 3;};}
	}
}

cl1 = tipo(el_objeto.tagName);
if (f_estilo == 1){
	if (cl1 == 0) return "normal";
	else return "texto" + clase_def[cl1];
	}
else{
	if (f_estilo == 2){
		cl2 = tipo(cs3[0].tagName);
		if (cl2){
			if (cl1 < cl2) return "texto" + clase_def[cl1] + clase_def[cl2];
			else return "texto" + clase_def[cl2] + clase_def[cl1];
			}
		else {
			f_estilo = 1;
			return "texto" + clase_def[cl1];
			}
		}
	
	else{

		cl2 = tipo(cs3[0].tagName);
		cl3 = tipo(cs4[0].tagName);
		if (!cl2 && cl3)
				{
				cl2 = cl3;
				if (cl1 < cl2) return "texto" + clase_def[cl1] + clase_def[cl2];
				else return "texto" + clase_def[cl2] + clase_def[cl1];
				}
		if (cl2 && cl3) return "texto" + clase_def[1] + clase_def[3] + clase_def[2];
			
		}
	}

}

function tipo(el_objeto){
switch (el_objeto){
	case "B":
		cl = 1;
		return cl;
		break;
	case "STRONG":
		cl = 1;
		return cl;
		break;
	case "I":
		cl = 2;
		return cl;
		break;
	case "EM":
		cl = 2;
		return cl;
		break;
	case "U":
		cl = 3;
		return cl;
		break;
	}
	cl = 0;
	return cl;
}

/* 
	función que estable las propiedades de la tabla en XML
	objeto: objeto creado para insertar el nuevo nodo XML 
	objetoHTML: objeto que contiene las propieades en HTML
*/
function prop_tabla(objeto, objetoHTML){
		objeto.setAttribute("clase", "tabla");
		objeto.setAttribute("ancho", "500");
		objeto.setAttribute("salto", "no");
		objeto.setAttribute("espacio", objetoHTML.getAttribute("cellSpacing"));
		objeto.setAttribute("alin", "center");
		objeto.setAttribute("sumario", "");
		}

		
/* 
	función que estable las propiedades de celda de la Tabla
	objeto: objeto creado para insertar el nuevo nodo XML 
	objetoHTML: objeto que contiene las propieades en HTML
*/

function prop_elemento(objeto, objetoHTML){
	
	filas = objetoHTML.getAttribute("rowSpan");
	columnas = objetoHTML.getAttribute("colSpan");
	if (!filas) filas = "1";
	if (!columnas) columnas = "1";
	alin = objetoHTML.getAttribute("align");
	if (!alin) alin = "left";
	posicion = objetoHTML.getAttribute("vAlign");
	s_comilla(objetoHTML.innerText);
/*if (objetoHTML.childNodes(0).nodeValue) {
	var xmlDoc = new ActiveXObject("Msxml2.DOMDocument");
	newNode = xmlDoc.createNode(1, "parrafo", "");
	objeto.appendChild(newNode);
	objeto.childNodes(0).appendChild(MyText);
	}*/
	//objeto.setAttribute("texto", "");
	objeto.setAttribute("alin", alin);
	objeto.setAttribute("columnas", columnas);
	objeto.setAttribute("texto", "");
	objeto.setAttribute("filas", filas);
	objeto.setAttribute("clase", "filaclara");
	objeto.setAttribute("posicion", posicion);
}
/*

*/
function limpia_enlace(cadena){
	delimiter = "#";
	tempArray=new Array(1);
	var Count=0;
	var tempString=new String(cadena);
	if(tempString.indexOf(delimiter) == 0){
		cadena = tempString.substr(1,tempString.length);
		tempArray[0] = cadena;
	}
	else if (tempString.indexOf(delimiter) < 0){
		tempArray[0] = "";
		tempArray[1] = tempString;
	}
	else{
		while (tempString.indexOf(delimiter)>0) {
	    tempArray[Count]=tempString.substr(0,tempString.indexOf(delimiter));
		tempString=tempString.substr(tempString.indexOf(delimiter)+delimiter.length, tempString.length-tempString.indexOf(delimiter)); 
	    Count=Count+1;
		}
		tempArray[Count]=tempString;
		
	}
	return tempArray;
}

/* 
	función que estable las propiedades de un enlace
	objeto: objeto creado para insertar el nuevo nodo XML 
	objetoHTML: objeto que contiene las propieades en HTML
*/


function prop_enlace(objeto, objetoHTML){
	enlace = objetoHTML.getAttribute("href");
	lacadena = limpia_enlace(enlace);
	if (lacadena.length == 1){
		objeto.setAttribute("referenciador", lacadena[0]);
		}
	else {
		objeto.setAttribute("url", lacadena[1]);
		objeto.setAttribute("referenciador", lacadena[0]);
	} 
	referenciador = objetoHTML.getAttribute("target");
	nombre = objetoHTML.getAttribute("name");
	if (!referenciador) referenciador = "_self";
	else if (referenciador == "_blank") referenciador = "nueva";
	else referenciador == referenciador;
	if (nombre) objeto.setAttribute("referencia", nombre);
	else objeto.setAttribute("referencia", "")
	
	//objeto.setAttribute("a_enlaceid_url", enlace);
	objeto.setAttribute("ventana", referenciador);
	
	objeto.setAttribute(ximStyle, "enlacenegrita");
}


/* 
	función que estable las propiedades de una lista
	objeto: objeto creado para insertar el nuevo nodo XML 
	objetoHTML: objeto que contiene las propieades en HTML
*/
function prop_lista (objeto, objetoHTML){
	if (objetoHTML.tagName == "OL") return "numerica";
	tipolista = objetoHTML.getAttribute("type");
	if (!tipolista) return "marcas";
	else return "marcas";
}

function ajusta_propiedades(e_xml, newNode, objetoHTML){
clase="normal";
f_estilo = 1;
//alert(e_xml + " " + e_xml.indexOf("elemento"));
	switch (e_xml){
		case "subtitulo":
			clase = "subtitulo";
			return;
			break;
		case "tabla":
			prop_tabla (newNode, objetoHTML);
			return;
			break;
		case "enlace":
			prop_enlace (newNode, objetoHTML);
			return;
			break;
		case "lista":
			newNode.setAttribute("tipo", prop_lista (newNode, objetoHTML));
			return;
			break;
		case "estilo":
			clase = define_clase(objetoHTML);
			return;
			break;
		case "tabla":
			prop_tabla (newNode, objetoHTML);
			return;
			break;
		case "parrafo":
			clase = "normal";
			return;
			break;
		case "elemento":
			prop_elemento (newNode, objetoHTML);
			return;
			break;
			
	}
	if (e_xml.indexOf("elemento") >= 0){
		prop_elemento (newNode, objetoHTML);
		return;
	}
	if (e_xml != "fila" && e_xml != "salto_parrafo" && e_xml !="enlace") newNode.setAttribute(ximStyle, clase);
	
}

function crea_lista(e_xml, nodoG, xmlDoc){
	if(e_xml == "lista"){
		newNode = xmlDoc.createNode(1, "cuerpo-lista", "");
		nodoG.item(nodoG.length-1).appendChild(newNode);
		lista = true;
		}
}
function crea_imagen(e_xml, nodoG, xmlDoc, objetoHTML){
	
	if (e_xml == "imagen"){
		newNode = xmlDoc.createNode(1, e_xml, "");
		//alineación
			if (objetoHTML.getAttribute("align")) newNode.setAttribute("align", objetoHTML.getAttribute("align"));
			else newNode.setAttribute("align", "default");
		//enlace y referenciador
			//if (objetoHTML.getAttribute("enlace")) newNode.setAttribute("a_enlaceid_enlace", objetoHTML.getAttribute("enlace"));
			//else 
			newNode.setAttribute("a_enlaceid_enlace", "");
			newNode.setAttribute("referenciador", "");
		//ancho
			if (objetoHTML.getAttribute("width")) newNode.setAttribute("ancho", objetoHTML.getAttribute("width"));
			else newNode.setAttribute("ancho", "");
		// alto
			if (objetoHTML.getAttribute("height")) newNode.setAttribute("alto", objetoHTML.getAttribute("height"));
			else newNode.setAttribute("alto", "");
		// borde
			if (objetoHTML.getAttribute("border")) newNode.setAttribute("borde", objetoHTML.getAttribute("border"));
			else newNode.setAttribute("borde", "0");
		// texto alternativo
			if (objetoHTML.getAttribute("alt")) newNode.setAttribute("texto", objetoHTML.getAttribute("alt"));
			else newNode.setAttribute("texto", "");
		// espacio horizontal
		if (objetoHTML.getAttribute("hspace")) newNode.setAttribute("espacio_horizontal", objetoHTML.getAttribute("hspace"));
			else newNode.setAttribute("espacio_horizontal", "");
		//espacion vertical
		if (objetoHTML.getAttribute("vspace")) newNode.setAttribute("espacio_vertical", objetoHTML.getAttribute("vspace"));
			else newNode.setAttribute("espacio_vertical", "");
		
		nodoG.item(nodoG.length-1).appendChild(newNode);
		newNode = xmlDoc.createNode(1, "url", "");

			nodoG.item(nodoG.length-1).childNodes.item(0).appendChild(newNode);
			cadena = objetoHTML.getAttribute("src");
			if (cadena.indexOf("file:///") == 0){
				var cadena2 = "";
				for (l=8;l<=cadena.length;l++) {
					cadena2 = cadena2 + cadena.charAt(l);
				}
				cadena = cadena2;
			}
			MyText = xmlDoc.createTextNode(cadena);
			
			nodoG.item(nodoG.length-1).childNodes.item(0).childNodes.item(0).appendChild(MyText);
		}

}


/*
<imagen align="default" texto="" a_enlaceid_enlace="" referenciador="_self" ancho="" alto="" espacio_horizontal="" espacio_vertical="" borde="">
		<url>3905</url>
</imagen>
*/
