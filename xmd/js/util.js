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

function innerXML( node )
{

	var child = node.firstChild;
	
	if( child == null )
		return "";
	var s = "";
	while( child != null )
	{
	if (navegador != "ie"){
		var xml = serializa_me (child);
		s+=xml;
		
		}
	else
		{
		s += child.xml;
		}
		child = child.nextSibling;
	}
	//alert(s);
	return s;
}

//
//						setInnerHTML
//
//	Attempts to manage special casing for loading TBODY and TR elements.
//
function setInnerHTML( h, oXml )
{
	switch( h.tagName )
	{
	case "TBODY":
		utilInsertRows( h, oXml );
		break;
	case "TR":
		utilInsertCells( h, oXml );
		break;
	default:
		err( "Error: util.setInnerHTML got invalid parent tag: " + tag );
	}
}

//
//						utilInsertRows
//
function utilInsertRows( h, oXml )
{
	var i;
	var children = oXml.childNodes;
	for( i = 0; i < children.length; i++ )
	{
		var child = children[i];
		var childTag = child.nodeName;

		// make sure it's a valid relationship
		childTag = childTag.toUpperCase();
		if( childTag != "TR" )
		{
			err( "Error: cannot add " + childTag + " to TBODY element." );
			return;
		}
		
		// let's make a new row
		var tr = h.insertRow();
		
		// clone the TR attribs if any
		utilCloneAttribsFromXml( tr, child );
		
		// now add the cells
		utilInsertCells( tr, child );
	}
}

//
//						utilInsertRowAt
//
function utilInsertRowAt( h, oXml, index )
{
	// first child should be row to insert
	var child = oXml;
	
	if( child == null )
	{
		err( "Error: la fila a insertar tiene un valor nulo" );
		return;
	}
	var childTag = child.nodeName.toUpperCase();
	
	// make sure it's a valid relationship
	childTag = childTag.toUpperCase();
	if( childTag != "TR" )
	{
		err( "Error: no se pudo insertar la etiqueta " + childTag + " en el elemento TBODY" );
		return;
	}

	
	
	// let's make a new row
	var tr = h.insertRow( index );

//alert(tr);
	
	// clone the TR attribs if any
	utilCloneAttribsFromXml( tr, child );

	// now add the cells
	utilInsertCells( tr, child );
}


//
//						utilInsertCells
//
function utilInsertCells( h, oXml )
{
	var i;
	var children = oXml.childNodes;
	for( i = 0; i < children.length; i++ )
	{
		var child = children[i];
		var childTag = child.nodeName;
		
		// make sure it's a valid relationship
		childTag = childTag.toUpperCase();
		/*if( childTag != "TD" )
		{
			err( "Error: cannot add " + childTag + " to TR element.1" );
			return;
		}
		*/
		// let's make a new one
		
		
		
		
		var td = h.insertCell(i);
		
		// clone the attribs if any
		utilCloneAttribsFromXml( td, child );
		
		// now we should be back in innerHTML land!
		td.innerHTML = innerXML( child );
		
	}
}


//
//						utilInsertCellAt
//
function utilInsertCellAt( h, oXml, index )
{
	var i;
	var child = oXml;
	var childTag = child.nodeName;
		
	// make sure it's a valid relationship
	childTag = childTag.toUpperCase();
	if( childTag != "TD" )
	{
		err( "Error: cannot add " + childTag + " to TR element.2" );
		return;
	}
		
	// let's make a new one
	var td = h.insertCell( index );
	// clone the attribs if any
	utilCloneAttribsFromXml( td, child );
		
	// now we should be back in innerHTML land!
	td.innerHTML = innerXML( child );
	
}

//
//						utilCloneAttribsFromXml
//
//	Walks the supplised XML attributes for this node and installs them on the HTML node
function utilCloneAttribsFromXml( h, x )
{
	var attribs = x.attributes;
	var i;
	var className = null;
	
	for( i = 0; i < attribs.length; i++ )
	{	
		var attrib  = attribs[i];
		
		var name = attrib.nodeName;
//		var val = attrib.text;
		var val = attrib.nodeValue;
	
		// special handling to avoid triggering behavior load until all other attribs loaded
		if( name.toUpperCase() == "CLASS" )
		{
			if (val == "@clase"){
				if (variable_estilo) val = variable_estilo;
			}
			className = val;
			continue;
		}
		h.setAttribute( name ,val );
	}
	
	// note: also remap "class" to "className" (scratch head...)
	if( className != null )
		h.setAttribute( "className", className );
	
}


