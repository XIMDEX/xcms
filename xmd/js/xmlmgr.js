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


function xmlmgr( r )
{
	// init vars
	this.root = r;
	
	this.aTransactions = new Array();
	this.transactionIndex = 0;
	this.currentTransaction = null;

	// init methods
	this.openTransaction = _xmlmgrOpenTransaction;
	this.closeTransaction = _xmlmgrCloseTransaction;
	this.cancelTransaction = _xmlmgrCancelTransaction;
	this.setNotify = _xmlmgrSetNotify;
	this.process = _xmlmgrProcess;
	this.canUndo = _xmlmgrCanUndo;
	this.canRedo = _xmlmgrCanRedo;
	this.undo = _xmlmgrUndo;
	this.redo = _xmlmgrRedo;
	this.exec = _xmlmgrExec;
	this.doAlerts = _xmlmgrDoAlerts;
	this.complement = _xmlmgrComplement;
	this.splitText = _xmlmgrSplitText;
	this.joinText = _xmlmgrJoinText;
	this.splitNode = _xmlmgrSplitNode;
	this.joinNodes = _xmlmgrJoinNodes;
	this.clearHistory = _xmlmgrClearHistory;
}

//
//						_xmlmgrOpenTransaction
//
//	Starts a transaction, providing a node to notify on upon undo/redo.
//
function _xmlmgrOpenTransaction( n )
{
	with( this )
	{
	if( currentTransaction != null )
		{
			err( "openTransaction: transaction already open" );
			return;
		}
		currentTransaction = new transaction( n );		
	}
}

//
//						_xmlmgrCloseTransaction
//
//	Closes out current transaction.
//
function _xmlmgrCloseTransaction()
{
	with( this )
	{
		if( currentTransaction == null )
		{
			err( "closeTransaction: no transaction open" );
			return;
		}
		aTransactions[transactionIndex++] = currentTransaction;
		// erase any transactions further ahead as redo is no longer an option
		aTransactions.length = transactionIndex;
		currentTransaction = null;
		// send document change event
		if (navegador == "ie")
			{
			root.hobj.edxdocumentchange.fire();
			}
		else
			{
			docChange();
			}
	}
}

//
//						_xmlmgrCancelTransaction
//
function _xmlmgrCancelTransaction()
{
	with( this )
	{
		if( currentTransaction == null )
		{
			err( "cancelTransaction: ninguna transaccion abierta" );
			return;
		}
		
		// simply remove pending transaction, error recovery must be done by caller
		currentTransaction = null;
	}
}

//
//						_xmlmgrSetNotify
//
//	Sets the notify node on an open transaction.  Useful when we don't know the
//	highest node until late in the process.  Param 'n' can be either a single
//	node or an array of nodes.
//
function _xmlmgrSetNotify( n )
{
	with( this )
	{
		if( currentTransaction == null )
		{
			err( "xmlmgr.setNotify: ninguna transaccion abierta" );
			return;
		}
		
		// see if we got single node or array of nodes
		var a;
		if( n instanceof Array )
		{
			a = n;
		}
		else
		{
			a = new Array();
			a[0] = n;
		}

		// convert to signatures
		var i;
		var aSig = new Array();
		for( i = 0; i < a.length; i++ )
		{
			aSig[i] = utilGetSigFromNode( a[i] );
		}
	
		// store it in current transaction
		currentTransaction.aNotify = aSig;
	}
}

//
//						_xmlmgrProcess
//
//	Public routine which processes a verb to make a change to the XML DOM.
//
//	Verbs and uses:
//		"updateNode" oNode, sNewVal        - updates an XML element, attribute, or text node value
//		"moveChildUp" oNode, index         - moves child at spec'd position up one slot
//		"moveChildDown" oNode, index       - moves child at spec'd position down one slot
//		"insertNode" oParent, oNode, index - inserts new node at  spec'd position
//		"deleteNode" oNode, index          - deletes node at  spec'd position
//		"splitText" oNode, index           - splits text at spec'd character
//		"joinText" oNode				   - joins text from oNode and next sibling
//		"splitNode" oNode, index		   - splits node at spec'd child index
//		"joinNodes"	oNode				   - joins node with next sibling
//
function _xmlmgrProcess()
{
	with( this )
	{
		if( currentTransaction == null )
		{
			err( "No se abrio correctamente el proceso" );
			return;
		}
		
		// load args into array for easy passing around
		var a = new Array();
		var i;
		for( i = 0; i < arguments.length; i++ )
		{
			a[i] = arguments[i];
		}
		// now actually do it
		return exec( a, true );
	}
}

