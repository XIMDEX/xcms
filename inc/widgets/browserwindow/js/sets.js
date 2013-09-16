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


(function(X) {
 
	var B = X.browser;
//	B.panels.leftTabs[2] = 'SetsView';
	
	var idCount = 0;
	
	B.SetsView = Object.xo_create(B.AbstractView, {
		
		_init: function(options) {
			B.SetsView._construct(this, options);
			this.id = 'browser-sets-view-' + (++idCount);
			this.className = 'browser-sets-view';
			this.label = 'Sets';
			this._tabId = 2;
			this.content = $('<div></div>')
				.addClass('browser-sets-view-content')
				.attr({id: this.id});
			this.setViewTitle('Sets View');
		}
	});
	
})(com.ximdex);
