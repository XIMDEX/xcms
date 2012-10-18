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

function field( p, sTemp, sPath, sOptions, index )
{

//alert('field: ' + field.prototype.fieldInitd);
	if( field.prototype.fieldInitd == undefined )
	{
		this.initProto = _fieldInitProto;
		this.initProto( "field" );
		field.prototype.fieldInitd = true;
	}

	// init base class vars
	this.edxnode( p, sTemp, sPath, sOptions, index );
	
	// init our vars	
	this.nodeClass = "field";
	this.type = sTemp;
	this.fieldType = this.type.substr(6);
	if( this.fieldType != "flow" )
		err( "el tipo de campo debe ser del tipo flow" );
	this.sHTMLSnapshot = null;
	this.bHonorIndexOnFocus = false;
	
	// to do our own edition
	this.editText = null;
	this.cursorIndex = -1;
	
}

//
//						_fieldInitProto
//
function _fieldInitProto( sClass )
{
	// call base class proto
	this.initProto = _edxnodeInitProto;
	this.initProto( sClass );
	
	// install methods
	eval( sClass + ".prototype.field = field; " +
	      sClass + ".prototype.xhtml = _fieldXHTML; " +
	      sClass + ".prototype.cleanup = _fieldCleanup; " +
	      sClass + ".prototype.onXmlNodeChange = _fieldOnXmlNodeChange; " +
	      sClass + ".prototype.setCursorPosition = _fieldSetCursorPosition; " +
	      sClass + ".prototype.associate = _fieldAssociate; " +
	      sClass + ".prototype.getEnterAction = _fieldGetEnterAction; " +
	      sClass + ".prototype.newNode = _fieldNewNode; " +
	      sClass + ".prototype.splitNode = _fieldSplitNode; " +
	      sClass + ".prototype.saveNode = _fieldSaveNode; " +
	      sClass + ".prototype.enterRight = _fieldEnterRight; " +
	      sClass + ".prototype.enterLeft = _fieldEnterLeft; " +
	      sClass + ".prototype.processBackspaceKey = _fieldProcessBackspaceKey; " +
  	      sClass + ".prototype.processSuprKey = _fieldProcessSuprKey; " +
	      sClass + ".prototype.processDeleteKey = _fieldProcessDeleteKey; " +
	      sClass + ".prototype.replaceSelection = _fieldReplaceSelection; " +
	      sClass + ".prototype.getSelection = _fieldGetSelection; " +
	      sClass + ".prototype.getTopContainer = _fieldGetTopContainer; " +
	      sClass + ".prototype.canApplyTag = _fieldCanApplyTag; " +
	      sClass + ".prototype.isApplied = _fieldIsApplied; " +
	      sClass + ".prototype.applyTag = _fieldApplyTag; " +
	      sClass + ".prototype.removeTag = _fieldRemoveTag; " +
	      sClass + ".prototype.getObservingInstance = _fieldGetObservingInstance; " +
	      sClass + ".prototype.restoreCursor = _fieldRestoreCursor; " +
	      sClass + ".prototype.canDelete = _fieldCanDelete; " +
	      sClass + ".prototype.deleteNode = _fieldDeleteNode" );
}

//
//						_fieldAssociate
//
function _fieldAssociate( h )
{
	this.edxnodeAssociate( h );
	
	// sanity check the tag we're being attached to to make sure it support content editable
	// couldn't find a good way to do this so I'll just check a few that bit ME
	switch( h.tagName.toUpperCase() )
	{
	case "TR":
	case "TD":
		err( "Error: no se puede asociar un campo:* plantilla a la etiqueta " + h.tagName + " en este momento." );
		return;
	default:
		break;
	}
	
	// set up for editing
	h.tabIndex = 0;
//	alert("tab ->" + h.tabIndex);
//	alert("inner ->" + h.innerHTML);

	var editnode = this.getXmlNode();
//alert(serializa_me(editnode));
	var s = utilGetXmlNodeValue( editnode );
//	alert('field: ' + s);
	h.innerText = s;
	this.editText = s;
	this.root.watchChanges( editnode, this );

	// attach event handlers
	h.attachEvent("onfocus", fieldOnFocus );
	h.attachEvent("onblur", fieldOnBlur );
	h.attachEvent( "onclick", fieldOnClick );
	h.attachEvent("onkeydown", fieldOnKeyDown );
	h.attachEvent("onkeyup", fieldOnKeyUp );
	h.attachEvent( "onkeypress", fieldOnKeyPress );

}

//
//						_fieldXHTML
//
function _fieldXHTML( oFrag )
{
//alert('xhtml');
	var sClass = utilGetXmlAttribute( oFrag, "class" );
	if (variable_estilo)
	{
		if (variable_estilo != "" && sClass=="@clase"){
			oFrag.setAttribute( 'class', variable_estilo );
			}
		else if (variable_estilo == "") oFrag.setAttribute( 'class', 'normal' );
	}
	else if (variable_estilo == "" && sClass=="@clase"){
		oFrag.setAttribute( 'class', 'normal' );
		}
	// if we have HTML code associated, we show it directly
	if( this.hobj != null )
	{
		var editnode = this.getXmlNode();
		var s = utilGetXmlNodeValue( editnode );
		this.hobj.innerText = s;
		this.editText = s;
		return null;	// 
	}
			if (navegador == "ie")
					{
					oEditXml2 = new ActiveXObject("Microsoft.XMLDOM");
					}
			else	
					{
					oEditXml2 = document.implementation.createDocument("","doc",null);
					}
	
	var MyText = oEditXml2.createTextNode("-");
	oFrag.appendChild(MyText);
	return oFrag;
}


//
//						fieldOnFocus
//
//	Note: Contains the "event" of object 'field'
//
function fieldOnFocus(event)
{
limpia_ventana();
	if (navegador == "firefox15")
		{
		var eTarget = event.target || event.srcElement;
		var e = new Object();
		var el_id = event.srcElement.getAttribute('edxid');
		e = dex[el_id];
		}
	else
		{
		var h = window.event.srcElement;
		var e = h.eobj;
		}
	e.root.focusField = e;
	
	// see if we're supposed to go to spec'd position
	if( e.bHonorIndexOnFocus )
		e.bHonorIndexOnFocus = false;
	else
		e.cursorIndex = 0;


	// make a text range pointing to current element's text		
	if (navegador == "ie")
		{
		var tr = window.document.selection.createRange();
		tr.moveToElementText( e.hobj );
		}
	else
		{
		var tr = window.getSelection()
		}
	

	
	// Checks if contain is between brackets: if this is so we select all the text.
	if( e.editText.length > 0 
		&& e.editText.charAt(0) == "[" 
		&& e.editText.charAt(e.editText.length-1) == "]" 
	)
	{
		if( e.root.aFieldSelection != null
			&& e.root.aFieldSelection.length == 1
			&& e.root.aFieldSelection[0].oField == e
			&& e.root.aFieldSelection[0].bAll == true
		)
		{
			
			
			// if it is alredy selected
			return;
		}
		
		e.cursorIndex = 0;
		if (navegador == "ie") tr.select();
		else
			{
//			alert(eTarget.innerHTML);
			window.getSelection().selectAllChildren(eTarget);
			}
		setTimeout( rootid + ".eobj.restoreFocus()", 100 );
		return;
	}
//alert(e.cursorIndex);
	// Searching coordenates of the cursor
	if (navegador == "ie")
		{
		tr.move( "character", e.cursorIndex );
		var rect = tr.getClientRects()[0];
		e.root.fieldCursor( rect.right, rect.top, rect.bottom - rect.top );
		}
	else
		{
		var rect = document.getBoxObjectFor(event.srcElement);
		//alert("y -> " + rect.y);
		e.root.fieldCursor( rect.width, rect.y, rect.height );
		}
		
	
	
}

