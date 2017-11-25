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



RngElement_choice = function(tagName, parentElement) {

	this.PATTERN = 'choice';

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

		// Need to check only elements that are childrens of this choice pattern
		var aux = [];
		var childsArray = rngChilds.asArray();
		for (var i=0; i<ximChilds.length; i++) {
			if (childsArray.contains(ximChilds[i])) {
				aux.push(ximChilds[i]);
			}
		}
		ximChilds = aux;
		var ximChildsLength = ximChilds.length;

		for (var i=0; i<rngChilds.length; i++) {
			var child = rngChilds[i];
			if (child.PATTERN == 'element') {

				// If an element already exists returns becouse the parent doesn't allow more childrens
				if (ximChilds.contains(child.tagName)) {
					return [];
				}
				if (child.isAllowedUnder(selNode)) childrens.push(child.tagName);

			} else {

				var patternChildrens = child.allowedChildrens(selNode);

				// If parent node doesn't have childrens append the pattern elements and continue the process.
				// If parent node have some childrens returns the patterns allowed childrens.
				if (ximChildsLength == 0) {
					childrens = childrens.concat(patternChildrens);
				} else {
					return patternChildrens;
				}
			}
		}

		//console.log(childrens);
		return childrens;
	};

	this._initialize(tagName, parentElement);

}

RngElement_choice.prototype = new RngElement();