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

function widget( p, sTemp, sPath, sOptions, index )
{
//alert('widget_init');
	if( widget.prototype.widgetInitd == undefined )
	{
		this.initProto = _widgetInitProto;
		this.initProto( "widget" );
		widget.prototype.widgetInitd = true;
	}

	// constructor
	this.edxnode( p, sTemp, sPath, sOptions, index );		// llamada al constructor de nodos edx

	// init class vars
	this.nodeClass = "widget";
	this.type = sTemp;
	var a = this.type.substr(7).split(".");
	this.widgetType = a[0];
	//alert('widgetType: ' + this.widgetType)
	if( a.length > 1 )
		this.widgetSubType = a[1];
	else
		this.widgetSubType = null;
}

//
//						_widgetInitProto
//
function _widgetInitProto( sClass )
{
	// call base class proto
	this.initProto = _edxnodeInitProto;
	this.initProto( sClass );
	
	// install class methods
	eval( sClass + ".prototype.widget = widget; " +
	      sClass + ".prototype.xhtml = _widgetXHTML; " +
	      sClass + ".prototype.associate = _widgetAssociate; " +
	      sClass + ".prototype.writeIndexText = _widgetWriteIndexText; " +
	      sClass + ".prototype.processNodeLink = _widgetProcessNodeLink; " +
	      sClass + ".prototype.toggleColors = _widgetToggleColors; " +
	      sClass + ".prototype.cleanup = _widgetCleanup; " +
	      sClass + ".prototype.onXmlNodeChange = _widgetOnXmlNodeChange" );
}	

//
//						_widgetAssociate
//
var variable_estilo;
function _widgetAssociate( h )
{
	with( this )
	{
		edxnodeAssociate( h );
			
		// make sure we're on right tag type
		if( widgetType == "text" && h.tagName != "INPUT" )
		{
			err( "Error: el elemento tipo 'text' solo puede usarse en elementos del tipo INPUT." );
			return;
		}
		else if( widgetType == "select" && h.tagName != "SELECT" )
		{
			err( "Error: el elemento tipo 'select' solo puede usarse en elementos del tipo SELECT." );
			return;
		}

		// if we're an index, populate our text
		if( widgetType == "index" )
		{
			writeIndexText();
			
			// see if we're node-linked to a template
			if( this.nodeLink )
			{
				h.attachEvent("onclick", widgetOnClick );
				h.style.cursor = "pointer";
			}
		}
		// attach event handlers
		if( widgetType == "icon" || widgetType == "image" )
		{
			h.attachEvent("ondblclick", widgetOndbClick );
			h.attachEvent("onclick", widgetOnClick );
			h.attachEvent("oncontextmenu", widgetOnContextMenu );
			
		}
		else if( widgetType == "text" )
		{
			h.attachEvent("onblur", widgetOnBlur );
			
			// Checking type to put corresponding icon on cursor: if it is an image (ximlet)-> pointer
			if (h.type != "image")
				{
				h.style.cursor = "text";
				}
			else
				{
				h.style.cursor = "pointer";
				}
		}
		else if( widgetType == "select" )
		{
			h.attachEvent("onchange", widgetOnBlur );
			// turn on selected item
			var editnode = getXmlNode();
			var val = utilGetXmlNodeValue( editnode );
			var i;
			for( i=0; i < h.childNodes.length; i++ )
			{
				if( h.childNodes[i].value == val )
				{
					h.childNodes[i].selected = true;
					break;
				}
			}
		
		}
		
	}
}


