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




RngPattern = function(parentElement) {

	this.PATTERN = 'abstract';

	this.parentElement = null;
	this.childNodes = null;

	this._initialize = function(parentElement) {
		this.parentElement = parentElement;
		this.childNodes = [];
	};

	this.appendChild = function(rngElement) {
		if (!this.childNodes) this.childNodes = [];
		this.childNodes.push(rngElement);
	};

	this.validate = function() {
		alert('RngPattern.validate() - ' + _('Implement me!'));
	};

	this.isAllowed = function(ximParent, ximBrother) {
		alert('RngPattern.isAllowed() - ' + _('Implement me!'));
	};

	this.isAllowedUnder = function(ximParent) {
		alert('RngPattern.isAllowedUnder() - ' + _('Implement me!');
	};

	this.allowedNodes = function(selNode) {
		alert('RngPattern.allowedNodes() - ' + _('Implement me!');
	};

	this.allowedSiblings = function(selNode) {
		alert('RngPattern.allowedSiblings() - ' + _('Implement me!');
	};

	this.allowedChildrens = function(selNode) {
		alert('RngPattern.allowedChildrens() - ' + _('Implement me!');
	};

	this._initialize(parentElement);

}