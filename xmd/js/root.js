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

function root( p, sTemp, sPath, index )
{


	if( root.prototype.rootInitd == undefined )
	{
		this.initProto = _rootInitProto;
		this.initProto( "root" );
		root.prototype.rootInitd = true;
	}

	// init base class vars
	this.region( p, sTemp, sPath, index );		// call base class constructor
	
	// init our own vars	
	this.nodeClass = "root";

	// public "property"
	this.selectedRegion = null;
	this.selectedIndex = null;
	this.focusField = null;
	this.popupOwner = null;
	
	// cursor vars
	this.cursorTimeout = -1;
	this.cursorDiv = null;
	
	// doc mgmt vars
	this.oEditXml = null;
	this.oEditRoot = null;
	this.sBasePath = null;
	this.oViewXml = null;
	this.oViewRoot = null;
	this.oView = null;
	this.nextID = 0;
	/*if (navegador == "ie")
		{
			this.dChanges = new ActiveXObject("Scripting.Dictionary");
			this.dIDs = new ActiveXObject("Scripting.Dictionary");
		}*/
		// We make two arrays to manage ids of nodes and associate xml with xhtml

	this.dIDs = new Array();
	if (navegador == "ie")
		{
		this.dChanges = new ActiveXObject("Scripting.Dictionary");
		}
	else
		{
		this.dChanges = new Object();		
		}

	this.oXmlMgr = new xmlmgr( this );

	// manage field selection
	this.aFieldSelection = null;

	// catch global events
	if (navegador == "ie")
		{
			window.document.attachEvent( "onselectionchange", rootOnSelectionChange );
		}
	else if (navegador == "firefox15")
		{
		if (document.getElementById("edxid").attachEvent){
		  	document.getElementById("edxid").attachEvent("onSelectionChange", _rootOnSelectionChange); 
		} else if (document.getElementById("edxid").addEventListener){
		  	document.getElementById("edxid").addEventListener("onSelectionChange", _rootOnSelectionChange, true); 
		}

		
			
		}

	// assign ourselves our ID
	this.id = this.assignID( this );
	
}

//
//						_rootInitProto
//
function _rootInitProto( sClass )
{
	// call base class proto
	this.initProto = _regionInitProto;
	this.initProto( sClass );
	
	eval( sClass + ".prototype.root  = root; " +
	      sClass + ".prototype.getViewDocRoot = _rootGetViewDocRoot; " +
	      sClass + ".prototype.getEditDocRoot = _rootGetEditDocRoot; " +
	      sClass + ".prototype.loadDocs = _rootLoadDocs; " +
	      sClass + ".prototype.getView = _rootGetView; " +
	      sClass + ".prototype.assignID = _rootAssignID; " +
	      sClass + ".prototype.deassignID = _rootDeassignID; " +
	      sClass + ".prototype.lookupID = _rootLookupID; " +
	      sClass + ".prototype.watchChanges = _rootWatchChanges; " +
	      sClass + ".prototype.unwatchChanges = _rootUnwatchChanges; " +
	      sClass + ".prototype.onSelectionChange = _rootOnSelectionChange; " +
	      sClass + ".prototype.clearFieldSelection = _rootClearFieldSelection; " +
	      sClass + ".prototype.alertChange = _rootAlertChange; " +
	      sClass + ".prototype.fieldCursor = _rootFieldCursor; " +
	      sClass + ".prototype.toggleCursor = _rootToggleCursor; " +
	      sClass + ".prototype.canApplyTag = _rootCanApplyTag; " +
	      sClass + ".prototype.applyTag = _rootApplyTag; " +
	      sClass + ".prototype.deleteTextSelection = _rootDeleteTextSelection; " +
	      sClass + ".prototype.copyTextSelection = _rootCopyTextSelection; " +
	      sClass + ".prototype.restoreFocus = _rootRestoreFocus; " +
	      sClass + ".prototype.getXmlManager = _rootGetXmlManager; " +
	      sClass + ".prototype.sanityCheck = _rootSanityCheck; " +
	      sClass + ".prototype.startSanityChecker = _rootStartSanityChecker" );
}