//
//						_fieldSetCursorPosition
//
//	Looks at position of input index and adjusts cursor position accordingly.
//
function _fieldSetCursorPosition( h )
{
	with( this )
	{
		
		var s = editText.substr( 0, cursorIndex );

		if (navegador == "ie")
			{
			var tr = window.document.body.createTextRange();
			tr.moveToElementText( h );
			tr.collapse( true );
			tr.moveEnd( "character", cursorIndex );
			var r = tr.getClientRects();
			var rcnt = r.length;
			var rect = r[rcnt - 1];
			}
		else
			{
			var tr = document.createRange();
//			tr.setSelectionRange(0,0);
			tr.selectNode(h);

			text = tr.toString();

			var rect = document.getBoxObjectFor(h);

			//alert(rect.screenY + " " + rect.y);
			oLeft = rect.x + (cursorIndex * (rect.width /  h.childNodes[0].length)) ;
			oTop = rect.y;
			oHeight = rect.height;
			oWidth = rect.width
			}
		if (navegador == "firefox15")
			{
//evt.clientX, rect.clientY

				root.fieldCursor( oLeft, rect.y, rect.height );
			}
		
		
		if (navegador == "ie")
			{
				// expand by one char to see if we're at end of a line
				if( cursorIndex != editText.length )
				{
					tr.expand( "character" );
					r = tr.getClientRects();
					if( r.length != rcnt )
					{
						// grew a new rectangle, go to start of it
						rect = r[r.length - 1];
						root.fieldCursor( rect.left, rect.top, rect.bottom - rect.top );
						return;
					}
			}
		}
		if (navegador == "ie")
			{
			// stay with end of previous
			root.fieldCursor( rect.right, rect.top, rect.bottom - rect.top );
			}
		else
			{
			root.fieldCursor( oLeft, rect.y, rect.height );
			}
	}
}

//
//						_fieldEnterRight
//
//	Enters field at far right edge.
//
function _fieldEnterRight()
{
	with( this )
	{
		cursorIndex = editText.length;
		bHonorIndexOnFocus = true;
		hobj.focus();
	}
}

//
//						_fieldEnterLeft
//
//	Enters field at far left edge.
//
function _fieldEnterLeft()
{
	with( this )
	{
		cursorIndex = 0;
		bHonorIndexOnFocus = true;
		hobj.focus();
	}
}

//
//						_fieldProcessBackspaceKey
//
function _fieldProcessBackspaceKey()
{
	with( this )
	{
		if( cursorIndex > 0 )
		{
			var s = editText;
			cursorIndex--;
			var s = s.substr( 0, cursorIndex ) + s.substr( cursorIndex + 1 );
			
			hobj.innerText = s;
			editText = s;
			setCursorPosition( hobj );
			return;
		}
		
		// find where we're going
		var el = utilTraverseLeft( this, true );
		var oCursor = null;
		if( el != null )
		{
			// save position as an XML node signature and an offset into same
			var leftPosition = el.editText.length;
			var leftSig = utilGetSigFromNode( el.getXmlNode() );
			var instance = el.getObservingInstance();
			oCursor = new cursorSave( leftSig, leftPosition, instance );
			//alert('');
		}
		
		// see if we scrub where we've been
		if( editText.length == 0 )
		{
			var xmlmgr = root.getXmlManager();
			xmlmgr.openTransaction( null );
			var n = deleteNode( getTopContainer() );
			xmlmgr.setNotify( n );
			xmlmgr.closeTransaction();
			root.alertChange( n, null );
		}
		
		// go to new home
		restoreCursor( oCursor );
	}
}

//
//						_fieldProcessSuprKey
//
function _fieldProcessSuprKey()
{
	with( this )
	{
		if( cursorIndex > 0 )
		{
			var s = editText;
			//cursorIndex--;
			var s = s.substr( 0, cursorIndex ) + s.substr( cursorIndex + 1 );
			
			hobj.innerText = s;
			editText = s;
			setCursorPosition( hobj );
			return;
		}
		
		// find where we're going
		var el = utilTraverseLeft( this, true );
		var oCursor = null;
		if( el != null )
		{
			// save position as an XML node signature and an offset into same
			var leftPosition = el.editText.length;
			var leftSig = utilGetSigFromNode( el.getXmlNode() );
			var instance = el.getObservingInstance();
			oCursor = new cursorSave( leftSig, leftPosition, instance );
			//alert('');
		}
		
		// see if we scrub where we've been
		if( editText.length == 0 )
		{
			var xmlmgr = root.getXmlManager();
			xmlmgr.openTransaction( null );
			var n = deleteNode( getTopContainer() );
			xmlmgr.setNotify( n );
			xmlmgr.closeTransaction();
			root.alertChange( n, null );
		}
		
		// go to new home
		restoreCursor( oCursor );
	}
}

//
//						_fieldGetObservingInstance
//
//	Used to help freeze dry a cursor position.  When multiple fields are observing
//	the same XML node, this finds the instance # of the current field so that we
//	can return to the proper observing instance later.  See restoreCursor() below for more.
//
function _fieldGetObservingInstance()
{
	with( this )
	{
		var i = 0;
		var e = root;
		var bDir = false;
		var editnode = getXmlNode();
		while( e != null )
		{
			var e = utilTraverseRight( e, bDir );
			if( e == this )
				return i;
			if( e.getXmlNode() == editnode )
				i++;
			bDir = true;
		}
		err( "getObservingInstance: couldn't find self" );
		return 0;
	}
}

//
//						_fieldRestoreCursor
//
//	Restores cursor position from freeze-dried snapshot.
//
function _fieldRestoreCursor( oCursor )
{
	with( this )
	{
		if( oCursor != null )
		{
			// re-hydrate sig to node
			var nLeft = utilGetNodeFromSig( oCursor.signature, root.oEditXml );
			var inst = oCursor.instance;
			
			// traverse all fields until we find one that owns this node
			var eCur = root;
			var bDir = false;
			while( true )
			{
				eCur = utilTraverseRight( eCur, bDir );
				if( eCur == null )
					break;
				bDir = true;
				if( eCur.getXmlNode() == nLeft )
				{
					if( inst-- == 0 )
					{
						eCur.cursorIndex = oCursor.offset;
						eCur.bHonorIndexOnFocus = true;
						eCur.hobj.blur();	// enfocamos el cursor
						eCur.hobj.focus();
						return;
					}
				}
			}
		}
		
		
		root.fieldCursor( -1 );
	}
}