//
//						stringToXmlNode
//
//	Attempts to load the supplied string into an XML doc.
//
function stringToXmlNode( sXml )
{

		if (navegador == "ie")
			{
			var oXml = new ActiveXObject("Msxml2.DOMDocument");
			//	alert(oXml);
			oXml.async = false;
			oXml.validateOnParse = false;
			if( oXml == null )
				{
				err( "Error: stringToXmlNode couldn't instantiate XMLDOM" );
				return null;
				}
			try {
				//alert(sXml.xml);
					oXml.loadXML( sXml );					
				}
			catch(e)
				{
					err( "Error: " + e + "\n no se pudo cargar como XML: " + sTmp );
					return null;
				}
			}
		else	
			{
			var oXml = document.implementation.createDocument("","tmp",null);

			}

	
	var oRoot = oXml.documentElement;
	
//	alert(serializa_me(oRoot));
	
	
	return oRoot;
}

//
//						utilGetXmlAttribute
//
//	Looks for spec'd attribute attrib and returns value or null if not found.
//
function utilGetXmlAttribute( node, sAttrib )
{
if (define_tipo  (node.nodeType) != "text")
	return node.getAttribute( sAttrib );
	
else 
	{
//	alert(node.parentNode.tagName);
	return null;
	}
	
}

//
//						utilSetXmlAttribute
//
function utilSetXmlAttribute( oXml, sAttrib, sVal )
{
	var attrib = oXml.ownerDocument.createAttribute( sAttrib );
	attrib.value = sVal;
	oXml.setAttributeNode( attrib );

}

//
//						utilUpdateXmlNodeValue
//
function utilUpdateXmlNodeValue( node, val )
{
	if( node == null )
	{
		err( "utilUpdateXmlNodeValue: null node" );
		return;
	}
	
	//revisar esto -> 
	
	el_tipo = define_tipo  (node.nodeType);
	
	//if (val == "") return;
	
	switch( el_tipo )
	{
	case "text":
		if (navegador == "firefox")
			node.childNodes[0].nodeValue = val;
		else
			node.nodeValue = val;
		break;
	case "attribute":
		node.value = val;
		break;
	case "element":
		if (node.childNodes.length != 0)
			{
			node.childNodes[0].nodeValue = val;
			}
		else
			{
			if (navegador == "ie")
				{
				var origenXML = new ActiveXObject("Msxml2.DOMDocument");
				origenXML.async = true;
				origenXML.resolveExternals = true;
				texto = origenXML.createTextNode(val);
				node.appendChild(texto);
				}
			else
				{
				node.textContent = val;
				}
			
			}
		break;
	default:
		if (navegador == "firefox")
			node.childNodes[0].nodeValue = val;
		else
			node.nodeValue = val;
	}
}

//
//						utilGetXmlNodeValue
//
function utilGetXmlNodeValue( node )
{
	if( node == null )
	{
		err( "Valor del nodo: null" );
		return;
	}
	
	el_tipo = define_tipo  (node.nodeType);
	switch( el_tipo )
	{
	case "text":
		return node.nodeValue;
		
	case "attribute":
		return node.value;
	
	case "element":
		var i;
		var s = "";
		for( i = 0; i < node.childNodes.length; i++ )
		{
			var child = node.childNodes[i];
			if( define_tipo  (child.nodeType) == "text" )
				s += child.nodeValue;
		}
		return s;
		
	default:
		//err( "utilGetXmlNodeValue: can't take value of node type '" + node.nodeTypeString + "'" );
		return "";
	}
}

//
//						utilXmlNodesCanMerge
//
//	Returns true if they're same type node with same attributes, contents may differ.
//
function utilXmlNodesCanMerge( n1, n2 )
{
	// check for same type
	if( n1.nodeName != n2.nodeName )
		return false;
	
	// if they're text they can always merge
		
	if( define_tipo  (n1.nodeType) == "text" )
		return true;
	
	// check for matching attributes
	var i;
	for( i = 0; i < n1.attributes.length; i++ )
	{
		att1 = n1.attributes[i];
		att2 = n2.getAttributeNode( att1.nodeName );
		if( att2 == null )
			return false;
		if( att1.value != att2.value )
			return false;
	}
	
	// looks good
	return true;
}

