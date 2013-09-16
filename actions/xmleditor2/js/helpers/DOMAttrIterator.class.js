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
 *  A Iterator object for DOM attributes
 *  IE shows user and language defined attributes
 *  If attribute is an user defined one, then specified = true
 *  For FF "attributes" is an array, indexed by numbers
 *  For IE "attributes" is an associative array, indexed by attribute names
 */
DOMAttrIterator = function(node) {

	this.node = null;
	this.childs = null;
	this.currentNode = null;

	this._initialize = function(node) {
		if (!node || !node.attributes) return;
		this.node = node;
		this.childs = this.node.attributes;
		this.currentNode = -1;
	};

	this.hasNext = function() {
		if (!this.node || !this.node.attributes) return false;
		var i = this.currentNode + 1;
		var node = this.childs[i];
		while (node && !node.specified) {
			node = this.childs[++i];
		}
		var ret = node ? true : false;
		return ret;
	};

	this.next = function() {
		if (!this.node || !this.node.attributes) return null;
		var node = null;
		while (this.hasNext() && node == null) {
			node = this.childs[++this.currentNode];
			if (!node.specified) node = null;
		}
		return node;
	};

	this.current = function() {
		if (!this.node || !this.node.attributes) return null;
		var node = this.childs[this.currentNode] ? this.childs[this.currentNode] : null;
		return node;
	};

	this.reset = function() {
		this.currentNode = -1;
	};

	this._initialize(node);

}