//
//						_fieldProcessDeleteKey
//
function _fieldProcessDeleteKey()
{
//alert(root.fieldCursor);
	with( this )
	{	
//	alert();
		// check for selection
		if( root.aFieldSelection != null )
		{
			root.fieldCursor( -1 );
			
			// see if we can find a cursor spot to return to
			var oCursor = null;
			if( root.aFieldSelection[0].bAll )
			{
				var eLeft = utilTraverseLeft( root.aFieldSelection[0].oField );
				if( eLeft != null )
				{
					var leftSig = utilGetSigFromNode( eLeft.getXmlNode() );
					var instance = eLeft.getObservingInstance();
					oCursor = new cursorSave( leftSig, eLeft.editText.length, instance );
				}
			}
			else
			{
				var e = root.aFieldSelection[0].oField;
				var instance = e.getObservingInstance();
				var leftSig = utilGetSigFromNode( e.getXmlNode() );
				oCursor = new cursorSave( leftSig, e.start, instance );
			}
			root.deleteTextSelection();
			
			restoreCursor( oCursor );
			return;
		}
		
		if( editText.length > cursorIndex )
		{
			var s = editText;
			s = s.substr( 0, cursorIndex ) + s.substr( cursorIndex + 1 );
			hobj.innerText = s;
			editText = s;
		}
	}
}

//
//						_fieldReplaceSelection
//
function _fieldReplaceSelection( sText )
{
	with( this )
	{
		root.fieldCursor( -1 );
			
		// pop the new char into the first selected field
		var oCursor = null;
		var oSel = root.aFieldSelection[0];
		
//		alert(sText);
		
		var e = oSel.oField;
		var h = e.hobj;
		var s = e.editText;
		
		e.editText = s.substr( 0, oSel.start ) + sText + s.substr( oSel.start );
		h.innerText = e.editText;

		// modify the first selection to start after newly added char
		oSel.start += sText.length;
		oSel.end += sText.length;
		oSel.bAll = false;

		// grab this position
		var leftSig = utilGetSigFromNode( oSel.oField.getXmlNode() );
		var instance = oSel.oField.getObservingInstance();
		oCursor = new cursorSave( leftSig, oSel.start, instance );

		root.deleteTextSelection();
		
// 		this.parent.load();
	
		restoreCursor( oCursor );
	}
}

//
//						_fieldCanDelete
//
function _fieldCanDelete()
{
	if( !this.parent.permitChildDelete() )
		return false;
	return true;
}

//
//						_fieldDeleteNode
//
//	Deletes a leaf edit node.  Expects an already-eop XML manager transaction.
//
function _fieldDeleteNode( nTopContainer )
{
	with( this )
	{
		var editnode = getXmlNode();
		var xmlmgr = root.getXmlManager();

		// delete the nodes and any newly empty parent(s)
		var nTop = utilDeleteXmlNode( editnode, nTopContainer, xmlmgr );
		
		// coalesce if possible
		if( nTop != nTopContainer)
		{
			utilCoalesceXmlNodes( nTop, xmlmgr );
		}

		return nTop;		
	}		
}


//
//						fieldOnKeyDown
//
//	Capturando los eventos del teclado del usario
//
function fieldOnKeyDown(event)
{
var eTarget = event.target || event.srcElement;

	if (navegador == "firefox15")
		{ 
		var keyCode = event.charCode;
		if (keyCode == 0) keyCode = event.keyCode;
			if (keyCode == 8)
				{
					event.stopPropagation();
					event.returnValue = false;
			  		event.preventDefault();
				}
		
		
		var h = event.srcElement;
		var e = new Object();
		var el_id = event.srcElement.getAttribute('edxid');
		e = dex[el_id];
		}
	else
		{
		var keyCode = window.event.keyCode;
		var h = window.event.srcElement;
		var e = h.eobj;
		}
	if( keyCode == 16 || keyCode == 17 || keyCode == 18 )
		return;
	
	switch( keyCode )
	{
	case 16:		// shift
	case 17:		// ctrl
	case 18:		// alt
		return;
	
	case 39:		// right arrow
	
	
		if( e.root.aFieldSelection != null )
		{
			e.root.clearFieldSelection();
			if (navegador == "ie")
				window.document.selection.empty();
		}		
		if( e.cursorIndex < e.editText.length )
		{
//			alert( event.ctrlKey );
			if( event.ctrlKey )
			{
				var w = e.editText.substr( e.cursorIndex ).search( /\s+/ );
				if( w != -1 )
				{
					e.cursorIndex += w;
					w = e.editText.substr( e.cursorIndex ).search( /\w/ );
					if( w != -1 )
					{
						e.cursorIndex += w;
						e.setCursorPosition(eTarget);
						break;
					}
				}
				e.cursorIndex = e.editText.length;
			}
			else
				e.cursorIndex++;
			
//			alert(e.editText);
			
			e.setCursorPosition(eTarget);
		}
		else
		{
			// look for adjacent field to hop to
			var er = utilTraverseRight( e, true );
			if( er != null )
			{
				er.enterLeft();
			}
		}
		break;
		
	case 37:		// left arrow
		
		if( e.root.aFieldSelection != null )
		{
			e.root.clearFieldSelection();
			if (navegador == "ie")
				window.document.selection.empty();
		}		
		if( e.cursorIndex > 0 )
		{
			if( event.ctrlKey )
			{
				var w = e.cursorIndex;
				while( w > 0 && e.editText.charAt( w-1 ) != " "  )
					w--;
				if( w == e.cursorIndex )
				{
					while( w > 0 && e.editText.charAt( w-1 ) == " " )
						w--;
					while( w > 0 && e.editText.charAt( w-1 ) != " " )
						w--;
				}
				e.cursorIndex = w;		
			}
			else
				e.cursorIndex--;
			e.setCursorPosition(eTarget);
		}
		else
		{
			var el = utilTraverseLeft( e, true );
			if( el != null )
			{
				el.enterRight();
			}
		}
		break;
	
	case 8:			// backspace		
		if( e.root.aFieldSelection != null )
		{
			e.processDeleteKey();
			break;
		}
		
		// normal processing	
		e.processBackspaceKey();

	    break;
	case 46:		// delete
		if( e.root.aFieldSelection != null )
		{
			e.processDeleteKey();
			break;
		}
		e.processSuprKey();
		// normal processing	
		
		break;
	case 53:		// delete
		if( event.shiftKey )
		{
			// see if we need to process a pending selection
			if( e.root.aFieldSelection != null )
			{
				e.replaceSelection( "%" );
				return;
			}
			var s = e.editText;
			e.editText = s.substr( 0, e.cursorIndex ) + "%" + s.substr( e.cursorIndex );
			h.innerText = e.editText;
			e.cursorIndex ++;
			e.setCursorPosition( eTarget );
		}
		break;		
	case 67:		// ctrl-C (copy)
	case 76:		// ctrl-V (paste)
	case 78:		// ctrl-X (cut)
		break;
		
	default:
		return;
	}
	
	// consume the event
	if (navegador != "ie")
		{
		//evt.charCode = 0;
		event.stopPropagation();
		}
	else 
		{
		if (keyCode!=46)
			{
			window.event.keyCode = 0;
			window.event.cancelBubble = true;
			}
		}
}


