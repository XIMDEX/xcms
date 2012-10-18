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


function borrarBotones()
{
	/* Funcion aniadida para borrar los botones de acciones al pulsar sobre un nodo*/
	ClearToolbarContextual('menu_contextual');

}

var nodes = new Array();
var toolbar = new Array;

function loadXmlFileContextual(sSrc)
	{
	//alert(sSrc);
	var xmlDoc = XmlDocument.create();
	xmlDoc.async = true;
	xmlDoc.resolveExternals = true;
	xmlDoc.onreadystatechange = function () {
		if (xmlDoc.readyState == 4) {
				parseXmlDocContextual(xmlDoc);
			}
		};
	// call in new thread to allow ui to update
	window.setTimeout(function () {
		xmlDoc.load(sSrc);	
		}, 10);	
		
		
	}

function parseXmlDocContextual(xmlDoc)
	{
	debug = 0;
	//alert(xmlDoc.xml);
		//if (xmlDoc.parseError.errorCode != 0) {
		//			  alert(xmlDoc.parseError.reason + " línea " + xmlDoc.parseError.line);
		//				return;
		//			}
	// check that the load of the xml file went well
	if( xmlDoc == null || xmlDoc.documentElement == null)
		alert('error al cargar la informacion del nodo');
		
	else
		{
		nodes;
		nodes = new Array();
		// there is one extra level of tree elements
		root = xmlDoc.documentElement;
		parentNode = root.getAttribute("nodeid");
		if(debug)
			alert(parentNode);
		// loop through all tree children
		cs = root.childNodes;
		l = cs.length;
		for (i = 0; i < l; i++)
			{
			if (cs[i].tagName == "node")
				{
				if(parentNode == cs[i].getAttribute("nodeid"))
					{
					index = 0;
					if(debug)
						alert('nodo '+i+' hijo: '+cs[i].getAttribute("name"))
					}
				else
					{
					index = 1;
					if(debug)
						alert('nodo '+i+' padre: '+cs[i].getAttribute("name"))
					}
				nodes[index] = new Array();
				toolbar[index] = new Array();
				nodes[index]['nodeid'] = cs[i].getAttribute("nodeid");
				nodes[index]['name'] = cs[i].getAttribute("name");
				nodes[index]['path'] = cs[i].getAttribute("path");
				nodes[index]['roles'] = new Array();
				cs2 = cs[i].childNodes;
				l2 = cs2.length;
				/// Por cada rol creo un array
				for (j = 0; j < l2; j++)
					{
					if (cs2[j].tagName == "role")
						{
						if(debug)
							alert('rol '+j+' '+cs2[j].getAttribute("name"));
						nodes[index]['roles'][j] = new Array();
						nodes[index]['roles'][j]['roleid'] = cs2[j].getAttribute("roleid");
						nodes[index]['roles'][j]['name'] = cs2[j].getAttribute("name");
						nodes[index]['roles'][j]['actions'] = new Array();
						

						cs3 = cs2[j].childNodes;
						l3 = cs3.length;
						
						/// Por cada rol creo un array
						for (k = 0; k < l3; k++)
							{
							if (cs3[k].tagName == "action")
								{
								if(debug)
									alert('action '+k+' '+cs3[k].getAttribute("name"));
								nodes[index]['roles'][j]['actions'][k] = new Array();
								nodes[index]['roles'][j]['actions'][k]['actionid'] = cs3[k].getAttribute("actionid");
								nodes[index]['roles'][j]['actions'][k]['name'] = cs3[k].getAttribute("name");
								nodes[index]['roles'][j]['actions'][k]['icon'] = cs3[k].getAttribute("icon");
								nodes[index]['roles'][j]['actions'][k]['description'] = cs3[k].getAttribute("description");
								}
							}
						
						if(l3 > toolbar[index].length)
							{
							if(debug)
								alert ('sustituyendo barra');
							toolbar[index] = nodes[index]['roles'][j]['actions'];
							}
												
						}
					}
         		}
			}
			
		ClearToolbarContextual('menu_contextual');
		var acciones = '<TABLE width="100%" id="tabla_menu_contextual" >';
		acciones +="<tr height='100%'><td width='100%' colspan=2 bgcolor='#CCCCCC' align='center'><b>Acciones</b></td></tr>"; 
		var numero_acciones_contextual = toolbar[0].length;
		if (numero_acciones_contextual>0)
		{
			for(i=0;i<numero_acciones_contextual;i++)
				{
				var accion = AppendButtonContextual('menu_contextual', toolbar[0][i]['icon'], toolbar[0][i]['actionid'], nodes[0]['nodeid'], toolbar[0][i]['name'], ' ruta:/'+nodes[0]['path']);
				acciones +="<tr>"+accion+"</tr>"; 
				}
		}
		else
		{
		acciones +="<tr><td align='center'><b>No hay acciones para este nodo.</b></td></tr>"; 
		
		}
		acciones += '<TABLE>';
		$('menu_contextual').innerHTML=acciones;
		SetInfo('Nodo Seleccionado',' ruta:/'+nodes[0]['path']);
		
		/// CODIGO COMENTADO PARA SUPRIMIR LA BARRA DE ACCIONES DEL PADRE
		///ClearToolbar('parenttoolbar');
		///for(i=0;i<toolbar[1].length;i++)
		///	AppendButton('parenttoolbar', toolbar[1][i]['icon'], toolbar[1][i]['actionid'], nodes[1]['nodeid'], toolbar[1][i]['name'], ' ruta:/'+nodes[1]['path']);
			if(debug)
				alert('Documento XML descargado y parseado con exito!!!');
				
		return(nodes);
		}
		
	}

