
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
 *  @version $Revision: 7858 $
 */


(function(X) {

	var B = X.browser;
	B.panels.leftTabs[4] = 'ModulesView';

	var idCount = 0;

	B.ModulesView = Object.xo_create(B.AbstractView, {

		_init: function(options) {


			B.ModulesView._construct(this, options);
			this.id = 'browser-modules-view-' + (++idCount);
			this.className = 'browser-modules-view';
			this.label = _("Modules");
			this._tabId = 4;
			this.content = $('<div></div>')
				.addClass('browser-modules-view-content')
				.attr({id: this.id});
				this.setViewTitle(_("Modules"));
			this.loadModulesList();
		},

		loadModulesList: function() {

			var container = $('<div></div>').addClass('browser-modules-view-list-container').appendTo(this.content);
			var url = '%s?action=%s&method=%s'.printf(X.restUrl, 'moduleslist', 'index');
			container.load(url);
		}
	});

})(com.ximdex);
