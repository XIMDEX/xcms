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
 * Function which represents a RNG Element
 * @constructor
 * @param {string} tagName The name of the element we want to instantiate.
 */
RngElement = function(tagName, isOptional) {

	this.tagName = null;
	this.parentNode = null;
	this.childNodes = [];
	this.attributes = {};
	this.htmlAttributes = {};
	this.defaultContent = null;
	this.type = [];
	this.description = null;
	this.parents = [];
	this.isOptional = true;

	/**
	 * Function which initializes a new instance.
	 * @private
	 * @param {string} tagName The name of the element we want to instantiate.
	 */

	this._initialize = function(tagName, isOptional) {
		this.tagName = tagName.toLowerCase();
		this.description = this.tagName;
		this.isOptional = isOptional;
	};

	/**
	 * Function which exports this element to a DOMNode element.
	 * @returns {DOMNode}
	 */
	this.toDomElement = function() {

		var domelement = document.createElement(this.tagName.toLowerCase());
		var attributes = this.attributes;

		for (var attrName in attributes) {
			domelement.setAttribute(attrName, '');
		}

		return domelement;
	};

	/**
	 * Function which checks if this element is allowed under the specified parent node and after the specified brother.
	 * @param {RngElement} rngParent Parent node.
	 * @param {RngElement} rngBrother Brother node.
	 * @returns {boolean}
	 */
	this.isAllowedUnder = function(rngParent, rngBrother) {
		// TODO: It's needed an implementation of <choice/>, cardinallity and position restrictions in RngDocument!
		// TODO: Implement checks for rngBrother element...
		return (this.parents.contains(rngParent.tagName) && !(rngParent.type.contains('ximlet'))) ? true : false;
	};

	/**
	 * Function which checks if this element is allowed nearby the specified node.
	 * @param {RngElement} rngNode Parent node.
	 * @returns {boolean}
	 */
	this.isAllowedNearBy = function(rngNode) {
		var allowed = false;
		this.parents.each(function(index, elem) {
			if(rngNode.parents.contains(elem))
				allowed = true;
		});
		return allowed;
	};

	/**
	 * Function which returns an array of rngElements tagNames than can be parents of this element.
	 * @returns {array string}
	 */
	this.getAllowedParents = function() {

		return this.parents;
	};

	this._initialize(tagName, isOptional);
}