//
//						fieldOnKeyPress
//
//	Note: has HTML node 'this' context
//
//	Oddly even tho we are cancelling bubble, we still end up having this
//	prop up to top level frame.
//
function fieldOnKeyPress(event)
{
var eTarget = event.target || event.srcElement;


	if (navegador == "firefox15")
		{
        
		var keyCode = event.keyCode;

		if (keyCode == 33 || keyCode == 34 || keyCode == 35 || keyCode == 36  )
			{
			 //Void keys 'init' 'end' 'avpag' 'repag' for ximEDITOR
		            event.stopPropagation();
					event.returnValue = false;
			  		event.preventDefault();
					if(keyCode==33)
					{
					 keyCode=3333;
					}
					if(keyCode==34)
					{
					 keyCode=3434;
					}
					if(keyCode==35)
					{
					 keyCode=3535;
					}
					if(keyCode==36)
					{
					 keyCode=3636;
					}
			}
		if(keyCode==46) 
			{
			 keyCode=4646;
			} 
		if(keyCode==9)
		{
		 keyCode=9900;
		}
		if(event.keyCode==0 && event.charCode==0) return; //key Alt Gr
		if(event.keyCode==19 && event.charCode==0) return; //key Pausa Inter
		if(event.keyCode==20 && event.charCode==0) return; //keyBloq Mayus	
		if(event.keyCode==27 && event.charCode==0) return; //keyEsc
		if(event.keyCode==37 && event.charCode==0) return; //left arrow									
		if(event.keyCode==38 && event.charCode==0) return; //up arrow									
	    if(event.keyCode==39 && event.charCode==0) return; //right arrow									
		if(event.keyCode==40 && event.charCode==0) return; //downp arrow									
		if(event.keyCode==44 && event.charCode==0) return; //key Imp Pant
		if(event.keyCode==45 && event.charCode==0) return; //key Insert
		if(event.keyCode==91 && event.charCode==0) return; //key Windows
		if(event.keyCode==145 && event.charCode==0) return; //key Bloq
		if(event.keyCode==112 && event.charCode==0) return; //key F1
		if(event.keyCode==113 && event.charCode==0) return; //key F2
		if(event.keyCode==114 && event.charCode==0) return; //key F3
		if(event.keyCode==115 && event.charCode==0) return; //key F4
		if(event.keyCode==116 && event.charCode==0) return; //key F5
		if(event.keyCode==117 && event.charCode==0) return; //key F6
		if(event.keyCode==118 && event.charCode==0) return; //key F7
		if(event.keyCode==119 && event.charCode==0) return; //key F8
		if(event.keyCode==120 && event.charCode==0) return; //key F9
		if(event.keyCode==121 && event.charCode==0) return; //key F10
		if(event.keyCode==122 && event.charCode==0) return; //key F11
		if(event.keyCode==123 && event.charCode==0) return; //key F12
		if(event.keyCode==144 && event.charCode==0) return; //key Bloq Num
		if (keyCode == 0){
			keyCode = event.charCode;
			if (keyCode == 99 && event.ctrlKey)
				{
				// Creating a new keycode 
				keyCode = 9999;
				}
			else if (keyCode == 118 && event.ctrlKey)
				{
				keyCode = 118118;
				}
			else if (keyCode == 120 && event.ctrlKey)
				{
				keyCode = 120120;
				}			
		}
		if (keyCode == 8  || keyCode == 4646 || keyCode==9900)
				{
					event.stopPropagation();
					event.returnValue = false;
			  		event.preventDefault();
				}
		var h = event.srcElement;
		
		var e = new Object();
		var el_id = event.srcElement.getAttribute('edxid');
		e = dex[el_id];
		
		if (e.editText == "[nuevo]")
			{
			e.cursorIndex = 0;
			window.getSelection().selectAllChildren(eTarget);
			setTimeout( rootid + ".eobj.restoreFocus()", 100 );
			e.root.onSelectionChange(event);
//			alert(e.root.aFieldSelection);
			}
		
		}
	else
		{
		var keyCode = window.event.keyCode;
		var h = window.event.srcElement;
		var e = h.eobj;
		}

	switch( keyCode )
	{
	case 3636:
	     event.keyCode=0;
         return false;
	case 3535:
	    event.keyCode=0;
        return false;
	case 3434:
	  event.keyCode=0;
      return false;
	case 3333:
	   event.keyCode=0;
       return false;  
	case 37:
		break;
	case 9999:
	case 120120:
	case 3:			// ctrl-C (copy)
	case 24:		// ctrl-X (cut)
		// Checking selection to lead to the clipboard
			if (event.ctrlKey)
				{

					if( e.root.aFieldSelection != null )
					{
					
					e.root.copyTextSelection();
					
					 if(navegador=="ie")
					 {			
						//e.root.copyTextSelection(); //On Firefox function copy does not work copy_clip
						
						// to activate ctrl + x -> ask for code 120120
						if( keyCode == 24 )
						{
							e.processDeleteKey();
						}
					 }
					 else
					 {
						//In this line it enter both for ctrl+x and for ctrl+c firefox
					 	if( keyCode == 120120 )
						{
						// alert("ctrl+x firefox");
							e.processDeleteKey();
						}

					 }	
					
					}
					break;
				}
		break;
	
	case 8:			// ctrl-H (backspace)
		break;
	case 4646:
	    break;
	case 9900:
	    break;
	case 10:
	case 13:
		// ignore this when there's a selection pending (no reason, just avoiding writing
		// complicated code)
		if( e.root.aFieldSelection != null )
			break;
			
		// do node splitting, auto-insertion handling here
		switch( e.getEnterAction() )
		{
		case "split":
			e.splitNode();
			break;
		case "new":
			e.newNode();
			break;
		default:
			break;
		}
		break;
	case 118118:	// ctrl-V (paste)
	case 22:		// ctrl-V (paste)
		if(firefox15)
		{
		/*
		netscape.security.PrivilegeManager.enablePrivilege('UniversalXPConnect');

		var clip = Components.classes['@mozilla.org/widget/clipboard;1'].createInstance(Components.interfaces.nsIClipboard);
			alert(clip);
			if (!clip) return false;
			
			var trans = Components.classes["@mozilla.org/widget/transferable;1"].
			              createInstance(Components.interfaces.nsITransferable);
			if (!trans) return false;
			trans.addDataFlavor("text/unicode");
		*/

		 if (event.ctrlKey)
			{
			

			params = new Array();
			paramsH = new Array();
			paramsE = new Array();		
			
		 	params = e;
			paramsH = h;
			paramsE = eTarget;
			
			document.getElementById('Vmodal').src = "../actions/xmleditor/inc/paste.php";
			document.getElementById('Vmodal').style.width = "540px";
			document.getElementById('Vmodal').style.height = "320px";
			document.getElementById('Vmodal').style.top = (window.innerHeight / 2) - 200;
			document.getElementById('Vmodal').style.left = (window.innerWidth / 2) - 300;
			document.getElementById('Vmodal').style.visibility = "visible";

			}
			break;
		
		}
		else //Ctrl + V para IE
		{
		 // get data to paste
		 if (event.ctrlKey)
			{
			//alert(window.clipboard);
			var sText = window.clipboardData.getData( "Text" );
			if( sText == "" || sText == null )
				break;
				
			// see if we need to process a pending selection
			if( e.root.aFieldSelection != null )
			{
				e.replaceSelection( sText );
				return;
			}
			var s = e.editText;
			e.editText = s.substr( 0, e.cursorIndex ) + sText + s.substr( e.cursorIndex );
			h.innerText = e.editText;
			e.cursorIndex += sText.length;
			e.setCursorPosition( eTarget );
			}
			break;
		}
	case 25:		// ctrl-Y (redo)
		var xmlmgr = e.root.getXmlManager();
		if( xmlmgr.canRedo() )
		{
			e.root.fieldCursor( -1 );
			xmlmgr.redo();
		}
		break;
		
	case 26:		// ctrl-Z (undo)
		e.saveNode();
		var xmlmgr = e.root.getXmlManager();
		if( xmlmgr.canUndo() )
		{
			e.root.fieldCursor( -1 );
			xmlmgr.undo();
		}
		break;
	default:
	
		//alert(e.root.aFieldSelection);
		// see if we need to process a pending selection
		if( e.root.aFieldSelection != null )
		{
			e.replaceSelection( String.fromCharCode( keyCode ) );
			return;
		}
		if (navegador == "firefox15") keyCode = event.charCode;
		var s = e.editText;
		e.editText = s.substr( 0, e.cursorIndex ) + String.fromCharCode( keyCode ) + s.substr( e.cursorIndex );
		h.innerText = e.editText;
		e.cursorIndex++;
		e.setCursorPosition( eTarget );
		break;
	}
	// consume the event
	if (navegador != "ie")
		{
		//evt.charCode = 0;
		event.stopPropagation();
		}
	else 
		{
		window.event.keyCode = 0;
		eTarget.cancelBubble = true;
		}
	
}

