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

//com.ximdex.widgets.include('browserwindow');

//com.ximdex.widgets.ready(function() {
$(document).ready(function() {
	var selectors = {
		spotlight: '.mini-spotlight',
		searchpanel: 'div.advanced-search'
	};


	Object.getWidgetConf({
		wn: 'spotlight',
		a: 'browser3',
		onComplete: function(data) {
			data = Object.extend(data, {
				showAdvanced: true,
				cbBeforeSearch: function(options) {
					//methods.beforeSearch(options);
				},
				cbSearch: function(event, params) {
                    angular.element(document).injector().get('xTabs').openAction( {
						command: 'searchpanel',
						method: 'showResults',
						name: _("Search results"),
						params: '',
						data: params.result
					}, [0]);
				}
			});

			$(selectors.spotlight)
				.miniSpotlight(data)
				.bind('searchpanel-open', function() {
					sp.searchpanel('open');
				});

		}.bind(this)
	});


	var sp = $('<div></div>').addClass('advanced-search').appendTo('body');
	var spOptions = {
		url_base: X.baseUrl,
		use_cache: false,
		masterFilter: [],
		showFilters: true,
		view: 'gridview',
		parentid: 10000,
		showSelectButton: false,
		selectButtonLabel: _('Select nodes')
	};
	sp.searchpanel(spOptions);

	/*$(selectors.browserWindow).bind('searchpanel-open', function() {
		sp.searchpanel('option', spOptions).searchpanel('open');
	});*/
	
	/* Control of expiration session and inactivity period*/
	var sessionLength = parseInt(X.session.get("sessionLength"));

	// Refresh session 5 seconds before the session expires. 30 minutes of inactivity
	var options = {'sessionLength': sessionLength, 'gapToRefresh': 5, 'inactivityLength': 30*60}; //30 minutes of inactivity
	
	var sessionTimer = new X.SessionTimer(options);
	
	
	
});
