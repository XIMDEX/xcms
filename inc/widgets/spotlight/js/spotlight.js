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

	$.widget('ui.miniSpotlight', {

		searchedTerms: null,

		_init: function() {

			this.searchedTerms = X.session.get('spotlight.terms') || [];

			this.options.queryFormat = this.options.queryFormat.toUpperCase();
			this.options.queryHandler = this.options.queryHandler.toUpperCase();
			this.options.outputFormat = this.options.outputFormat.toUpperCase();
			this.options.datastore = new DataStore(this.options.datastore);

			this.input = $('input', this.element);
			$(this.input).autocomplete({
				// NOTE: Cannot change the source once it's asigned, so use a dynamic algorithm
				source: function(data, cb) {
					var r = new RegExp('^'+data.term);
					var filtered = [];
					for (var i=0,l=this.searchedTerms.length; i<l; i++) {
						if (r.test(this.searchedTerms[i])) {
							filtered.push(this.searchedTerms[i]);
						}
					}
					cb(filtered);
				}.bind(this)
			});
			
			$ul = $(this.input).data().autocomplete.menu.element; 
			var $spotlightInput = $(this.input);
			if ($.isFunction($().live)){
				$("li a",$ul).live("click", 
				function(){
					$spotlightInput.val($(this).text()); 
					$ul.hide();
				});
			}
			

			$(this.element).append(
				$('<div/>')
					.addClass('mini-spotlight-advanced-no icon')
					.html('Search')
			);

			if (this.options.showAdvanced) {
				$(this.element).append(
					$('<div/>')
					.addClass('mini-spotlight-advanced icon')
					.html('Advanced')
				);
			}

			this.registerTriggers();
			this.registerEvents();
		},

		registerTriggers: function() {

			if (this.options.showAdvanced) {
				$('.mini-spotlight-advanced', this.element).click(function(event) {
					$(this.element).trigger('searchpanel-open', [{event: event, widget: this.element}]);
				}.bind(this));
			}

			$('.mini-spotlight-advanced-no', this.element).click(function(event) {
				this.search($(this.input).val());
			}.bind(this));
		},

		registerEvents: function() {

			$(document).keypress(function(event) {
				if (event.metaKey && event.charCode == 32) {
					$(this.input).focus();
				}
			}.bind(this));

			$(this.input).keypress(function(event) {
				if (event.keyCode == 13) {
					this.search($(this.input).val());
					$(this.input).val('');
				}
			}.bind(this));
		},

		search: function(text) {
			var text=text.trim();
			if (this.options.datastore.options.ds.running) return;

			if (!this.searchedTerms.contains(text)) {
				this.searchedTerms.push(text);
				X.session.set('spotlight.terms', this.searchedTerms);
			}

			var rules = {
				parentid: 10000,
				depth: 0,
				items: 50,
				page: 1,
				condition: 'or',
				filters: [
					{content: text, comparation: 'contains', field: 'name'}, {
					comparation: 'equal',content: text, field: 'nodeid', from: '', to: ''}
				],
				sorts: [
					{order: 'asc', field: 'Name'}
				]
			};

			var qp = new QProcessor(rules);
			$(this.element).data('query', qp);
			var query = qp.getQuery(this.options.queryFormat);
			/*console.info(qp.getQuery('DOM'));
			console.info(qp.getQuery('XML'));
			console.info(qp.getQuery('JSON'));*/

			if (query === null) {
				console.error('falta algun filtro');
				return;
			}

			switch (this.options.queryFormat) {
				case 'JSON':
					query = 'handler=%s&output=%s&%s&action=browser3&method=search'.printf(
						this.options.queryHandler,
						this.options.outputFormat,
						query
					);
					break;
				case 'XML':
					query = {
						handler: this.options.queryHandler,
						output: this.options.outputFormat,
						query: query,
						action: 'browser3',
						method: 'search'
					};
					break;
			}

			this.lastQuery = query;
			this.options.cbBeforeSearch(query);

			$.post(X.restUrl, query, function(data, textStatus, xhr) {
				this.options.cbSearch(null, {widget: this, result: data, selection: null});
			}.bind(this));
		},

		getLastQuery: function() {
			return this.lastQuery;
		},

		options: {
			showAdvanced: true,
			queryFormat: 'JSON', // [DOM | XML | JSON]
			queryHandler: 'SQL', // [SQL | SOLR | XVFS]
			outputFormat: 'JSON', // [XML | JSON]
			cbAdvanced: null,
			cbBeforeSearch: function(data, textStatus) {},
			cbSearch: function(data, textStatus) {}
		},

		getter: ['getLastQuery']

	});

})(com.ximdex);

