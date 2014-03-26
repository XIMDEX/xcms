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

	X.searchpanel.ResultsView = Object.xo_create({

		VIEW_GRID: 'gridview',
		VIEW_LIST: 'listview',
		VIEW_TREE: 'treeview',

		methods: {gridview: 'listview', listview: 'listview', treeview: 'treeview'},

		resultsSelector: '.results .results-view',
		view: null,
		element: null,
		wm: null,
		as: null,

		_init: function(options) {

			this.view = options.view || 'list';
			this.element = $(this.resultsSelector, options.container).unbind().empty();
			this.as = options.as;

			this.setView(this.view);
		},

		_getMethod: function() {
			var m = this.methods[this.view];
			return function() {
				return this.element[m].apply(this.element, arguments);
			}.bind(this);
		},

		setView: function(view) {
			var method = '_create_' + view;
			if (Object.isFunction(this[method])) {
				this.view = view;
				this.wm = this._getMethod();
				this[method]();
				this._manageActions();
			}
		},

		setModel: function(source, model) {
			this.wm('setModel', model);
			var $div = $(".number_results", this.element), $span = null;
			if($div.length) 
				$div.remove();

			// Put the number of results if there is at least one record
			if(source.records && source.records > 0 && window.drawertool == null) {
				$div = $("<div class='number_results'/>").html(_("Results"));
				$span = $("<span/>").html(source.records);
				$div.append($span);
				this.element.append($div);

				$('.xim-search-panel .searchpanel-paginator')
					.paginator('loadOptions', {
						pages: parseInt(source.pages),
						selected: parseInt(source.page)
					})
					.bind('pageClick', function(event, params) {
						var page = params.page;
						$(this.element).trigger('pageChange', [{page: page}]);
					}.bind(this));
			}

		},

		getModel: function() {
			return this.wm('getModel');
		},

		getSelection: function() {
			var selection = this.wm('getSelection');
			selection = $(selection.asArray()).map(function(index, item) {
				var o = item.__indexes ? item : $(item).data('data');
				return o;
			});
			return selection;
		},

		clear: function() {
			this.wm('setModel', []);
		},

		clearSorts: function() {
			this.wm('clearSorts');
		},

		_showPaginator: function() {

			$('.xim-search-panel .searchpanel-paginator')
				.paginator()
				.paginator('loadOptions', {
					pages: 0,
					selected: 0
				});
		},

		_create_gridview: function() {
			this.element
				.listview({
					paginator: {
						show: true,
						legend: _('Show'),
						view: 'select',
						items: [
					        {value: 3, text: '3'},
					        {value: 5, text: '5'},
					        {value: 10, text: '10'},
							{value: 25, text: '25'},
							{value: 50, text: '50'},
							{value: 75, text: '75'},
							{value: 100, text: '100'}
						]
					},
					showBrowser: false,
					showSelectionHandlers: true,
					colModel: X.searchpanel.colmodels.search,
					url_base: X.baseUrl,
					img_base: X.baseUrl + '/xmd/images/icons'
				})
				.listview('setModel', [])
				.bind('paginatorChange', function(event, params) {
					$(this).trigger('itemsChange', [params]);
				})
				.bind('columnSort', function(event, params) {
					var sort = {
						field: params.column.name,
						order: params.order
					};
					$(this).trigger('lvColumnSort', [{sort: sort}]);
				});

			this._showPaginator();
		},

		_create_listview: function() {

		},

		_create_treeview: function() {

			var conf = {
				colModel: X.searchpanel.colmodels.search,
				datastore: X.searchpanel.datastores.search,
				root: {
					name: {value: _('Projects'), visible: true},
					nodeid: {value: 10000, visible: false},
					icon: {value: 'projects.png', visible: true, type: 'image'},
					children: {value: 1, visible: false},	// Non zero!
					isdir: {value: 1, visible: false},
					path: {value: '/'+_('Projects'), visible: false},
					hasActions: {value: 1}
				},
				url_base: X.baseUrl,
				loading_icon: '/actions/browser3/resources/images/loading.gif',
				collapsed_icon: 'ui-icon-triangle-1-e',
	         expanded_icon: 'ui-icon-triangle-1-se'
			};

			conf.datastore.queryParams = function(params, options) {
				var lastQuery = this.as.getLastQuery();
				if (Object.isEmpty(lastQuery)) return null;
				var query = lastQuery.getFullQuery(
					this.as.options.queryHandler,
					this.as.options.inputFormat,
					this.as.options.outputFormat,
					this.as.options.filters
				);
				query = query.replace(/query\[parentid]=(\d*)&/, 'query[parentid]=%s&'.printf(params.nodeid.value));
				return query;
			}.bind(this);

			$(this.element)
				.treeview(conf)
//				.bind('itemClick', function(event, params) {
//				}.bind(this))
				.bind('select', function(event, params) {
					$('.destroy-on-click').unbind().remove();
				}.bind(this));

//			var tds = $(this.element).treeview('getDatastore');
//			tds.clear();
//			tds.append(Object.clone(conf.root));
//			$(this.element).treeview('setRootModel', tds.get_model(), false, true);
		},

		_manageActions: function() {

			$(this.element)
				.bind('actionsDropdown', function(event, params) {

					var selector = params.event.target;
					var selection = params.selection;

					var nodes = selection;

					// Get the selector position before the Ajax request...
					var pos = $(selector).offset();
					pos = {x: pos.left + $(selector).width(), y: pos.top};

					$('div#bw1').browserwindow('getActions', {
						nodes: nodes,
						cb: this._createFloatMenu.bind(this),
						data: $(params.element).data('data'),
						selector: selector,
						menuPos: pos
					});
				}.bind(this));

				this.wm('addCreateNodeListener', function(widget, node) {

					var data = $(node).data('data');

					$('<span></span>')
						.addClass('xim-actions-dropdown xim-listview-actions-dropdown')
						.addClass('ui-icon ui-icon-triangle-1-e')
						.mousedown(function(event) {

							var w = this.element;
							var target = event.target;
							var isSelected = $(target).closest('.xim-listview-selected').length > 0 ? true : false;
							var selection = [$(node).data('data')];

							if (isSelected) {
								// Stoping propagation here prevent the clear of selected nodes
								event.stopPropagation();
								selection = $(w).listview('getSelection').get();
							}

							w.trigger('actionsDropdown', [{
								ui: w,
								element: node,
								data: data,
								event: event,
								selection: selection
							}]);
						}.bind(widget))
						.appendTo($('.xim-listview-icon', node).parent());

					return node;
				}.bind(this));
		},

		_createFloatMenu: function(params) {

			var nodeid = params.data.nodeid.value;
			var cmenu = $('div.xim-actions-menu').unbind().empty();
			var show = true;

			if (cmenu.length == 0) {

				cmenu = $('<div></div>')
					.addClass('xim-actions-menu destroy-on-click')
					.attr('id', 'cmenu-'+nodeid);

			} else if ($(cmenu).attr('id') == 'cmenu-'+nodeid) {
				show = false;
			}

			show = show & (params.actions.length > 0);

			if (!show) {
				$(cmenu).unbind().remove();
				return;
			}

			if (Object.isFunction(params.actions.each)) {

				params.actions.each(function(index, item) {
					item.params = item.params.replace('%3D', '=');
					this._createButton({
						text: item.name,
						title: item.name,
						value: item.command,
						data: {
							action: item,
							nodes: params.nodes,
							ids: params.ids
						},
						className: 'view-action',
						icon: item.icon,
						click: function(e) {
							var data = $(e.currentTarget).data('data');
							$('div#bw1').browserwindow('openAction', data.action, params.ids);
						}.bind(this),
						container: cmenu
					});
				}.bind(this));

				cmenu
					.css({
						position: 'absolute',
						left: params.menuPos.x,
						top: params.menuPos.y,
						zIndex: 2000
					})
					.appendTo('body');

			}
		},

		_createButton: function(options) {
			options = $.extend({
				text: '',
				title: '',
				value: '',
				data: {},
				className: '',
				click: function() {},
				icon: null,
				container: null
			}, options);

			var b = $('<button></button>')
				.addClass('window-toolbar-button '+options.className + ' ' +options.value)
				//.html($('<span></span><span class="tooltip"></span>').html(options.text))
				.val(options.value)
				.data('data', options.data)
//				.attr('title', options.title)
				.click(options.click);

			$(b)
			   .append($('<span>'+_('Icon')+'</span>'))
				.append($('<span class="triangle"></span>'))
				.append($('<span class="tooltip"></span>').html(options.text));

			if (!Object.isEmpty(options.icon)) {

				var icon = options.icon.substr(0, options.icon.length-4);
				b.addClass('icon-'+icon);
			}

			if (options.container !== null) {
				b.appendTo(options.container);
			}
			$(b).wrap('<div class="button-container"/>');
			return b;
		}

	});

})(X);
