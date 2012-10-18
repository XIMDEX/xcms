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


//FUNCTION "DISPATCHER" TO EACH ON OF BROWSER ACTIONS

function accion_browser(command,area)
{
 area="area2"; //TEMPORAL MEASSURE. IN THE FUTURE THE ACTION WILL BE ALSO EXECUTED ON AREA 1.

 switch(command)
 {
	case 'addtocolector':
		mostrar_colectores(nodeid,userid,ximnews,area);
	break;
	case 'addtoarea':
		mostrar_areas(nodeid,userid,ximnews,area);
	break;
	case 'addximlet':
		mostrar_ximlets();
	break;
	case 'deletenode':
		obtener_dependencias();
	break;
	case 'modifynodeproperties':
		mostrar_propiedades(nodeid,userid,ximnews,area);
	break;
	case 'movenode':
		obtener_arbol();
	break;
	case 'publicatesection':
		obtener_servidores();
	break;

 }
}

//FUNCTIONS SHARED BY ALL ACTIONS

function recopilar_nodos(area)
{
	var nodoid="";	
	var valor=0;
	var nombre_doc="";
	var limite_nodos = 0;
	nodos = $(area).getElementsByTagName("INPUT");
	limite_nodos = nodos.length;
	xml="<nodos>";
	/*
	There are two hidden input in the td of node of right panel with different name, but when it parses the array it catchs the same name for both but doing an alert of $(area).innerHTML is shown with different name it is because integer value is passed and if it is not text takes the value. 
	*/
	for(var i=0;i<limite_nodos;i++)
	{
		valor = parseInt(nodos[i].value,10);
		if(!isNaN(valor)){ 
			nodoid=nodos[i].value;
			nombre_doc=nodos[i].parentNode.nextSibling.innerHTML;
			xml=xml+ "<nodo id='"+nodoid+"' name='"+nombre_doc+"' />"
		}
	}
	xml=xml+"</nodos>";
	return xml;
}

function abrir_intermedio()
{
 my_winH = document.body.offsetHeight;
 my_winW = document.body.offsetWidth;	

 var intermedio=$('browser_intermedio');
 intermedio.style.visibility="visible";
//intermedio.style.height="200px";

 var capas=intermedio.getElementsByTagName('DIV');
 var limite_capas = capas.length;
 intermedio.style.visibility="visible";
 for(i=0;i<limite_capas;i++){
	 capas[i].style.visibility="visible";
 }

 $("area3").style.height = parseInt(my_winH/3);
}

function cerrar_intermedio()
{
//Closes the iframe of dictionary values if it is not closed and close propierty menu
//if (typeOf(document.getElementById('multivaluada') == 'undefined')
if($('multivaluada')!=null)
{
   $('multivaluada').style.visibility = 'hidden';
}

 var intermedio=$('browser_intermedio');
 intermedio.style.visibility="hidden";
 //intermedio.style.height="5px";
 var capas=intermedio.getElementsByTagName('DIV');
 for(i=0;i<capas.length;i++)
 {
	 capas[i].style.visibility="hidden";
 }
// parent.parent.frames['toolbar'].borraBotones();
// parent.parent.frames['toolbar'].SetSelectedNode(nodeid);
}