//
//						_rootGetView
//
//	Returns view manager object.
//
function _rootGetView()
{
	return this.oView;
}

//
//						_rootGetViewDocRoot
//
function _rootGetViewDocRoot()
{
	return this.oViewRoot;
}

//
//						_rootGetEditDocRoot
//
function _rootGetEditDocRoot()
{
	return this.oEditRoot;
}

//
//						_rootLoadDocs
//
//	Checks for valid xmldoc and viewurl props and loads document.
//
function _rootLoadDocs()
{
	var fDidSomething = false;
	var sEditView = null;
	var bRet;
	with( this )
	{

		// load edit doc if spec'd and not already loaded
		if( oEditXml == null && hobj.getAttribute('xmlurl') != null )
		{
			if (navegador == "ie")
					{
					oEditXml = new ActiveXObject("Microsoft.XMLDOM");
					}
			else	
					{
					oEditXml = document.implementation.createDocument("","doc",null);
					}
					
			if( oEditXml == null )
			{
				err( "Por favor, actualize su versión de MSXML" );
				return;
			}
			oEditXml.async = false;
			oEditXml.validateOnParse = false;
			try {
				bRet = oEditXml.load( hobj.getAttribute('xmlurl') );
				// If there is an error while loading file ->
				if  (navegador == "ie")
					{
					if (oEditXml.parseError.errorCode != 0) {
						  alert(oEditXml.parseError.reason + " línea " + oEditXml.parseError.line);
							return;
						}
					}
				else {
				
					//
				
					}
				if( !bRet )
				{
					err( "No se pudo cargar el documeto: " + hobj.getAttribute('xmlurl') );
					return;
				}
				oEditRoot = oEditXml.documentElement;
				if( oEditRoot == null )
				{
					err( "Error: no se puede editar: " + hobj.getAttribute('xmlurl') );
					return;
				}
				
				// Searching children of edited document
				var children = oEditXml.childNodes;
				var i;
				for( i = 0; i < children.length; i++ )
				{
					// look for processing instruction nodes
					if( children[i].nodeType == 7 )
					{
					
						var name = children[i].nodeName;
						var proc = children[i].nodeValue;
						if( name == "edxview" )
						{
							sEditView = proc;
						}
					}
				}
				
				// save base path of XML file
				var sUrl = hobj.getAttribute('xmlurl');
				if( sUrl.lastIndexOf( "/" ) != -1 )
				{
					sBasePath = sUrl.substr( 0, sUrl.lastIndexOf( "/" ) + 1 );
				}
				else if( sUrl.lastIndexOf( "\\" ) != -1 )
				{
					sBasePath = sUrl.substr( 0, sUrl.lastIndexOf( "\\" ) + 1 );
				}
				else
				{
					sBasePath = "";
				}
			}
			catch(e)
			{
				err( "Error cargando el documento: " + e );
				oEditXml = null;
				return;
			}
			fDidSomething = true;
		}

		
		// if viewurl prop spec'd, that overdides proc instruction
		if( hobj.viewurl != null && hobj.viewurl.length > 0 )
			sEditView = hobj.viewurl;
		
		
		// load view doc if spec'd and not already loaded
		if( oViewXml == null && sEditView != null )
		{
			if (navegador == "ie")
					{
					oViewXml = new ActiveXObject("Microsoft.XMLDOM");
					}
			else	
					{
					oViewXml = document.implementation.createDocument("","",null);
					}
			if( oViewXml == null )
			{
				err( "No se pudo incializar el objeto XML. Necesita actualizar la versión de su navegador" );
				return;
			}
			oViewXml.async = false;
			oViewXml.validateOnParse = true;


			try {
				if (navegador == "ie")
					{
					bRet = oViewXml.load( utilResolveUrl( sEditView, sBasePath ) );
					}
				else
					{
					bRet = oViewXml.load( utilResolveUrl( sEditView, sBasePath ) );
					oViewXml.normalizeDocument();

					for (b = 0; b < oViewXml.childNodes[0].childNodes.length; b++)
						{
						if (is_ignorable(oViewXml.childNodes[0].childNodes[b]))
							{
								oViewXml.childNodes[0].removeChild(oViewXml.childNodes[0].childNodes[b]);
							}
						}						
					}

				if( !bRet )
				{
					err( "Error: No se pudo cargar la plantilla vista: " + sEditView );
					return;
				}
				
					oViewRoot = oViewXml.documentElement;

				if( oViewRoot == null )
				{
					err( "Error: couldn't get view root node for: " + sEditView );
					return;
				}
			}
			catch(e)
			{
				err( "Ha ocurrido un error en la carga del documento: " + e );
				oViewXml = null;
				return;
			}
			
			// instantiate the view class
			oView = new view( oViewRoot );

			fDidSomething = true;
		}

		// see if we've got both docs now	
		if( fDidSomething && oViewXml != null && oEditXml != null )
		{
			load();
		}
	}
}

