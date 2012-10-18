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

/*----------------------------------------------------------------------------\
|                               XLoadTree 1.11                                |
|-----------------------------------------------------------------------------|
|                         Created by Erik Arvidsson                           |
|                  (http://webfx.eae.net/contact.html#erik)                   |
|                      For WebFX (http://webfx.eae.net/)                      |
|-----------------------------------------------------------------------------|
| An extension to xTree that allows sub trees to be loaded at runtime by      |
| reading XML files from the server. Works with IE5+ and Mozilla 1.0+         |
|-----------------------------------------------------------------------------|
|                   Copyright (c) 1999 - 2002 Erik Arvidsson                  |
|-----------------------------------------------------------------------------|
| This software is provided "as is", without warranty of any kind, express or |
| implied, including  but not limited  to the warranties of  merchantability, |
| fitness for a particular purpose and noninfringement. In no event shall the |
| authors or  copyright  holders be  liable for any claim,  damages or  other |
| liability, whether  in an  action of  contract, tort  or otherwise, arising |
| from,  out of  or in  connection with  the software or  the  use  or  other |
| dealings in the software.                                                   |
| - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - |
| This  software is  available under the  three different licenses  mentioned |
| below.  To use this software you must chose, and qualify, for one of those. |
| - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - |
| The WebFX Non-Commercial License          http://webfx.eae.net/license.html |
| Permits  anyone the right to use the  software in a  non-commercial context |
| free of charge.                                                             |
| - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - |
| The WebFX Commercial license           http://webfx.eae.net/commercial.html |
| Permits the  license holder the right to use  the software in a  commercial |
| context. Such license must be specifically obtained, however it's valid for |
| any number of  implementations of the licensed software.                    |
| - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - |
| GPL - The GNU General Public License    http://www.gnu.org/licenses/gpl.txt |
| Permits anyone the right to use and modify the software without limitations |
| as long as proper  credits are given  and the original  and modified source |
| code are included. Requires  that the final product, software derivate from |
| the original  source or any  software  utilizing a GPL  component, such  as |
| this, is also licensed under the GPL license.                               |
|-----------------------------------------------------------------------------|
| 2001-09-27 | Original Version Posted.                                       |
| 2002-01-19 | Added some simple error handling and string templates for      |
|            | reporting the errors.                                          |
| 2002-01-28 | Fixed loading issues in IE50 and IE55 that made the tree load  |
|            | twice.                                                         |
| 2002-10-10 | (1.1) Added reload method that reloads the XML file from the   |
|            | server.                                                        |
| 2003-03-02 | Changed to XmlDocument to enable resolveExternals              |
|            | Improved the error checking a little                           |
|-----------------------------------------------------------------------------|
| Dependencies: xtree.js - original xtree library                             |
|               xtree.css - simple css styling of xtree                       |
|               xmlextras.js - provides xml http objects and xml document     |
|                              objects                                        |
|-----------------------------------------------------------------------------|
| Created 2001-09-27 | All changes are in the log above. | Updated 2002-10-10 |
\----------------------------------------------------------------------------*/

/* Variable converted to global (local at método _startLoadXmlTree)
 * to fix load problems with nodes.
 * When it was local, sometimes the method finish before the property
 * "readyState = 4" (full document load), and then it never finished to load
*/
var xmlDoc;
webFXTreeConfig.loadingText = "Loading...";
webFXTreeConfig.loadErrorTextTemplate = "Load error \"%1%\"";
webFXTreeConfig.emptyErrorTextTemplate = "Error \"%1%\" does not contain elements";

var display_idnodos = false;

/*
 * WebFXLoadTree class
 */

function WebFXLoadTree(sText, sXmlSrc, sAction, sBehavior, sIcon, sOpenIcon, idNode) {

	this.idNode = idNode;
	nodes[idNode] = this;
	// Calling super
	this.WebFXTree = WebFXTree;
	this.WebFXTree(sText, sAction, sBehavior, sIcon, sOpenIcon, idNode);

	// Setting default property values
	this.src = sXmlSrc;
	this.loading = false;
	this.loaded = false;
	this.errorText = "";

	// Checking start state and load if open
	if (this.open)
		_startLoadXmlTree(this.src, this);
	else {
		// Creating loading item otherwise
		this._loadingItem = new WebFXTreeItem(webFXTreeConfig.loadingText);
		this.add(this._loadingItem);
	}
}

