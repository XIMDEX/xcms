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
  # Tarea   : función que recorre un objeto HTML y transforma sus nodos en XML
  # - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -  

*/


depura = false;
function limpia_cajas(){
if (depura){
	mitexto2.value = "";
	mitexto.value = "";
	}
}


function pega_codigo(){
		var my_var = document.createDocumentFragment();
		my_var = tbContentElement.DOM.body;
		
		my_var.innerHTML = limpia(my_var.innerHTML);
		
		// comentar para ximDEX
		if (depura) mitexto2.value = my_var.innerHTML;

		
		// paso los nodos en los que se compone el código HTML
		cs = my_var.childNodes;
		

		// creo el objeto XML
		var xmlDoc = new ActiveXObject("Msxml2.DOMDocument");
		var root;
		var newNode;
		xmlDoc.async = false;
		xmlDoc.resolveExternals = false; 
		

		xmlDoc.appendChild(xmlDoc.createElement("cuerpo-ximPORTA"));
		root = xmlDoc.documentElement;

		for (i = 0; i < cs.length; i++){
			lista = false;
			if (cs[i].nodeValue){
				e_xml = "parrafo";
				newNode = xmlDoc.createNode(1, e_xml, "");
				newNode.setAttribute(ximStyle, claseparrafo);
				root.appendChild(newNode);
				
				MyText = xmlDoc.createTextNode(cs[i].nodeValue);
					if (root.childNodes.item(i)) root.childNodes.item(i).appendChild(MyText);
					else if (root.childNodes.item(i-1)) root.childNodes.item(i-1).appendChild(MyText);
					else root.childNodes.item(root.childNodes.length-1).appendChild(MyText);
				}
			else{
				if (cs[i].childNodes.length == 0) {
					anadir_nodo(cs[i], xmlDoc, root, i);
					}
				if (cs[i].childNodes.length > 0)
					{

					anadir_nodo(cs[i], xmlDoc, root, i);
					
					cs2 = suma_nodo(cs[i].childNodes);
					if(!root.childNodes.item(i)) {el_nodo2 = limpia_nodo(root.childNodes.item(i-1));}
					else el_nodo2 = limpia_nodo(root.childNodes.item(i));
					if (!el_nodo2) {
					
						}
					for (j = 0; j < cs2.length; j++)
						{
						if (cs2[j].nodeValue) anadir_texto(cs2[j], xmlDoc, root);
						else anadir_nodo(cs2[j], xmlDoc, el_nodo2, j);
						cs3 = suma_nodo(cs2[j].childNodes);
						
						
						// if (!el_nodo2.childNodes.item(j)) {alert(j);}
						if (el_nodo2) el_nodo3 = limpia_nodo(el_nodo2.childNodes.item(j));
						else {
							if (cs3.length == 0) {}
							//el_nodo3 = limpia_nodo(el_nodo2.childNodes.item(0));
							}
						for (k = 0; k < cs3.length; k++){
							
							if (cs3[k].nodeValue) anadir_texto(cs3[k], xmlDoc, el_nodo2);
							else anadir_nodo(cs3[k], xmlDoc, el_nodo3, k);
							
							cs4 = suma_nodo(cs3[k].childNodes);
							el_nodo4 = limpia_nodo(el_nodo3.childNodes.item(k));
							
							for (l = 0; l < cs4.length; l++){
								
								if (cs4[l].nodeValue) anadir_texto(cs4[l], xmlDoc, el_nodo3);
								else anadir_nodo(cs4[l], xmlDoc, el_nodo4, l);
								
								cs5 = suma_nodo(cs4[l].childNodes);
								el_nodo5 = limpia_nodo(el_nodo4.childNodes.item(l));
								
								for (m = 0; m < cs5.length; m++){
									
									if (cs5[m].nodeValue) anadir_texto(cs5[m], xmlDoc, el_nodo4);
									else anadir_nodo(cs5[m], xmlDoc, el_nodo5, m);
									
									cs6 = suma_nodo(cs5[m].childNodes);
									el_nodo6 = limpia_nodo(el_nodo5.childNodes.item(m));
									
									for (n = 0; n< cs6.length; n++){
										
										if (cs6[n].nodeValue) anadir_texto(cs6[n], xmlDoc, el_nodo5);
										else anadir_nodo(cs6[n], xmlDoc, el_nodo6, n);
										
										cs7 = suma_nodo(cs6[n].childNodes);
										el_nodo7 = limpia_nodo(el_nodo6.childNodes.item(n));

										for (o = 0; o < cs7.length; o++){
											
											if (cs7[o].nodeValue) anadir_texto(cs7[o], xmlDoc, el_nodo6);
											else anadir_nodo(cs7[o], xmlDoc, el_nodo7, o);
											
											cs8 = suma_nodo(cs7[o].childNodes);
											el_nodo8 = limpia_nodo(el_nodo7.childNodes.item(o));
											
											for (p = 0; p < cs8.length; p++){
											
												if (cs8[p].nodeValue) anadir_texto(cs8[p], xmlDoc, el_nodo7);
												else anadir_nodo(cs8[p], xmlDoc, el_nodo8, p);
												
												cs9 = suma_nodo(cs8[p].childNodes);
												el_nodo9 = limpia_nodo(el_nodo8.childNodes.item(p));
											
												for (q = 0; q < cs9.length; q++){
											
													if (cs9[q].nodeValue) anadir_texto(cs9[q], xmlDoc, el_nodo8);
													else anadir_nodo(cs9[q], xmlDoc, el_nodo9, q);
												
													}
												}
												
											}
										}
									}
								}
							
							
							
							}
						}
					}
					
			}
		

		
		

	}
	

		if (depura){
		mitexto.value = "";
		xmlDoc = limpia_XML(xmlDoc);
		xmlDoc = limpia_XML(xmlDoc);
		mitexto.value = xmlDoc.xml;
		}
		if (!depura){
		xmlDoc = limpia_XML(xmlDoc);
		xmlDoc = limpia_XML(xmlDoc);
		var arr = new Array();
		arr["xml_content"] = xmlDoc.xml;
		window.returnValue = arr;
		window.close();
		}

}
function limpia_XML(objetoXML){
var xmlDocT = new ActiveXObject("Msxml2.DOMDocument");
var el_nodo = objetoXML.childNodes.item(0).childNodes;

for (a = 0; a < el_nodo.length; a++){
if (el_nodo(a).text == '' || el_nodo(a).text == ' ' || el_nodo(a).text == ' '){
	if(el_nodo(a).tagName == "enlace"){
			newNode = xmlDocT.createNode(1, "parrafo", "");
			newNode.setAttribute(ximStyle, "normal");
			newNode2 = xmlDocT.createNode(1, "enlace", "");
			newNode2.setAttribute("clase", el_nodo(a).getAttribute("clase"));
			newNode2.setAttribute("referencia", el_nodo(a).getAttribute("referencia"));
			newNode2.setAttribute("ventana", el_nodo(a).getAttribute("ventana"));
			newNode2.setAttribute("referenciador", el_nodo(a).getAttribute("referenciador"));
			MyText2 = xmlDocT.createTextNode("[referenciador]");
			
			newNode2.appendChild(MyText2);
			newNode.appendChild(newNode2);
			el_nodo(a).parentNode.replaceChild(newNode, el_nodo(a));
	}
	else {el_nodo(a).parentNode.removeChild(el_nodo(a));}
	
	}
			if (el_nodo(a)){
			if (!el_nodo[a].nodeValue) {
				el_nodo2 = el_nodo(a).childNodes;
				for (b = 0; b < el_nodo2.length; b++){
					
					b = limpia_fin(el_nodo(a), el_nodo2(b), b);
					
//					alert(el_nodo2.length);
					
					if (b<0 && el_nodo2.length == 0){
						if (el_nodo(a)) el_nodo2 = el_nodo(a).childNodes;
						else return objetoXML;
						
						if (el_nodo2(0)) el_nodo3 = el_nodo2(0).childNodes;
						else return objetoXML;
						}
					else if (el_nodo2.length == 0 && a < el_nodo.length-1) {a++; el_nodo2 =  el_nodo(a).childNodes;}
					else if (b<0){ b++;el_nodo3 = el_nodo2(b).childNodes;}
					else if (el_nodo2(b)) el_nodo3 = el_nodo2(b).childNodes;
//					else alert(el_nodo(a).xml);
						for (c = 0; c < el_nodo3.length; c++){
							
							c = limpia_fin(el_nodo2(b), el_nodo3(c), c);
							if (c<0) {c = 0; }
							
							if (el_nodo3(c)) el_nodo4 = el_nodo3(c).childNodes;
							else if (el_nodo3.length == 0 && b < el_nodo2.length)
								{
								b++;
								if (el_nodo2(b)) el_nodo3 = el_nodo2(b).childNodes;
								else {
									a++; el_nodo2 =  el_nodo(a).childNodes;
									b = 0;
									el_nodo3 = el_nodo2(b).childNodes;
									
									}
								}
							if (el_nodo3(c)){
								for (d = 0; d < el_nodo4.length; d++){
								
									
	
									d = limpia_fin(el_nodo3(c), el_nodo4(d), d);
									if (d< 0) d = 0;
									el_nodo5 = el_nodo4(d).childNodes;
									
									for (e = 0; e < el_nodo5.length; e++){
										e = limpia_fin(el_nodo4(d), el_nodo5(e), e);
										if (e < 0) e = 0;
										
										if (el_nodo5(e)){
										el_nodo6 = el_nodo5(e).childNodes;
											for (f = 0; f < el_nodo6.length; f++){
												
												f = limpia_fin(el_nodo5(e), el_nodo6(f), f);
												if(el_nodo6(f)){
												el_nodo7 = el_nodo6(f).childNodes;
													for (g = 0; g < el_nodo7.length; g++){
													g = limpia_fin(el_nodo6(f), el_nodo7(g), g);
													
													if (el_nodo7(g)){
														el_nodo8 = el_nodo7(g).childNodes;
														for (h = 0; h < el_nodo8.length; h++){
															h = limpia_fin(el_nodo7(g), el_nodo8(h), h);
															}
														}
													}
													}
												}
											}
										}
									}
								}
							}
						
					}
				}
			
			}
}
	return objetoXML;
}

