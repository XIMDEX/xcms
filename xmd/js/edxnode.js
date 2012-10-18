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

function edxnode( p, sTemplate, sPath, sOptions, index )
{
//alert('index ' + index);
	if( edxnode.prototype.edxnodeInitd == undefined )
	{
		this.initProto = _edxnodeInitProto;
		this.initProto( "edxnode" );
		edxnode.prototype.edxnodeInitd = true;
	}

	// init base class vars
	

	
	this.hobj = null;
	this.nodeClass = "edxnode";
	
	// capture tree info
	if( p == null )
	{
		// we must be root
		this.parent = null;
		this.root = this;
		this.next = null;
		this.prev = null;
	}
	else
	{
		this.parent = p;

		this.root = p.root;
		this.id = this.root.assignID( this );
		if( index == undefined )
			p.appendChild( this );
		else
			{
			p.insertChild( this, index );
			}
	}
	this.edxtemplate = sTemplate;
	this.edxpath = sPath;
	this.oTemplate = null;
	this.oEditNode = null;
	this.type = "unknown";

	this.childNodes = new Array();
	this.processOptions( sOptions );
}

//
//						_edxnodeInitProto
//
function _edxnodeInitProto( sClass )
{
	// install base class methods
	eval( sClass + ".prototype.edxnode = edxnode; " +
	      sClass + ".prototype.getXmlNode = _edxnodeGetXmlNode; " +
	      sClass + ".prototype.setXmlNode = _edxnodeSetXmlNode; " +
	      sClass + ".prototype.getTemplate = _edxnodeGetTemplate; " +
	      sClass + ".prototype.associate = _edxnodeAssociate; " +
	      sClass + ".prototype.edxnodeAssociate = _edxnodeAssociate; " +
	      sClass + ".prototype.sanityCheck = _edxnodeSanityCheck; " +
	      sClass + ".prototype.edxnodeSanityCheck = _edxnodeSanityCheck; " +
	      sClass + ".prototype.load = _edxnodeLoad; " +
	      sClass + ".prototype.processOptions = _edxnodeProcessOptions; " +
	      sClass + ".prototype.appendChild = _edxnodeAppendChild; " +
	      sClass + ".prototype.removeChild = _edxnodeRemoveChild; " +
	      sClass + ".prototype.insertChild = _edxnodeInsertChild; " +
	      sClass + ".prototype.isChildOf = _edxnodeIsChildOf; " +
	      sClass + ".prototype.canSplit = _edxnodeCanSplit; " +
	      sClass + ".prototype.splitNode = _edxnodeSplitNode; " +
	      sClass + ".prototype.canDelete = _edxnodeCanDelete; " +
	      sClass + ".prototype.setHtmlAttributes = _edxnodeSetHtmlAttributes; " +
	      sClass + ".prototype.performAssociation = _edxnodePerformAssociation; " +
	      sClass + ".prototype.cleanup = _edxnodeCleanup; " +
	      sClass + ".prototype.edxnodeCleanup = _edxnodeCleanup; " +
	      sClass + ".prototype.factory = _edxnodeFactory" );
		  
		  
}


//
//						_edxnodeGetXmlNode
//
//	
//	Obtains node of XML tree associated with node
//
function _edxnodeGetXmlNode()
{
	with( this )
	{
		
		if( oEditNode != null )
			return oEditNode;

		if( edxpath == null )
		{
			return null;
		}

		if( parent == null ) 	// root node
			{
				oEditNode = root.oEditRoot.selectSingleNode( edxpath );
			}
		else
			{
//			alert('Parent -> ' + edxpath);
//			alert(this.id);
//alert(edxpath);
				oEditNode = parent.getXmlNode().selectSingleNode( edxpath );
	
			}

		// if it's null, and it's spec'd as an attribute directly below current context
		// we'll create it on the fly.  this make life easier in field.applyTag() as we
		// don't have to use templates for stuff like link tags
		if( oEditNode == null && edxpath.charAt(0) == "@" )
		{
			// set to empty string
			utilSetXmlAttribute( parent.getXmlNode(), edxpath.substr(1), "" );
			
		}
		return oEditNode;
	}
}

//
//						_edxnodeSetXmlNode
//
//	Installs an XML node.  If a previous one existed we try to reload the item.
//
function _edxnodeSetXmlNode( oXml )
{
	with( this )
	{
		var prev = oEditNode;
		oEditNode = oXml;
		if( prev != null )
			load();
	}
}