//
//						utilTraverseRight
//
//	Attempts to find a field to the right of the spec'd one.
//	Depth-first tree traversal.
//
function utilTraverseRight( cur, bUp )
{
	// catch root
	if( cur == null )
		return null;

	if( bUp )
	{
		// look right first
		var en = cur.nextSibling;
		if( en == null )
			return utilTraverseRight( cur.parent, true );
	}
	else
	{
		// look down first
		if( cur.childNodes.length == 0 )
			return utilTraverseRight( cur, true );
		var en = cur.childNodes[0];
	}	
	if( en.nodeClass == "field" )
		return en;
	else
		return utilTraverseRight( en, false );
}

//
//						utilTraverseLeft
//
//	Attempts to find a field to the left of the spec'd one.
//	Depth-first tree traversal.
//
function utilTraverseLeft( cur, bUp )
{
	// catch root
	if( cur == null )
		return null;

	if( bUp )
	{
		// look left first
		var en = cur.previousSibling;
		if( en == null )
			return utilTraverseLeft( cur.parent, true );
	}
	else
	{
		// look down first
		if( cur.childNodes.length == 0 )
			return utilTraverseLeft( cur, true );
		var en = cur.childNodes[cur.childNodes.length - 1];
	}	
	if( en.nodeClass == "field" )
		return en;
	else
		return utilTraverseLeft( en, false );
}

//
//						utilParseOptions
//
//	Expects edxoptions in CSS-like format:  name:value;name:value; etc.
//	Peels apart options string and returns an array of name/value pairs (see 
//	immediately below).  Normalizes all trimming excess white space and 
//	converting to lower case.
//
function utilParseOptions( sOptions )
{
	var ops = sOptions.split( ";" );
	var a = new Array();
	var i;
	
	for( i = 0; i < ops.length; i++ )
	{
		var op = utilTrim( ops[i] );
		if( op.length == 0 )
			continue;
			
		var nv = ops[i].split( ":" );
		if( nv.length != 2 )
		{
			err( "Error: options must be of format 'name:value' and semicolon delimited: " + sOptions );
			return a;
		}
		a[a.length] = new option( utilTrim(nv[0]).toLowerCase(), utilTrim(nv[1]).toLowerCase() );
	}
	return a;
}

//
//						option
//
//	Constructor for micro-option class.
//
function option( n, v )
{
	this.name = n;
	this.value = v;
}

//
//						utilTrim
//
//	Removes leading/trailing whitespace from a string.
//
function utilTrim( s )
{
	s = s.replace( /^\s+/, "" );
	s = s.replace( /\s+$/, "" );
	return s;
}

//
//						utilGetSigFromNode
//
//	Takes an XML node and builds a signature path to that node.
//	For convenience, also recognizes n as an existing signature and
//	simply returns it.
//
function utilGetSigFromNode( n )
{
	// test to see if it's already a signature array
	if( n instanceof Array )
	{
		return n;
	}

	var a = new Array();
	
	// first, see if it's an attribute-- they don't have parents like normal nodes
	if( define_tipo  (n.nodeType) == "attribute" )
	{
		a[0] = "@" + n.name;
		if (navegador == "firefox15")
			{
			n = n.ownerElement.selectSingleNode( "." );		// get parent element
			}
		else
			{
			n = n.selectSingleNode( ".." );	
			}

	}
	
	var par = n.parentNode;
	while( par != null )
	{
		var i;
		for( i = 0; i < par.childNodes.length; i++ )
		{
			if( par.childNodes[i] == n )
				break;
		}
		a[a.length] = i;
		n = par;
		par = n.parentNode;
	}
	return a.reverse();
}

//
//						utilGetNodeFromSig
//
//	Takes a signature array and returns the assoc'd node.
//
function utilGetNodeFromSig( a, oXmlDoc )
{
	var n = oXmlDoc;
	var i;
	for( i = 0; i < a.length; i++ )
	{
		if( typeof(a[i]) == "string" )
		{
			// hit an attribute
			n = n.selectSingleNode( a[i] );
		}
		else
			n = n.childNodes[a[i]];
	}
	return n;
}

//
//						utilGetSigStringFromSig
//
//	Returns a string representation of a node signature.
//
function utilGetSigStringFromSig( a )
{
	var i;
	var s = ""
	for( i = 0; i < a.length; i++ )
	{
		if( s.length != 0 )
			s += ":";
		s += a[i];
	}
	return s;
}

//
//						utilGetSigFromSigString
//
//	Returns a node signature from a string representation.
//
function utilGetSigFromSigString( s )
{
	var p = s.split(":");
	var i;
	var a = new Array();
	
	for( i = 0; i < p.length; i++ )
	{
		if( p[i].charAt(0) != "@" )
			a[i] = p[i] / 1;
		else
			a[i] = p[i];
	}
}


