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

// detección de navegador
var ie = false;
var firefox10 = false;
var firefox15 = false;
function detectBrowser() {
    var ie = document.all != undefined;
    var opera = window.opera != undefined;
    if (opera) return "opera";
    if (navegador == "ie")
		{
		ie = true;
		return "ie";
		}
    if ((window)&&(window.netscape)&&(window.netscape.security)) {
      if (window.XML) {
        	firefox15 = true;
			return "firefox15";
	      }
      else	
	  		{
			firefox10 = true;
			return "firefox10";
			}
    }
	ie = true;
    return "ie";      // If we do not know what browser is, return ie.
  }
var navegador = detectBrowser();


// Function to obtain name of file at end of path. Receives a string and return the end of path.

function fin_ruta( la_ruta ){
		//n_archivo.value.length
		nuevo_valor = "";
		nuevo_valor2 = "";
		for (i=la_ruta.length-1;i>=0;i--) {
			if (la_ruta.charAt(i) != "/") nuevo_valor = nuevo_valor +  la_ruta.charAt(i);
			else break;
		}
		for (i=nuevo_valor.length-1;i>=0;i--) {
			nuevo_valor2 = nuevo_valor2 +  nuevo_valor.charAt(i);
		}
		return nuevo_valor2;	
		
	}

/// lack of Firefox ->functions to emule on Firefox 1.5 selectNodes and selecSingleNode of Explorer

if( document.implementation.hasFeature("XPath", "3.0") )
{
   // prototying the XMLDocument
   XMLDocument.prototype.selectNodes = function(cXPathString, xNode)
   {
      if( !xNode ) { xNode = this; } 
      var oNSResolver = this.createNSResolver(this.documentElement);
//	  alert("cXPathString ->" + cXPathString);

      var aItems = this.evaluate(cXPathString, xNode, oNSResolver, XPathResult.ORDERED_NODE_SNAPSHOT_TYPE, null);
//	alert(aItems.snapshotLength)
      var aResult = [];

      for( var i = 0; i < aItems.snapshotLength; i++)
      {
	  
         aResult[i] =  aItems.snapshotItem(i);

      }
      return aResult;
   }
   // prototying the Element
   Element.prototype.selectNodes = function(cXPathString)
   {
      if(this.ownerDocument.selectNodes)
      {
	     return this.ownerDocument.selectNodes(cXPathString, this);
      }
      else{throw "For XML Elements Only";}
   }
}
if( document.implementation.hasFeature("XPath", "3.0") )
{
   // prototying the XMLDocument
   XMLDocument.prototype.selectSingleNode = function(cXPathString, xNode)
   {

      if( !xNode ) { xNode = this; } 
      var xItems = this.selectNodes(cXPathString, xNode);
      if( xItems.length > 0 )
      {
	 // alert('xItems ' + xItems[0]);
         return xItems[0];
      }
      else
      {
         return null;
      }
   }
   
   // prototying the Element
   Element.prototype.selectSingleNode = function(cXPathString)
   {	
      if(this.ownerDocument.selectSingleNode)
      {
         return this.ownerDocument.selectSingleNode(cXPathString, this);
      }
      else{throw "For XML Elements Only";}
   }

}	


/// Function to check if the node is a blank space

function is_all_ws( nod )
{
  // Use ECMA-262 Edition 3 String and RegExp features
  return !(/[^\t\n\r ]/.test(nod.data));
}

function is_ignorable( nod )
{

  return ( nod.nodeType == 8) || // Coment
         ( (nod.nodeType == 3)  // blank space
		  && is_all_ws(nod) ); // a text node, all ws
}

if (navegador == "firefox15"){
	var serializer = new XMLSerializer();
}

function serializa_me (el_nodo)
	{
	if (navegador == "firefox15")
		{
		var Mxml = serializer.serializeToString(el_nodo);
		return Mxml;
		}
	else
		{
		return el_nodo.xml;
		}
	
	}
	
	
/// Function to define node type
/*
Node.ELEMENT_NODE == 1 
Node.ATTRIBUTE_NODE == 2 
Node.TEXT_NODE == 3 
Node.CDATA_SECTION_NODE == 4 
Node.ENTITY_REFERENCE_NODE == 5 
Node.ENTITY_NODE == 6 
Node.PROCESSING_INSTRUCTION_NODE == 7 
Node.COMMENT_NODE == 8 
Node.DOCUMENT_NODE == 9 
Node.DOCUMENT_TYPE_NODE == 10 
Node.DOCUMENT_FRAGMENT_NODE == 11 
Node.NOTATION_NODE == 12 
*/
// Recive a node with type property and return a type (string) accord with table above. Compatibilidad IE / Firefox 1.5