//
//						fieldOnKeyUp
//
//	Note: has HTML node 'this' context
//
function fieldOnKeyUp(event)
{
var eTarget = event.target || event.srcElement;


if (navegador == "firefox15")
		{
		
		var keyCode = event.charCode;
		if (keyCode == 0) keyCode = event.keyCode;
		if (keyCode == 0)
			{
			return;
			}
		var h = event.srcElement;
		var e = new Object();
		var el_id = event.srcElement.getAttribute('edxid');
		e = dex[el_id];
		}
	else{
		var keyCode = window.event.keyCode;
		var e = window.event.srcElement.eobj;
	}
	switch( keyCode )
	{
	case 16:		//shift
	case 17:		// ctrl
	case 18:		// alt
		return;
	
	default:
		return;
	}
	// consume the event
	//eTarget.charCode = 0;
	/*if (navegador == "ie")
		{
		eTarget.cancelBubble = true;
		}
	else
		{
		event.stopPropagation();
		}*/
}

//
//						fieldOnBlur
//
//	Checks for change in the field and captures the value to XML if necessary.
//
//	Note: has HTML node 'this' context
//
function fieldOnBlur(event)
{
var eTarget = event.target || event.srcElement;
	if (navegador == "firefox15")
		{
		
		var e = new Object();
		var el_id = event.srcElement.getAttribute('edxid');
		e = dex[el_id];
		h = event.srcElement;
		}
	else
		{
		var h = window.event.srcElement;
		var e = h.eobj;
		}

	// Saving the node if it has changed.
	e.saveNode();
			
	// turn off cursor for this field
	e.root.fieldCursor( -1 );
}

//
//						fieldOnClick
//
function fieldOnClick(event)
{
var eTarget = event.target || event.srcElement;
	limpia_ventana();
	if (navegador == "firefox15")
		{
//		alert('al');
		var e = new Object();
		var el_id = event.srcElement.getAttribute('edxid');
		h = event.srcElement;
		e = dex[el_id];
		}
	else
		{
		var h = window.event.srcElement;
		var e = h.eobj;
		}
	//var h = window.event.srcElement;
	//var e = h.eobj;
	
	
	if (navegador == "ie")
		{
		var tr = window.document.selection.createRange();
		var rect = tr.getClientRects()[0];
		//alert(rect.left);
		e.root.fieldCursor( rect.left, rect.top, rect.bottom - rect.top );
		}
	else
		{
		var tr = window.getSelection();
		var rect = document.getBoxObjectFor(event.srcElement);
		e.root.fieldCursor( event.clientX, rect.clientY, rect.height);
		}

	
	if (navegador == "ie")
		{
			var tr2 = window.document.body.createTextRange();
			tr2.moveToElementText( h );
			tr2.setEndPoint( "EndToStart", tr );
			e.cursorIndex = tr2.text.length;
		}
	else
		{
			var tr2 = document.createRange();
			tr2.selectNode(event.srcElement);
			tr2.setEnd(event.srcElement,tr.anchorNode);
			e.cursorIndex = tr.anchorOffset;
		}
if (navegador == "firefox15")
	{
	e.root.onSelectionChange(event);
	}
}


//
//						_fieldSaveNode
//
//	Saves off to XML if changed.
//
function _fieldSaveNode()
{
	with( this )
	{
		var editnode = getXmlNode();
		
		if( editnode != null )
		{
			if( utilGetXmlNodeValue( editnode ) != hobj.innerText )
			{
				
				var xmlmgr = root.getXmlManager();
				
				xmlmgr.openTransaction( editnode );
				xmlmgr.process( "updateNode", editnode, hobj.innerText );
				xmlmgr.closeTransaction();

				root.alertChange( editnode, this );
			}
		}
	}
}

//
//						_fieldOnXmlNodeChange
//
function _fieldOnXmlNodeChange( sender )
{
	// ignore updates from ourself
	if( sender == this )
		return;

	var editnode = this.getXmlNode();
	if( editnode != null )
	{
		if( utilGetXmlNodeValue( editnode ) != this.hobj.innerText )
		{
			this.editText = utilGetXmlNodeValue( editnode );
			this.hobj.innerText = this.editText;
			this.cursorIndex = 0;
		}
	}
	
}

//
//						_fieldGetEnterAction
//
//	Walks up tree looking for a defined enter action: split, new, or none
//	Defaults to 'none' if no definition is found.
//
function _fieldGetEnterAction()
{
	var action = "none";
	var cur = this;
	while( cur != null )
	{
		if( cur.enterAction != undefined )
		{
			action = cur.enterAction;
			break;
		}
		cur = cur.parent;
	}
	return action;
}

