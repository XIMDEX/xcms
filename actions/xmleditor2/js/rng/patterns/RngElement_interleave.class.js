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




RngElement_interleave = function(tagName, parentElement) {

	this.PATTERN = 'interleave';

	this.allowedSiblings = function(selNode) {
		var rngParent = selNode.parentElement.schemaNode;
		var siblings = rngParent.allowedChildrens(selNode.parentElement);
		//console.log(siblings);
		return siblings;
	};

	this.allowedChildrens = function(selNode) {
		var childrens = [];
		var rngChilds = this.childNodes;
		var ximChilds = selNode.childNodes.asArray();

		for (var i=0; i<rngChilds.length; i++) {
			var child = rngChilds[i];

			if (child.PATTERN == 'element') {

				if (!ximChilds.contains(child.tagName)) {
					//console.error(child.tagName, selNode.parentElement.childNodes.asArray());
					if (child.isAllowedUnder(selNode)) childrens.push(child.tagName);
				}

			} else {
				childrens = childrens.concat(child.allowedChildrens(selNode));
			}
		}

		//console.log(childrens);
		return childrens;
	};

	this._initialize(tagName, parentElement);

}

RngElement_interleave.prototype = new RngElement();