//
//						_rootAssignID
//
//	Assigns a unique edxid and places in a hash for quick lookup.  Returns ID value.
//
function _rootAssignID( e )
{
	var sID = "e" + this.nextID++;
	this.dIDs[sID] = e;
	return sID;
}

//
//						_rootDeassignID
//
function _rootDeassignID( sID )
{
	if (this.dIDs[sID])
		{
		this.dIDs[sID] = null;
		}
	else
	{
		err( "deassignID: " + sID + " no encontrado" );
	}
}

//
//						_rootLookupID
//
//	Returns the node edx associated with ID
//
function _rootLookupID( sID )
{
	if (this.dIDs[sID])
		{
		return this.dIDs[sID];
		}
	else
	{
		err( "lookupID: " + sID + " no encontrado" );
	}
}

//
//						_rootWatchChanges
//
//	Register a particular edx node to monitor changes on a certain XML DOM node.
//
function _rootWatchChanges( oXmlNode, oEdx )
{
	with( this )
	{
		var a;		
	if (navegador == "firefox15")
		{
		
			if( dChanges.oXmlNode )
			{
				a = dChanges.oXmlNode ;
				a[a.length] = oEdx;
				//alert(a[a.length]);
			}
			else
			{
				a = new Array();
				a[0] = oEdx;
				dChanges[oXmlNode] = a ;
//				alert(serializa_me(oXmlNode));

				
			}
		}
	else
		{
			if( dChanges.Exists( oXmlNode ) )
				{

					a = dChanges.Item(oXmlNode );
					a[a.length] = oEdx;
				}
				else
				{
					a = new Array();
					a[0] = oEdx;
					dChanges.Add( oXmlNode, a );
				}
		}
	}
}

//
//						_rootUnwatchChanges
//
//	Un-register a particular edx node from monitoring changes on a certain XML DOM node.
//
function _rootUnwatchChanges( oXmlNode, oEdx )
{
	with( this )
	{
		if (navegador == "ie")
			{
				if( dChanges.Exists( oXmlNode ) )
				{
					var a = dChanges.Item(oXmlNode );
					var i;
					for( i = 0; i < a.length; i++ )
					{
					
						if( a[i] == oEdx )
						{
						//alert('es igual');
							// remove this one
							a.splice( i, 1 );
							return;
						}
					}
				}
			}
		else
			{
///////////
/*
var result = "";
for (var i in dChanges)
      result += i + " = " + dChanges[i] + "\n";
   alert( result);

alert(dChanges[oXmlNode]);
*/
///////////////
				if( dChanges[oXmlNode] )
					{
						var a = dChanges[oXmlNode] ;
						//alert(a.length);
						var i;
						for( i = 0; i < a.length; i++ )
							{
							
								if( a[i] == oEdx )
								{
								//alert('es igual');
									// remove this one
									a.splice( i, 1 );
									return;
								}
							}
					}
			}
		//err( "Error: unwatchChanges couldn't find object to de-register" );
	}
}