//
//						_widgetXHTML
//
function _widgetXHTML( oFrag )
{
//alert('widgetXHTML');
	try {
	 
	with( this )
	{
		if( widgetType == "image" )
		{
			var editnode = getXmlNode();
			if( editnode != null )
			{
			my_temppath = "../../../xmd/loadaction.php?action=filemapper&nodeid=" + editnode.childNodes[0].nodeValue;

				utilSetXmlAttribute( oFrag, "src", utilResolveUrl( my_temppath, root.sBasePath ) );
				root.watchChanges( editnode, this );
			}
		}
		else if( widgetType == "index" )
		{
			var editnode = getXmlNode();
			if( editnode != null )
			{
				root.watchChanges( editnode.parentNode, this );
			}
		}
		else if( widgetType == "icon" )
		{
			if( parent.type != "region" )
			{
				err( "Error: el icono no tiene una plantilla tipo 'region' como contenedor" );
				return;
			}
	
			// get tooltip name to show
			var v = root.getView();
			var uiname = v.getTemplateName( parent.getTemplate() );
			
			if( oFrag.tagName.toUpperCase() == "IMG" )
			{
				var isrc = utilGetXmlAttribute( oFrag, "src" );
				if( isrc == null || isrc == "" )
				{			
					// look up icon for built-ins
					isrc;
					switch( widgetSubType )
					{
					case "details":
						isrc = "iconDetails.gif";
						break;
					case "hidden":
						isrc = "iconHidden.gif";
						break;
					case "para":
						isrc = "iconPara.gif";
						break;
					case "add":
						isrc = "iconAdd.gif";
						break;
					case "link":
						isrc = "iconLink.gif";
						break;
					case "style":
						isrc = "iconestilo.gif";
						break;
					case "ximPORTA":
						isrc = "icon_ximPORTA.gif";
						break;
					case "bulletin":
						isrc = "bulletin.gif";
						break;
					case "editnew":
						isrc = "edit_new.gif";
						break;
					case "table":
						isrc = "iconTable.gif";	
						break;
					case "box":	
						isrc = "iconAddBox.gif";	
						break;
					case "subbox":
						isrc = "iconAddSubBox.gif";	
						break;						
					case null:
					case "default":
					default:
						isrc = "iconDefault.gif";
						break;
					}
					utilSetXmlAttribute( oFrag, "src", "../xmd/images/icons-editor/" + isrc );
					
				}
				else
				{
					isrc = utilResolveUrl( isrc, root.sBasePath );
				}
				utilSetXmlAttribute( oFrag, "title", uiname );
			}
			else
			{
				utilSetXmlAttribute( oFrag, "title", uiname );
			}
			var style = utilGetXmlAttribute( oFrag, "style" );
			if( style == null )
				utilSetXmlAttribute( oFrag, "style", "cursor: pointer;" );
			else
				utilSetXmlAttribute( oFrag, "style", "cursor: pointer;" + style );
		}
		else if( widgetType == "text" )
		{
			var editnode = getXmlNode();

			
			if( editnode != null )
			{
				switch (define_tipo  (editnode.nodeType))
				{
					case "element":
						utilSetXmlAttribute( oFrag, "value", editnode.childNodes[0].nodeValue );
						break;
					case "attribute":
						utilSetXmlAttribute( oFrag, "value", editnode.nodeValue );
						break;
				
				}
				
				root.watchChanges( editnode, this );
			}
		}
		else if( widgetType == "select" )
		{
			// debug - maybe someday make these watch their assoc'd node for changes too
		}
	}
	}
	catch(e)
	{
		//err( "Ocurrió una excepción de XHTML: " + e + " type: " + this.widgetType );
	}
	return oFrag;
}

//
//						widgetOnClick
//
//	Note: has HTML node 'this' context
//
function widgetOnClick(event)
{
	//alert('widgetonclick');
if (navegador == "ie")
	{
		var e = window.event.srcElement.eobj;
	}
else
	{
	limpia_ventana();
		var eTarget = event.target || event.srcElement;
		var e = new Object();
		var el_id = eTarget.getAttribute('edxid');
		e = dex[el_id];
	}
	if( e.widgetType == "icon" || e.widgetType == "image" )
	{
		e.parent.select();
	}
	else if( e.widgetType == "index" )
	{
		e.processNodeLink();
	}
}

//
//						_widgetProcessNodeLink
//
//	Highlights the index and finds any region(s) and sets their new XML node.
//
function _widgetProcessNodeLink()
{
	with( this )
	{
		if( root.selectedIndex != null )
		{
			root.selectedIndex.toggleColors();
		}
		root.selectedIndex = this;
		toggleColors();
		
		// find node-linked template(s)
		widgetUpdateLinkedTemplates( root, nodeLink, getXmlNode() );				
	}
}

//
//						_widgetToggleColors
//
function _widgetToggleColors()
{
	with( this )
	{
		var c = hobj.currentStyle.color;
		if( c == "#fffffe" )
			c = "transparent";
		var bc = hobj.currentStyle.backgroundColor;
		if( bc == "transparent" )
			bc = "#fffffe";
				
		hobj.style.color = bc;
		hobj.style.backgroundColor = c;
	}
}

