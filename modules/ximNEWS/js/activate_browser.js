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


var colorCeldaActiva = "#c0c0c0";
var colorCeldaOver = "#dddddd";
var colorCeldaInactiva = "#FAFAFA";
var ultima = 0;
var itemsLevel1;
var tabContainer;


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
   //el resto de celdas se dejan en blanco
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
   //The rest of cells will be in blank
   var tabla=fila.parentNode;
   var filas=tabla.getElementsByTagName("tr");
   for(var i=0;i<filas.length;i++)
   {
    if (filas[i] != fila) filas[i].style.backgroundColor=colorCeldaInactiva;
   }
}
/*
function activar_categorias(area)
{	  
	var categorias = area.getElementsByTagName("td");
	for(var c=0 ; c < categorias.length; c++){
	    var cat = categorias.item(c);
	    
       class='filaclara' 
       onmouseover='cambiar_color_over(this)' 
       onmouseout='cambiar_color_out(this)'
       cambiar_color_clic()    
       cat.firstChild 
       var categoryID = cat.firstChild.getAttribute("name");
       style='cursor:hand;' 
       onclick='show_bulletins(categoryID);
	}
}
		      
//Each line of the table body is activated in order to activate in its first column the events onmouseover and onclick
// passing the object id information represented by the line. 

function activar_boletines(area,indices,cuerpo){
	var rows = area.getElementsByTagName("tr");
   for(var r=0; r < rows.length; r++){
	    var cols = rows[r].getElementsByTagName("td");
	    for(var c=0; c < cols.length; c++){
		     var content = getText(cols[c]);
		     var texto = document.createTextNode(content);
		     if(c > 0){
              if(content == "ControlVer"){
                 span = document.createElement("<span style='font-size:10px;cursor:hand;' >");
		           span.setAttribute("name",indices[r]);
                 span.onclick = show_news;
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


function activar_noticias()
{	 
	//Accessing to all the bulletin news
	var noticias = boletin.getElementsByTagName("noticia");
	 
	//News table header data
   var cabtablanoticias=document.getElementById("cab_tabla_noticias");
	cabtablanoticias.innerHTML="&nbspNoticias del bolet&iacute;n: "+boletin.getAttribute("name");

	cabtablanoticias.style.setAttribute("visibility","visible");           
}
*/