//
//						_rootAlertChange
//
//	Notify any listeners of a change on this node.  Interesting little two step here.
//	Some listeners may vanish from notify list as we issue the alerts.  Others may
//	arrive.  We do not want to not send to any vanished listeners nor do we want to
//	send to new arrivals, only the original and still valid.
//
function _rootAlertChange( oXmlNode, oEdx )
{
	with( this )
	{
	
		if (navegador == "ie")
			{
				if( dChanges.Exists( oXmlNode ) )
				{
				
					// get current notify list
					var a = dChanges.Item(oXmlNode );
					
					// snapshot it
					var aOrig = utilArrayCopy( a );
					
					// walk the snapshot notifying
					var i;
					for( i = 0; i < aOrig.length; i++ )
					{
						var e = aOrig[i];
		
						// see if it's still on the list
						if( utilArrayIndex( a, e ) != -1 )
						{
							// looks valid, do it
							e.onXmlNodeChange( oEdx );
						}
					}
				}
			}
		else
			{
				if( dChanges[oXmlNode] )
				{
					var a = dChanges[oXmlNode] ;
					
					var aOrig = utilArrayCopy( a );
					var i;
					for( i = 0; i < aOrig.length; i++ )
					{
						var e = aOrig[i];
		
						// see if it's still on the list
						if( utilArrayIndex( a, e ) != -1 )
						{
							// looks valid, do it
							e.onXmlNodeChange( oEdx );							
						}
					}
					
				}
			}
	}
}

//
//						_rootFieldCursor
//
//	Positive values enable the "fake" cursor.  A negative X value disables it.
//
function _rootFieldCursor( x, y, h )
{

	with( this )
	{
	//if (navegador == "firefox15") return;
		if( x < 0 )
		{
			if( cursorDiv != null )
				cursorDiv.style.display = "none";
			if( cursorTimeout != -1 )
			{
				window.clearTimeout( cursorTimeout );
				cursorTimeout = -1;
			}
			return;
		}
		
		// building cursor
		if( cursorDiv == null )
		{
			cursorDiv = document.createElement( "DIV" );
			cursorDiv.style.position = "absolute";
			cursorDiv.style.backgroundColor = "black";
			cursorDiv.style.width = "1px";
			cursorDiv.style.top = "0px";
			cursorDiv.style.zIndex = 10000;
			hobj.appendChild( cursorDiv );
		}

		// positioning cursor and showing it
		if (navegador == "ie")
			{
			 	var r = hobj.getBoundingClientRect();
			 	var oLeft = r.left;
			 	var oTop = r.top;
				var oHeight = h;
			}
		else 
			{
			 	var r = document.getBoxObjectFor(hobj);
				var oLeft = r.x;
				var oTop = r.y;
				var oHeight = h;
			}
		
		
		
		if (window.parent.window.parent.document.getElementById('toolbar'))
			{
			arbol = window.parent.window.parent.toolbar.checkea_tree();
			if (arbol != false) x = x - arbol - 2;
			else x = x - 2;
			y = y - 109;
			
			
			}
		else{
			
		}
		
		cursorDiv.style.left = x - oLeft;
		cursorDiv.style.top = y - oTop;
		
		cursorDiv.style.height = oHeight;
		cursorDiv.style.display = "block";
		if( cursorTimeout == -1 )
		{
			toggleCursor();
		}
	}
}

//
//						_rootToggleCursor
//
function _rootToggleCursor()
{
	with( this )
	{
		if( cursorDiv.style.backgroundColor == "black" )
			cursorDiv.style.backgroundColor = "white";
		else
			cursorDiv.style.backgroundColor = "black";
		if (navegador == "ie")
			{
			var cmd = hobj.id + ".eobj.toggleCursor()";
			}
		else
			{
			var cmd = "document.getElementById('" + hobj.id + "').eobj.toggleCursor()"
			}
		//alert(cmd);
		cursorTimeout = window.setTimeout( cmd, 300 );
	}
}

//
//						_rootGetXmlManager
//
function _rootGetXmlManager()
{
	return this.oXmlMgr;
}

//
//						rootOnSelectionChange
//
//	Catch global selection changes.  Has context in the parent behavior.
//
function rootOnSelectionChange()
{

	this.eobj.onSelectionChange();
}