//
//						widgetUpdateLinkedTemplates
//
function widgetUpdateLinkedTemplates( e, sTemplate, oXml )
{
	if( e.edxtemplate == sTemplate )
	{
		e.setXmlNode( oXml );
		return;
	}
	var i;
	for( i = 0; i < e.childNodes.length; i++ )
	{
		widgetUpdateLinkedTemplates( e.childNodes[i], sTemplate, oXml );
	}
}

/// captura del doble click
function widgetOndbClick(event){
	limpia_ventana();
		var eTarget = event.target || event.srcElement;
		var e = new Object();
		var el_id = event.srcElement.getAttribute('edxid');
		e = dex[el_id];
		
	var args = new Array();
	
		if( e.widgetType == "icon" )
		{
			var rootid = e.root.hobj.id;
			var v = e.root.getView();
			var sName = v.getTemplateName( e.parent.getTemplate() );
			var objeto = e.parent.getTemplate();
			var editnode = e.parent.getXmlNode();
			if (editnode.tagName != "tabla")
				{
					var a = v.getDisplays( e.parent.getTemplate() );
					if( a[0] != e.parent.viewState ) e.parent.viewState = a[0];
					else e.parent.viewState = a[1];
					
				}
			
			
			if (editnode.tagName == "categoria_noticia"){
				//arr = showModalDialog( "init.php?nodeid="+ editnode.getAttribute("a_enlaceid_noticia_url") + "&actionid=6065", args, "font-family:Verdana; dialogWidth:700px; dialogHeight:420px;");
				//editamos la noticia en un documento nuevo
				editar_documento(editnode.getAttribute("a_enlaceid_noticia_url"));
			}
			
			//Starts code for wizard of created tables
			if (editnode.tagName == "tabla"){
				//alert(editnode.childNodes.item(0));
				if (navegador == "firefox15")
				{
				
//return;	
							params = new Array();
							paramsH = new Array();
							paramsE = new Array();
		
							params = e;
							document.getElementById('Vmodal').src = "inc/asistentetabla4gecko.php";
							document.getElementById('Vmodal').style.width = "500px";
							document.getElementById('Vmodal').style.height = "320px";
							document.getElementById('Vmodal').style.top = (window.innerHeight / 2) - 150;
							document.getElementById('Vmodal').style.left = (window.innerWidth / 2) - 250;
							document.getElementById('Vmodal').style.visibility = "visible";
							return;

				}
				
				else
				{
				args["xml"] = editnode.xml; //.childNodes.item(0)
				
				var arr = showModalDialog("../actions/xmleditor/inc/asistentetabla.php",              args,
                            "font-family:Verdana; dialogWidth:450px; dialogHeight:410px;scroll=no;");
			
				
				if (arr != null){
					
					
					var nuevatabla = new ActiveXObject("Msxml2.DOMDocument");
					nuevatabla.async = false;
					nuevatabla.resolveExternals = false; 
					
					
					
							nuevatabla.loadXML(arr["xml_content"]);
							var newNode = nuevatabla.childNodes.item(0).childNodes.item(0);
							var oldChild = editnode.childNodes.item(0);
							editnode.replaceChild(newNode, oldChild);
							
							var alin = nuevatabla.childNodes.item(0).getAttribute('alin');
							if (!alin){
								alin = "";
								}
							var sumario = nuevatabla.childNodes.item(0).getAttribute('sumario');
							if (!sumario) {
								sumario="";
								} 
							var ancho = nuevatabla.childNodes.item(0).getAttribute('ancho');
							if (!ancho) {
								ancho="";
								}
							var salto = nuevatabla.childNodes.item(0).getAttribute('salto');
							if (!salto) {
								salto="no";
								}
							var espacio = nuevatabla.childNodes.item(0).getAttribute('espacio');
							if (!espacio) {
								espacio="";
								}
							var margen = nuevatabla.childNodes.item(0).getAttribute('margen');
							if (!margen){
								margen = "0";
								}
							var clase = nuevatabla.childNodes.item(0).getAttribute('clase');
							if (!clase){
								
								}
								else
								{
								editnode.setAttribute("clase",clase);
								}
								
							editnode.setAttribute("alin",alin);
							editnode.setAttribute("sumario",sumario);
							editnode.setAttribute("ancho",ancho);
							editnode.setAttribute("salto",salto);
							editnode.setAttribute("espacio",espacio);
							editnode.setAttribute("margen",margen);
							
		
							e = e.parent;
							//e_global = e;
							e.load();
					}
				}

			}
			
			//Here ends the code for wizard of created tables
			
			
			if (editnode.childNodes){
				if (editnode.childNodes.length == 3){
					 if (editnode.childNodes.item(2).tagName == "docxap"){
					 	if (confirm ("¿Desea abrir el asistente de creación de Categorías?")){
							var args = new Array();
							var arr = null;
							var xmlDoc = new ActiveXObject("Msxml2.DOMDocument");
							xmlDoc.async = true;
							xmlDoc.resolveExternals = true;
							var pos = 0;
							for (bol = 0; bol < editnode.childNodes.item(2).childNodes.item(0).childNodes.length; bol++){
								if (editnode.childNodes.item(2).childNodes.item(0).childNodes.item(bol).tagName == "cuerpo-boletines"){
									pos = bol;
									args["xml"] = editnode.childNodes.item(2).childNodes.item(0).childNodes.item(bol).xml;
								}
							}
							arr = showModalDialog( "ximnewssec_cat.php?nodeid="+ Xnodeid ,
		                           		  args,
		                       		      "font-family:Verdana; dialogWidth:480px; dialogHeight:250px;");
							if (arr != null){
								longi = editnode.childNodes.item(2).childNodes.item(0).childNodes.item(pos).childNodes.length;
								for (n = 0; n < longi; n++){
									editnode.childNodes.item(2).childNodes.item(0).childNodes.item(pos).removeChild(editnode.childNodes.item(2).childNodes.item(0).childNodes.item(pos).childNodes.item(0));
								}
								xmlDoc.loadXML(arr["xml"]);
								var root2 = xmlDoc.documentElement;
								var cs2 = root2.childNodes;
								longi = cs2.length;
								for (N = 0; N< longi; N++){
									editnode.childNodes.item(2).childNodes.item(0).childNodes.item(pos).appendChild(cs2.item(0));
								}
							}
							else return;
						}
						else return;
						//editnode.childNodes.item(2).childNodes(0).appendChild(newNode);
					 }
					}
				}
			// Waiting for a moment...
			/*if (objeto.childNodes.item(1).childNodes.item(0).tagName == "enlace" || objeto.childNodes.item(1).childNodes.item(0).tagName == "estilo")
				{
				var editnode = e.parent.parent.getXmlNode();
				var padre = e.parent.parent.parent.getXmlNode();
					
				//editnode.childNodes.item(0).nodeValue;
				if (editnode.tagName == "enlace") {
					var args = new Array();
					var arr = null;
		   			arr = null;
					args["url"] = editnode.getAttribute('a_enlaceid_url');
					args["referenciador"] = editnode.getAttribute('referenciador');
					args["ventana"] = editnode.getAttribute('ventana');
					args["clase"] = editnode.getAttribute('clase');
					arr = showModalDialog( "inc/archivos.php",
		                           		  args,
		                       		      "font-family:Verdana; dialogWidth:630px; dialogHeight:420px;");
		  			if (arr != null) {
						editnode.setAttribute ("a_enlaceid_url", arr["ID"]);
						editnode.setAttribute ("referenciador", arr["referenciador"]);
						editnode.setAttribute ("ventana", arr["ventana"]);
						editnode.setAttribute ("clase", arr["clase"]);
						variable_estilo = arr["clase"];
						if(arr["borrar"] == "si") alert('acción borrar');
						}
					else{return;}
					}
				if (editnode.tagName == "estilo") {
					 var args = new Array();
						var arr = null;
					   	arr = null;
						args["clase"] = editnode.getAttribute('clase');
						arr = showModalDialog( "inc/estilo_map.html",
					                             args,
					                             "font-family:Verdana; dialogWidth:380px; dialogHeight:190px;");
					  	if (arr != null) {
							editnode.setAttribute ("clase", arr["clase"]);
							variable_estilo = arr["clase"];
							//borrar la etiqueta
							if(arr["borrar"] == "si"){
								alert('acción borrar');
								
							}
					}
					else{return;}
					}
				}
				*/
			
			var id = rootid;
			e = e.parent;
			e.load();
		}
}


