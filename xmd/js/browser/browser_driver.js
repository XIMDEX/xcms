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


var nodes = new Array();
var toolbar = new Array();
var accion_nodo;
var ximnews_aux = 0;
var nodeID;
var arr_comand = new Array();
var arr_menu = new Array();
	arr_menu['icon'] = new Array();
	arr_menu['actionid'] = new Array();
	arr_menu['name'] = new Array();
	arr_menu['command'] = new Array();
	arr_menu['activa'] = new Array();

function Obtener_TipoNodo(area,name)
{
	var arr_tipo = new Array();
	var tipo_nodos = $(area).getElementsByTagName("INPUT");
	var limite = tipo_nodos.length;
	for(var i=0;i<limite;i++)
	{
		if(tipo_nodos[i].name==name)
		{ 
			arr_tipo.push(tipo_nodos[i].value);
		}
	}
	return arr_tipo
}
//Si hay archivos de diferente tipo en el panel derecho desactiva la accion de mover nodo
function mover_nodos(area){
	var arr_tipo_nodo = new Array();
	var toolbaraux = parent.parent.frames['toolbar'].document.getElementById('selectedtoolbar');
	var acciones = toolbaraux.getElementsByTagName('img');
	arr_tipo_nodo = Obtencion_TipoNodo(area);
	if(arr_tipo_nodo.length>1){
		var limite = acciones.length;
		for(var i=0;i<limite;i++)
		{
			if(acciones[i].alt=='Mover nodo'){
				acciones[i].style.filter="alpha(opacity=30);"
			}
		}
	}
}
function Obtencion_TipoNodo(area)
{
	var arr_tipo = new Array();
	var tipo_nodos = $(area).getElementsByTagName("INPUT");
	var limite = tipo_nodos.length;
	var nombrenodo='';
	for(var i=0;i<limite;i++)
	{
		if(tipo_nodos[i].name=='node_type'){
			if(tipo_nodos[i].value != nombrenodo){
				nombrenodo = tipo_nodos[i].value;
				arr_tipo.push(tipo_nodos[i-1].value);
			}		
		}
	}
	return arr_tipo;
}
function Obtener_Menu(area,accion)
{
	parent.parent.frames['toolbar'].borraBotones();
	var nodos=$(area).getElementsByTagName("INPUT");
	var num_nodos = 0;
	var num_nodos_sel = 0;
	var aux='';
	var arr_tipo = new Array();
	var arr_nodos = new Array();
	var indice = 0;
	var arr_tipo_final = new Array();
	var arr_nodo_final = new Array();
	arr_tipo = Obtener_TipoNodo(area,"node_type");
	arr_nodos = Obtener_TipoNodo(area,"nodeID_sel");	
	num_nodos = arr_nodos.length;
	num_nodos_sel = nodos.length;
	accion_nodo = accion;
	if(num_nodos==0){
	   parent.parent.frames['toolbar'].borraBotones();
	   return 1;
	}
	 
	for(var i=0;i<num_nodos;i++){
		for(var j=0;j<num_nodos_sel;j++){
			 if (nodos[j].name=='nodeID_sel' && arr_nodos[i]==nodos[j].value){
				 if(arr_tipo[i] != aux){
					aux = arr_tipo[i];
					arr_tipo_final.push(aux);
					arr_nodo_final.push(arr_nodos[i]);
					indice++;
				 }
			 }
	   }
	}

	loadXmlFileMenu(arr_tipo_final,arr_nodo_final,accion);	  
	
return 1;
}

var nodes = new Array();
var toolbar = new Array;