//
//						_rootOnSelectionChange
//
function _rootOnSelectionChange(event)
{
	with( this )
	{
		if (navegador == "firefox15")
			{
			var sel = window.getSelection();
			if (sel == "") 
				{
				clearFieldSelection();
				return;
				}
			var tr = document.createRange();

			tr = sel;
			par = event.srcElement;
			if (par.parentNode.getAttribute('edxid'))
				{
					if (par.parentNode.childNodes[0].tagName == "IMG")
						{
						if (par.parentNode.childNodes[1].childNodes[0])
							{
							tr = par.parentNode.childNodes[1].childNodes[0].nodeValue;
							par = par.parentNode;
							}
						else
							{
							//alert(par.getAttribute('edxid'));
							}
						
						
					}
				}
			el_id =  par.getAttribute('edxid');
			while( par != null && par.getAttribute('edxid') == undefined )
					el_id =  par.getAttribute('edxid');
			var epar = dex[el_id];
			

			
			}
		else
			{
				var sel = window.document.selection;
				if( sel.type != "Text" )
					{
						clearFieldSelection();
						return;
					}
				var tr = sel.createRange();
				var par = tr.parentElement();
			// Obtaining parent of selection
		
				while( par != null && par.eobj == undefined )
					par = par.parentNode;
				if( par == null )
				{
					
					clearFieldSelection();
					return;
				}
				// attempt to build list of selected or partially selected fields
				
				var epar = par.eobj;
			}
		//
		
		var a = new Array();
		
		
		
		
		var er = epar.nodeClass == "field" ? epar : utilTraverseRight( epar, false );

		while( er != null && (er == epar || er.isChildOf( epar )) )
		{
			
			var esel = er.getSelection( tr );

//alert(esel.start);
			
			if( esel != null )
				a[a.length] = esel;
				

				
			er = utilTraverseRight( er, true );
		}

		// see what we got
		if( a.length == 0 )
		{
			clearFieldSelection();
			return;
		}

		// set it as the current selection and notify
		aFieldSelection = a;

		// shut off field cursor		
		fieldCursor( -1 );

		
		if (navegador == "ie")
			{
			root.hobj.edxselectionchange.fire();
			}
		else
			{
			selChange();
			}
		
	}
}

//
//						_rootClearFieldSelection
//
function _rootClearFieldSelection()
{
	with( this )
	{
		if( aFieldSelection != null )
		{
			aFieldSelection = null;
//			alert('_rootClearFieldSelection');
			if (navegador == "ie")
			{
				root.hobj.edxselectionchange.fire();
			}
			else
			{
				selChange();
			}
		}
	}
}

//
//						_rootCanApplyTag
//
//	Scans selected field list seeing if we can apply spec'd tag to all items.
//
function _rootCanApplyTag( sTag )
{
	with( this )
	{
		if( aFieldSelection == null )
			return false;
	
		var i;
		for( i = 0; i < aFieldSelection.length; i++ )
		{
			if( !aFieldSelection[i].oField.canApplyTag( sTag ) )
				return false;
		}
		// made it, looks like we're good
		return true;
	}
}