//
//						widgetOnContextMenu
//
//	Note: has HTML node 'this' context
//
function widgetOnContextMenu(event)
{
	limpia_ventana();
	var altura_v = 0;
		var eTarget = event.target || event.srcElement;
		var e = new Object();
		var el_id = event.srcElement.getAttribute('edxid');
		e = dex[el_id];
	
	if( e.widgetType == "icon" )
	{
		// make sure we can get root's ID to connect the dots with the popup
	if (navegador == "ie")
		{
		var rootid = e.root.hobj.id;
		//alert(rootid);
		}
	else
		{
		var rootid = e['root'].id;
		//alert(rootid);
		}
		
		if( rootid == null || rootid == "" )
		{
			err( "Error: el nodo principal no tiene un ID válido. Asigne un ID válido al nodo principal" );
			return false;
		}

		// get buffer to accumulate content to		
		var pb = new Array()
		pb[pb.length] = "<table cellspacing=0 cellpadding=3 style='background-color:#e8e8e8;font-family:verdana;font-size:11px;border:1px solid #404000;'>";

		// show current template name		
		var v = e.root.getView();
		var sName = v.getTemplateName( e.parent.getTemplate() );

		pb[pb.length] = "<tr><td style='background-color:#afafaf'><b>" + sName + "</b></td></tr><tr><td>";
		altura_v = altura_v + 18;
		// get list of views on the parent region
		var a = v.getDisplays( e.parent.getTemplate() );
		var i;
		
		for( i = 0; i < a.length; i++ )
		{
			if( a[i] == e.parent.viewState )
				pb[pb.length] = "&bull; <b>" + a[i] + "</b><br>";
			else
				pb[pb.length] = "&nbsp; <u edxcmd=viewchange rootid=" + rootid + " style='cursor: pointer'>" + a[i] + "</u><br>";
			altura_v = altura_v + 18;
		}
		
		// see if we need to build move up/down nav and insertion/deletion
		var pp = e.parent.parent;
		if( pp != null && pp.type == "container" )
		{
			var fUp = pp.canMoveUp( e.parent );
			var fDown = pp.canMoveDown( e.parent );
			if( fUp || fDown )
				{
				pb[pb.length] = "<font color=808040>----</font><br>";
				altura_v = altura_v + 18;
				}
			if( fUp )
				{
				pb[pb.length] = "&uarr; <u edxcmd=moveup rootid=" + rootid + " style='cursor: pointer'>Subir</u><br>";
				altura_v = altura_v + 18;
				}
			if( fDown )
				{
				pb[pb.length] = "&darr; <u edxcmd=movedown rootid=" + rootid + " style='cursor: pointer'>Bajar</u><br>";
				altura_v = altura_v + 18;
				}

			// insertion
			a = v.getContainerMatches( pp.edxtemplate );
			if( a.length > 0 )
				{
				pb[pb.length] = "<font color=808040>----</font><br>";
				altura_v = altura_v + 10;
				}
			for( i = 0; i < a.length; i++ )
			{
				var oMatch = a[i];
				if (oMatch.uiname != "") pb[pb.length] = "&rarr; <u edxcmd=insert inserttemplate=" + oMatch.edxtemplate + " rootid=" + rootid + " style='cursor: pointer'>insertar " + oMatch.uiname + "</u><br>";
				altura_v = altura_v + 18;
			}
			
			// deletion
			pb[pb.length] = "<font color=808040>----</font><br>";
			pb[pb.length] = "&larr; <u edxcmd=delete rootid=" + rootid + " style='cursor: pointer'>borrar</u><br>";
			altura_v = altura_v + 22;
		}
		
		pb[pb.length] = "</td></tr></table>";
		
		// build offscreen to get size
		var sHTML = pb.join("");

		var oOff = window.document.createElement( "DIV" );
		
		oOff.innerHTML = sHTML;
		//var sp2 = window.document.getElementById("PVcontenedor");
		//alert(sp2.innerHTML);
		if (navegador == "ie")
			{
			window.document.body.insertAdjacentElement( "beforeEnd", oOff );
			}
		else
			{
			document.body.appendChild( oOff );
			
			
			}
		
		
		
		
		
		oOff.style.display = "block";
		oOff.style.position = "absolute";
		oOff.style.left = "-10000px";
		oOff.style.top = "0px";
		oOff.style.height = altura_v + "px";
//		oOff.setAttribute ('style', 'z-index: 100; ' + oOff.style);
		var w = oOff.offsetWidth;
		var h = oOff.offsetHeight;

		// create the popup
	if (navegador == "ie")
		{
		e.oPopup = window.createPopup();
		var oBody = e.oPopup.document.body;
		oBody.innerHTML = sHTML;
		oBody.onclick = widgetPopupClick;
		// Posiciono la ventana del menu
		var r = e.hobj.getBoundingClientRect();
		// asocio la ventana del menu con el objeto que la ha llamado
		e.root.popupOwner = e;
		
		//alert(e.root.popupOwner.id);
		// muestro el menu
		e.oPopup.show(r.right + 212,r.bottom + 119, w, h, document.body);
		//e.oPopup.show(r.right, r.bottom, w, h, document.body);
		}
	else
		{
		var sp2 = window.document.getElementById("PVcontenedor");
		sp2.appendChild(oOff);
		
		sp2.onclick = widgetPopupClick;
		
		oOff.style.left = "0px";
		oOff.style.top = "0px";
		sp2.style.left = event.clientX + 8;
		sp2.style.top = event.clientY + 5;
		sp2.style.visibility = "visible";
		if ((event.clientY + 5 + altura_v) > window.innerHeight)
			{
				sp2.style.top = window.innerHeight - altura_v;
			}
		
		dex['e0']['popupOwner'] = e;
		
		}
		
		
		
		
		
	}
	return false;
}

