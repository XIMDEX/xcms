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



/**
 *  Loads a RelaxNG schema into a javascript object.<br>
 *	NOTE: XPath implementation: http://js-xpath.sourceforge.net/
 * @constructor
 */
RngDocument = function() {

	this._xmldoc = null;
	this._schemaModel = null;

	/**
	 * Returns the RNG model object.
	 * @returns {object}
	 */
	this.getModel = function() {
		return this._schemaModel;
	};

	/**
	 * Returns the instance of the parsed RNG document.
	 * @returns {DOMDocument}
	 */
	this.getXmlDocument = function() {
		return this._xmldoc;
	};

	/**
	 * Starts the parsing of a new RNG document.
	 * @param {DOMDocument} xmldoc XML document
	 */
	this.loadXML = function(xmldoc) {

		this._xmldoc = xmldoc.firstChild;
		this._schemaModel = {};

		var start = this._findStartElement(this._xmldoc);
		if (start) this._parse_element(start, null);
	};

	/**
	 * Finds the first element (or root node) in the document.
	 * @private
	 * @param {DOMNode} node
	 * @returns {DOMNode}
	 */
	this._findStartElement = function(node) {

		// NOTE: Start element is the element with the name=docxap attribute

		// jQuery...
		var docxap = $(this._xmldoc).find('start > element[name=docxap]');
		docxap = docxap[0] || null;
		return docxap;
	};

	/**
	 * Recursive method that will parse a XML node.
	 * @private
	 * @param {DOMNode} node Node to parse
	 * @param {RngElement} parent Parent element on the RNG model
	 */
	this._parseNode = function(node, parent) {

		if (!node) return;

		var it = new DOMNodeIterator(node, 1);
		while (it.hasNext()) {
			var child = it.next();
			// Replacing the colons of namespaces
			var method = '_parse_' + child.tagName.replace(':', '_');
			if (this[method]) {
				this[method](child, parent);
			} else {
				console.info(_("Method") + " '" + method + "' "+ _("does not exists!"));
			}
		}

		if (!['define', 'oneOrMore', 'zeroOrMore'].contains(node.tagName)) this._parseNode(node.nextSibling, parent);
	};

	// Parsers

	/**
	 * Parse a specific RNG tag
	 * @private
	 * @param {DOMNode} node Node to parse
	 * @param {RngElement} parent Parent element on the RNG model
	 */
	this._parse_oneOrMore = function(node, parent) {
		var element = new RngElement_oneOrMore(node.tagName, parent);
		this._parseNode(node, element);
	};

	/**
	 * Parse a specific RNG tag
	 * @private
	 * @param {DOMNode} node Node to parse
	 * @param {RngElement} parent Parent element on the RNG model
	 */
	this._parse_zeroOrMore = function(node, parent) {
		var element = new RngElement_zeroOrMore(node.tagName, parent);
		this._parseNode(node, element);
	};

	/**
	 * Parse a specific RNG tag
	 * @private
	 * @param {DOMNode} node Node to parse
	 * @param {RngElement} parent Parent element on the RNG model
	 */
	this._parse_ref = function(node, parent) {

		// TODO: Use jQuery?

		var defineName = node.getAttribute('name');
		var define = null;
		var it = new DOMNodeIterator(this._xmldoc, 1);
		while (it.hasNext() && define == null) {
			var child = it.next();
			if (child.tagName == 'define' && child.getAttribute('name') == defineName) {
				define = child;
			}
		}

		if (define) this._parseNode(define, parent);
	};

	/**
	 * Parse a specific RNG tag
	 * @private
	 * @param {DOMNode} node Node to parse
	 * @param {RngElement} parent Parent element on the RNG model
	 */
	this._parse_element = function(node, parent) {

		if (node.nodeType != 1) return;
		//console.log(node, node.getAttribute('name'), parent);

		var name = node.getAttribute('name');
		var parentName = parent ? parent.tagName : '__start__';

		var element = new RngElement(name, parent);
		this._appendElement(element, parent);
		this._parseNode(node, element);

		return;
	};

	/**
	 * Parse a specific RNG tag
	 * @private
	 * @param {DOMNode} node Node to parse
	 * @param {RngElement} parent Parent element on the RNG model
	 */
	this._parse_attribute = function(node, parent) {
		var name = node.getAttribute('name');
		parent.attributes[name] = {};
		parent.attributes[name]['values'] = {};
		this._parse_attribute_values(node, parent);
	};

	/**
	 * Called by {@link RngDocument#_parse_attribute}
	 * @private
	 * @param {DOMNode} node Node to parse
	 * @param {RngElement} parent Parent element on the RNG model
	 */
	this._parse_attribute_values = function(node, parent) {

		// TODO: Correct this method to _parse_value() (value is a rng pattern)
		// TODO: Use nodeIterator

		var name = node.getAttribute('name');
		var l = node.childNodes.length;

		for(var i = 0; i < l; i ++) {
			var choice = node.childNodes[i];
			if(choice.nodeName == 'choice') {
				var lc = choice.childNodes.length;
				for(var ic = 0; ic < lc; ic ++) {
					var child = choice.childNodes[ic];
					if (child.nodeType == Node.ELEMENT_NODE) {
						// TODO: Make an array instead?
						parent.attributes[name]['values'][ic] = child.childNodes[0].nodeValue.trim();
					}
				}
			}
		}
	}

	/**
	 * Parse a specific RNG tag
	 * @private
	 * @param {DOMNode} node Node to parse
	 * @param {RngElement} parent Parent element on the RNG model
	 */
	this._parse_choice = function(node, parent) {
		var element = new RngElement_choice(node.tagName, parent);
		this._parseNode(node, element);
	}

	/**
	 * Parse a specific RNG tag
	 * @private
	 * @param {DOMNode} node Node to parse
	 * @param {RngElement} parent Parent element on the RNG model
	 */
	this._parse_interleave = function(node, parent) {
		var element = new RngElement_interleave(node.tagName, parent);
		this._parseNode(node, element);
	}

	/**
	 * Parse a specific RNG tag
	 * @private
	 * @param {DOMNode} node Node to parse
	 * @param {RngElement} parent Parent element on the RNG model
	 */
	this._parse_optional = function(node, parent) {
		var element = new RngElement_optional(node.tagName, parent);
		this._parseNode(node, element);
	}

	/**
	 * Parse a specific RNG tag
	 * @private
	 * @param {DOMNode} node Node to parse
	 * @param {RngElement} parent Parent element on the RNG model
	 */
	this._parse_text = function(node, parent) {
		// nothing...
	};

	/**
	 * Parse a specific RNG tag
	 * @private
	 * @param {DOMNode} node Node to parse
	 * @param {RngElement} parent Parent element on the RNG model
	 */
	this._parse_xim_attribute_relations = function(node, parent) {
		// NOTE: ximdex namespace
		this._parseNode(node, parent);
	}

	/**
	 * Parse a specific RNG tag
	 * @private
	 * @param {DOMNode} node Node to parse
	 * @param {RngElement} parent Parent element on the RNG model
	 */
	this._parse_xim_attribute = function(node, parent) {
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
	 * Parse a specific RNG tag
	 * @private
	 * @param {DOMNode} node Node to parse
	 * @param {RngElement} parent Parent element on the RNG model
	 */
	this._parse_xim_default_content = function(node, parent) {
		// NOTE: ximdex namespace
		var l = node.childNodes.length;
		if(l > 0) {
			parent.defaultContent = node.childNodes[0].nodeValue;
		}
	}
	
	/**
	 * Parse a specific RNG tag
	 * @private
	 * @param {DOMNode} node Node to parse
	 * @param {RngElement} parent Parent element on the RNG model
	 */
	this._parse_xim_attributetype = function(node, parent) {
		// NOTE: ximdex namespace
		var l = node.childNodes.length;
		if(l > 0) {
			parent.attributeType = node.childNodes[0].nodeValue;
		}
	}

	/**
	 * Parse a specific RNG tag
	 * @private
	 * @param {DOMNode} node Node to parse
	 * @param {RngElement} parent Parent element on the RNG model
	 */
	this._parse_xim_type = function(node, parent) {
		// NOTE: ximdex namespace
		var l = node.childNodes.length;
		if(l > 0) {
			parent.type = node.childNodes[0].nodeValue;
		}
	}

	/**
	 * Parse a specific RNG tag
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
	 * Returns the RngElement asociated with a tagName
	 * @param {string} tagName
	 * @returns {RngElement}
	 */
	this.getElement = function(tagName, parentTagName) {
		return this._schemaModel[tagName][parentTagName] || null;
	};

	/**
	 * Appends an element to this RNG model under the specified parent
	 * @param {RngElement} rngElement
	 * @param {RngElement} rngParent
	 * @returns {RngElement}
	 */
	this._appendElement = function(rngElement, rngParent) {

		if (!rngParent) {
			this._schemaModel[rngElement.tagName] = rngElement;
			return;
		}

		// The parent element has to be an "element pattern"
		while (rngParent.PATTERN != 'element') {
			rngParent = rngParent.parentElement;
		}

		var childName = rngElement.tagName;
		var parentName = rngParent.tagName;

		if (!this._schemaModel[childName]) {
			this._schemaModel[childName] = {};
		}

		if (!this._schemaModel[childName][parentName]) {
			this._schemaModel[childName][parentName] = rngElement;
		}
	};

	/**
	 * Returns a DOMElement represented by the RngElement.
	 * This is a shortcut for {@link RngElement#toDomElement}.
	 * @param {string} tagName
	 * @returns {DOMNode}
	 */
	this.createDomElement = function(tagName) {
		var elem = this.getElement(tagName);
		return elem.toDomElement();
	};

	/**
	 * Validates a XML document with this RNG.
	 * Not implemented.
	 */
	this.validate = function(node) {
		// Create RNGValidator.class.js ......
	};

}