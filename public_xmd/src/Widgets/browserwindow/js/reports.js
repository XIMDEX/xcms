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
//	B.panels.leftTabs[3] = 'ReportsView';
	
	var idCount = 0;
	
	B.ReportsView = Object.xo_create(B.AbstractView, {
		
		_init: function(options) {
			B.ReportsView._construct(this, options);
			this.id = 'browser-reports-view-' + (++idCount);
			this.className = 'browser-reports-view';
			this.label = 'Informes';
			this._tabId = 3;
			this.content = $('<div></div>')
				.addClass('browser-reports-view-content')
				.attr({id: this.id});
			this.setViewTitle('Reports View');
			this.loadReportsList();
		},
		
		loadReportsList: function() {
			
			var container = $('<div></div>').addClass('browser-reports-view-list-container').appendTo(this.content);
			var url = '%s?action=%s&method=%s'.printf(X.restUrl, 'reportslist', 'index');
			container.load(url, this.registerEvents.bind(this));
		},
		
		registerEvents: function() {
			
			$('li.browser-reports-view-item', this.content).click(function(event) {
			
				var action = {};
				$(event.target).attr('href').substr(1).split('&').each(function(index, item) {
					var aux = item.split('=');
					action[aux[0]] = aux[1];
				});
				
//				console.log(action);

				this.browser.browserwindow('openAction', action, [-1]);
				
				return false;
				
			}.bind(this));
		}
		
	});
	
})(com.ximdex);
