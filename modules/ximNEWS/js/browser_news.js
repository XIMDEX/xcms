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

//Visor de Noticias
function load_news()
{
	var nodeid = document.getElementById("nodoIDF").value;
	var url = "'../../inc/BRWN_Adapter.php";
	var pars = "tipo=noticias&nodeid="+nodeid;
	var ajax = new Ajax.Request( url, {parameters: pars,method:"get",onComplete: list_news});
}
function list_news(resp)
{
	var respuesta = resp.responseText;
	/*
	   var headersWidth = new Array();
	   headersWidth[0] = "100";
	   var headers = new Array();
	   headers[0] = "nombre";
	   var base = noticias.item(0);
	   var lista = base.getElementsByTagName('*');
	   var items = filter1(lista);
	   var fraccion = parseInt(items.length);
	   var ratio = 500/(fraccion);
	   ratio = parseInt(ratio);
	   var n = 1;
           for(var h=0; h < items.length; h++){ 
	        headers[n] = items[h];
		headersWidth[n] = ratio;
		n++;
	   }		
           //News table body data
           var rawData = new Array();
	   var rawIdref = new Array();
           for(var i=0; i < noticias.length; i++){
		rawData[i] = new Array();
		base = noticias.item(i);
		lista = base.getElementsByTagName('*');
		rawData[i][0] = base.getAttribute("name");
		var itemsA = filter2(lista);
		nv= 1;
		for(var v=0; v < itemsA.length; v++){
		     rawData[i][nv] = itemsA[v];
		     nv++;
		}			
		rawIdref[i] = noticias.item(i).getAttribute("nodeid");	
           }
	   var tabla = create_table(headers, headersWidth,rawData);	
           tabla.setAttribute("width",560);
	   var cuerpo = tabla.getElementsByTagName("tbody");
	   activate_row_table(rawIdref,cuerpo[0]);
         */
	   var plugin = document.getElementById("area1");
           plugin.innerHTML = respuesta;					
}

function create_table(headers, headerWidth,rawData){
   var tabla = document.createElement('<table>');
	 var tablaH = document.createElement("<thead>");
	 var fila = "<tr class='cabeceratabla'>";
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
				//row.setAttribute("class","filaoscuranegrita");
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
								   span = document.createElement("<span style='font-size:12px; ' >");
								}
								else{
					         span = document.createElement("<span style='font-size:12px;cursor:hand;' >");
					         span.setAttribute("name",indices[r]);
					         //span.onmouseover = show_news_info;
			                 span.onclick = show_news_info;
								}	 
					      span.appendChild(texto);
								cols[c].removeChild(cols[c].firstChild);
					      cols[c].appendChild(span);
					}			
		}
}
function show_news_info(){
   //highlight the whole row
   var tabla=document.getElementById("area1").firstChild;
   var filas=tabla.getElementsByTagName("tr");
   for(var i=1;i<filas.length;i++)
   {
    if(filas[i].firstChild.firstChild==this)
	{
     filas[i].className="filaoscuranegrita";
	}
	else
	{
	 filas[i].className="filaclara"; 
	} 
   }
   
   //shows news
   var enlaceArea2 = document.getElementById("area2");
	 if(enlaceArea2.firstChild){
	    enlaceArea2.removeChild(enlaceArea2.firstChild);
	 }
	 
	 var idref = this.getAttribute("name");
	 
	 var noticia = ximGetElementsByAttribute("cuerpo_noticia","nodeid",idref);
	 
	 var headers = new Array("PROPIEDAD","VALOR");
	 var headerWidth = new Array('60','140');
	 var lista = noticia.getElementsByTagName('*');
	 var rawData = filter3(lista);
	 
	 var tabla = create_table(headers, headerWidth,rawData);			
	 enlaceArea2.appendChild(tabla);
     enlaceArea2.style.setAttribute("visibility","visible");
}

function cerrar_ventana(){
	//It allows to hide the window clicking out of the table
	var enlaceArea2 = document.getElementById("area2");
     enlaceArea2.style.setAttribute("visibility","hidden");
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
function filter1(lista){
    var resultado = new Array();
		for(var ls=0; ls < lista.length; ls++){
			    elemento = lista[ls];
				  if(HasAttribute(elemento,'boletin')){			 
			       boletin = elemento.getAttribute("boletin");
			       if(boletin == "yes"){
			          indice = resultado.length;
				        resultado[indice] = elemento.getAttribute("label");
		        }
	        }	
		}
		return resultado;
}		

function filter2(lista){
    var resultado = new Array();
		for(var ls=0; ls < lista.length; ls++){
			    elemento = lista[ls];
				  if(HasAttribute(elemento,'boletin')){			 
			       boletin = elemento.getAttribute("boletin");
			       if(boletin == "yes"){
			          indice = resultado.length;
				        resultado[indice] = getText(elemento);
		        }
	        }	
				
		}
		return resultado;
}
function translate(value){
   var resultado;
   switch(value){
	 
	   case "noticia_fecha":
		    resultado = "Fecha";
				break;
     case "a_enlaceid_noticia_imagen_asociada":
		    resultado = "Imagen";
				break;
     case "a_enlaceid_noticia_video_asociado":
		    resultado = "Video";
				break;
     case "a_enlaceid_noticia_archivo_asociado": 
		    resultado = "Archivo" ;
				break;
     case "a_enlaceid_noticia_enlace_asociado":
		    resultado = "Enlace";
				break; 
	 
	 }
   return resultado;
}
function filter3(lista){
   var resultado = new Array();
	 for(var ls=0; ls < lista.length; ls++){
			 elemento = lista[ls];
       if(HasAttribute(elemento,"type")){
			    type = elemento.getAttribute("type");
			    if(type == "attribute"){
			       //Accessing to nodeMap with the list of attribute node
			       attrs = elemento.attributes;
				     for(var i=0; i < attrs.length; i++){
				         name = attrs.item(i).name;
							   indice = resultado.length;
								 resultado[indice] = new Array();
								 if(name != "type"){								   
										resultado[indice][0] = translate(name);
									  resultado[indice][1] = elemento.getAttribute(name);
							   }	
				     }		
		      }
	        else if(type == "text" || type == "textarea"){
				     name = elemento.getAttribute("name");
			       var label;
				     if(HasAttribute(elemento,"label")){
				        label = elemento.getAttribute("label");
				     }
				     else{
				        label = name;
				     } 
				     indice = resultado.length;
						 resultado[indice] = new Array();
				     resultado[indice][0] = label;
			       resultado[indice][1] = getText(elemento);
		      }	
	     }
	 } 
	 return resultado; 
}	
			 
