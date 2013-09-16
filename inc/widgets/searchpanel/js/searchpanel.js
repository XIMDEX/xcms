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

window.com.ximdex = Object.extend(window.com.ximdex, {
	searchpanel: {
		colmodels: {},
		datastores: {},
		filtersUrl: X.restUrl + '?action=searchpanel&method=filters',
		dsUrl: X.restUrl + '?action=searchpanel&method=datastores',
		options: {}
	}
});

(function(X) {

	$.widget('ui.searchpanel', {

		nodeTypesList: null,
		filtersContainer: null,
		sf: null,
		as: null,
		ls: null,
		ss: null,
		sd: null,
		view: null,
		rs: null,

		_init: function(options) {

			this.element.dialog({
				title: _('Search'),
				autoOpen: false,
				width: 900,
				height: 600,
				minWidth: 630,
				minHeight: 280,
				modal: false,
				resizable: true,
				closeOnEscape: true/*,
				buttons: {
					'Load results': function() {
						var model = this.getResults();
						var selection = Object.clone(this.getSelection());
						this.element.trigger('loadResults', [{widget: this, model: model, selection: selection}]);
						this.close();
					}.bind(this),
					'Close': this.close.bind(this),
					'Search': this.search.bind(this)
				}*/
			});

			var continueLoad = function() {

				$.getScript(
					X.searchpanel.dsUrl,
					function(data, textStatus) {

						this.options = $.extend({}, this.options, X.searchpanel.options);

						this.options.nodetypesDatastore = new DataStore(X.searchpanel.datastores.nodetypes);

						this.options.nodetypesDatastore.load_data({
								params: {},
								options: {}
							},
							function(store) {

								this.nodeTypesList = store.get_model();
								if (Object.isEmpty(this.nodeTypesList)) {
									this.nodeTypesList = {nodetypes: []};
								}

								this._continueInit();

							}.bind(this)
						);

					}.bind(this));
			}.bind(this);


			this.filtersContainer = $('.xim-search-filters', this.element);

			if (this.filtersContainer.length == 0) {
				$.get(
					X.restUrl,
					{action: 'searchpanel', method: 'template'},
					function(data, textStatus) {
						$(data).appendTo(this.element);
						this.filtersContainer = $('.xim-search-filters', this.element);
						//continueLoad();
					}.bind(this)
				);
			} else {
				//continueLoad();
			}

			continueLoad();
		},

		_continueInit: function() {

			this.options.searchDatastore = new DataStore(X.searchpanel.datastores.search);

			this.sf = new X.searchpanel.SearchFilters({
				filtersSet: this.options.filters,
				nodeTypesList: this.nodeTypesList
			});

			this.as = new X.searchpanel.AdvancedSearch({
				widget: this.element,
				datastore: this.options.searchDatastore,
				queryHandler: this.options.queryHandler,
				inputFormat: this.options.inputFormat,
				outputFormat: this.options.outputFormat,
				filters: this.options.filters,
				cache: this.options.cache,
				masterFilter: this.options.masterFilter
			});

			var lsContainer = $('.last-searches-items', this.element);
			this.ls = new X.searchpanel.LastSearches({container: lsContainer});
			this.ls.refreshContainer();
			lsContainer.bind('last-searches-select', this.onLastSearchesSelect.bind(this));

			var ssContainer = $('.saved-searches-items', this.element);
			this.ss = new X.searchpanel.SavedSearches({
				container: ssContainer,
				datastore: this.options.searchDatastore
			});
			this.ss.refreshContainer();
			ssContainer.bind('saved-searches-select', this.onSavedSearchesSelect.bind(this));

			// IMPORTANT: See #2017
//			this.sd = new X.searchpanel.Sets({onOk: this.on_addToSetButton.bind(this)});

			var gv = X.widgetsVars.getWidget(this.element.attr('id'));
			if (gv !== null) {
				if (gv.queryHandler) this.options.queryHandler = gv.queryHandler;
				if (gv.inputFormat) this.options.inputFormat = gv.inputFormat;
				if (gv.outputFormat) this.options.outputFormat = gv.outputFormat;
			}

			$('.buttonset button', this.element).each(function(index, button) {
				var value = $(button).val();
				var method = this['on_%sButton'.printf(value)];
				if (method === undefined) method = function() {};
				$(button).click(method.bind(this));
			}.bind(this));


			this.filtersContainer.show();
		},

		on_searchButton: function(event) {
			this.options.page = 1;
			this.options.sorts = [];
			this.rs.clearSorts();
			this.search();
		},

		on_resetButton: function(event) {
			this.cleanUp();
		},

		on_saveButton: function(event) {
			var query = this.as.getCurrentQuery();
			if (Object.isEmpty(query)) {
				this.showErrors([_('No search was performed.')]);
				return;
			}
			this.ss.add(query);
		},

		on_createSetButton: function(event) {
			// IMPORTANT: See #2017
		},

		on_addToSetButton: function(event) {
			$.get(
				X.restUrl,
				{
					action: 'browser3',
					method: 'listSets'
				},
				function(data) {
					console.log(data);
				}
			);

		},

		on_selectNodesButton: function(event) {
			var selection = Object.clone(this.getSelection());
			this.element.trigger('nodesSelected', [{widget: this, selection: selection}]);
			this.close();
		},

		onLastSearchesSelect: function(event, query) {
			this.clearFilters();
			this.as.execQuery(query, this.loadResults.bind(this), false);
		},

		onSavedSearchesSelect: function(event, source, results, query) {

			$('.xim-search-options input[name="sopt"][value="%s"]'.printf(query.condition), this.element).attr('checked', true);

			this.sf.clearFilters(this.filtersContainer);

			$.each(query.filters, function(index, item) {

				var filter = this.sf.createSearchFilter(this.filtersContainer);
				$('.xim-filter-field', filter).val(item.field).change();
				$('.xim-filter-comparation', filter).val(item.comparation).change();
				if (item.from && item.to) {
					$('.xim-filter-date-content', filter).val(item.from);
					$('.xim-filter-date-to-content', filter).val(item.to);
				} else {
					$('.xim-filter-content', filter).val(item.content);
				}

			}.bind(this));
			this.loadResults(source, results, [], false);
		},

		search: function() {
			//this.clearResults();
			var options = {
				view: this.rs.view,
				parentid: this.options.parentid || 10000,
				items: this.options.itemsPerPage || 50,
				page: this.options.page || 1,
				sorts: this.options.sorts || []
			};
			var saveSearch = options.page == 1;
			this.as.search(options, this.loadResults.bind(this) , saveSearch);
		},

		loadResults: function(source, results, errors, addToLastSearches) {

			errors = errors || [];
			if (errors.length > 0) {
				this.showErrors(errors);
				return;
			}

			var resultsContainer = $('.results', this.element);
			if (resultsContainer.data('visible') !== true) {
				resultsContainer.show().data('visible', true);
			}

			this.rs.setModel(source, results);

			if (addToLastSearches !== false) {
				var search = this.as.getLastQuery();
				var f = search.getFilters();
				var title = '%s %s %s ...'.printf(f[0].field, f[0].comparation, f[0].content);
				this.ls.add(title, search.getQuery(this.options.inputFormat));
			}
		},

		cleanUp: function() {
			this.clearFilters();
			this.clearResults();
			$(this.element).unbind('itemsChange').unbind('pageChange');
		},

		clearFilters: function() {
			this.filtersContainer.hide();
			$('.xim-filter-date-content, .xim-filter-date-to-content', this.element).datepicker('destroy');
			$('.xim-search-filter', this.filtersContainer).unbind().remove();
			this.sf.loadFilters(
				this.filtersContainer,
				function() {
					this.sf.hideMasterFilter(this.options.masterFilter)
				}.bind(this)
			);
			this.filtersContainer.show();
		},

		clearResults: function() {
			if (Object.isObject(this.rs)) this.rs.clear();
		},

		showErrors: function(errors) {
			errors = errors || [];
			if (errors.length > 0) {
				var msg = '<ul class="errors">';
				for (var i=0, l=errors.length; i<l; i++) {
					msg += '<li class="error">%s</li>'.printf(errors[i]);
				}
				msg += '</ul>';
				var d = new X.dialogs.MessageDialog({title: _('Advanced search'), message: $(msg)});
				d.open();
			}
		},

		open: function() {

			if (this.element.dialog('isOpen')) return;

			var $selBtn = $('div.xim-search-panel-right div.buttonset button.selectionButton', this.element);
			$selBtn.html(this.options.selectButtonLabel);

			if (this.options.showSelectButton) {
				$selBtn.show();
			} else {
				$selBtn.hide();
			}

			this.cleanUp();
			this.element.dialog('open');

			this.rs = new X.searchpanel.ResultsView({
				view: this.options.view,
				container: this.element,
				as: this.as
			});

			$(this.element).bind('itemsChange', function(event, params) {
				this.options.itemsPerPage = params.data.value;
				this.options.page = 1;
				this.search();
			}.bind(this));

			$(this.element).bind('pageChange', function(event, params) {
				this.options.page = params.page;
				this.search();
			}.bind(this));

			$(this.element).bind('lvColumnSort', function(event, params) {
				this.options.page = 1;
				this.options.sorts = [{
					field: params.sort.field,
					order: params.sort.order === true ? 'ASC' : 'DESC'
				}];
				this.search();
			}.bind(this));

			this.as.setMasterFilter(this.options.masterFilter);

			if (!this.options.showFilters) {
				$('div.filters', this.element).hide();
				this.options.page = 1;
				this.options.sorts = [];
				this.search();
			} else {
				$('div.filters', this.element).show();
			}

			var hideLeftPanel = Object.isArray(this.options.masterFilter)  && this.options.masterFilter.length > 0;
			if (hideLeftPanel) {
				$('.xim-search-panel-left', this.element).hide();
				$('.xim-search-panel-right', this.element).addClass('expanded');
			} else {
				$('.xim-search-panel-left', this.element).show();
				$('.xim-search-panel-right', this.element).removeClass('expanded');
			}

			this.sf.hideMasterFilter(this.options.masterFilter);
		},

		close: function() {
			this.element.dialog('close');
			this.cleanUp();
			$('div.filters', this.element).show();
		},

		destroy: function() {
			$('.xim-filter-date-content, .xim-filter-date-to-content', this.element).datepicker('destroy');
			this.element.dialog('destroy');
			$(this.element).unbind().remove();
		},

		getDatastore: function(datastore) {
			return this.options.searchDatastore;
		},

		setDatastore: function(datastore) {
			this.options.searchDatastore = datastore;
		},

		getLastQuery: function() {
			return this.as.getLastQuery();
		},

		getResults: function() {
			return this.rs.getModel();
		},

		getSelection: function() {
			var selection = this.rs.getSelection();
			return selection;
		},

		options: {
			datastore: null,
			url_base: X.baseUrl,
			img_base: '',
			loading_icon: '/actions/browser3/resources/images/loading.gif',
			queryHandler: 'SQL',
			inputFormat: 'JSON',
			outputFormat: 'JSON',
			showSelectButton: false,
			 selectButtonLabel: _('Select nodes'),
			cache: false,
			filters: null,	// [ ximdex | toldox | null ]
			masterFilter: null,
			showFilters: true,
			view: 'gridview'	// [ gridview | listview | treeview ]
		},

		getter: ['getDatastore', 'getLastQuery', 'getResults', 'getSelection']

	});

})(com.ximdex);
