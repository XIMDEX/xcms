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
 * Represents a RNG Element
 * @constructor
 * @param {string} tagName The name of the element we want to instantiate.
 */
RngElement = function(tagName, parentElement) {

	this.PATTERN = 'element';

	this.tagName = null;
	this.parentElement = null;
	this.parentIsPattern = null;
	this.childNodes = null;
	this.attributes = null;
	this.htmlAttributes = null;
	this.defaultContent = null;
	this.type = null;

	/**
	 * Initialize a new instance.
	 * @private
	 * @param {string} tagName The name of the element we want to instantiate.
	 */
	this._initialize = function(tagName, parentElement) {
		this.tagName = tagName; //.toLowerCase();
		this.childNodes = [];
		//this._initializeChildNodes();
		this.attributes = {};
		this.htmlAttributes = {};

		if (parentElement) {

			// Need to know if parent element is a pattern or an element
			this.parentIsPattern = (parentElement.PATTERN != 'element');

			// Adding this element to the parent's childNodes array...
			parentElement.appendChild(this);

			// ... but parentElement attribute must be an "element pattern"
			while (parentElement.PATTERN != 'element') {
				parentElement = parentElement.parentElement;
			}
		}

		this.parentElement = parentElement;
	};

	/**
	 * Returns an array of all elements of type 'element' that are chilrens of this element
	 * Note that this is not the same as the childNodes attribute.
	 * @return {array}
	 */
	this.getChildrens = function() {

		var ret = [];
		var childs = this.childNodes;

		for (var i=0; i<childs.length; i++) {
			var child = childs[i];
			if (child.PATTERN == 'element') {
				ret.push(child);
			} else {
				ret = ret.concat(child.getChildrens());
			}
		}

		return ret;
	};


	/**
	 * Returns an array of all elements of type 'element' that are siblings of this element
	 * @return {array}
	 */
	this.getSiblings = function() {
		return this.parentElement ? this.parentElement.getChildrens() : [this];
	};

	/**
	 * Exports this element to a DOMNode element.
	 * @returns {DOMNode}
	 */
	this.toDomElement = function(doc) {

		doc = doc || document;
		var domelement = doc.createElement(this.tagName.toLowerCase());
		var attributes = this.attributes;

		for (var attrName in attributes) {
			domelement.setAttribute(attrName, '');
		}

		return domelement;
	};

	this.isAllowed = function(ximParent, ximBrother) {

		var allowedChildrens = this.allowedChildrens(ximParent);
	};

	/**
	 * Function which checks if this element is allowed under the specified ximElement in two ways:<br>
	 * 1. Checks if the ximElement RNG schema allows this element type under itself.<br>
	 * 2. Checks, depending on the actual XML, if the RNG rules allows a new instance of this element.
	 * @param {XimElement} ximElement Parent of this node element.
	 * @returns {boolean}
	 */
	this.isAllowedUnder = function(ximElement) {

		var rngElement = ximElement.schemaNode;

		// Checking schema compatibility
		var rngChildrens = rngElement.getChildrens().asArray();
		if (!rngChildrens.contains(this.tagName)) return false;

		// Checking xml compatibility
		var ximChildrens = ximElement.childNodes.asArray();
		if (this.parentIsPattern) {
			// If this element is under a pattern, let the decision to this pattern.
			return false;
		} else if (ximChildrens.contains(this.tagName)) {
			// RNG defaults to allow only one element, if this element already exists new elements are not allowed
			return false;
		}

		// Let's say ok....
		return true;
	};

	/**
	 * Returns an array of allowed childrens and siblings of this element
	 * depending on the selected node on the XML.
	 * @param {XimElement} ximElement Selected node (Must be of same type as this element).
	 * @returns {Array}
	 */
	this.allowedNodes = function(selNode) {
		return {
			childrens: this.allowedChildrens(selNode),
			siblings: this.allowedSiblings(selNode)
		};
	};

	this.allowedSiblings = function(selNode) {
		// If this is the root element no siblings are allowed
		if (!selNode.parentElement) {
			return [];
		}
		var rngParent = selNode.parentElement.schemaNode;
		var siblings = rngParent.allowedChildrens(selNode.parentElement);
		//console.log(siblings);
		return siblings;
	};

	this.allowedChildrens = function(selNode) {
		var childrens = [];
		var rngChilds = this.childNodes;

		for (var i=0; i<rngChilds.length; i++) {
			var child = rngChilds[i];
			if (child.PATTERN == 'element') {
				// TODO: Check cardinallity
				if (child.isAllowedUnder(selNode)) childrens.push(child.tagName);
			} else {
				childrens = childrens.concat(child.allowedChildrens(selNode, null));
			}
		}
		return childrens;
	};

	this._initialize(tagName, parentElement);
};


RngElement.prototype = new RngPattern();