WebFXLoadTree.prototype = new WebFXTree;

// Overriding the expand method to load the xml file
WebFXLoadTree.prototype._webfxtree_expand = WebFXTree.prototype.expand;
WebFXLoadTree.prototype.expand = function() {
	if (!this.loaded && !this.loading) {
		// load
		_startLoadXmlTree(this.src, this);
	}
	this._webfxtree_expand();
};

/*
 * WebFXLoadTreeItem class
 */

function WebFXLoadTreeItem(sText, sXmlSrc, sAction, eParent, sIcon, sOpenIcon, idNode, childCount, nodeState, nodeOpen, nodeSelected)
	{
	// Calling super
	this.idNode = idNode;
	this.WebFXTreeItem = WebFXTreeItem;
	this.WebFXTreeItem(sText, sAction, eParent, sIcon, sOpenIcon, idNode, childCount, nodeState);
	nodes[this.idNode] = this;
	// Setting default property values
	this.src = sXmlSrc;
	this.loading = false;
	this.loaded = false;
	this.errorText = "";
	this.openNode = nodeOpen;
	this.selectedNode = nodeSelected;
	if(	this.selectedNode)
		{
		eval(sAction);
		//alert(_("Selecting j ")+this.idNode);
		}
	this.open= nodeOpen;
	if (this.open)
		{
		//alert(_("Cargando ")+this.idNode);

		///if(webFXTreeHandler.cookies.getCookie(this.idNode) == '1')
		///	{
		///	alert(_("It would open ")+this.idNode);
		///	this.expand();
		///	}
		_startLoadXmlTree(this.src, this);

//		this.expand();
		}
	else
		{
		//Creating loading item otherwise
		this._loadingItem = new WebFXTreeItem(webFXTreeConfig.loadingText);
		this.add(this._loadingItem);
		}
	}

WebFXLoadTreeItem.prototype = new WebFXTreeItem;

// Overriding the expand method to load the xml file
WebFXLoadTreeItem.prototype._webfxtreeitem_expand = WebFXTreeItem.prototype.expand;
WebFXLoadTreeItem.prototype.expand = function() {
	if (!this.loaded && !this.loading) {
		// load
		_startLoadXmlTree(this.src, this);
	}

	this._webfxtreeitem_expand();
	this.select();
	this.collapsed=false;
};

//It reloads the src file if already loaded
WebFXLoadTree.prototype.reload =
WebFXLoadTreeItem.prototype.reload = function () {
	//alert("Open: "+this.open+" Loaded: "+this.loaded+" Flag: "+this.collapsed);
	//If it is not load, expands
	//this.select();
	if (!this.loaded){
		this.expand();
	}
	if(this.collapsed){
		this.collapsed=false;
		this.expand();
		this.reload();

	}
	else if (this.loaded) {
		// Removing
		//if (this.childNodes.length )

		while (this.childNodes.length > 0){
			this.childNodes[this.childNodes.length - 1].remove();
		}

		this.loaded = false;
		this._loadingItem = new WebFXTreeItem(webFXTreeConfig.loadingText);
		this.add(this._loadingItem);

		//if (this.open)
		this.expand();

	}
	else if (this.open && !this.loading){
		_startLoadXmlTree(this.src, this);
	}
};

/*
 * Helper functions
 */

// Creating the xmlhttp object and starts the load of the xml document
function _startLoadXmlTree(sSrc, jsNode) {
///
var numMaxFiles_origen = busca_numMaxFiles();
//alert(_('Previous: ')+ sSrc);
sSrc = sSrc + "&nelementos="+ numMaxFiles_origen;
//alert(_('Next: ')+ sSrc);

//If we are debbuging 
if(	parent.document.getElementById("debugfilter") && parent.document.getElementById("debug_filter").style.display == "block" ) {
	sSrc = sSrc + "&find="+parent.document.getElementById("debugfilter").value;
}

	if (jsNode.loading || jsNode.loaded)
		return;
	jsNode.loading = true;
	/* Variable converts to global (see the initial comment)
	 * var xmlDoc = XmlDocument.create();
	*/

	xmlDoc = XmlDocument.create();
	xmlDoc.async = true;
	xmlDoc.resolveExternals = true;
	xmlDoc.onreadystatechange = function () {
		if (xmlDoc.readyState == 4) {
			_xmlFileLoaded(xmlDoc, jsNode);
		}
	};
	// Call in new thread to allow ui to update
	window.setTimeout(function () {
		xmlDoc.load(sSrc);
	}, 10);
}