//
//						_fieldNewNode
//
//	Walks up tree looking for appropriate place to create a new template node.
//
function _fieldNewNode()
{
	err( "newNode not implemented yet" );
}

//
//						_fieldSplitNode
//
//	Splits the current node and walks up tree splitting until it hits a barrier.
//	Used to generate new paragraphs on enter and that sort of thing.
//
function _fieldSplitNode()
{
	with( this )
	{

		root.fieldCursor( -1 );				// turn off cursor
		saveNode();							// save off in case changed

		// perform the split		
		var editnode = getXmlNode();
		var xmlmgr = root.getXmlManager();
		xmlmgr.openTransaction( null );		// don't know alert node yet
		xmlmgr.process( "splitText", editnode, cursorIndex );
		var newNode = editnode.nextSibling;
		
		// find the index of where we split
		var i;
		var par = editnode.parentNode;

		for( i = 0; i < par.childNodes.length; i++ )
		{
			if( par.childNodes[i] == newNode )
				break;
		}
		if( i == par.childNodes.length )
		{
			err( "splitNode couldn't find next sibling" );
			return;
		}
				
		// walk up the tree splitting until we hit the container that says no
		var topChild;
		if( !parent.canSplit() )
			topChild = this;
		else
			topChild = parent.splitNode( i, editnode );
		
		// set undo/redo notify and close out
		xmlmgr.setNotify( topChild.parent.getXmlNode() );
		xmlmgr.closeTransaction();

		// update parent container
		var enew = topChild.parent.nodeHasSplit( topChild );
		
	
		//topChild.parent.load();
		// establish focus in new node
		var enew = utilTraverseRight( enew, false );
		enew.hobj.focus();
		
	}
}

//
//						_fieldGetSelection
//
//	Looks to see if all or part of current field lies inside the supplied
//	text range.  If an intersection is found, returns a fieldSelection object
//	defining the selection, else null.
//
function _fieldGetSelection( tr )
{

	var start, end, dup;
	with( this )
	{
	var tr_in = false;
	var out_in = false;
	
	
	
		if (navegador == "ie")
			{
			var ourtr = window.document.body.createTextRange();
			ourtr.moveToElementText( hobj );
			tr_in = tr.inRange( ourtr )
			out_in = ourtr.inRange( tr )
			}
		else
			{
			
			miselection = document.getSelection();
//			alert(miselection.anchorOffset());
				var ourtr = document.createRange();
				ourtr.selectNode(hobj);
				
				
				
				//alert(ourtr);
				text = ourtr.toString();
				
				//alert(text.length);
				aux_tr= tr.toString();
				//alert(text.indexOf(tr));
				if ((text.indexOf(tr) == 0) && (text.length == aux_tr.length))
					{
					tr_in=true;

					}
				else
					{
					tr_in=false;					
					}
				if (!tr_in)
					{
					if (text.indexOf(tr) >= 0 )
						{
						out_in = true;
						}
					}
			}
		
		if( tr_in )
		{
			// we are equal to or completely contained in the main range
			return new fieldSelection( this, true, 0, editText.length );
		}
		
		// see if main selection is completely inside us
		if( out_in )
		{
			if (navegador == "ie")
				{
					dup = ourtr.duplicate();
					dup.setEndPoint( "EndToStart", tr );
					start = dup.text.length;
					
					dup = ourtr.duplicate();
					dup.setEndPoint( "StartToEnd", tr );
					end = editText.length - dup.text.length;
					if( start == end )	// don't return empty
						return null;					
					return new fieldSelection( this, false, start, end );
				}
			else
				{
				
				count = 0;
				pos = text.indexOf(tr);
				//alert("antes " + pos);
				while ( pos != -1  && pos <= cursorIndex)
					{
					ultimo_pos = pos;
				   	count++;
				   	pos = text.indexOf(tr,pos+1);
					//alert("entre " + pos);
					}
				
					
				
					dup = ourtr.cloneRange();
					start = ultimo_pos;
					text2 = tr.toString();
					end = start + text2.length;
					return new fieldSelection( this, false, start, end );
					
					
				}
		}
		
		// check for left endpoint within main range
		dup = ourtr.duplicate();
		dup.collapse( true );
		if( tr.inRange( dup ) )
		{
			// left side is "in"
			dup.setEndPoint( "EndToEnd", tr );
			end = dup.text.length;
			if( end == 0 )	// don't return empty
				return null;
			return new fieldSelection( this, false, 0, end );
		}
		
		// check for right endpoint within main range
		dup = ourtr.duplicate();
		dup.collapse( false );
		if( tr.inRange( dup ) )
		{
			// right side is "in"
			dup.setEndPoint( "StartToStart", tr );
			start = editText.length - dup.text.length;
			if( start == editText.length )	// don't return empty
				return null;
			return new fieldSelection( this, false, start, editText.length );			
		}
		
		// no intersection
		return null;
	}
}

//
//						fieldSelection
//
//	Simple object describing a selection or partial selection in a field.
//
function fieldSelection( e, all, st, en )
{
	this.oField = e;
	this.bAll = all;
	this.start = st;
	this.end = en;
	//read_TAX(e);
}

//
//						_fieldGetTopContainer
//
//	Looks up node hierarchy for a non-splitting container.
//
function _fieldGetTopContainer()
{

	// look upwards for non-splitting container
	var par = this.parent;
	while( par != null && par.canSplit() )
		par = par.parent;
		
	// if we didn't hit a container, we can't apply, done
	if( par == null || par.nodeClass != "container" )
		return null;

	return par;
}

//
//						_fieldCanApplyTag
//
//	Returns true if we can wrap ourselves (or selected portion thereof) in spec'd tag.
//
//	Note: these next few routines are tricky cuz we're destroy the synchronization
//	between the edxnode tree and the XML node tree as we work.  We need to only
//	assume we still have our original XML node but not much else.
//
function _fieldCanApplyTag( sTag )
{

	with( this )
	{
		// get master container
		var eTop = getTopContainer();
		if( eTop == null )
			return false;
			
		var oTopNode = eTop.getXmlNode();
		var aMap = root.getView().getContainerMap( eTop.edxtemplate );
		
		// buqueda del contenedor principal
		var aAncestry = new Array();
		var n  = getXmlNode();
		
		while( n != oTopNode )
		{
			
			// if we hit the desired tag, we of course then can apply it (already applied)
			if( n.nodeName == sTag )
				return true;
				 
			aAncestry[aAncestry.length] = n;
			n = n.parentNode;
		}
		
		// now walk down looking for place to apply spec'd tag
		var i;
		for( i = aAncestry.length - 1; i >= 0; i-- )
		{
			var sCurTag = aAncestry[i].nodeName;
			var aChildMap = aMap[sTag];
			if( aChildMap instanceof Array && aChildMap[sCurTag] != undefined )
				{
				return true;}
			aMap = aMap[sCurTag];
			
			if( !(aMap instanceof Array) ){
				break;
				}
		}

		// never hit match		
		return false;
	}
}