//
//						utilArrayIndex
//
//	Finds index of an item within the passed array.  Returns -1 if not found.
//
function utilArrayIndex( a, item )
{
	var i;
	
	for( i = 0; i < a.length; i++ )
	{
		if( a[i] == item )
			return i;
	}
	return -1;
}

//
//						utilCoalesceXmlNodes
//
//	Descends into spec'd node and does as much work as it can merging like type nodes.
//
function utilCoalesceXmlNodes( nParent, xmlmgr )
{
	var i;
	var bDidSomething = true;
	
	while( bDidSomething )
	{
		bDidSomething = false;
		for( i = 0; i < nParent.childNodes.length - 1; i++ )
		{
			var n1 = nParent.childNodes[i];
			var n2 = nParent.childNodes[i+1];
			if( utilXmlNodesCanMerge( n1, n2 ) )
			{
				if( define_tipo  (n1.nodeType) == "text" )
				{
					xmlmgr.process( "joinText", n1 );
				}
				else
				{
					xmlmgr.process( "joinNodes", n1 );
				}
				bDidSomething = true;
				break;
			}
		}
	}
	for( i = 0; i < nParent.childNodes.length; i++ )
	{
		utilCoalesceXmlNodes( nParent.childNodes[i], xmlmgr );
	}
}

//
//						utilDeleteXmlNode
//
//	Removes an XML node and looks up tree to see if further cleanup can happen.
//	Returns parent of last node deleted.
//
function utilDeleteXmlNode( nDelete, nTopContainer, xmlmgr )
{
	// get parent of this node
	var nParent = nDelete.parentNode;

	// find index of ourselves in parent
	var iIndex = utilArrayIndex( nParent.childNodes, nDelete );
		
	// tell XML manager to do the dirty work
	xmlmgr.process( "deleteNode", nParent, iIndex );
		
	// see if this happens to leave us with an empty parent node
	if( nParent.childNodes.length == 0 && nParent != nTopContainer )
	{
		return utilDeleteXmlNode( nParent, nTopContainer, xmlmgr );
	}
	else
	{
		return nParent;
	}
}

//
//						utilArrayCopy
//
function utilArrayCopy( a )
{
	var aNew = new Array();
	var i;
	for( i = 0; i < a.length; i++ )
	{
		aNew[i] = a[i];
	}
	return aNew;
}

//
//						utilResolveUrl
//
//	If the URL is relative, applies the supplied base path and returns full URL.
//
function utilResolveUrl( sUrl, sBase )
{
	var sFullPath = sUrl;
	
	// look for UNC or URLs like c:
	if( sUrl.charAt(0) == '\\' || sUrl.charAt(1) == ':' )
		return sUrl;
		
	// look for web URLs
	if( sUrl.indexOf( "http://" ) == -1 && sUrl.charAt(0) != '/'  )
		sFullPath = sBase + sUrl;
	return sFullPath;
}

//
//						err
//
function err(sMsg)
{
	alert( sMsg );
}

var dbglog = "";

//
//						dbg
//
//	Appends a string to a debug buffer.  Print 'dbglog' from immediate window 
//	in Visual Interdev to see what's going on.
//    
function dbg( s )
{
	var d = new Date;
	dbglog += "(" + d.getSeconds() + "." + d.getMilliseconds() + ") " + s + "\n";
}

//// Prototipo de insertAdjacent

if(typeof HTMLElement!="undefined" && !
HTMLElement.prototype.insertAdjacentElement){
	HTMLElement.prototype.insertAdjacentElement = function
(where,parsedNode)
	{
		switch (where){
		case 'beforeBegin':
			this.parentNode.insertBefore(parsedNode,this)
			break;
		case 'afterBegin':
			this.insertBefore(parsedNode,this.firstChild);
			break;
		case 'beforeEnd':
			this.appendChild(parsedNode);
			break;
		case 'afterEnd':
			if (this.nextSibling) 
this.parentNode.insertBefore(parsedNode,this.nextSibling);
			else this.parentNode.appendChild(parsedNode);
			break;
		}
	}

	HTMLElement.prototype.insertAdjacentHTML = function
(where,htmlStr)
	{
		var r = this.ownerDocument.createRange();
		r.setStartBefore(this);
		var parsedHTML = r.createContextualFragment(htmlStr);
		this.insertAdjacentElement(where,parsedHTML)
	}


	HTMLElement.prototype.insertAdjacentText = function
(where,txtStr)
	{
		var parsedText = document.createTextNode(txtStr)
		this.insertAdjacentElement(where,parsedText)
	}
}