function define_tipo ( el_tipo )
	{
		switch( el_tipo )
		{
			case 1:
				return "element";
			case 2:
				return "attribute";
			case 3:
				return "text";
			case 4:
				return "cdata";
			case 5:
				return "entity_reference";
			case 6:
				return "entity";
			case 7:
				return "processing_instruction";
			case 8:
				return "comment";
			case 9:
				return "document";
			case 10:
				return "document_type";
			case 11:
				return "document_fragment";
			case 12:
				return "notation";
			default:
				return "";
		}
	}

	
	/// associates properties
	
function asocia_propiedades(Snode, Tnode){
	if (Snode.hasAttributes()) 
       			 {
				 for (y = 0; y < Snode.attributes.length; y ++)
				 	{
//    				  Tnode.setAttribute(Snode.attributes[0].name, Snode.attributes[0].value);
						Tnode[Snode.attributes[y].name] = Snode.attributes[y].value;
					}
    			 }
		return Tnode;
	
}


var notWhitespace = /\S/;
function limpia_blanco(node) {
  for (var x = 0; x < node.childNodes.length; x++) {
    var childNode = node.childNodes[x]
    if ((childNode.nodeType == 3)&&(!notWhitespace.test(childNode.nodeValue))) {
// that is, if it's a whitespace text node
      node.removeChild(node.childNodes[x])
      x--
    }
    if (childNode.nodeType == 1) {
// elements can have text child nodes of their own
      limpia_blanco(childNode)
    }
  }
}

////// Copy to clipboard

function copy_clip(meintext)
{

 if (window.clipboardData) 
   {
   
   // the IE-way
   window.clipboardData.setData("Text", meintext);
   
   // Probably not the best way to detect netscape/mozilla.
   // I am unsure from what version this is supported
   }
   else if (window.netscape) 
   { 
   
   // This is importent but it's not noted anywhere
   netscape.security.PrivilegeManager.enablePrivilege('UniversalXPConnect');
   
   // create interface to the clipboard
   var clip =
Components.classes['@mozilla.org/widget/clipboard;[[[[1]]]]'].createInstance(Components.interfaces.nsIClipboard);
   if (!clip) return;
   
   // create a transferable
   var trans =
Components.classes['@mozilla.org/widget/transferable;[[[[1]]]]'].createInstance(Components.interfaces.nsITransferable);
   if (!trans) return;
   
   // specify the data we wish to handle. Plaintext in this case.
   trans.addDataFlavor('text/unicode');
   
   // To get the data from the transferable we need two new objects
   var str = new Object();
   var len = new Object();
   
   var str =
Components.classes["@mozilla.org/supports-string;[[[[1]]]]"].createInstance(Components.interfaces.nsISupportsString);
   
   var copytext=meintext;
   
   str.data=copytext;
   
   trans.setTransferData("text/unicode",str,copytext.length*[[[[2]]]]);
   
   var clipid=Components.interfaces.nsIClipboard;
   
   if (!clip) return false;
   
   clip.setData(trans,null,clipid.kGlobalClipboard);
   
   }
   alert("La siguiente información fue insertada en el portapapeles:\n\n" + meintext);
   return false;
}


/// Prototype of function "SwapNode"
if (navegador != "ie"){
	Node.prototype.swapNode = function (node) {
    var nextSibling = this.nextSibling;
	var parentNode = this.parentNode;
	
//	alert(this.innerHTML);
//	alert(node.innerHTML);
	
    node.parentNode.replaceChild(this, node);
	//parentNode.appendChild(node);
	
	parentNode.insertBefore(node, nextSibling);
	
 	}
	
	Node.prototype.swapNodeUp = function (node) {
    var nextSibling = this;
	var parentNode = this.parentNode;
	
//	alert(this.innerHTML);
//	alert(node.innerHTML);
	
    node.parentNode.replaceChild(this, node);
	//parentNode.appendChild(node);
	
	parentNode.insertBefore(node, nextSibling);
	
 	}
	
}

//Catchs keys Init, end, pag down and pag up
window.onkeypress = function (evt)
{ 
 var etiq=evt.target.tagName;
 var esEditor=false;
 if(etiq=="HTML")
 {
  if(evt.target.childNodes[0].childNodes[0].innerHTML=="editor ximDEX v 2.0")
  {
   esEditor=true;
  } 
 }
 
 if(etiq!="TEXTAREA" || esEditor)
 	{
	 //Block of keys: Tab, Init, end, pag down  y pag up
		if ( evt.keyCode==9 || evt.keyCode==33 || evt.keyCode==34 || 
			evt.keyCode==35 || evt.keyCode==36) 
		{
		 evt.stopPropagation();
		 evt.returnValue = false;
		 evt.preventDefault();
		}
	} 
} 