function showTreeContextual(nodes)
	{
	document.write('<UL>');
	l = nodes.length;
	for(i = 0; i < l; i++)
		{
		document.write('<LI>NODO ID: '+nodes[i]['nodeid']+'<BR>NAME: '+nodes[i]['name']+'<BR>PATH: '+nodes[i]['path']+'<BR>');

		document.write('<UL>');
		l2 = nodes[i]['roles'].length;
		for(j = 0; j < l2; j++)
			{

			document.write('<LI>ROL ID: '+nodes[i]['roles'][j]['roleid']+'<BR>NAME: '+nodes[i]['roles'][j]['name']+'<BR>');

			document.write('<UL>');
			l3 = nodes[i]['roles'][j]['actions'].length;
			for(k = 0; k < l3; k++)
				{
				document.write('<LI>ACCION ID: '+nodes[i]['roles'][j]['actions'][k]['actionid']+'<BR>NAME: '+nodes[i]['roles'][j]['actions'][k]['name']+'<BR>ICON: '+nodes[i]['roles'][j]['actions'][k]['icon']+'<BR>DESCRIPTION: '+nodes[i]['roles'][j]['actions'][k]['description']+'<BR>');
				}
			document.write('</UL>');
			}
		document.write('</UL>');
		}
	document.write('</UL>');
	}
	var ie55 = /MSIE ((5\.[56789])|([6789]))/.test( navigator.userAgent ) &&
			navigator.platform == "Win32";

	window.onerror = function () {return true;};


function addworkspaceContextual(titlebar, url, unclosable)
	{
		parent.parent.frames['content'].addtabpage(titlebar, url, unclosable);
	}

function addtree(titlebar, url, unclosable)
	{
		parent.parent.frames['tree'].addtabpage(titlebar, url, unclosable);
	}

function getmaintree()
	{
	return parent.parent.frames['tree'].boxes[0].contentWindow;
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
	
function _SetSelectedNodeContextual(nodeid)
	{
	//aniadido para hacer desaparecer los botones de la barra y evitar los problemas de retardo en la conexion :idemxime:
	borrarBotones();
	if (nodeid == 0) return;
	

	if(nodeid)
		loadXmlFileContextual(urlroot+'/xmd/loadaction.php?method=toolbardata&nodeid='+nodeid,1);	
	}
	
	
function ExecuteActionContextual(actionid, nodeid, titlebar)
	{
	//Se oculta el menu contextual del nodo ya que se quedaba visible al seleccionar una accion
	$('menu_contextual').style.visibility='hidden';
	//alert('[EJECUTANDO ACCION]\n\tactionID: '+actionid+'\n\tnodeID: '+nodeid+'\n\ttitle: '+titlebar);
	addworkspaceContextual(titlebar, './loadaction.php?actionid='+actionid+'&nodeid='+nodeid);
	}
	
function LoadPage()
	{
	addToggle(document.getElementById('treebutton'));
	addToggle(document.getElementById('statusbutton'));
	
	toggle(document.getElementById('treebutton'));
	toggle(document.getElementById('statusbutton'));
	
	makePressed(document.getElementById('treebutton'));
	makePressed(document.getElementById('statusbutton'));
	
	}

function ClearToolbarContextual(toolbar)
	{
	if(document.getElementById(toolbar).childNodes.length >= 1)
		{
		var num_elementos = document.getElementById(toolbar).childNodes.length;
		for (i = (num_elementos); i > 0; i--)
			{
			document.getElementById(toolbar).removeChild(document.getElementById(toolbar).firstChild);
			}
		}
	}
	
function _AppendButtonContextual(toolbar, icon, actionid, nodeid, actiondesc, nodedesc)
{
	common = true;
	var accion="<td><img src='"+urlroot+"/xmd/images/icons/"+ icon+"'";
	accion +="style='cursor:pointer' alt='"+actiondesc+"'"; 
	accion +="onClick='ExecuteActionContextual";
	accion +='('+actionid+', '+nodeid+',"'+actiondesc+'")';
	accion += ";'></td>"
	accion += "<td>"+actiondesc+"</td>";
	return accion;
}