//
//						_xmlmgrExec
//
//	Private routine which runs a command.  Called either by process() or by undo()/redo()
//	to perform a basic XML DOM manipulation.  If bForw is true a complement undo command is 
//	first entered into the current transaction.  Else, a complement redo command is gen'd.
//
function _xmlmgrExec( a, bForw )
{
	with( this )
	{
		if( currentTransaction == null )
		{
			err( "exec: No hay una transcci&oacute;n abierta" );
			return;
		}
		// decode command
		switch( a[0] )
		{
		case "updateNode":
			if( a.length != 3 )
			{
				err( "updateNode necesita tres parametros" );
				return;
			}
			//alert('updatenode');
			var sig = utilGetSigFromNode( a[1] );
			var node = utilGetNodeFromSig( sig, root.oEditXml );
			var val = a[2];
			var oldVal = utilGetXmlNodeValue( node );
			
			var comp = new Array();
			comp[0] = "updateNode";
			comp[1] = sig;
			comp[2] = oldVal;
			var ct = currentTransaction;
			complement( comp, bForw );
			
			// perform the update
			utilUpdateXmlNodeValue( node, val );
			break;
		
		case "moveChildUp":
			if( a.length != 3 )
			{
				err( "moveChildUp requiere dos parametros" );
				return;
			}
			var sig = utilGetSigFromNode( a[1] );
			var node = utilGetNodeFromSig( sig, root.oEditXml );
			var index = a[2];
			var comp = new Array();
			comp[0] = "moveChildDown";
			comp[1] = sig;
			comp[2] = index - 1;
			complement( comp, bForw );
			var tmp = node.removeChild( node.childNodes[index] );
			
//			alert(serializa_me(node));
//			alert(serializa_me(tmp));
			
			node.insertBefore( tmp, node.childNodes[index-1] );
			
			break;

		case "moveChildDown":
			if( a.length != 3 )
			{
				err( "moveChildDown requires two params" );
				return;
			}
			var sig = utilGetSigFromNode( a[1] );
			var node = utilGetNodeFromSig( sig, root.oEditXml );
			var index = a[2];
			
			var comp = new Array();
			comp[0] = "moveChildUp";
			comp[1] = sig;
			comp[2] = index + 1;
			complement( comp, bForw );
			
			var tmp = node.removeChild( node.childNodes[index] );
			if( index == node.childNodes.length - 1 )
				node.appendChild( tmp );
			else
				node.insertBefore( tmp, node.childNodes[index+1] );
			break;
		
		case "insertNode":
			if( a.length != 4 )
			{
				err( "insertNode requiere 4 parametros" );
				return;
			}
			
			var parentSig = utilGetSigFromNode( a[1] );
			var parentNode = utilGetNodeFromSig( parentSig, root.oEditXml );
			var node = a[2];
			var index = a[3];
			var comp = new Array();
			comp[0] = "deleteNode";
			comp[1] = parentSig;
			comp[2] = index;
			complement( comp, bForw );
			if( index == parentNode.childNodes.length )
				{
					parentNode.appendChild( node );
				}
			else
				{
//parentNode.insertBefore( node, parentNode.childNodes[index].nextSibling );
					parentNode.insertBefore( node, parentNode.childNodes[index] );
				}		

			break;

			
			
			
		case "deleteNode":
			if( a.length != 3 )
			{
				err( "deleteNode requires two params" );
				return;
			}
			var sig = utilGetSigFromNode( a[1] );
			var node = utilGetNodeFromSig( sig, root.oEditXml );
//			alert(node.xml);
			var index = a[2];
//			alert(node.xml);
			var oldnode = node.removeChild( node.childNodes[index] );
			var comp = new Array();
			comp[0] = "insertNode";
			comp[1] = sig;
			comp[2] = oldnode.cloneNode( true );
			//alert(comp[2].xml);
			comp[3] = index;
			complement( comp, bForw );
			break;
		
		case "splitText":
			splitText( a, bForw );
			break;
		
		case "joinText":
			joinText( a, bForw );
			break;
		
		case "splitNode":
			splitNode( a, bForw );
			break;
		
		case "joinNodes":
			joinNodes( a, bForw );
			break;
			
		default:
			err( "xmlmgr: unknown verb: " + a[0] );
			return;
		}
	}
}

//
//						_xmlmgrComplement
//
function _xmlmgrComplement( comp, bForw )
{
	with( this )
	{
		var ct = currentTransaction;
		if( bForw )
			{
			ct.aUndo[ct.aUndo.length] = comp;
			}
		else
			{
			ct.aRedo[ct.aRedo.length] = comp;
			}
	}
}