//
//						widgetPopupClick
//
function widgetPopupClick(event)
{
	
		
	
	if (navegador == "ie")
		{
			var ev = this.document.parentWindow.event;
			if( ev.srcElement.tagName != "U" )
				return false;
			
			var id = rootid;
			
			var r = this.document.parentWindow.parent.document.getElementById( id );
			
			if( r == null )
			{
				err( "No se pudo encontrar edxroot para procesar el menú" );
			}
			
			r = r.eobj;
			
			// get widget icon ref
			var e = r.popupOwner;
			
			if( e == null )
				{
					err( "Error: no se encuentra el elemento propietario del menú" );
					return;
				}
			var oPop = e.oPopup;
			e.oPopup = null;
			
			// get region ref
			e = e.parent;
			r.popupOwner = null;
			ev = ev.srcElement;
		}
	else
		{
		var eTarget = event.target || event.srcElement;
		var e = new Object();
		var ev = new Object();
		ev = event.target;
		if (ev.tagName != "U")
			return false;
		
		var id = "edxid";
		
		//alert(dex['e0']['popupOwner'].id);
//		return;
		
		var r = dex['e0'];
		
		OID = dex['e0']['popupOwner'].id;
		
		e = dex[OID];
		dex_global = dex[OID];
		if( e == null )
				{
					err( "Error: no se encuentra el elemento propietario del menú" );
					return;
				}
		
		e = e.parent;
		
		}

	switch( ev.getAttribute('edxcmd') )
	{
	case "viewchange":
		e.viewState = ev.innerText;
		e.load();		
		break;
	case "moveup":
		e.parent.moveUp( e );

		break;
	case "movedown":
		e.parent.moveDown( e );

		break;
	case "insert":		
		variable_estilo = "";

		e.parent.insert( ev.getAttribute('inserttemplate'), e );
		if (ev.getAttribute('inserttemplate') != "tabla")
			{
				e.parent.moveUp( e );
				if (ev.getAttribute('inserttemplate').indexOf("fila") < 0)
					{
					e.parent.load();
					}
			}
			else
			{
			e.parent.moveUp( e );
			}
//		alert(e.id);
		break;
	case "delete":
		e.parent.deleteChild( e );
		break;
	}
	if (navegador == "ie")
		{
		oPop.hide();
		}
	else
		{
		limpia_ventana();
		}
	r.popupOwner = null;
}


