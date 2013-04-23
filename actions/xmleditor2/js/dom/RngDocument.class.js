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
 *  @version $Revision: 8300 $
 */


/**
 *  Function which loads a RelaxNG schema into a javascript object.
 *	NOTE: XPath implementation: http://js-xpath.sourceforge.net/
 * @constructor
 */
RngDocument = function() {

	this.XMLNS_RNG = 'http://relaxng.org/ns/structure/1.0';
	this.VALID_XIM_ATTRS = ['type'];

	this._xmldoc = null;
	this._schemaModel = null;
	this._parsedRefs = [];

	/**
	 * Function which returns the RNG model object.
	 * @returns {object}
	 */
	this.getModel = function() {
		return this._schemaModel;
	};

	/**
	 * Function which returns the instance of the parsed RNG document.
	 * @returns {DOMDocument}
	 */
	this.getXmlDocument = function() {
		return this._xmldoc;
	};

	/**
	 * Function which starts to parse a new RNG document.
	 * @param {DOMDocument} xmldoc XML document
	 */
	this.loadXML = function(xmldoc) {

		// We need to map the default namespace to a prefix for XPath queries to work.
		// Use this method only if the document has a default namespace without a prefix.
		Sarissa.setXpathNamespaces(xmldoc, 'xmlns:rng="%s" xmlns:xim="%s"'.printf(this.XMLNS_RNG, X.XMLNS_XIM));

		// There is a problem using jQuery selectors over a XML DOM document on IE,
		// the solution is using the Sarissa XPath implementation.
		//
		// NOTE: The following two lines are needed for IE.
		// NOTE: The xmlns:xim namespace value MUST BE equal to what is defined in the RNG document.
		// Use setProperty() if you are not using setXpathNamespaces().
		xmldoc.setProperty('SelectionLanguage', 'XPath');
//		xmldoc.setProperty('SelectionNamespaces', 'xmlns:rng="%s" xmlns:xim="%s"'.printf(this.XMLNS_RNG, X.XMLNS_XIM));

		this._xmldoc = xmldoc.firstChild;
		this._schemaModel = {};

		var start = this._findStartElement(this._xmldoc);
		if (start !== null) this._parse_element(start, null,false);
	};

	/**
	 * Function wich finds the first element (or root node) in the document.
	 * @private
	 * @param {DOMNode} node
	 * @returns {DOMNode}
	 */
	this._findStartElement = function(node) {

		var xpath = '//rng:start/rng:element[@name="docxap"]';
		var docxap = this._xmldoc.selectNodes(xpath);
		docxap = docxap[0] || null;
		return docxap;
	};

	/**
	 * Recursive method that will parse a XML node.
	 * @private
	 * @param {DOMNode} node Node to parse
	 * @param {RngElement} parent Parent element on the RNG model
	 * @param {boolean} isOptional
	 */
	this._parseNode = function(node, parent, isOptional) {

		if (!node) return;

		var it = new DOMNodeIterator(node, 1);
		while (it.hasNext()) {
			var child = it.next();
			// Replace the colons of namespaces
			var method = '_parse_' + child.tagName.replace(':', '_');
			if (this[method]) {
				this[method](child, parent, isOptional);
				this._parseNodeXimAttributes(child, parent, isOptional);
			} else {
				console.info("method '" + method + "' don't exists!");
			}
		}

		if (!['define', 'oneOrMore', 'zeroOrMore', 'optional'].contains(node.tagName)) this._parseNode(node.nextSibling, parent);
	};

	/**
	 * Function which parses attributes in the Ximdex namespace
	 * TODO: Improving this algorithm, now it is only processing successfully
	 * the tag attribute <attribute/>. It should process any tag attribute
	 * in order to give semantic to the RNG schema.
	 *
	 * @private
	 * @param {DOMNode} node Node to parse
	 * @param {RngElement} parent Parent element on the RNG model
	 */
	this._parseNodeXimAttributes = function(node, parent) {

		for (var i=0,l=this.VALID_XIM_ATTRS.length; i<l; i++) {

			var attrName = this.VALID_XIM_ATTRS[i];
			var attr = node.attributes.getNamedItemNS(X.XMLNS_XIM, attrName);

			if (attr !== null) {
				var name = node.getAttribute('name');
				// Only if "name" is an attribute of an element.
				if (parent.attributes[name]) {
					parent.attributes[name][attrName] = attr.value;
				}
//				console.log(node.getAttribute('name'), attr, node, parent);
			}
		}
	};

	// Parsers

	/**
	 * Function which parses a specific RNG tag
	 * @private
	 * @param {DOMNode} node Node to parse
	 * @param {RngElement} parent Parent element on the RNG model
	 */
	this._parse_oneOrMore = function(node, parent, isOptional) {
		this._parseNode(node, parent, isOptional);
	};

	/**
	 * Function which parses a specific RNG tag
	 * @private
	 * @param {DOMNode} node Node to parse
	 * @param {RngElement} parent Parent element on the RNG model
	 */
	this._parse_zeroOrMore = function(node, parent, isOptional) {
		this._parseNode(node, parent, true);
	};

	this._parse_optional = function(node, parent, isOptional) {
		this._parseNode(node, parent, true);
	};

	this._parse_interleave = function(node, parent, isOptional) {
		this._parseNode(node, parent, isOptional);
	};

	this._parse_data = function(node, parent, isOptional) {
		this._parseNode(node, parent, isOptional);
	};

	/**
	 * Function which parses a specific RNG tag
	 * @private
	 * @param {DOMNode} node Node to parse
	 * @param {RngElement} parent Parent element on the RNG model
	 */
	this._parse_ref = function(node, parent, isOptional) {

		var defineName = node.getAttribute('name');

		if(!this._parsedRefs[parent.tagName])
			this._parsedRefs[parent.tagName] = [];
		if(this._parsedRefs[parent.tagName].contains(defineName))
			return null;
		this._parsedRefs[parent.tagName].push(defineName);

		var xpath = '/rng:grammar/rng:define[@name="%s"]'.printf(defineName);
		var define = this._xmldoc.selectNodes(xpath);
		define = define[0] || null;
//		console.log(xpath, define);

		if (define)
			this._parseNode(define, parent, isOptional);

	};

	/**
	 * Function which parses a specific RNG tag
	 * @private
	 * @param {DOMNode} node Node to parse
	 * @param {RngElement} parent Parent element on the RNG model
	 */
	this._parse_element = function(node, parent, isOptional) {

		if (node.nodeType != 1 || !node.getAttribute('name')) return;

		var name = node.getAttribute('name').toLowerCase().replace(":", "_");

		if (!this._schemaModel[name]) {

			//NOTE: It is controled if the element already exists to not being added to the father childNodes array more than once.
			//NOTE: With this correction, the "Named Patterns" would not be reused between different elements.
			//TODO: Locating the moment in which an element <ref/> nested to an element <define/> is called several times.
			//TODO: Fixing the previous point, this problem will be solved.
			this._schemaModel[name] = new RngElement(name, isOptional);
		}
		/*else{
			this._schemaModel[name].isOptional == this._schemaModel[name].isOptional && isOptional; 
		}*/ 

		if (parent != null) {
			this._schemaModel[name].parentNode = parent;
			this._schemaModel[name].parents.push(parent.tagName);
			parent.childNodes.push(this._schemaModel[name]);
		}

		this._parseNode(node, this._schemaModel[name], false);
	};

	/**
	 * Function which parses a specific RNG tag
	 * @private
	 * @param {DOMNode} node Node to parse
	 * @param {RngElement} parent Parent element on the RNG model
	 */
	this._parse_attribute = function(node, parent) {
		var name = node.getAttribute('name');
		parent.attributes[name] = {};
		parent.attributes[name]['values'] = {};
		parent.attributes[name]['type'] = null;
		this._parse_attribute_values(node, parent);
//		this._parse_xim_attributetype(node, parent);
	};

	/**
	 * Called by {@link RngDocument#_parse_attribute}
	 * @private
	 * @param {DOMNode} node Node to parse
	 * @param {RngElement} parent Parent element on the RNG model
	 */
	this._parse_attribute_values = function(node, parent) {

		var name = node.getAttribute('name');
		var values = node.selectNodes('./rng:choice/rng:value/text()');
		parent.attributes[name]['values'] = $(values).map(function(index, node) {
			return node.nodeValue;
		});
//		console.log(parent.attributes[name]['values']);
	}

	/**
	 * function which parses a specific RNG tag
	 * @private
	 * @param {DOMNode} node Node to parse
	 * @param {RngElement} parent Parent element on the RNG model
	 */
	this._parse_choice = function(node, parent, isOptional) {
		this._parseNode(node, parent, isOptional);
	}

	/**
	 * Function which parses a specific RNG tag
	 * @private
	 * @param {DOMNode} node Node to parse
	 * @param {RngElement} parent Parent element on the RNG model
	 */
	this._parse_interleave = function(node, parent, isOptional) {
		this._parseNode(node, parent, isOptional);
	}

	/**
	 * Function which parses a specific RNG tag
	 * @private
	 * @param {DOMNode} node Node to parse
	 * @param {RngElement} parent Parent element on the RNG model
	 */
	this._parse_text = function(node, parent, isOptional) {
		// nothing...
	};

	/**
	 * Function which parses a specific RNG tag
	 * @private
	 * @param {DOMNode} node Node to parse
	 * @param {RngElement} parent Parent element on the RNG model
	 */
//	this._parse_xim_attribute_relations = function(node, parent) {
//		// NOTE: ximdex namespace
//		this._parseNode(node, parent);
//	}

	/**
	 * Function which parses a specific RNG tag
	 * @private
	 * @param {DOMNode} node Node to parse
	 * @param {RngElement} parent Parent element on the RNG model
	 */
	this._parse_xim_attribute = function(node, parent, isOptional) {
		// NOTE: ximdex namespace
		var ptdName = node.getAttribute('name');
		var htmlName = node.getAttribute('value');
		if (parent.attributes[ptdName]) {
			parent.attributes[ptdName]['htmlName'] = htmlName;

			// NOTE: htmlAttributes array is not usefull at the moment!!!
			//parent.htmlAttributes[htmlName] = {xmlName: ptdName};
		}
	}

	/**
	 * Function which parses a specific RNG tag
	 * @private
	 * @param {DOMNode} node Node to parse
	 * @param {RngElement} parent Parent element on the RNG model
	 */
	this._parse_xim_default_content = function(node, parent, isOptional) {
		// NOTE: ximdex namespace
		var l = node.childNodes.length;
		if(l > 0) {
			parent.defaultContent = node.childNodes[0].nodeValue;
		}
	}

	/**
	 * Function which parses a specific RNG tag
	 * @private
	 * @param {DOMNode} node Node to parse
	 * @param {RngElement} parent Parent element on the RNG model
	 */
//	this._parse_xim_attributetype = function(node, parent) {
//		var name = node.getAttribute('name');
//		var value = node.selectSingleNode('./xim:attributetype/text()');
//		parent.attributes[name]['type'] = value ? value.data : null;
//	}

	/**
	 * Function which parses a specific RNG tag
	 * @private
	 * @param {DOMNode} node Node to parse
	 * @param {RngElement} parent Parent element on the RNG model
	 */
	this._parse_xim_type = function(node, parent, isOptional) {
		// NOTE: ximdex namespace
		var l = node.childNodes.length;
		if(l > 0) {
			parent.type = node.childNodes[0].nodeValue.split('|');
		}
	}

	/**
	 * Function which parses a specific RNG tag
	 * @private
	 * @param {DOMNode} node Node to parse
	 * @param {RngElement} parent Parent element on the RNG model
	 */
	this._parse_xim_description = function(node, parent, isOptional) {
		// NOTE: ximdex namespace
		var l = node.childNodes.length;
		if(l > 0) {
			//console.log(node.childNodes[0].nodeValue, "-->", window.i18n_message_catalog.translate(node.childNodes[0].nodeValue)  );
		//	parent.description = window.i18n_message_catalog.acents(window.i18n_message_catalog.translate(node.childNodes[0].nodeValue) );
			parent.description = node.childNodes[0].nodeValue;
		}
	}

	/**
	 * Function which parses a specific RNG tag
	 * @private
	 * @param {DOMNode} node Node to parse
	 * @param {RngElement} parent Parent element on the RNG model
	 */
	this._parse_xim_wizard = function(node, parent) {
		// NOTE: ximdex namespace
		parent.wizard = node.firstChild.nodeValue.trim();
	}

	// ----- API -----

	/**
	 * Function which returns the RngElement asociated with a tagName
	 * @param {string} tagName
	 * @returns {RngElement}
	 */
	this.getElement = function(tagName) {
		tagName = tagName.replace(':', '_');

		return this._schemaModel[tagName] || null;
	};

	/**
	 * Function which returns a DOMElement represented by the RngElement.<br>
	 * This is a shortcut for {@link RngElement#toDomElement}.
	 * @param {string} tagName
	 * @returns {DOMNode}
	 */
	this.createDomElement = function(tagName) {
		var elem = this.getElement(tagName);
		return elem.toDomElement();
	};

	/**
	 * Function which validates a XML document with this RNG.<br>
	 * Not implemented.
	 */
	this.validate = function(node) {
		// Create RNGValidator.class.js ......
	};

	/**
	 * Function which returns an array of rngElements tagNames with a specified type.
	 * @param {string} type
	 * @returns {array string}
	 */
	this.getRngElementNameByType = function(type) {

		var xpath = '//xim:type[contains(., "%s")]/ancestor::rng:element[1]'.printf(type);
		var nodes = this._xmldoc.selectNodes(xpath);

		nodes = $(nodes).map(function(index, node) {
			return node.getAttribute('name');
		});

		return nodes;
	};

	/**
	 * Function which returns an array of rngElements tagNames than can be parents of 'elementName'.
	 * @param {string} elementName
	 * @returns {array string}
	 */
	this.getAllowedParents = function(elementName) {

		if(this._schemaModel[elementName])
			return this._schemaModel[elementName].getAllowedParents();
		else
			return [];
	};

}