//
//						_fieldIsApplied
//
//	Returns true if the spec'd tag is found anywhere up above the current field.
//
function _fieldIsApplied( sTag, oTopNode )
{
	with( this )
	{
		// trace out ancestry up to master container node
		var n  = getXmlNode();
		
		while( n != oTopNode )
		{
			// if we hit the desired tag, we of course then can apply it (already applied)
			if( n.nodeName == sTag )
				return true;
			n = n.parentNode;
		}
		// fell thru, not applied
		return false;
	}		
}

//
//						_fieldApplyTag
//
//	Inserts spec'd tag somewhere up above us.  If bHigh is set, try to set it as high
//	as possible in the hierarchy if there are multiple choices.  Else, try to set low.
//
//	Returns highest affected node.
//
//	As with comment further above, we are working only in the XML tree here-- the edxnode
//	is in an unknown state from possible previous processing.  Caution!
//
function _fieldApplyTag( sTag, oSel, bHigh, oTopNode, aMap )
{
	
	with( this )
	{

	if (sTag == "enlace" && navegador == "firefox15" && document.getElementById('toFirefox').value  == "true")
			{
				params["url"] = "";
				params["clase"] = "";
				params["referenciador"] = "";
				params["ventana"] = "";
				params["nodeid"]= XnodeGparent;
				return null;
			}
	if (sTag == "estilo" && navegador == "firefox15" && document.getElementById('toFirefox').value  == "true")
			{
				params["clase"] = "";
				return null;
			}
		// trace out ancestry up to master container node
		var aAncestry = new Array();
		var editnode  = getXmlNode();
		var n = editnode;


		while( n != oTopNode )
		{
			aAncestry[aAncestry.length] = n;
			n = n.parentNode;
		}
		
	
		// now walk down looking for place to apply spec'd tag
		var i;
		var iApply = -1;
		var nana = -1;
		for( i = aAncestry.length - 1; i >= 0; i-- )
		{
			var sCurTag = aAncestry[i].nodeName;
			if (sCurTag == "parrafo" || sCurTag == "enlace") nana = i;
			var aChildMap = aMap[sTag];
			
			if( aChildMap instanceof Array && aChildMap[sCurTag] != undefined )
			{
				iApply = i;
				if( bHigh )	// looking for highest possible?
					break;	// done
			}
			aMap = aMap[sCurTag];
			if( !(aMap instanceof Array) )
				break;
		}
		if( iApply == -1 && nana == -1)
		{
			// must be some mistake, never hit a match
			err( "No se puede insertar una etiqueta " + sTag + " en la selección");
			return null;
		}
		else 
			{
			iApply = 0;
			}

		// should already be an open XML transaction
		var xmlmgr = root.getXmlManager();
		//alert(oSel.end);
		// see if we need to do a self-split first
		if( !oSel.bAll )
		{
			if( oSel.end != editText.length )
			{
				xmlmgr.process( "splitText", editnode, oSel.end );
			}
			if( oSel.start != 0 )
			{
				xmlmgr.process( "splitText", editnode, oSel.start );
				editnode = editnode.nextSibling;
			}
		}
		
		// split up to selected ancestor as needed
		var nHighest = aAncestry[iApply].parentNode;

		var nPrev = editnode;
		//alert(nPrev.nodeValue);
		var nCur = editnode.parentNode;
		while( nCur != nHighest )
		{
			if( nCur.childNodes.length != 1 )
			{
				var iIndex = utilArrayIndex( nCur.childNodes, nPrev );
				if( iIndex == -1 )
				{
					err( "applyTag: XML node found in parent" );
					return null;
				}
				if( iIndex != nCur.childNodes.length - 1 )
				{
					xmlmgr.process( "splitNode", nCur, iIndex + 1 );
				}
				if( iIndex != 0 )
				{
					xmlmgr.process( "splitNode", nCur, iIndex );
					nCur = nCur.nextSibling;
					
				}
			}
			nPrev = nCur;
			nCur = nCur.parentNode;
		}
		
		// insert the desired node immediately above the selected ancestor node
		var oDoc = nHighest.ownerDocument;
		var nsuri = nHighest.namespaceURI;
		if (navegador == "ie")
				var nNew = oDoc.createNode( 1, sTag, nsuri );
		else
			{
				var nNp = document.implementation.createDocument("","dex",nsuri);
				var nNew2 = document.implementation.createDocument("",sTag,nsuri);
				var oTmp = nNew2.childNodes[0].cloneNode( true );
				
				nNp.childNodes[0].appendChild(oTmp);
				var nNew = nNp.childNodes[0].childNodes[0];
				//alert(serializa_me(nNew));
				
				
			}
		//
		if (sTag == "enlace" && navegador == "ie"){
				var args = new Array();
				var arr = null;
	   			arr = null;
				args["nodeid"]= XnodeGparent;
				arr = showModalDialog( "../actions/xmleditor/inc/archivos.php",
	                           		  args,
	                       		      "font-family:Verdana; dialogWidth:630px; dialogHeight:420px;");
	  			if (arr != null) {
					nNew.setAttribute ("a_enlaceid_url", arr["ID"]);
					nNew.setAttribute ("referenciador", arr["referenciador"]);
					nNew.setAttribute ("ventana", arr["ventana"]);
					nNew.setAttribute ("clase", arr["clase"]);
					variable_estilo = arr["clase"];
					}
				else{return;}
		
		}
		
		
		else if (sTag == "enlace" && navegador == "firefox15" && document.getElementById('toFirefox').value  == "false")
			{
			nNew.setAttribute ("a_enlaceid_url", params["ID"]);
			nNew.setAttribute ("referenciador", params["referenciador"]);
			nNew.setAttribute ("ventana", params["ventana"]);
			nNew.setAttribute ("clase", params["clase"]);
			variable_estilo =  params["clase"];
			document.getElementById('toFirefox').value = "true";
			params = new Array();	
		}
		if (sTag == "estilo" && navegador == "ie"){
				var args = new Array();
				var arr = null;
			   	arr = null;
				arr = showModalDialog( "../actions/xmleditor/inc/estilo.php",
			                             args,
			                             "font-family:Verdana; dialogWidth:380px; dialogHeight:190px;");
			  	if (arr != null) {
					nNew.setAttribute ("clase", arr["clase"]);
					variable_estilo = arr["clase"];
			}
			else{return;}
		}
		else if (sTag == "estilo" && navegador == "firefox15" && document.getElementById('toFirefox').value  == "false")
			{
				nNew.setAttribute ("clase", params["clase"]);
				variable_estilo = params["clase"];
				document.getElementById('toFirefox').value = "true";
				params = new Array();	
			}
		
		
		iIndex = utilArrayIndex( nHighest.childNodes, nPrev );
		
		if( iIndex == -1 )
		{
			err( "applyTag: no se encontr&oacute; la plantilla XML a insertar" );
			return null;
		}
	
		xmlmgr.process( "deleteNode", nHighest, iIndex );
		
		xmlmgr.process( "insertNode", nHighest, nNew, iIndex );
		
		xmlmgr.process( "insertNode", nNew, nPrev, 0 );
		
//		alert(serializa_me(nHighest));
		
		// whew
		return nHighest;
		
	}
}