function limpia_fin(nodo1, nodo2, num){

var xmlDocT = new ActiveXObject("Msxml2.DOMDocument");
	if (nodo2.text == '' || nodo2.text == ' '){ if(nodo2.tagName){if (nodo2.tagName != "salto_parrafo"){ if (nodo2.tagName.indexOf("elemento") < 0){ nodo1.removeChild(nodo2);  return num--;}}}}
	if (nodo1){
	if (nodo1.tagName == "parrafo") {
		
		if(nodo1.text == '') {
				//alert(nodo1.parentNode.childNodes.item(0).xml);
				if (num<0) return; 
				if (nodo1.tagName.indexOf("elemento") < 0) nodo1.parentNode.removeChild(nodo1.parentNode.childNodes.item(num));
				
				}
		if (!nodo1.childNodes.item(0)) return num;
		
		if (nodo1.childNodes.item(0).tagName){
			if (nodo1.childNodes.item(0).tagName == "lista"){
				
				nodo1.parentNode.replaceChild(nodo1.childNodes.item(0), nodo1);
				return num--;
			}
			if (nodo1.childNodes.item(0).tagName == "tabla"){
				nuevo_nodo = nodo1.childNodes.item(0);
				
				nodo1.parentNode.replaceChild(nuevo_nodo, nodo1);
				return num--;
			}
			
				if (nodo1.childNodes.item(0).tagName == "estilo" && nodo1.childNodes.item(0).getAttribute("clase") == "normal" || nodo1.childNodes.item(0).getAttribute("clase") == "textojustificado"){
						nodo1.replaceChild(nodo1.childNodes.item(0).childNodes.item(0), nodo1.childNodes.item(0));
					}
				}
		
		// excepcion map.es
		if (nodo1.parentNode.tagName == "enlace") {
			nodo1.parentNode.replaceChild(nodo1.childNodes.item(0), nodo1);
			}
		}
		
	if (nodo1.tagName == "item" || nodo1.tagName == "subtitulo") {
		if (nodo1.childNodes.item(0).tagName){
			if ((nodo1.childNodes.item(0).tagName == "estilo" && nodo1.childNodes.item(0).getAttribute("clase") == "normal") || (nodo1.childNodes.item(0).tagName == "estilo" && nodo1.childNodes.item(0).getAttribute("clase") == "texto")){
					for (longi = 0; longi < nodo1.childNodes.item(0).childNodes.length; longi++){
						nodo1.appendChild(nodo1.childNodes.item(0).childNodes.item(longi));
					}
					if(nodo1.childNodes.item(0).text == "") nodo1.removeChild(nodo1.childNodes.item(0));
					//nodo1.replaceChild(nodo1.childNodes.item(0).childNodes.item(0), nodo1.childNodes.item(0));
				}
			}
		}
	if (nodo1.tagName == "imagen"){
		if (nodo1.childNodes.item(0).tagName == "imagen") nodo1.parentNode.replaceChild(nodo1.childNodes.item(0), nodo1);
		
	}
	if (nodo1.tagName){
	if (nodo1.tagName.indexOf("elemento") >= 0) {
			if (nodo1.childNodes.item(0)){
			if (nodo1.childNodes.item(0).nodeValue){
				newNode = xmlDocT.createNode(1, "parrafo_celda", "");
				MyText = xmlDocT.createTextNode(nodo1.childNodes.item(0).nodeValue);
				nodo1.removeChild(nodo1.childNodes.item(0));
				newNode.setAttribute(ximStyle, "normal");
				nodo1.appendChild(newNode);
				nodo1.childNodes.item(0).appendChild(MyText);
			}
			else if (nodo1.childNodes.item(0).tagName == "enlace"){
				newNode = xmlDocT.createNode(1, "parrafo_celda", "");
				newNode.setAttribute(ximStyle, "normal");
				newNode.setAttribute("referencia", nodo1.childNodes.item(0).getAttribute("referencia"));
				Nnodo = nodo1.childNodes.item(0);
				nodo1.removeChild(nodo1.childNodes.item(0));
				newNode.appendChild(Nnodo);
				nodo1.appendChild(newNode);
			}
			else if (nodo1.childNodes.item(0).tagName == "estilo"){
				newNode = xmlDocT.createNode(1, "parrafo_celda", "");
				newNode.setAttribute(ximStyle, nodo1.childNodes.item(0).getAttribute("clase"));
				Nnodo = nodo1.childNodes.item(0);
				//alert(Nnodo.childNodes.item(0).xml);
				nodo1.removeChild(nodo1.childNodes.item(0));
				newNode.appendChild(Nnodo.childNodes.item(0));
				nodo1.appendChild(newNode);
			}
			else if (nodo1.childNodes.item(0).tagName == "parrafo"){
				coll = nodo1.childNodes.item(0).childNodes
				Nnodo = '<parrafo_celda clase="' + nodo1.childNodes.item(0).getAttribute("clase") + '">';
				for (D = 0; D < coll.length; D++){
					Nnodo = Nnodo + coll(D).xml
					}
				Nnodo = Nnodo + "</parrafo_celda>";
				var temporal = new ActiveXObject("Msxml2.DOMDocument");
				temporal.loadXML(Nnodo);
				
				nodo1.removeChild(nodo1.childNodes.item(0));
				nodo1.appendChild(temporal.childNodes.item(0));
				
			}
			else if (nodo1.childNodes.item(0).tagName == "subtitulo"){
				coll = nodo1.childNodes.item(0).childNodes
				Nnodo = '<parrafo_celda clase="' + nodo1.childNodes.item(0).getAttribute("clase") + '">';
				for (D = 0; D < coll.length; D++){
					Nnodo = Nnodo + coll(D).xml
					}
				Nnodo = Nnodo + "</parrafo_celda>";
				var temporal = new ActiveXObject("Msxml2.DOMDocument");
				temporal.loadXML(Nnodo);
				
				nodo1.removeChild(nodo1.childNodes.item(0));
				nodo1.appendChild(temporal.childNodes.item(0));
				
			}
		}
		else {
			newNode = xmlDocT.createNode(1, "parrafo_celda", "");
			newNode.setAttribute(ximStyle, "normal");
			MyText2 = xmlDocT.createTextNode(nodo1.text);
			newNode.appendChild(MyText2);
			nodo1.appendChild(newNode);			
		}
	}
	}
	if (nodo1.tagName == "estilo") {
		if (nodo1.childNodes.item(0).tagName){
			if (nodo1.childNodes.item(0).tagName == "estilo"){
					nodo1.replaceChild(nodo1.childNodes.item(0).childNodes.item(0), nodo1.childNodes.item(0));
				}
			if (nodo1.childNodes.item(0).tagName == "enlace"){
					nodo1.parentNode.replaceChild(nodo1.childNodes.item(0), nodo1);
				}
			}
			
		if(nodo1.parentNode.tagName != "parrafo" && nodo1.parentNode.tagName != "item" && nodo1.parentNode.tagName != "enlace" && nodo1.parentNode.tagName != "parrafo_celda"){
			newNode = xmlDocT.createNode(1, "parrafo", "");
			newNode.setAttribute(ximStyle, "normal");
			if (nodo1.getAttribute("clase") != "normal") {
				newNode2 = xmlDocT.createNode(1, "estilo", "");
				newNode2.setAttribute("clase", nodo1.getAttribute("clase"));
				newNode2.setAttribute("referencia", "");
				MyText2 = xmlDocT.createTextNode(nodo1.text);
				newNode.appendChild(newNode2);
				newNode2.appendChild(MyText2);
				}
			else {
				MyText2 = xmlDocT.createTextNode(nodo1.text);
				newNode.appendChild(MyText2);
				}
			nodo1.parentNode.replaceChild(newNode, nodo1);
			}
		if (nodo1.getAttribute("clase") == "normal"){
				if (nodo1.parentNode) nodo1.parentNode.replaceChild(nodo1.childNodes.item(0), nodo1);
				}
		}
	if (nodo1.tagName == "lista" && nodo1.parentNode.tagName){
		if (nodo1.parentNode.tagName == "item"){
			nodo1.parentNode.parentNode.appendChild(nodo1);
			//nodo1.parentNode.removeChild(nodo1);
			}
		}
	if (nodo1.tagName == "enlace") {
		if(nodo1.parentNode.tagName != "parrafo" && nodo1.parentNode.tagName != "item" && nodo1.parentNode.tagName != "parrafo_celda")
			{
			newNode = xmlDocT.createNode(1, "parrafo", "");
			newNode.setAttribute(ximStyle, "normal");
			newNode2 = xmlDocT.createNode(1, "enlace", "");
			newNode2.setAttribute("clase", nodo1.getAttribute("clase"));
			newNode2.setAttribute("referencia", nodo1.getAttribute("referencia"));
			newNode2.setAttribute("ventana", nodo1.getAttribute("ventana"));
			newNode2.setAttribute("referenciador", nodo1.getAttribute("referenciador"));
			MyText2 = xmlDocT.createTextNode(nodo1.text);
			
			newNode2.appendChild(MyText2);
			newNode.appendChild(newNode2);
			nodo1.parentNode.replaceChild(newNode, nodo1);
			}
			
			/*if (nodo1.childNodes.item(0).tagName == "parrafo" && nodo1.childNodes.item(0).childNodes.item(0).tagName == "estilo") {
			nodo1.parentNode.replaceChild(nodo1.childNodes.item(0).childNodes.item(0), nodo1.childNodes.item(0));
			}*/
		
	}
	return num;
	}
}