//
//						_rootApplyTag
//
//	First scan and see if any of the selected fields already have the spec'd tag applied.
//	If so, we unapply.  Otherwise we proceed and apply.
//
function _rootApplyTag( sTag )
{
	with( this )
	{
//	alert(aFieldSelection);
		if( aFieldSelection == null )
			return;

		// if we have lots of stuff selected, try to apply tag up high
		var bHigh = aFieldSelection.length > 2;

		// open a transaction
		var xmlmgr = root.getXmlManager();
		xmlmgr.openTransaction( null );
		
		// snapshot the top nodes and maps for all segments
		var aTopNodes = new Array();
		var aMaps = new Array();
		var v = getView();
		var i;
		for( i = 0; i < aFieldSelection.length; i++ )
		{
			var e = aFieldSelection[i].oField.getTopContainer();
			aTopNodes[i] = e.getXmlNode();
			aMaps[i] = v.getContainerMap( e.edxtemplate );
		}

		// see which way we're going, setting or clearing		
		var bSense = true;
		for( i = 0; i < aFieldSelection.length; i++ )
		{
			if( aFieldSelection[i].oField.isApplied( sTag, aTopNodes[i] ) )
			{
				bSense = false;
				break;
			}
		}
				
		
		// perform the apply or remove operations
		var aChanged = new Array();
		for( i = 0; i < aFieldSelection.length; i++ )
		{
			aChanged[i] = null;
			if( bSense )
			{
			//alert(aFieldSelection[i]);
				if( !aFieldSelection[i].oField.isApplied( sTag, aTopNodes[i] ) )
					aChanged[i] = aFieldSelection[i].oField.applyTag( sTag, aFieldSelection[i], bHigh, aTopNodes[i], aMaps[i] );
					if (aChanged[i] == null)
						{
						xmlmgr.closeTransaction();
						return;
						}
			}
			else
			{
				if( aFieldSelection[i].oField.isApplied( sTag, aTopNodes[i] ) )
					aChanged[i] = aFieldSelection[i].oField.removeTag( sTag, aFieldSelection[i], aTopNodes[i], aMaps[i] );
					if (aChanged[i] == null)
						{
						xmlmgr.closeTransaction();
						return;
						}
			}
		}

		// now run thru collapsing as many nodes as we can
		for( i = 0; i < aChanged.length; i++ )
		{
			if( aChanged[i] != null )
				utilCoalesceXmlNodes( aChanged[i], xmlmgr );
		}
		
		// build minimal notify list
		var aNotify = new Array();
		for( i = 0; i < aChanged.length; i++ )
		{
			if( aChanged[i] != null && utilArrayIndex( aNotify, aChanged[i] ) == -1 )
				aNotify[aNotify.length] = aChanged[i];
		}
		
		// clear the document selection
if (navegador == "ie")
		window.document.selection.empty();

		clearFieldSelection();
				
		// and do alerts to cause redraws
		for( i = 0; i < aNotify.length; i++ )
		{
			alertChange( aNotify[i], null );			
		}
		
		// close out transaction
		xmlmgr.setNotify( aNotify );
		xmlmgr.closeTransaction();
		if (navegador == "ie")
			e.load();
		else
			load();
	}
}

//
//						_rootDeleteTextSelection
//
//	Walks a selection deleting the individual pieces and redrawing the view(s) as
//	needed.
//
function _rootDeleteTextSelection()
{
	with( this )
	{
		var xmlmgr = root.getXmlManager();
		xmlmgr.openTransaction( null );
		
		// snapshot the top nodes and maps for all segments
		var aTopNodes = new Array();
		var i;
		for( i = 0; i < aFieldSelection.length; i++ )
		{
			var e = aFieldSelection[i].oField.getTopContainer();
			if( e == null )
			{
				aTopNodes[i] = aFieldSelection[i].oField.getXmlNode().parentNode;
			}
			else
			{
				aTopNodes[i] = e.getXmlNode();
			}
		}

		var aNotify = new Array();
		
		// first run thru and process any partial deletions (whole node not going away)
		for( i = 0; i < root.aFieldSelection.length; i++ )
		{
			var sel = root.aFieldSelection[i];
			if( !sel.bAll || !sel.oField.canDelete() )
			{
				// just remove some chars
				var e = sel.oField;
				var s = e.editText.substr( 0, sel.start ) + e.editText.substr( sel.end );
				e.editText = s;
				var n = e.getXmlNode();
				xmlmgr.process( "updateNode", n, s );
				if( utilArrayIndex( aNotify, n ) == -1 )
				{
					aNotify[aNotify.length] = n;
				}
			}
		}
		
		// now do the ones where whole nodes will vanish
		for( i = 0; i < root.aFieldSelection.length; i++ )
		{
			var sel = root.aFieldSelection[i];
			if( sel.bAll && sel.oField.canDelete() )
			{

				var e = sel.oField;
				var n = e.deleteNode( aTopNodes[i] );
				if( utilArrayIndex( aNotify, n ) == -1 )
				{
					aNotify[aNotify.length] = n;
				}
				root.load();
			}
		}

		// some of the XML nodes on the notify list may actually have been
		// deleted during course of processing subsequent nodes (coalescing action)
		// so we look for parentless nodes and remove 'em from list
		for( i = 0; i < aNotify.length; i++ )
		{
			if( aNotify[i].parentNode == null )
			{
				aNotify.splice( i, 1 );
				i--;
			}
		}		
		
		// done processing XML
		xmlmgr.setNotify( aNotify );
		xmlmgr.closeTransaction();
		
		// clear selection
		root.clearFieldSelection();
		if (navegador == "ie")
			window.document.selection.empty();
		
		// redraw the appropriate parent container(s)
		for( i = 0; i < aNotify.length; i++ )
		{
			alertChange( aNotify[i], null );
		}
	}
}