//
//						_edxnodeGetTemplate
//
function _edxnodeGetTemplate()
{
	with( this )
	{
		if( oTemplate != null )
			return oTemplate;
		var v = root.getView();
		oTemplate = v.getTemplate( edxtemplate );
		if( oTemplate == null )
			err( "Plantilla vacía en " + nodeClass );
		return oTemplate;
	}
}


//
//						_edxnodeAssociate
//
//	Called when we can attach ourselves to an HTML node in the HTML DOM.
//
function _edxnodeAssociate( h )
{
	this.hobj = h;
	
	h.setAttribute('eobj', this);
	

	dex[this.id] = this;
	
//	alert(dex['e0']);
/*var result= "";
	for (var i in dex)
		{
		result += i + " = " + dex[i] + "\n";
		var result2 = "";
		for (var def in dex[i])
			{
				result2 += def + " = " + dex[i][def] + "\n";
			}
			alert(dex['e0']['id']);
		}
		//alert( result);
	*/	
	
}

//
//						_edxnodeProcessOptions
//
function _edxnodeProcessOptions( sOptions )
{
	// process edxoptions
	if( sOptions && sOptions.length != 0 )
	{
		var a = utilParseOptions( sOptions );
		var i;
		
		for( i = 0; i < a.length; i++ )
		{
			switch( a[i].name )
			{
			case "enter-action":
				switch( a[i].value )
				{
				case "none":
				case "split":
				case "new":
					this.enterAction = a[i].value;
					break;
				default:
					err( "Error: invalid value for 'enter-action' option: " + this.enterAction + "\nMust be none, split, or new." );
				}
				break;
	
			case "allow-split":
				switch( a[i].value )
				{
				case "true":
				case "yes":
					this.allowSplit = true;
					break;
				case "false":
				case "no":
					this.allowSplit = false;
					break;
				default:
					err( "Error: invalid option for 'allow-split' option: " + a[i].value + "\nMust be true, yes, false, or no." );
				}
				break;
			
			case "node-link":
				// enlaza una plantilla particual al index
				this.nodeLink = a[i].value;
				break;
			
			case "display-link":
				// links the display selection for a region to a particular attribute node
				// enlaza la selección para una región a un atributo
				this.displayLink = a[i].value;
				break;
				
			case "debug":
				if( a[i].value == "true" )
				{
					this.root.startSanityChecker();
				}
				break;
				
			default:
				err( "Error: unrecognized edxoption: " + a[i].name );			
			}
		}
	}
}

//
//						_edxnodeLoad
//
//	
//	Carga inicial del código XHTML en ximEDITOR
//

function _edxnodeLoad()
{
	var i;
	with( this )
	{
		if( hobj == null )
		{
			err( "error en la carga: no se encontró el HTML" );
			return;
		}

		// see if we've previously had edxnodes that need to be cleaned up
		while( childNodes.length != 0 )
			childNodes[0].cleanup();
		childNodes = new Array();

		// build our xhtml		
		var oXml = xhtml( null );
		if( hobj.tagName == "TD" ){
			hobj.setAttribute ( "rowSpan", my_rowspan);
			hobj.setAttribute ( "colSpan", my_colspan);
			hobj.setAttribute ( "class", my_clase);
			}
		// fields return null on reload since they have to load node directly
		if( oXml == null )
			return;
			

		// install it in our html node
		if( hobj.tagName == "TBODY" || hobj.tagName == "TR" )
		{
			// scrub down first
			if( hobj.tagName == "TBODY" )
			{
				while( hobj.rows.length != 0 )
					hobj.deleteRow( 0 );
			}
			else
			{
				while( hobj.cells.length != 0 )
					hobj.deleteCell( 0 );
			}
			
			// load the table/row
			setInnerHTML( hobj, oXml );
			
		}
		else
		{
			var s = innerXML( oXml );
			s = s.replace( />\s+</g, "><" );
			hobj.innerHTML = s;			
		}

			
		// asocia el html con sus respectivos nodos "edx"
		performAssociation( hobj );		
	}
}