function limpia_nodo(nodoXML){
	if (!lista)	nodoXML = nodoXML;
	else nodoXML = nodoXML.childNodes.item(0);
	lista = false;
	return nodoXML;
}

function suma_nodo(nodoHTML){
	if (f_estilo == 1) return nodoHTML;
	if (f_estilo == 2) {f_estilo = 1; return nodoHTML[0].childNodes;}
	if (f_estilo == 3) {f_estilo = 1; return nodoHTML[0].childNodes(0).childNodes;}
//	return nodoHTML.childNodes;
	
}

var f_elem = false;
function anadir_nodo(nodoHTML, xmlDoc, nodoXML, num){

	e_xml = consulta_etiqueta(nodoHTML.tagName);
	/*if (e_xml == "tabla" && document.ximP.activo.checked) {
		if(nodoHTML.childNodes.length == 1){
			if(nodoHTML.childNodes.item(0).childNodes.length == 1){
				if(nodoHTML.childNodes.item(0).childNodes.item(0).childNodes.length == 1){
					if(nodoHTML.childNodes.item(0).childNodes.item(0).childNodes.item(0).childNodes)
						 if(nodoHTML.childNodes.item(0).childNodes.item(0).childNodes.item(0).childNodes.item(0).tagName == "SPAN"){
							
								nodoHTML.replaceChild(nodoHTML.childNodes.item(0).childNodes.item(0).childNodes.item(0).childNodes.item(0), nodoHTML.childNodes.item(0));
								tabla = false;
								e_xml = "subtitulo";
							
					}
				}
			}
		}
	}*/
	if (e_xml =="fila"){my_longi = 0;
		for (am = 0; am < nodoHTML.childNodes.length; am ++){
			if (nodoHTML.childNodes.item(am).tagName == "TD") my_longi++;
		}
		 e_xml = e_xml + my_longi;}
	if (e_xml =="elemento"){
		if (num == 0){ e_xml = e_xml + (num+1); f_elem = false}
		else{
			if (!nodoXML.childNodes.item(num-1).tagName.indexOf("elemento") && !f_elem) e_xml = e_xml + (num+1);
			else {e_xml = e_xml + num; f_elem = true;}
			}
		}
	
	newNode = xmlDoc.createNode(1, e_xml, "");
	
	// miro la clase 
	ajusta_propiedades(e_xml, newNode, nodoHTML);
	if (e_xml != "enlace") newNode.setAttribute(ximStyle, clase);
	
	if (e_xml =="enlace"){
		if (!newNode.getAttribute("url") && newNode.getAttribute("referencia")!= ""){
			if (nodoXML.tagName == "parrafo" || nodoXML.tagName == "subtitulo")	nodoXML.setAttribute("referencia", newNode.getAttribute("referencia"));
			
			else if (nodoXML.parentNode.tagName == "parrafo" || nodoXML.parentNode.tagName == "subtitulo")	nodoXML.parentNode.setAttribute("referencia", newNode.getAttribute("referencia"));
			}
	}
	if (!nodoXML) {alert("El código HTML introducido presenta errores de sintáxis"); alert(nodoHTML.parentNode.innerHTML);	 }

	nodoXML.appendChild(newNode)

	//creo una lista
	crea_lista(e_xml, nodoXML.childNodes, xmlDoc);
	
	//creo una imagen
	crea_imagen(e_xml, nodoXML.childNodes, xmlDoc, nodoHTML);
}
function anadir_texto(nodoHTML, xmlDoc, nodoXML){
	MyText = xmlDoc.createTextNode(nodoHTML.nodeValue);
	if (nodoXML.childNodes)	nodoXML.childNodes.item(nodoXML.childNodes.length-1).appendChild(MyText);
	//else alert(nodoXML.xml);
}
