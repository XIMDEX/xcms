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

	X.searchpanel.SavedSearches = Object.xo_create({
		
		container: null,
		ds: null,
		nfd: null,
		
		_init: function(options) {
		
			this.options = $.extend({
				container: null,
				template: '<li>%s</li>'
			}, options);
			
			this.nfd = new X.browser.NewFilterDialog({
				handler: 'SQL',
				owner: this,
				onOk: this.refreshContainer.bind(this)
			});
			
			this.container = options.container;
			this.ds = new DataStore({
				ds: new DataSource({
					method: 'get',
					type: 'json',
					url:  X.restUrl
				}),
				queryParams: function(params, options) {
					return {
						filterid: params.filterid,
						action: 'browser3',
						method: 'getFilter'
					};
				},
				selector: function(data) {
					return data.data;
				},
				colModel: options.datastore.options.colModel
			});
		},
		
		add: function(query) {
			this.nfd.open(query);
		},
		
		addToContainer: function(item) {
			if (Object.isEmpty(this.container)) return;
			var li = $(this.options.template.printf(item.name))
				.click(this.execFilter.bind(this, item.id));
			this.container.prepend(li);
		},
		
		refreshContainer: function() {
			if (Object.isEmpty(this.container)) return;
			$.get(X.restUrl, {action: 'browser3', method: 'listFilters'},
				function(data) {
					if (typeof(data) === 'string') data = $.secureEvalJSON(data);
					this.container.empty();
					for (var i=0, l=data.length; i<l; i++) {
						this.addToContainer(data[i]);
					}
				}.bind(this)
			);
		},
		
		execFilter: function(event, filterId) {
			if (filterId.type != undefined){
				filterId = event;			
			}
			this.ds.load_data({
					params: {filterid: filterId},
					options: this.options
				},
				function(store) {
					var results = store.get_model();
					var query = store.source.query;
					this.container.trigger('saved-searches-select', [store.source, results, query]);
				}.bind(this)
			);
		}
	});
	
})(X);