//
//						_rootCopyTextSelection
//
//	Copy text from selected fields to clipboard.
//
function _rootCopyTextSelection()
{
	with( this )
	{
		// walk fields grabbing their text
		var sText = "";
		var i;
		for( i = 0; i < aFieldSelection.length; i++ )
		{
			var sel = root.aFieldSelection[i];
			if( !sel.bAll )
			{
				// grab just selected char
				var e = sel.oField;
				sText += e.editText.substring( sel.start, sel.end )
			}
			else
			{
				var e = sel.oField;
				sText += e.editText;
			}
		}
		
		// place on clipboard
		if (navegador == "ie")
			{
			window.clipboardData.setData( "Text", sText );
			}
		else
			{
				//copy_clip(sText);
			}
	}		
}

//
//						_rootRestoreFocus
//
//	Kludge routine called by timeout to try to get focus back after doing a select in a field.
//
function _rootRestoreFocus()
{
	with( this )
	{
		if( aFieldSelection != null && aFieldSelection.length == 1 )
		{
			var e = aFieldSelection[0].oField;
			e.hobj.focus();
		}
	}
}


//
//						_rootStartSanityChecker
//
//	Initiates a timeout-based periodic sanity checking run.
//
function _rootStartSanityChecker()
{
	if( !this.hobj.sanityCheckActive )
	{
		setTimeout( this.hobj.id + ".eobj.sanityCheck();", 2000 );
	}
}
	

//
//						_rootSanityCheck
//
//	Very useful debug routine which walks the key data structures every second
//	and performs a series of sanity checks and validations.  Don't leave running
//	for production of course, but during development it will let you know quickly
//	if you've stomped on data integrity, saving many hours of tracing back thru
//	side effects.
//
function _rootSanityCheck()
{
	with( this )
	{
		var bSuccess = true;
		hobj.sanityCheckActive = false;
		
		// gather edxids an array as we go
		var d = new ActiveXObject("Scripting.Dictionary");
		bSuccess = edxnodeSanityCheck( d );
		
		if( dIDs.Count != d.Count )	
		{
			err( "sanityCheck failed: dIDs.Count=" + dIDs.Count + " sanity check count=" + d.Count );
			bSuccess = false;
		}
		
		// correlate the edxids from the two sources
		bSuccess &= rootCorrelate( dIDs, d );
		bSuccess &= rootCorrelate( d, dIDs );
		
		// run ourselves again in 1 second
		hobj.sanityCheckActive = true;
		setTimeout( root.hobj.id + ".eobj.sanityCheck();", bSuccess ? 1000 : 15000 );
	}
}
	
//
//						rootCorrelate
//
//	Compares to dictionaries for equivalency.
//
function rootCorrelate( d1, d2 )
{
	//  enumerate the dIDs and see what we're missing
	var da = (new VBArray(d1.Keys())).toArray();
	var s = "";
	var i;
		
	for( i = 0; i < da.length; i++ )
	{
		if( d2.Exists( da[i] ) )
		{
			if( d1.Item( da[i] ) != d2.Item( da[i] ) )
				s += "mismatch: " + da[i] + "\n";
		}
		else
		{
			s += "missing: " + da[i] + "\n";
		}
	}
	if( s.length != 0 )
	{
		err( "correlation error:\n" + s );
		return false;
	}
	return true;
}