//
//						_fieldRemoveTag
//
//	Removes tag from up above.  Same basic comments as applyTag above.
//
//	Returns highest affected node.
//
function _fieldRemoveTag( sTag, oSel, oTopNode, aMap )
{
	with( this )
	{
	// trace out ancestry up to master container node
		var aAncestry = new Array();
		var editnode  = getXmlNode();
		
		
	
	if (sTag == "enlace" && navegador == "firefox15" && document.getElementById('toFirefox').value  == "true")
			{
				params["url"] = editnode.parentNode.getAttribute("a_enlaceid_url");
				params["clase"] = editnode.parentNode.getAttribute("clase");
				params["referenciador"] = editnode.parentNode.getAttribute("referenciador");
				params["ventana"] = editnode.parentNode.getAttribute("ventana");
				params["nodeid"]= XnodeGparent;
//				editnode.parentNode
				return null;
			}
	if (sTag == "estilo" && navegador == "firefox15" && document.getElementById('toFirefox').value  == "true")
			{
				params["clase"] = editnode.parentNode.getAttribute("clase");
				return null;
			}
	
	var n = editnode;
		while( n != oTopNode )
		{
			aAncestry[aAncestry.length] = n;
			n = n.parentNode;
		}
	
	
		// trace out ancestry up to master container node
		var aAncestry = new Array();
		var editnode  = getXmlNode();
		
		var n = editnode;
		while( n != oTopNode )
		{
			aAncestry[aAncestry.length] = n;
			n = n.parentNode;
		}
		
		// now walk down looking for place to apply spec'd tag
		var i;
		var iApply = -1;
		for( i = aAncestry.length - 1; i >= 0; i-- )
		{
			if( aAncestry[i].nodeName == sTag )
			{
				iApply = i;
				break;
			}
		}
		if( iApply == -1 )
		{
			// must be some mistake, never hit a match
			err( "borrado de etiqueta: no se encontró la etiqueta: '" + sTag + "'" );
			return null;
		}
	
		// should already be an open XML transaction
		var xmlmgr = root.getXmlManager();
		
		// see if we need to do a self-split first
		if( !oSel.bAll )
		{
			if( oSel.end != editText.length )
			{
				xmlmgr.process( "splitText", editnode, oSel.end );
			}
			if( oSel.start != 0 )
			{
				xmlmgr.process( "splitText", editnode, oSel.start );
				editnode = editnode.nextSibling;
			}
		}
		
		// split up to selected ancestor as needed
		var nHighest = aAncestry[iApply].parentNode;
		var nPrev = editnode;
		var nCur = editnode.parentNode;
		while( nCur != nHighest )
		{
			if( nCur.childNodes.length != 1 )
			{
				var iIndex = utilArrayIndex( nCur.childNodes, nPrev );
				if( iIndex == -1 )
				{
					err( "borrado: no se encontró el nodo" );
					return null;
				}
				if( iIndex != nCur.childNodes.length - 1 )
				{
					xmlmgr.process( "splitNode", nCur, iIndex + 1 );
				}
				if( iIndex != 0 )
				{
					xmlmgr.process( "splitNode", nCur, iIndex );
					nCur = nCur.nextSibling;
				}
			}
			nPrev = nCur;
			nCur = nCur.parentNode;
		}
		if (sTag == "enlace" && navegador == "ie"){
				var args = new Array();
				args["url"] = nPrev.getAttribute("a_enlaceid_url");
				args["clase"] = nPrev.getAttribute("clase");
				args["referenciador"] = nPrev.getAttribute("referenciador");
				args["ventana"] = nPrev.getAttribute("ventana");
				args["nodeid"]= XnodeGparent;
				var arr = null;
	   			arr = null;
				
				arr = showModalDialog( "../actions/xmleditor/inc/archivos.php",
	                           		  args,
	                       		      "font-family:Verdana; dialogWidth:630px; dialogHeight:420px;");
	  			if (arr != null) {
					nPrev.setAttribute ("a_enlaceid_url", arr["ID"]);
					nPrev.setAttribute ("referenciador", arr["referenciador"]);
					nPrev.setAttribute ("ventana", arr["ventana"]);
					nPrev.setAttribute ("clase", arr["clase"]);
					variable_estilo = arr["clase"];
					if (arr["borrar"] == "no") return nHighest;
					}
				else if (arr == null) {return nHighest;}
		}
		if (sTag == "enlace" && navegador == "firefox15"){
			if (params["borrar"] == "no")
				{
				nPrev.setAttribute ("a_enlaceid_url", params["ID"]);
				nPrev.setAttribute ("referenciador", params["referenciador"]);
				nPrev.setAttribute ("ventana", params["ventana"]);
				nPrev.setAttribute ("clase", params["clase"]);
				variable_estilo = params["clase"];
				document.getElementById('toFirefox').value = "true";
				params = new Array();
				return nHighest;
				}
			
			
		}
		if (sTag == "estilo" && navegador == "ie"){
				var args = new Array();
				args["clase"] = nPrev.getAttribute("clase");
				var arr = null;
			   	arr = null;
				arr = showModalDialog( "../actions/xmleditor/inc/estilo.php",
			                             args,
			                             "font-family:Verdana; dialogWidth:370px; dialogHeight:180px;");
			  	if (arr != null) {
					nPrev.setAttribute ("clase", arr["clase"]);
					variable_estilo = arr["clase"];
					//editText.setAttribute( 'class', variable_estilo );
					if (arr["borrar"] == "no") return nHighest;
					}
				else if (arr == null) {return nHighest;}
		}
		if (sTag == "estilo" && navegador == "firefox15"){
			if (params["borrar"] == "no")
				{
				nPrev.setAttribute ("clase", params["clase"]);
				variable_estilo = params["clase"];
				document.getElementById('toFirefox').value = "true";
				params = new Array();
				return nHighest;
				}
		}
		// remove the selected ancestor node
		var nRemove = nPrev;
		var nSave = nRemove.childNodes[0];
		iIndex = utilArrayIndex( nHighest.childNodes, nRemove );
		if( iIndex == -1 )
		{
			err( "removeTag: el nodo a borrar no ha sido encontrado" );
			return null;
		}
		xmlmgr.process( "deleteNode", nHighest, iIndex );
		xmlmgr.process( "insertNode", nHighest, nSave, iIndex );
		
		
		if (navegador == "firefox15") document.getElementById('toFirefox').value = "true";
		params = new Array();
		
		return nHighest;
	}
}

//
//						cursorSave
//
//	Teeny object for saving location of cursor in signature/offset format.
//
function cursorSave( sig, off, inst )
{
	this.signature = sig;
	this.offset = off;
	this.instance = inst;
}

//
//						_fieldCleanup
//
function _fieldCleanup()
{
	with( this )
	{
		if( root.focusField == this )
			root.focusField = null;
		var editnode = getXmlNode();
		if( editnode != null )
		{
			root.unwatchChanges( editnode, this );
		}
		root.deassignID( id );
		parent.removeChild( this );
		//read_TAX2(null)
	}
}