function loadXmlFileMenu(nodetype,nodes,accion)
{
	//frequency: 3;
	var xmlDoc = XmlDocument.create();
	var sSrc = nodeUrl+"&method=toolbardata&nodes='+nodes+'&nodetype='+nodetype+"&userid="+ userid;
	if(nodes.length==1){
		xmlDoc.async = false;	
	}
	else{
		xmlDoc.async = true;
	}
	xmlDoc.resolveExternals = false;
	xmlDoc.onreadystatechange = function () {
		if (xmlDoc.readyState == 4) {
				parseXmlMenu(xmlDoc);
			}
		};
	// call in new thread to allow ui to update
	window.setTimeout(function () {
		xmlDoc.load(sSrc);
	}, 10);
	
	return 1;
}
function parseXmlMenu(xmlDoc)
{
	var arr_acciones = new Array();
	var arr_icon = new Array();
	var	arr_actionid = new Array();
	var	arr_name = new Array();
	var arr_command = new Array();
	var arr_activa = new Array();
	debug = 0;
//alert(xmlDoc.xml);
	if( xmlDoc == null || xmlDoc.documentElement == null)
	{
		alert('error al cargar la informacion del nodo');
	}	
	else
	{
		root = xmlDoc.documentElement;
		parentNode = root.getAttribute("nodeid");
		cs = root.childNodes;
		l = cs.length;
		for (i = 0; i < l; i++)
		{
			if (cs[i].tagName == "node")
			{
				if(parentNode == cs[i].getAttribute("nodeid")){
					index = 0;
				}
				else{
					index = 0;
				}
				arr_acciones[index] = new Array();
				arr_acciones[index]['icon'] = new Array();
				arr_acciones[index]['actionid'] = new Array();
				arr_acciones[index]['nodeid'] = new Array();
				arr_acciones[index]['name'] = new Array();
				arr_acciones[index]['command'] = new Array();
				
				nodes[index] = new Array();
				toolbar[index] = new Array();
				nodes[index]['nodeid'] = '';
				nodes[index]['name'] = cs[i].getAttribute("name");
				nodes[index]['path'] = '';
				nodes[index]['roles'] = new Array();
				cs2 = cs[i].childNodes;
				l2 = cs2.length;
				/// Por cada rol creo un array
				for (j = 0; j < l2; j++)
				{
					if (cs2[j].tagName == "role"){
						if(debug){
							alert('rol '+j+' '+cs2[j].getAttribute("name"));
						}

						nodes[index]['roles'][j] = new Array();
						nodes[index]['roles'][j]['roleid'] = cs2[j].getAttribute("roleid");
						nodes[index]['roles'][j]['name'] = cs2[j].getAttribute("name");
						nodes[index]['roles'][j]['actions'] = new Array();
						cs3 = cs2[j].childNodes;
						l3 = cs3.length;
						/// Por cada rol creo un array
						if(l3==0){
							 parent.parent.frames['toolbar'].borraBotones();
							break;
						}
						for (k = 0; k < l3; k++)
						{
							if (cs3[k].tagName == "action")
							{
								if(debug)
								{
									alert('action '+k+' '+cs3[k].getAttribute("name"));
								}

								arr_icon[k] = cs3[k].getAttribute("icon");
								arr_actionid[k] = cs3[k].getAttribute("actionid");;
								arr_name[k] = cs3[k].getAttribute("name");
								arr_command[k] = cs3[k].getAttribute("command");
								arr_activa[k] = cs3[k].getAttribute("activa");
							}
						}

						if(l3 > toolbar[index].length)
						{
							if(debug)
							{	
								alert ('sustituyendo barra');
							}
							toolbar[index] = nodes[index]['roles'][j];
						}
												
					}
				}
         	}
		}
	Inicializar_Menu();
		for(i=0;i<arr_icon.length;i++){
			Generar_Menu_Acciones(arr_icon[i], arr_actionid[i], arr_name[i],arr_command[i],arr_activa[i]);
		}
			if(debug)
			{
				alert('Documento XML descargado y parseado con exito!!!');
			}				
		return(nodes);
		}
}
function Crear_Botonera(){
	var limite = arr_menu['icon'].length;
	parent.parent.frames['toolbar'].ClearToolbar('selectedtoolbar');
	for(var i=0;i<limite;i++)
		{
			AppendButtonAcciones('selectedtoolbar', arr_menu['icon'][i], arr_menu['actionid'][i], arr_menu['name'][i],arr_menu['command'][i],arr_menu['activa'][i]);
		}
mover_nodos("area2");
	return 1;
}

function addworkspace(titlebar, url, unclosable)
	{
	parent.frames['content'].addtabpage(titlebar, url, unclosable);
	}

function addtree(titlebar, url, unclosable)
	{
	parent.frames['tree'].addtabpage(titlebar, url, unclosable);
	}

function getmaintree()
	{
	return parent.frames['tree'].boxes[0].contentWindow;
	}
	
function reloadNode(nodeid)
	{
	getmaintree().reloadNode(nodeid);
	}

function SetInfo(actiondesc,nodedesc)
	{
	document.getElementById('selectaction').value=actiondesc;
	document.getElementById('selectnode').value=nodedesc;
	SetCursorToEnd(document.getElementById('selectnode'));
	}
	
function HideInfo()
	{
	SetInfo('','');
	if(nodes[0]['path'])
		SetInfo('Nodo Seleccionado',' ruta:/'+nodes[0]['path']);
	}

function SetCursorToEnd (el)
	{
	if (el.createTextRange)
		{
		var v = el.value;
		var r = el.createTextRange();
		r.moveStart('character', v.length);
		r.select();
		}
	}
function ClearToolbarAcciones(toolbar)
	{
	if(document.getElementById(toolbar).childNodes.length >= 1)
		{
		for (i = (document.getElementById(toolbar).childNodes.length); i > 0; i--)
			{
			document.getElementById(toolbar).removeChild(document.getElementById(toolbar).firstChild);
			}
		}
	}

function Inicializar_Menu()
{
	var limite = arr_menu['icon'].length;
	for(var i=0;i<limite;i++){
		arr_menu['icon'].pop();
		arr_menu['actionid'].pop();
		arr_menu['name'].pop();
		arr_menu['command'].pop();
		arr_menu['activa'].pop();
	}
return 1;
}
function Generar_Menu_Acciones(icono,actionid,name,command,activa)
{
	arr_menu['icon'].push(icono);
	arr_menu['actionid'].push(actionid);
	arr_menu['name'].push(name);
	arr_menu['command'].push(command);
	arr_menu['activa'].push(activa);
	Crear_Botonera();
	//Inicializar_Menu();
	return 1;
}

function AppendButtonAcciones(toolbar, icon, actionid,  actiondesc,command,activa)
{
	common = true;
	var toolbaraux = parent.parent.frames['toolbar'].document.getElementById('selectedtoolbar');
	var td = parent.parent.frames['toolbar'].document.createElement( 'td' );
	//creamos un td por cada botón
	var cont = parent.parent.frames['toolbar'].document.createElement( 'img' );
	var action='';
	//botones de acciones
	if (command=='deletenode'){
		icon = 'delete_file_txt_bin.png';
		actiondesc = 'Borrar nodo';
	}
	cont.src = "images/icons/" + icon;
	cont.name=actionid;
	if (activa=='1'){
		cont.style.cursor='pointer';
		cont.alt = actiondesc;
		action = "accion_browser('"+command+"','area2');"
		cont.onmousedown = new Function(action);
	}
	else{
		cont.style.filter="alpha(opacity=30);"
		cont.alt = "Acción no disponible";
	}
	if (accion_nodo=='add'){
		//cont.style.visibility='hidden';
	}
	td.appendChild(cont);
	toolbaraux.appendChild(td);
	return 1;
}