// It converts an xml tree to a js tree. See article about xml tree format
function _xmlTreeToJsTree(oNode) {
	// Retreiving attributes

	var text = oNode.getAttribute("text");
	
	if(display_idnodos)
		 text += " ("+ oNode.getAttribute("nodeid")+") ";

	var parent = null;
	var action = oNode.getAttribute("action");
	var icon = oNode.getAttribute("icon");
	var openIcon = oNode.getAttribute("openIcon");
	var src = oNode.getAttribute("src");
	var idNode = oNode.getAttribute("nodeid");
	var childCount = oNode.getAttribute("children");
	var nodeState = oNode.getAttribute("state");
	var nodeOpen = (oNode.getAttribute("open") == "yes");
	var nodeSelected = (oNode.getAttribute("selected") == "yes");

	var numMaxFiles_origen = busca_numMaxFiles();


//	alert(numMaxFiles_origen);

//	src = src + "&nelementos=" + numMaxFiles_origen;

//	alert(src);

	///alert("open="+oNode.getAttribute("open"));
	// Creating jsNode
	var jsNode;
	if (src != null && src != "")
		jsNode = new WebFXLoadTreeItem(text, src, action, parent, icon, openIcon, idNode, childCount, nodeState, nodeOpen, nodeSelected);
	else
		jsNode = new WebFXTreeItem(text, action, parent, icon, openIcon, idNode, childCount, nodeState);

	// Going through childNOdes
	var cs = oNode.childNodes;
	var l = cs.length;
	for (var i = 0; i < l; i++) {
		if (cs[i].tagName == "tree")
			jsNode.add( _xmlTreeToJsTree(cs[i]), true );
	}

	return jsNode;
}

// It inserts an xml document as a subtree to the provided node
function _xmlFileLoaded(oXmlDoc, jsParentNode) {
	if (jsParentNode.loaded)
		return;
// Triying to paginate 
// End of attempt
	var bIndent = false;
	var bAnyChildren = false;
	jsParentNode.loaded = true;
	jsParentNode.loading = false;
	// Checking that the load of the xml file was successfully
	if( oXmlDoc == null || oXmlDoc.documentElement == null) {
		if (oXmlDoc && oXmlDoc.parseError != null) {
			alert(oXmlDoc.parseError.reason);
		}
		else {
			alert("XML parse error");
		}
		jsParentNode.errorText = parseTemplateString(webFXTreeConfig.loadErrorTextTemplate, jsParentNode.src);
	}
	else {
		// There is one extra level of tree elements
		var root = oXmlDoc.documentElement;

		// Loop through all tree children
		var cs = root.childNodes;
		var l = cs.length;

			for (var i = 0; i < l; i++) {
				//alert();
				if (cs[i].tagName == "tree") {
					bAnyChildren = true;
					bIndent = true;
					//alert(cs[i].xml);
					jsParentNode.add( _xmlTreeToJsTree(cs[i]), true);
				}
			}

	}

	// Removing dummy
	if (jsParentNode._loadingItem != null) {
		jsParentNode._loadingItem.remove();
		bIndent = true;
	}

	if (bIndent) {
		// Indent now that all items are added
		jsParentNode.indent();
	}
	//alert(_("Loading ")+jsParentNode.loaded);
	// It just reloads if there are any children 
	if(bAnyChildren)
		{
		jsParentNode.expand();
		}
	if(jsParentNode.selectedNode)
		{
//		alert("Selecting "+jsParentNode.selectedNode);
		jsParentNode.select();
		}
	// It shows an error in status bar
	//if (jsParentNode.errorText != "")
	//	window.status = jsParentNode.errorText;
}

// It parses a string and replaces %n% with argument nr n
function parseTemplateString(sTemplate) {
	var args = arguments;
	var s = sTemplate;

	s = s.replace(/\%\%/g, "%");

	for (var i = 1; i < args.length; i++)
		s = s.replace( new RegExp("\%" + i + "\%", "g"), args[i] )

	return s;
}
