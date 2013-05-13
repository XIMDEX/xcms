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
 *  @version $Revision: 8545 $
 */


/**
 * Representation of a Ximdoc document.
 * Function which loads the XML into a javascript object and creates relations between the XML node and RNG node.
 * It defines an API to operate with the nodes and export them to valid XML.
 * @constructor
 */
XimDocument = function(editorConfig) {

	/**
	 * @constant
	 */
	this.POSITION_LAST = 0x01;

	/**
	 * @constant
	 */
	this.POSITION_BEFORE = 0x02;

	/**
	 * @constant
	 */
	this.POSITION_AFTER = 0x03;

	this._nodeId = null;
	this._lastUID = [];
	this._rootNode = null;
	this._ximModel = null;
	this._xmldoc = null;
	this._rngDoc = null;
	this._exportDoc = null;
	this._channelId = null;
	this._schemaValidatorIsActive = null;
	this._editorConfig = editorConfig;
	this.editor = null;
	this._errors=[];
	this.ELEMENT_NOT_FOUND_MESSAGE = _("Elements not found in Relax-ng schema: ");

	/**
	 * Function which returns the ximdex node ID.
	 * @returns {int}
	 */
	this.getNodeId = function() {
		return this._nodeId;
	};

	/**
	 * Function which returns the ximdoc model object.
	 * @returns {object}
	 */
	this.getXimModel = function() {
		return this._ximModel;
	};

	/**
	 * Function which sets the ximdoc model object.
	 * @returns {object}
	 */
	this.setXimModel = function(model) {
		this._ximModel = model;
	};

	/**
	 * Function which returns the RNG model object.
	 * @returns {object}
	 */
	this.getRngModel = function() {
		return this._rngDoc.getModel();
	};

	/**
	 * Function which returns the RNG document.
	 * @returns {DOMDocument}
	 */
	this.getRngDocument = function() {
		return this._rngDoc;
	};

	/**
	 * Function which returns the XML document.
	 * @returns {DOMDocument}
	 */
	this.getXmlDocument = function() {
		return this._xmldoc;
	};

	/**
	 * Function which returns the transformation channel ID.
	 * @returns {int}
	 */
	this.getChannelId = function() {
		return this._channelId;
	};

	this.expertModeIsAllowed = function() {
		return (this._editorConfig.expert_mode_allowed == 0) ? false : true;
	};

	this.publicationIsAllowed = function() {
		return (this._editorConfig.publication_allowed == 0) ? false : true;
	};

	this.toggleSchemaValidator = function() {
		if (!this.expertModeIsAllowed()) this._schemaValidatorIsActive = false;
		this._schemaValidatorIsActive = !this._schemaValidatorIsActive;
		//console.info('Schema validation: %s', (this._schemaValidatorIsActive ? 'On' : 'Off'));
	};

	this.schemaValidatorIsActive = function() {
		return this._schemaValidatorIsActive;
	};

	this.setSchemaValidator = function(validate) {
		if (!this.expertModeIsAllowed()) validate = true;
		this._schemaValidatorIsActive = (validate ? true : false);
		//console.info('Schema validation: %s', (this._schemaValidatorIsActive ? 'On' : 'Off'));
	};

	/**
	 * Function which starts the parsing of a new Ximdoc Document.
	 * @param {DOMDocument} xmldoc The document to be parsed
	 * @param {DOMDocument} rngdoc The RNG schema to validate the ximdoc document.
	 */
	this.loadXML = function(xmldoc, rngdoc) {
		this._xmldoc = xmldoc;
		this._rngDoc = rngdoc;
		this._ximModel = {};
		this._nodeId = null;
		this._lastUID = [];
		this._rootNode = null;
		this._errors = [];

		/*
		var grammar = this._findGrammarElement(this._xmldoc);
		if(grammar)
			grammar.setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:xim', 'http://www.ximdex.com/');
		*/

		var docxap = this._findDocxapElement(this._xmldoc);
		if (docxap) {
			//docxap.setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:xim', 'http://www.ximdex.com/');

			docxap.setAttribute('transformer', 'xEDIT');

			// Temporal measure
			$('semantic', docxap).remove();

			var nodeid = docxap.getAttribute('uid');
			nodeid = nodeid.split('.');
			this._nodeId = nodeid[0];
			this._rootNode = this._parseNode(docxap, null);
			this._showErrors();

		}
	};

	
	/**
 	 * Method which alert the errors found after parse the document 
	 * @private  
	 * Create at 2012-11-20 
	 **/
	this._showErrors = function(){
		var result="";
		if (this._errors.length){
			result +=this.ELEMENT_NOT_FOUND_MESSAGE;
			for (var i = 0; i < this._errors.length; i++){
				result +="<br/>- "+this._errors[i];
			}
			this.editor.alert(result);
		}
	}

	/**
	 * Function which finds the root node to start to parse from it.
	 * @private
	 * @param {DOMNode} node
	 */
	this._findDocxapElement = function(node) {
		var docxap = node.getElementsByTagName('docxap');
		docxap = docxap[0];
		return docxap;
	};

	this._findGrammarElement = function(node) {
		var grammar = node.getElementsByTagName('grammar');
		grammar = grammar[0];
		return grammar;
	};

	/**
	 * Recursive. Function which parses a Ximdoc DOMNode.
	 * @private
	 * @param {DOMNode} node Node tobe parsed
	 * @param {XimElement} parent Parent of the node that will be parsed
	 * @returns {XimElement}
	 */
	this._parseNode = function(node, parent) {

		if (!node || node.nodeType != 1) return;

		var parentUID = parent ? parent['uid'] : null;
		var ximElement = this.importXmlElement(node);
		ximElement = this.appendChild(ximElement, parent);
		if (!ximElement){
			this._errors.push(node.tagName);
			return false;
		}
		ximElement.isRoot = parent ? false : true;

		var it = new DOMNodeIterator(node, 1);
		while (it.hasNext()) {
			var child = it.next();
			this._parseNode(child, ximElement);
		}

		return ximElement;
	};

	/**
	 * Function which parses the DOMNode attributes
	 * @private
	 * @param {DOMNode} node Node to be parsed
	 * @returns {Array}
	 */
	this._parseAttributes = function(node) {

		var nodeName = node.tagName.toLowerCase();
		var attributes = node.attributes;
		var l = attributes.length;
		var attrs = {};
		var rngElement = this._rngDoc.getElement(nodeName);

		for (var i=0; i<l; i++) {

			var name = attributes[i].nodeName;
			var value = attributes[i].nodeValue;
			var htmlName = null;

			if (rngElement
				&& rngElement.attributes
				&& rngElement.attributes[name]) {

				htmlName = rngElement.attributes[name];

			}

			attrs[name] = value;
		}

		return attrs;
	};

	/**
	 * Function which returns a string that represents the node value.
	 * @private
	 * @deprecated Use {@link XimDocument#_getNodeArrayValue} instead.
	 */
	this._getNodeValue = function(node) {

		var it = new DOMNodeIterator(node);
		var value = '';
		while (it.hasNext()) {
			var child = it.next();
			if (child.nodeType == 3) {
				value += child.nodeValue;
			}
			if (child.nodeType == 1 && child.getAttribute('content') == 'yes') {
				value.concat(this._getNodeValue(child));
				//value += this._getNodeValue(child);
			}
		}
		return value;
	};

	/**
	 * Function which returns an array that represents the node value.
	 * @private
	 * @param {DOMNode} domnode
	 * @returns {Array}
	 */
	this._getNodeArrayValue = function(domnode) {

		// An array of values should be returned, because where will be as pieces of "value" as
		// node number of children + 1

		var it = new DOMNodeIterator(domnode);
		var value = [];
		var isFirstNode=true;
		var findApplyElement = false;
		var previousChild = false;
		while (it.hasNext()) {
                        var child = it.next();
			//nodetype==1 => is a tag
                        if (child.nodeType==1 && isFirstNode && child.getAttribute("uid")){
                                findApplyElement = true;
                        }

			//nodetype==3 => is text
                        if (child.nodeType == 3 || (previousChild.nodeType && previousChild.nodeType == 1)) {
                                if (findApplyElement){
                                        value.push("");
                                        findApplyElement = false;
                                }

                                value.push(child.nodeValue);
                        }

                        if (isFirstNode && (child.nodeType==3 || child.getAttribute("uid")))
                                isFirstNode=false;

                        // TODO: If child is editable and has children
                        // we have to return a structure instead a value.
                        var editable = child.nodeType == 1 ? (child.getAttribute('editable') || 'yes') : 'yes';

                        // TODO: Not working yet!
                        editable = 'no';

                        if (child.nodeType == 1 && editable.toLowerCase() != 'no') {
                                value = value.concat(this._getNodeArrayValue(child));
                        }

			previousChild = child;
                }
		// Correcting bug with apply elements when they are at the start of the string
		/*if (domnode.childNodes[0] && domnode.childNodes[0].nodeType != 3) {
			value.splice(0, 0, '');
		}*/

		return value;
	};


	// ---- API ----

	/**
	 * Function which returns the next unused UID for a new element.
	 * @private
	 * @returns {int}
	 */
	this._getNewUID = function(nodeId) {
		if (!nodeId)
			nodeId = this._nodeId;
		var uid = (this._lastUID[nodeId] === null || typeof(this._lastUID[nodeId]) == "undefined")
					? -1 : parseInt(this._lastUID[nodeId]);
		this._lastUID[nodeId] = ++uid;
		return nodeId + '.' + (uid);
	};

	/**
	 * Function which recursively assigns a UID to an element and all of his childrens.
	 * @param {XimElement} ximElement
	 */
	this.setElementUID = function(ximElement, parent) {
		if (!ximElement.uid) {
			// It is a new element
			var nodeId = ximElement.getIdNodeForUid(this._nodeId, parent);
			ximElement.uid = this._getNewUID(nodeId);
			ximElement.attributes.uid = ximElement.uid;
		}

		var count = ximElement.childNodes.length;
		for (var i=0; i<count; i++) {
			//if(!ximElement.attributes['ximlet_id'])
				this.setElementUID(ximElement.childNodes[i]);
		}
	};

	/**
	 * Function which returns an element of the ximModel structure with a specific UID.
	 * @param {int} uid
	 * @return {XimElement}
	 */
	this.getElement = function(uid) {
		var node = this._ximModel[uid] || null;
		return node;
	};

	/**
	 * Function which returns the root element of this Ximdoc
	 * @return {XimElement}
	 */
	this.getRootElement = function() {
		return this._rootNode;
	};

	/**
	 * Function which exports a XimElement to a DOMNode.
	 * Shortcut for {@link XimElement#toDomElement}.
	 * @param {int} uid
	 * @return {DOMNode}
	 */
	this.createElement = function(uid) {
		var elem = this.getElement(uid);
		return elem.toDomElement();
	};

	/**
	 * Function which transforms a XML DOMElement into a XimElement and returns it.
	 * @param {DOMNode} domElement XML node to transform.
	 * @param {boolean} importChilds If TRUE imports all domElement childrens.
	 * @returns {XimElement}
	 */
	this.importXmlElement = function(domElement, importChilds) {
		var tagName = domElement.tagName.toLowerCase();
		var rngElement = this._rngDoc.getElement(tagName);		
		if (!rngElement) {
			//console.error('Element <' + tagName + '/> not found in RNG Schema');
			return null;
		}

		var ximElement = new XimElement(rngElement);
		ximElement.uid = domElement.getAttribute('uid');
		ximElement.attributes = this._parseAttributes(domElement);

		var childs = rngElement.childNodes;
		var count = childs.length;
		ximElement.value = [];

		/**
		 * TODO: This method should not use a RNGElement element to obtain a node values because it is supposed to import a DOMElement.
		 *		 It should obtaing the values from XimDocument.importHtmlElement() or a specific function of de XimElement.
		 *		 The method XimDocument._getNodeArrayValue() returns the values of the children, but it should returns also the value of the element passed as argument.
		 *		 The current solution is using a RngElement to obtain this value.
		 */

		/*if (count == 0) {
			// Sol A
			// If the item has no children is assumed that must have a default content
	    	ximElement.value = [rngElement.defaultContent];
	    	//ximElement.value = [domElement.nodeValue];
    	} else {
			// Sol B
			// The item has a default structure content defined in Rng Model
    		ximElement.value.push(rngElement.defaultContent);
    		ximElement.value = ximElement.value.concat(this._getNodeArrayValue(domElement));
    	}*/

    	ximElement.value = this._getNodeArrayValue(domElement);

		if (importChilds) {
			for (var i=0; i<count; i++) {
				var child = childs[i];
				child = this.getRngDocument().createDomElement(child.tagName.toLowerCase());
				child = this.importXmlElement(child, true);
				ximElement.appendChild(child);
			}
		}

		return ximElement;
	};

	/**
	 * Function which transforms a HTML DOMElement into a XimElement of the "rngTagName" type and returns it.
	 * If rngTagName is NULL try to obtain the tagName using the uid.
	 * @param {DOMNode} domElement HTML node to transform.
	 * @param {string} rngTagName RNG element in which the DOMNode will be transformed.
	 * @returns {XimElement}
	 */
	this.importHtmlElement = function(domElement, rngTagName) {

		if (!rngTagName) {
			var uid = domElement.getAttribute('uid');
			var elem = this.getElement(uid);
			if (elem) {
				rngTagName = elem.tagName;
			} else {
				return null;
			}
		}

		rngTagName = rngTagName.toLowerCase();
		var rngElement = this._rngDoc.getElement(rngTagName);
		var ximElement = new XimElement(rngElement);

		ximElement.uid = domElement.getAttribute('uid');
		if (rngElement.tagName == 'ximlet') {
			ximElement.value = (domElement.ximElement.value.length > 0) ? [domElement.ximElement.value[0].trim()] : [''];
			ximElement.macro = domElement.ximElement.macro;
		} else {
			ximElement.value = this._getNodeArrayValue(domElement);
		}

		ximElement.attributes = {};
		var count = domElement.attributes.length;

		for (var i=0; i<count; i++) {

			var attribute = domElement.attributes[i];

			if (rngElement.htmlAttributes[attribute.nodeName]) {

				/*var xmlName = rngElement.htmlAttributes[attribute.nodeName].xmlName;

				var newAttribute = {
					htmlName: attribute.nodeName,
					value: attribute.nodeValue
				};

				ximElement.attributes[xmlName] = newAttribute;*/

				//console.warn('TODO: �Se esta usando aun htmlAttributes?');
			}
		}

		return ximElement;
	};

	/**
	 * Function which inserts a new node in the ximModel structure.
	 * @param {XimElement} ximElement The element to insert.
	 * @param {XimElement} parent The parent node or NULL if ximElement is the root element.
	 * @param {XimElement} brother Optional, the node in which ximElement will be inserted before or after.
	 * @param {int} position Optional, The operation to perform: insert last, before or after the brother node.
	 * @returns {XimElement}
	 */
	this.appendChild = function(ximElement, parent, brother, position) {

		if (!ximElement) {
			//console.error('XimDocument::appendChild needs a ximElement object!.');
			return null;
		}

		if (!position) position = this.POSITION_LAST;
		if (typeof(parent) != 'object') parent = this.getElement(parent);

		if (!ximElement.uid) {
			// It is a new element
			this.setElementUID(ximElement, parent);
		} else {
			// Updating the last used UID
			var uidParts = ximElement.uid.split('.');
			var auxNodeID = parseInt(uidParts[0]);
			var auxID = parseInt(uidParts[1]);
			if (typeof(this._lastUID[auxNodeID]) == "undefined" || this._lastUID[auxNodeID] === null ||
				auxID > this._lastUID[auxNodeID])
				this._lastUID[auxNodeID] = auxID;
		}

		var uid = ximElement.uid;
		if (!this._ximModel[uid]) {

			if (parent) {
				// Root node allready exists...
				switch (position) {
					case this.POSITION_LAST:
						parent.appendChild(ximElement);
						break;
					case this.POSITION_BEFORE:
						parent.insertBefore(ximElement, brother);
						break;
					case this.POSITION_AFTER:
						parent.insertAfter(ximElement, brother);
						break;
				}
			}

			//this._ximModel[uid] = ximElement;
			this._updateModelWithElement(ximElement);

		} else {
			ximElement = this._ximModel[uid];
			//console.warn('Ya existe el uid = ' + uid, this._ximModel[uid]);
		}

		return ximElement;
	};

	/**
	 * Function which recursively updates the UIDs and elements registered in the model.
	 * @private
	 * @param {XimElement} ximElement Element to update.
	 */
	this._updateModelWithElement = function(ximElement) {
		this._ximModel[ximElement.uid] = ximElement;
		var count = ximElement.childNodes.length;
		for (var i=0; i<count; i++) {
			// Being sure of adding all the UIDs of the childrens to the model
			this._updateModelWithElement(ximElement.childNodes[i]);
		}
	};

	/**
	 * Function which inserts a new node in the structure before 'brother' object.
	 * @param {XimElement} ximElement The element to insert.
	 * @param {XimElement} parent The parent node or NULL if ximElement is the root element.
	 * @param {XimElement} brother Optional, the node in which ximElement will be inserted before.
	 * @returns {XimElement}
	 */
	this.insertBefore = function(ximElement, parent, brother) {
		var ximNode = this.appendChild(ximElement, parent, brother, this.POSITION_BEFORE);
		return ximNode;
	};

	/**
	 * Function which inserts a new node in the structure after 'brother' object.
	 * @param {XimElement} ximElement The element to insert.
	 * @param {XimElement} parent The parent node or NULL if ximElement is the root element.
	 * @param {XimElement} brother Optional, the node in which ximElement will be inserted after.
	 * @returns {XimElement}
	 */
	this.insertAfter = function(ximElement, parent, brother) {
		var ximNode = this.appendChild(ximElement, parent, brother, this.POSITION_AFTER);
		return ximNode;
	};

	/**
	 * Function which updates the content and the attribute values of the XimElement especified by the uid param
	 * @param {int} uid UID of the element to be updated.
	 * @param {XimElement} newElement Element with the new content.
	 * @returns {XimElement} A reference to the updated node.
	 */
	this.updateElement = function(uid, newElement) {

		var ximElement = this.getElement(uid);
		if (!ximElement) return null;

		ximElement.value = newElement.value;
		for (attrName in newElement.attributes) {
			if (attrName != 'uid') {
				ximElement.attributes[attrName] = newElement.attributes[attrName];
			}
		}

		this._ximModel[uid] = ximElement;
		return this.getElement(uid);
	};

	/**
	 * Function which clones the specified ximElement and appends it to a parent
	 * @param {XimElement} ximElement Element to clone.
	 * @param {XimElement} ximParent Parent of the cloned element.
	 * @returns {XimElement} A reference to the cloned node.
	 */
	this.cloneElement = function(ximElement, ximParent) {

		//ximParent = ximParent || this.getRootElement();

   		var rngElement = this.getRngDocument().getElement(ximElement.tagName);
    	var clonedElement = new XimElement(rngElement);

    	if (ximParent) {
    		clonedElement = this.appendChild(clonedElement, ximParent);
			clonedElement = this.updateElement(clonedElement.uid, ximElement);
		} else {
			clonedElement.value = ximElement.value;
			for (attrName in ximElement.attributes) {
				if (attrName != 'uid') {
					clonedElement.attributes[attrName] = ximElement.attributes[attrName];
				}
			}
			clonedElement.uid = null;
		}

		// Cloning children recursivelly
		var l = ximElement.childNodes.length;
		for (var i=0; i<l; i++) {
			this.cloneElement(ximElement.childNodes[i], clonedElement);
		}

		return clonedElement;
	};

	/**
	 * Function which removes a node and return a instance of the removed node if successfull.
	 * @param {XimElement} ximElement Element to be removed.
	 * @returns {XimElement} The removed node.
	 */
	this.removeChild = function(ximElement) {
		ximElement.parentNode.removeChild(ximElement);
		delete this._ximModel[ximElement.uid];
		return ximElement;
	};

	/**
	 * Function which validates the XML with the associated schema.
	 * This work is performed in the server for now.
	 *
	 *
	 * The callback function receives two parameters:
	 *  {boolean} valid -> Indicates if validation was successfull.
	 *  {string} msg -> Message with errors if valid = false.
	 *
	 * @param {function} callback Callback function that will be called after the server validation.
	 */
    this.validateXML = function(callback) {

		if (!this._schemaValidatorIsActive) {
			callback(true, '');
			return;
		}

    	var content = this.saveXML({
			asString: true,
			hideXimlets: true
    	});

		var encodedContent = "content=" + encodeURIComponent(content);

		// XML validation on the server
		com.ximdex.ximdex.editors.ValidateHandler(kupu.getBaseURL(), encodedContent, {
			onComplete: function(req, json) {
				var msg = null;
				if (!json.valid) {
					msg = _("Document cannot be validated!");

					if (json.errors) {
						msg = msg+"\n\n"
						var l = json.errors.length;
						for (var i=0; i<l; i++) {
							msg += '-\n\n' + json.errors[i] + '\n';
						}
					}
				}
				if (typeof(callback) == 'function') callback(json.valid, msg);
			},
			onError: function(req) {
				if (typeof(callback) == 'function') callback(false, _("Document cannot be validated!"));
			}
		});
	};

	/**
	 * Function which exports a XimDocument object into a DOMDocument object or, optionally, to a XML string.
	 * @param {boolean} asString Exports to a XML string instead a DOMDocument.
	 * @returns {DOMDocument | string}
	 */
	this.saveXML = function(options) {

		options = Object.extend({
			asString: false,
			hideXimlets: false,
			resolveXimlinks: false,
			onCreateNode: null
		}, options);

		this._exportDoc = null;
		this._exportDoc = this.editor.createDomDocument();
		if (!this._exportDoc) return null;

		// getting the docxap element...
		var docxap = this.getRootElement();
		if (!docxap) return null;

		docxap = this._reverseParseNode(docxap, null, options);
		this._exportDoc.appendChild(docxap);

		var ret = this._exportDoc;
		if (options.asString) ret = new XMLSerializer().serializeToString(ret);

		return ret;
	};

	/**
	 * Called by {@link XimDocument#saveXML}.
	 * Function which exports a XimElement into a DOMNode.
	 * @private
	 * @param {XimElement} element
	 * @param {DOMNode} parent
	 */
	this._reverseParseNode = function(element, parent, options) {

		// NOTE: This way of exporting the document is the most correct but causes that the tagNames were in capitals �?�?�?
		//
		// Fixed: http://www.w3.org/TR/DOM-Level-2-Core/core.html#Namespaces-Considerations
		// "... For HTML, the tagName parameter may be provided in any case, but it must be mapped to the canonical uppercase form by the DOM (Level 1) implementation ..."
		//
		// Methods createElementNS() and createAttributeNS() "... are meant to be used by namespace aware applications ..."


		options = Object.extend({
			hideXimlets: false,
			resolveXimlinks: false,
			onCreateNode: null
		}, options);

		var node = element.toDomElement(this._exportDoc);
		var count = element.childNodes.length;

		if (options.resolveXimlinks && element.ximLink) {

			node.setAttribute('__ximlink_idnode__', element.ximLink.nodeid);
			node.setAttribute('__ximlink_idchannel__', element.ximLink.channel);
			node.setAttribute('__ximlink_name__', element.ximLink.name);
			node.setAttribute('__ximlink_url__', element.ximLink.url);
			node.setAttribute('__ximlink_text__', element.ximLink.text);
			node.setAttribute('__ximlink_folder__', element.ximLink.folder);

//			console.log(node);
		}

		for (var i=0; i<count; i++) {
			var child = element.childNodes[i];
			if(!options.hideXimlets || !child.isSectionXimlet())
				child = this._reverseParseNode(child, node, options);
		}

		if (parent) {

			// Mapping the ximElement "value" attribute with the "childNodes" array and obtaining the real content.
			var count = parent.childNodes.length;
			var nodeTypeAcum = 0;
			var nextBrother = null;
			for (var i=(count-1); i>=0; i--) {
				if(nodeTypeAcum != parent.childNodes[i].nodeType) {
					nodeTypeAcum = parent.childNodes[i].nodeType;
				} else if(nodeTypeAcum == 3) {
					nextBrother = parent.childNodes[i+1];
				}
			}

			if(!parent.attributes['ximlet_id'] || !options.hideXimlets) {
				parent.insertBefore(node, nextBrother);
			} else if (nextBrother && nextBrother.nodeType == 3) {
				parent.insertBefore(node, nextBrother);
			}
		}

		if (Object.isFunction(options.onCreateNode)) {
			node = options.onCreateNode(node, element, options);
		}
		return node;
	};

	/**
	 * @deprecated Called by {@link XimDocument#_reverseParseNode}.
	 * @private
	 */
	this._reverseParseAttributes = function(node, element) {

		var rngElement = this.getRngModel()[element.tagName];
		var attributes = element.attributes;

		for (var attrName in attributes) {
			// NOTE:
			//		MSIE attributes returns ALL the object attributes, user defined and language defined attributes.
			//		FF attributes only returns user defined attributes.
			//		We need to double check if the attribute exists as an "user attribute".
			//		The <docxap/> tag is a special case.
			if (rngElement.attributes[attrName] || rngElement.tagName == 'docxap') {
				var attrValue = attributes[attrName];
				try {
					node.setAttribute(attrName, attrValue);
				} catch(e) {
					//console.error('%s.setAttribute(%s, %s)', element.tagName, attrName, attrValue);
				}
			} else {
				//console.info('Attribute not defined in RNG schema: %s.%s = %s', rngElement.tagName, attrName, attributes[attrName].value);
			}
		}

		return node;
	};

	/**
	 * Function which scrolls up the element in the hierachy.
	 */
	this.scrollUp = function() {
		// implement me
	},

	/**
	 * Function which scrolls down the element in the hierachy.
	 */
	this.scrollDown = function() {
		// implement me
	}

}