//
//						_edxnodePerformAssociation
//
function _edxnodePerformAssociation( h )
{
	with( this )
	{

		if (navegador == "ie")
			{
			var children = h.all;
			}
		else
			{
			var children = h.getElementsByTagName("*");
			
			}
		var last = "";
		try {
			for( i = 0; i < children.length; i++ )
			{
				var child = children[i];
				
				if( child.getAttribute('edxtemplate') != undefined )
				{
					var sID = child.getAttribute('edxid');
					if( sID == undefined )
					{
						err( "Nodo principal sin id: " + child.tagName );
						continue;
					}
					var e = root.lookupID( sID );
					last = e.nodeClass;
					//alert('last: ' + last);
					
					e.associate( child );
				}
			}
		}
		catch(e)
		{
			err( "Ocurrió una excepción en la asociación: último nodo cargado: " + last );
		}
	}
}


//
//						_edxnodeAppendChild
//
//	Maintain a list of all edxnode children in order.
//
function _edxnodeAppendChild( node )
{
	this.insertChild( node, this.childNodes.length );
}

//
//						_edxnodeRemoveChild
//
//	borra un nodo-hijo
//
function _edxnodeRemoveChild( node )
{
	with( this )
	{
	//alert('_edxnodeRemoveChild');
		var i;
		for( i = 0; i < childNodes.length; i++ )
		{
			if( childNodes[i] == node )
			{
				if( node.previousSibling != null )
					node.previousSibling.nextSibling = node.nextSibling;
				if( node.nextSibling != null )
					node.nextSibling.previousSibling = node.previousSibling;
				childNodes.splice( i, 1 );
				return node;
			}
		}
		err( "El nodo seleccionado no se pudo borrar" );
		return null;
	}
}

//
//						_edxnodeInsertChild
//
//	Inserts new child at spec'd index.
//
function _edxnodeInsertChild( node, index )
{
	with( this )
	{
	
	//alert(index + " " + childNodes.length);
		// sanity check
		if( index > childNodes.length )
		{
			err( "insertChild: can't insert past end of array." );
			return;
		}
		
		// resolve forw/back pointers
		if( index != 0 )
		{
			childNodes[index-1].nextSibling = node;
			node.previousSibling = childNodes[index-1];
		}
		else
			node.previousSibling = null;
		
		if( index < childNodes.length )
		{
			childNodes[index].previousSibling = node;
			node.nextSibling = childNodes[index];
		}
		else
			node.nextSibling = null;
		
		// insert into array
		childNodes.splice( index, 0, node );
	}
}

//
//						_edxnodeIsChildOf
//
//	Returns true if the node is a child of the spec'd parent.
//
function _edxnodeIsChildOf( par )
{
	var e = this;
	while( e != null && e != par )
		e = e.parent;
	return e == par;
}

//
//						_edxnodeCanSplit
//
function _edxnodeCanSplit()
{
	with( this )
	{
		// containers and field default to splittable, all else not
		var bRet = nodeClass == "container" || nodeClass == "field";
		
		// look for explicit option for this node
		if( this.allowSplit != undefined )
			bRet = allowSplit;
		
		// if we already know we can't split, look no further
		if( !bRet )
			return false;
			
		// check against parent to see if it works
		switch( parent.nodeClass )
		{
		case "region":
		case "root":
			bRet &= parent.canSplit();
			break;
		case "container":
			bRet &= parent.permitChildSplit( this );
			break;
		default:
			err( "unexpected parent container for " + nodeClass + ": " + parent.nodeClass );
			bRet = false;
		}
		return bRet;
	}
}

//
//						_edxnodeSplitNode
//
//	Attempts to split the node at the spec'd index.  However if the lastNodeSplit
//	matches our node, we move up the tree.
//
function _edxnodeSplitNode( index, lastNodeSplit )
{
	with( this )
	{
		// we should have been asked before being called
		if( !canSplit() )
		{
			err( "splitNode called on non-splittable node" );
			return null;
		}
			
		// see if we're attached to same XML node as caller
		var editnode = getXmlNode();
		if( editnode == lastNodeSplit )
		{
			// we're a wrapper of some sort, see if we can just move on up
			if( parent.canSplit() )
				return parent.splitNode( index, lastNodeSplit );
			else
				return this;
		}
		
		// do a split (there should already be an open transaction
		var xmlmgr = root.getXmlManager();
		xmlmgr.process( "splitNode", editnode, index );
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
		
		// ascend the tree looking for parent that finally blocks the split
		if( parent.canSplit() )
			return parent.splitNode( i, editnode );
		else
			return this;
	}
}

