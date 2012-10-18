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

	X.searchpanel.AdvancedSearch = Object.xo_create({

		options: null,
		lastQuery: null,
		cache: null,
		masterFilter: null,

		_init: function(options) {

			this.options = $.extend({
				widget: null,
				datastore: null,
				queryHandler: 'SQL',
				inputFormat: 'json',
				outputFormat: 'json',
				filters: null,
				cache: null,
				masterFilter: []
			}, options);

			this.setMasterFilter(this.options.masterFilter);

			this.cache = {
				query: null,
				results: null
			};
		},

		setMasterFilter: function(filter) {
			this.options.masterFilter = filter;
			$.each(this.options.masterFilter, function(index, item) {
				item._isMaster = true;
			});
		},

		getLastQuery: function() {
			return this.lastQuery;
		},

		getCurrentQuery: function(options) {

			var rules = this.createRules();

			options = options || {};
			options = $.extend({
				parentid: 10000,
				depth: 0,
				items: 50,
				page: 1,
				condition: rules.condition,
				filters: rules.filters,
				sorts: rules.sorts
			}, options);

			var query = new QProcessor(options);
			return query;
		},

		createRules: function() {

			var aux = [];
			$.each(this.options.masterFilter, function(index, item) {
				aux.push(item);
			});

			var rules = {
				condition: $('input:radio[name=sopt]:checked', this.options.widget).val(),
				filters: aux,
				sorts: []
				//sorts: [{field: 'Name', order: 'asc'}]
			};

			$('.xim-search-filters .xim-search-filter', this.options.widget).each(function(id, elem) {

				var filter = {
					field: null,
					comparation: null,
					content: null,
					from: null,
					to: null
				};

				filter.field = $('.xim-filter-field', elem).val();
				filter.comparation = $('.xim-filter-active-comparation', elem).val();
				var content = $('.xim-filter-active-content', elem);

				if (content.length > 1) {

					function filterValue(elements, prefix) {
						return $.grep(elements, function(elem, index) {
							if ($(elem).hasClass('xim-filter-date-' + prefix)) {
								return true;
							}
						});
					}

					filter.content = $(filterValue(content, 'content')).val();
					filter.from = filter.content;
					filter.to = $(filterValue(content, 'to-content')).val();

				} else {
					filter.content = content.val();
					filter.from = filter.content;
				}

				rules.filters.push(Object.clone(filter));
			});

			return rules;
		},

		_showLoading: function() {

			if ($('.xim-listview-loading').length > 0) return;

			var container = $('<div></div>')
				.addClass('xim-listview-loading');
			var loading = $('<img></img>')
				.attr('src', X.baseUrl + '/actions/browser3/resources/images/loading.gif');
			$('.results-view').fadeTo(500, 0.3);
			$('#loading').append(container.append(loading));
		},
		_hideLoading: function() {
			$('.results-view').fadeTo(500, 1);
			if ($('.xim-listview-loading').length == 0) return;
			$('.xim-listview-loading').remove();
		},

		search: function(options, cb, save) {

			this._showLoading();
			if (this.options.datastore.options.ds.running) return;
			if (!Object.isFunction(cb)) cb = function(results, errors) {};

			var widget = this;

			this.lastQuery = this.getCurrentQuery(options);
			var query = this.lastQuery.getFullQuery(
				this.options.queryHandler,
				this.options.inputFormat,
				this.options.outputFormat,
				this.options.filters
			);
			/*console.info(query);
			console.info(this.lastQuery.getQuery('DOM'));
			console.info(this.lastQuery.getQuery('XML'));
			console.info(this.lastQuery.getQuery('JSON'));*/

			this.execQuery(query, cb, save);
		},

		execQuery: function(query, cb, save) {

			if (query === null) {
				cb(null, [_('No filter was indicated')]);
				return;
			}

			if (this.cache.query == query) {
				cb(this.cache.results, []);
				return;
			}

			this.options.datastore.datasource().type(this.options.outputFormat.toLowerCase());
			this.options.datastore.load_data({
					params: query,
					options: this.options
				},
				function(store) {
					var results = store.get_model();
					if (this.options.cache === true) {
						this.cache.query = query;
						this.cache.results = results;
					}
					cb(store.source, results, [], save);
					this._hideLoading();
				}.bind(this)
			);
		}
	});

})(X);