function limpia_ventana()
	{
	if (navegador=="ie") return;
		var sp2 = window.document.getElementById("PVcontenedor");
		if (sp2.firstChild)
			{
			sp2.removeChild(sp2.firstChild); 
			sp2.style.visibility = "hidden;"
			sp2.style.left = "-1000px";
			}
	}

//
//						widgetOnBlur
//
//	Checks for change in the widget and captures the value to XML if necessary.
//
//	Note: has HTML node 'this' context
//
function widgetOnBlur(event)
{
		var eTarget = event.target || event.srcElement;
		var e = new Object();
		
		var h = event.srcElement;
		
		var el_id = event.srcElement.getAttribute('edxid');
		var e = dex[el_id];
		
		//alert(e.widgetType);

// antes para ie ->	var h = window.event.srcElement;
//	var e = h.eobj;


	if( e.widgetType == "text" || e.widgetType == "select" )
	{
		var editnode = e.getXmlNode();
		if( editnode != null )
		{
			if( editnode.nodeValue != h.value )
			{
				var xmlmgr = e.root.getXmlManager();
				//alert(xmlmgr);
				xmlmgr.openTransaction( editnode );
				xmlmgr.process( "updateNode", editnode, h.value );
				xmlmgr.closeTransaction();
				e.root.alertChange( editnode, e );
				variable_estilo = h.value;
				
			}
		}
	}
}


