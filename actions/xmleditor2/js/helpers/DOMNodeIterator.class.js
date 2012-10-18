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
 *  @version $Revision: 7842 $
 */



/**
 *  A Iterator object for childrens of a DOMNode object
 *	nodeType is optional. Normally it will be Node.ELEMENT_NODE (1).
 *	If nodeType is especified the returned node must match the nodeType.
 */
DOMNodeIterator = function(node, nodeType) {

	this.node = null;
	this.nodeType = null;
	this.childs = null;
	this.currentNode = null;

	this._initialize = function(node, nodeType) {
		if (!node || !node.childNodes) return;
		this.node = node;
		this.nodeType = nodeType || false;
		this.childs = this.node.childNodes;
		this.currentNode = -1;
	};

	this.hasNext = function() {
		if (!this.node || !this.node.childNodes) return false;
		var i = this.currentNode + 1;
		var node = this.childs[i];
		while (node && this.nodeType && node.nodeType != this.nodeType) {
			node = this.childs[++i];
		}
		var ret = node ? true : false;
		return ret;
	};

	this.next = function() {
		if (!this.node || !this.node.childNodes) return null;
		var node = null;
		while (this.hasNext() && node == null) {
			node = this.childs[++this.currentNode];
			if (this.nodeType && node && node.nodeType != this.nodeType) node = null;
		}
		return node;
	};

	this.current = function() {
		if (!this.node || !this.node.childNodes) return null;
		var node = this.childs[this.currentNode] ? this.childs[this.currentNode] : null;
		return node;
	};

	this.reset = function() {
		this.currentNode = -1;
	};

	this._initialize(node, nodeType);

}