//
//						_xmlmgrCanUndo
//
function _xmlmgrCanUndo()
{
	return this.transactionIndex > 0;
}

//
//						_xmlmgrCanRedo
//
function _xmlmgrCanRedo()
{
	return this.transactionIndex < this.aTransactions.length;
}

//
//						_xmlmgrUndo
//
function _xmlmgrUndo()
{
	with( this )
	{
//	alert(transactionIndex);
		if( transactionIndex == 0 )
			return;
var mflg = false;
		// go back a step		
		transactionIndex--;
		var ct = aTransactions[transactionIndex];
		currentTransaction = ct;
		
		var steps = ct.aUndo;
		// walk steps in reverse order
		var i;
		ct.aRedo = new Array();
		for( i = steps.length - 1; i >= 0; i-- )
		{

			//alert(steps[i]);
		if (steps[i][0]=='moveChildDown')
				{
					mflg = true;
				}
			exec( steps[i], false );
		}
		currentTransaction = null;
		
		// DOM node(s) to alert on
		doAlerts( ct.aNotify );
root.load();
		// send document change event
		//root.hobj.edxdocumentchange.fire();
		if (navegador == "ie")
			{
			root.hobj.edxdocumentchange.fire();
			}
		else
			{
			docChange();
			}
			
		// Checking if last operation was insertion of a node to delay the insertion
		if (mflg)
					{
							transactionIndex--;
					var ct = aTransactions[transactionIndex];
					currentTransaction = ct;
					
					var steps = ct.aUndo;
					// walk steps in reverse order
					var i;
					ct.aRedo = new Array();
					for( i = steps.length - 1; i >= 0; i-- )
					{
			
						if (steps[i][0] != "deleteNode")
							{
							return;							
							}

					
						exec( steps[i], false );
					}
					currentTransaction = null;
					
					// DOM node(s) to alert on
					doAlerts( ct.aNotify );
			root.load();
					// send document change event
					//root.hobj.edxdocumentchange.fire();
					if (navegador == "ie")
						{
						root.hobj.edxdocumentchange.fire();
						}
					else
						{
						docChange();
						}
			}
	}
}

//
//						_xmlmgrRedo
//
function _xmlmgrRedo()
{
	with( this )
	{
		if( transactionIndex == aTransactions.length )
			return;

		var mflg = false;	
		
		// go forward a step		
		var ct = aTransactions[transactionIndex];
		currentTransaction = ct;
		transactionIndex++;
		var steps = ct.aRedo;

		// walk steps in forward order
		ct.aUndo = new Array();
		var i;
		for( i = steps.length - 1; i >= 0; i-- )
		{
			if (steps[i][0]=='insertNode')
				{
					mflg = true;
				}
			exec( steps[i], true );
			
		}
		currentTransaction = null;

		// DOM node to alert on
		doAlerts( ct.aNotify );
		root.load();
		// send document change event
		//root.hobj.edxdocumentchange.fire();
		if (navegador == "ie")
			{
			root.hobj.edxdocumentchange.fire();
			}
		else
			{
			docChange();
			}
		//compruebo si es un nodo el que se ha insertado para avanzar un paso más en el rehacer
		if (mflg)
				{
				var ct = aTransactions[transactionIndex];
				currentTransaction = ct;
				transactionIndex++;
				var steps = ct.aRedo;
		
				// walk steps in forward order
				ct.aUndo = new Array();
				var i;
				for( i = steps.length - 1; i >= 0; i-- )
				{
					if (steps[i][0]=='insertNode')
						{
							mflg = true;
						}
					exec( steps[i], true );
					
				}
				currentTransaction = null;
		
				// DOM node to alert on
				doAlerts( ct.aNotify );
				root.load();
				// send document change event
				//root.hobj.edxdocumentchange.fire();
				if (navegador == "ie")
					{
					root.hobj.edxdocumentchange.fire();
					}
				else
					{
					docChange();
					}
				}
	}
}

//
//						_xmlmgrDoAlerts
//
function _xmlmgrDoAlerts( a )
{
	with( this )
	{
		var i;
		for( i = 0; i < a.length; i++ )
		{
			var n = utilGetNodeFromSig( a[i], root.oEditXml );
			root.alertChange( n, null );
		}
	}
}


//
//						transaction
//
//	Constructor for a transaction record, take a node to notify on or null if not known yet.
//
function transaction( n )
{
	this.aNotify = new Array();
	if( n != null )
	{
		this.aNotify[0] = utilGetSigFromNode( n );
	}
	this.aRedo = new Array();
	this.aUndo = new Array();
}