//
//						_widgetWriteIndexText
//
function _widgetWriteIndexText()
{
	with( this )
	{
		var editnode = getXmlNode();
		var i;
		var par = editnode.parentNode;
		for( i = 0; i < par.childNodes.length; i++ )
		{
			if( par.childNodes[i] == editnode )
				break;
		}
		hobj.innerText = "" + (i+1);
	}
}

//
//						_widgetOnXmlNodeChange
//
function _widgetOnXmlNodeChange( sender )
{

	with( this )
	{
		//alert(widgetType);
		if( widgetType == "image" )
		{
			var editnode = getXmlNode();
			//alert(serializa_me(editnode));
			if( editnode != null )
			{my_temppath = "../../../xmd/loadaction.php?action=filemapper&nodeid=" + editnode.childNodes[0].nodeValue;
				hobj.src = utilResolveUrl( my_temppath, root.sBasePath );
			}
		}
		else if( widgetType == "text" )
		{
//		alert(widgetType);
			var editnode = getXmlNode();
			if( editnode != null )
			{
				if(editnode.nodeValue){
					if (editnode.name == "texto"){
						if (editnode.nodeValue.indexOf('"') > 0){
							editnode.nodeValue = limpia_cadena( editnode.nodeValue, '"', false);
						}
					}
									
				}
				if (hobj.innerText){
					hobj.innerText = editnode.nodeValue;
					}
			}
		}
		else if( widgetType == "index" )
		{
			writeIndexText();		
		}
	}
}

//Function to validate that the ID is not a number


/// Function of character cleanup
function limpia_cadena(item,delimiter, my_var) {
	tempArray=new Array(1);
	var Count=0;
	var tempString=new String(item);
	
  while (tempString.indexOf(delimiter)>0) {
    tempArray[Count]=tempString.substr(0,tempString.indexOf(delimiter));
	tempString=tempString.substr(tempString.indexOf(delimiter)+delimiter.length, tempString.length-tempString.indexOf(delimiter)); 
    Count=Count+1;
  }
  tempArray[Count]=tempString;
  devuelve = "";
	for(n=0; n<=Count; n++){
		if (!my_var){devuelve = devuelve + "'" + tempArray[n];}
		else {devuelve = devuelve + "'" + tempArray[n];};
	}
  return devuelve;
}


////

//
//						_widgetCleanup
//
function _widgetCleanup()
{
	with( this )
	{
		if( widgetType == "image" || widgetType == "text" )
		{
			var editnode = getXmlNode();
			if( editnode != null )
			{
				root.unwatchChanges( editnode, this );
			}
		}
		else if( widgetType == "index" )
		{
			var editnode = getXmlNode();
			if( editnode != null )
			{
				root.unwatchChanges( editnode.parentNode, this );
			}
			if( root.selectedIndex == this )
				root.selectedIndex = null;
		}
		edxnodeCleanup();
	}
}