//
//						_edxnodeCanDelete
//
function _edxnodeCanDelete()
{
	// if they haven't implemented this, they can't be deleted
	return false;
}

//
//						_edxnodeCleanup
//
//	Called when a node is being destroyed.
//
function _edxnodeCleanup()
{
	with( this )
	{
		while( childNodes.length != 0 )
			childNodes[0].cleanup();
		
		root.deassignID( id );
		if( parent != null )
			parent.removeChild( this );
	}
}

//
//						_edxnodeSetHtmlAttributes
//
//	Installs a couple HTML attribs which back link to the edxnode object structure.
//
function _edxnodeSetHtmlAttributes( oXml )
{
	with( this )
	{
		// set a couple things in the html
		utilSetXmlAttribute( oXml, "edxid", id );
		utilSetXmlAttribute( oXml, "eobj", "-" );
	}
}


//
//						_edxnodeFactory
//
//	Manufactures edxnodes from a template name.
//
function _edxnodeFactory( sTemplate, sPath, sOptions, index, xIndex )
{
	if( sTemplate == null )
	{
		err( "No se ha encontrado la propiedad edxtemplate: todos los elementos deben tener un valor para la propiedad edxtemplate" );
		return null;
	}
//alert(sPath);
	// check for built-ins
	if( sTemplate.indexOf(":") > 0 )
	{
		// alert('sTemplate -> ' + sTemplate);
		// built-ins
		if( sTemplate.substr(0,6) == "field:" )
		{
			return new field( this, sTemplate, sPath, sOptions, index );
			
		}
		else if( sTemplate.substr(0,7) == "widget:" )
		{
			return new widget( this, sTemplate, sPath, sOptions, index );
		}
		else
		{
			err( "Only built-in template names may contain ':': " + sTemplate );
			return null;
		}
	}
	else
	{
		// standard template spec'd in view doc
		var v = this.root.getView();
		var oTemp = v.getTemplate( sTemplate );

		// discover our type from the template
		var type = v.getTemplateType( oTemp );
		

		
		if( type == "region" )
		{

			return new region( this, sTemplate, sPath, sOptions, index );
		}
		else if( type == "container" )
		{

			return new container( this, sTemplate, sPath, sOptions, index, xIndex );
		}
		else
		{
			err( "Tipo de plantilla no reconocida: " + type );
			return null;
		}
	}
}

//
//						_edxnodeSanityCheck
//
//	See root class sanityCheck() routine for more info.
//
function _edxnodeSanityCheck( d )
{
	with( this )
	{
		// collect IDs as we go
		d.Add( id, this );
		
		// verify that if we have an assoc'd HTML object, we can access it
		if( hobj != null )
		{
			try
			{
				var uid = hobj.uniqueID;
			}
			catch(e)
			{
				err( "sanityCheck failed: couldn't access uniqueID for HTML element: " + this.template );
				return false;
			}
		}
		
		// see if we have children to check
		if( childNodes.length == 0 )
			return true;
	
		var i;
		var cur;
		cur = childNodes[0];
		if( cur.previousSibling != null )
		{
			err( "sanityCheck failed: first child previous ptr not null: " + this.edxtemplate );
			return false;
		}
	
		// walk list forwards
		for( i = 0; cur != null; i++ )
		{
			if( childNodes[i] != cur )
			{
				err( "sanityCheck failed: childNodes ref didn't match linked list: " + this.edxtemplate );
				return false;
			}
			cur = cur.nextSibling;
		}
		if( i != childNodes.length )
		{
			err( "sanityCheck failed: childNode count error running linked list: " + this.edxtemplate );
			return false;
		}
	
		// walk list backwards
		cur = childNodes[childNodes.length-1];
		for( i = childNodes.length - 1; cur != null; i-- )
		{
			if( childNodes[i] != cur )
			{
				err( "sanityCheck failed: walking backwards, childNodes ref didn't match linked list: " + this.edxtemplate );
				return false;
			}
			cur = cur.previousSibling;
		}
		if( i != -1 )
		{
			err( "sanityCheck failed: childNode count error running linked list backwards: " + this.edxtemplate );
			return false;
		}
		
		// finally check the children
		for( i = 0; i < childNodes.length; i++ )
		{
			if( childNodes[i].sanityCheck( d ) == false )
				return false;
		}
	}
	return true;
}