//
//						splitText
//
function _xmlmgrSplitText( a, bForw )
{
	with( this )
	{
		if( a.length != 3 )
		{
			err( "splitText requires two params" );
			return;
		}
		var sig  = utilGetSigFromNode( a[1] );
		var node = utilGetNodeFromSig( sig, root.oEditXml );
		var index = a[2];
		
		var newNode;
		
		//el_tipo = define_tipo  (node.nodeType);
		switch( define_tipo  (node.nodeType) )		
		{
		case "text":
			newNode = node.cloneNode( false );
			break;
		case "element":
			newNode = node.cloneNode( true );
			break;
		default:
			err( "Can't do text split on nodes of type '" + define_tipo (node.nodeType) + "'" );
			return;
		}

		var comp = new Array();
		comp[0] = "joinText";
		comp[1] = sig;
		complement( comp, bForw );
		
		// now assign the appropriate text to each side depending on cursor position
		var sLeft = utilGetXmlNodeValue( node );
		var sRight = sLeft;
		sLeft = sLeft.substr( 0, index );
		sRight = sRight.substr( index );
		if (sRight == "") sRight = "[nuevo]";
		utilUpdateXmlNodeValue( node, sLeft );
		utilUpdateXmlNodeValue( newNode, sRight );
		
		// and insert the new node to the right of the original
		var par = node.parentNode;
		if( node.nextSibling != null )
			par.insertBefore( newNode, node.nextSibling );
		else
			par.appendChild( newNode );
	}
}

//
//						joinText
//
function _xmlmgrJoinText( a, bForw )
{
	with( this )
	{
		if( a.length != 2 )
		{
			err( "joinText requires one param" );
			return;
		}
		var sig  = utilGetSigFromNode( a[1] );
		var node = utilGetNodeFromSig( sig, root.oEditXml );
		var nextNode = node.nextSibling;
		
		switch( define_tipo(node.nodeType) )
		{
		case "text":
		case "element":
			break;
		default:
			err( "Can't do text join on nodes of type '" + define_tipo  (node.nodeType) + "'" );
			return;
		}
		var s = utilGetXmlNodeValue( node );
		
		var comp = new Array();
		comp[0] = "splitText";
		comp[1] = sig;
		comp[2] = s.length;
		complement( comp, bForw );
				
		s += utilGetXmlNodeValue( nextNode );
		utilUpdateXmlNodeValue( node, s );
		
		// and remove the right node
		node.parentNode.removeChild( node.nextSibling );
	}
}

//
//						_xmlmgrSplitNode
//
//	Splits a node at the spec'd child index.
//
function _xmlmgrSplitNode( a, bForw )
{
	with( this )
	{
		if( a.length != 3 )
		{
			err( "splitNode requires two params" );
			return;
		}
		var sig = utilGetSigFromNode( a[1] );
		var node = utilGetNodeFromSig( sig, root.oEditXml );
		var index = a[2];
		
		var comp = new Array();
		comp[0] = "joinNodes";
		comp[1] = sig;
		complement( comp, bForw );
				
		// do a split
		var newNode = node.cloneNode( false );
		
		// remove from left, append to right
		while( node.childNodes.length != index )
		{
			var n = node.removeChild( node.childNodes[index] );
			newNode.appendChild( n );
		}
		
		// attach to parent
		var par = node.parentNode;
		if( node.nextSibling != null )
			par.insertBefore( newNode, node.nextSibling );
		else
			par.appendChild( newNode );
		
	}
}

//
//						_xmlmgrJoinNodes
//
//	Joins a node with its next sibling.
//
function _xmlmgrJoinNodes( a, bForw )
{
	with( this )
	{
		if( a.length != 2 )
		{
			err( "joinNodes requires one param" );
			return;
		}
		var sig = utilGetSigFromNode( a[1] );
		var node = utilGetNodeFromSig( sig, root.oEditXml );
		
		var comp = new Array();
		comp[0] = "splitNode";
		comp[1] = sig;
		comp[2] = node.childNodes.length;
		complement( comp, bForw );
				
		// move children from next sibling onto left node
		var nextNode = node.nextSibling;
		var i;
		while( nextNode.childNodes.length != 0 )
		{
			var n = nextNode.removeChild( nextNode.childNodes[0] );
			node.appendChild( n );
		}
		
		// remove the next sibling from the parent
		var par = node.parentNode;
		par.removeChild( nextNode );
	}
}

//
//						_xmlmgrClearHistory
//
//	Flushes the undo/redo buffer.
//
function _xmlmgrClearHistory()
{
	this.aTransactions = new Array();
	this.transactionIndex = 0;
}
