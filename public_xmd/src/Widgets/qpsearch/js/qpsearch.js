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

(function($) {

	$.widget('ui.qpsearch', {

		cache: null,
		field: null,
		button: null,
		filters: null,
		advanced: null,
		nodetypeFilter: null,
		dateFilterFrom: null,
		dateFilterTo: null,
		publicationFilterFrom: null,
		publicationFilterTo: null,

		_init: function() {
			var widget = this;
			var $this = this.element;
			this.cache = {
				query: null,
				results: null
			};

			$('.xim-qpsearch-suggester', $this)
				.suggester(this.options.suggester)
				.bind('itemSelected', function(event, params) {
					widget.doSearch();
				});

			this.field = $('.xim-qpsearch-suggester input.xim-autocomplete-input', $this)
				.click(function(event) {
					if ($(this).data('firstClick') == 1) {
						$(this).val('');
						$(this).data('firstClick', 0);
					}
				})
				.data('firstClick', 1);

			this.button = $('button#xim-qpsearch-searchbutton', $this)
				.click(function(event) {
					widget.doSearch();
				});

			this.filters = $('#xim-qpsearch-filters', $this).addClass('xim-qpsearch-filters-hidden');
			this.nodetypeFilter = $('#xim-qpsearch-filter-nodetype', $this);
			this.advanced = $('#xim-qpsearch-advanced', $this)
				.click(function(event) {
					widget.filters.toggleClass('xim-qpsearch-filters-hidden');
					return false;
				});

			$('#xim-qpsearch-reset').click(function(e) {
				$('#xim-qpsearch-textfield').val('');
				$('option[value=0]', '#xim-qpsearch-filter-nodetype').attr('selected', 'true');
				$('input', $('#xim-qpsearch-filters')).val('');
			});

			this.dateFilterFrom = $('#xim-qpsearch-datefield-from');
			$('#xim-qpsearch-filter-datebutton-from')
				.click(
					this._datepickerController(
						'#xim-qpsearch-datefield-from',
						'#xim-qpsearch-filter-datebutton-from',
						'#xim-qpsearch-filter-date'
					)
				);

			this.dateFilterTo = $('#xim-qpsearch-datefield-to');
			/*$(this.dateFilterTo, $this)
				.datepicker({
					inline: false,
					dateFormat: 'dd/mm/yy',
					changeYear: true,
					changeMonth: true,
				});*/
			$('#xim-qpsearch-filter-datebutton-to')
				.click(
					this._datepickerController(
						'#xim-qpsearch-datefield-to',
						'#xim-qpsearch-filter-datebutton-to',
						'#xim-qpsearch-filter-date'
					)
				);

			this.publicationFilterFrom = $('#xim-qpsearch-publicationfield-from');
			$('#xim-qpsearch-filter-publicationbutton-from')
				.click(
					this._datepickerController(
						'#xim-qpsearch-publicationfield-from',
						'#xim-qpsearch-filter-publicationbutton-from',
						'#xim-qpsearch-filter-publication'
					)
				);

			this.publicationFilterTo = $('#xim-qpsearch-publicationfield-to');
			$('#xim-qpsearch-filter-publicationbutton-to')
				.click(
					this._datepickerController(
						'#xim-qpsearch-publicationfield-to',
						'#xim-qpsearch-filter-publicationbutton-to',
						'#xim-qpsearch-filter-publication'
					)
				);

			this._createAdvancedSearch();
			//console.log(this.field, $(this.options.target)[this.options.target_type]('getModel'));
		},

		_datepickerController: function(field, button, dpicker) {
			var $this = this.element;
			return function(event) {
				var flag = $(button, $this).data('dp-visible');
				if (!flag) {
					$(button, $this).data('dp-visible', true);
					$(dpicker, $this)
						.datepicker({
							//altField: '#xim-qpsearch-datefield',
							//altFormat: 'dd-mm-yy',
							dateFormat: 'dd/mm/yy',
							changeYear: true,
							changeMonth: true,
							onSelect: function(dateText, inst) {
								//console.info(dateText, $('#xim-qpsearch-datefield'));
								$(field, $this).val(dateText);
								$(dpicker, $this).datepicker('destroy');
								$(button, $this).data('dp-visible', false);
							}
						});
				} else {
					$(button, $this).data('dp-visible', false);
					$(dpicker, $this).datepicker('destroy');
				}
			}
		},

		_createAdvancedSearch: function() {

			var widget = this;

			new DataStore({
				ds: new DataSource({
					method: 'get',
					type: 'json',
					url:  X.restUrl
				}),
				colModel: [
					{name: 'idnodetype', label: 'IdNodeType', type: 'number', align: 'center'},
					{name: 'name', label: 'Name'},
					{name: 'icon', label: 'Icon', type: 'image', width: '24px'},
				],
				selector: 'nodetypes'
			})
				.load_data({},
					function(store) {
						var model = store.get_model();
						$('<option></option>')
							.val('0')
							.html('--- Any ---')
							.appendTo(widget.nodetypeFilter);
						$(model).each(function(id, elem) {
							var img = widget.options.img_base + '/' + elem.icon.value;
							$('<option style="background: url(\''+img+'\') no-repeat;"></option>')
								.css({
									'height': '16px',
									'padding-left': '30px'
								})
								.val(elem.idnodetype.value)
								.html('('+elem.idnodetype.value+') '+elem.name.value)
								.appendTo(widget.nodetypeFilter);
						});
					}.bind(this)
				);
		},

		doSearch: function() {

			if (this.options.datastore.options.ds.running) return;

			var widget = this;
			var text = $(this.field).val();
			//if (text.length < 3) return;
			//console.log(text);

			var qp = new QProcessor({
				nodeid: 10000,
				parentid: 0,
				max_depth: 10000,
				element_ini: 0,
				num_elements: 100,
				page: 0
			});
			if (text.length > 0) {
				qp.addFilter({
					name: 'Name',
					value: text,
					type: 'exacta'
				});
			}
			if ($(this.nodetypeFilter).val() != 0) {
				qp.addFilter({
					name: 'NodeType',
					value: $(this.nodetypeFilter).val()
				});
			}
			if ($(this.dateFilterFrom).val().length > 0) {
				var dateFrom = $(this.dateFilterFrom).val();
				//console.log(date, date.getTime());
				// ms to secs
				//date = new Date(date).getTime() / 1000;
				var dateTo = $(this.dateFilterTo).val();
				if (dateTo.length == 0) dateTo = dateFrom;
				qp.addFilter({
					name: 'Date',
					from: dateFrom,
					to: dateTo
				});
			}
			if ($(this.publicationFilterFrom).val().length > 0) {
				var dateFrom = $(this.publicationFilterFrom).val();
				//console.log(date, date.getTime());
				// ms to secs
				//date = new Date(date).getTime() / 1000;
				var dateTo = $(this.publicationFilterTo).val();
				if (dateTo.length == 0) dateTo = dateFrom;
				qp.addFilter({
					name: 'Publication',
					from: dateFrom,
					to: dateTo
				});
			}
			qp.addSort({
				field: 'Nodes.Name',
				order: 'ASC'
			});

			var query = qp.getQuery();
			//console.info(qp.getQuery(), qp.getQuery(true));
			if (query === null) {
				console.error('falta algun filtro');
				return;
			}

			if (this.cache.query == query) {
				this.loadResults(this.cache.results);
				return;
			}

			this.options.datastore.load_data({
					params: {
						query: query,
					},
					options: this.options
				},
				function(store) {
					var results = store.get_model();
					if (this.options.use_cache === true) {
						this.cache.query = query;
						this.cache.results = results;
					}
					this.loadResults(results);
				}.bind(this)
			);
		},

		loadResults: function(results) {
			var target = this.options.target;
			var method = this.options.target_type;
			$(target)[method]('setModel', results);
		},

		/*normalize_node: function(node) {
			node.idnode = node.id;
			delete node.id;
			node.idparent = node.parentName;
			delete node.parentName;
			node.children = node.numChildren;
			delete node.numChildren;
			node.nodetypeid = node.type;
			delete node.type;
			node.nodetypename = node.typeName;
			delete node.typeName;
			node.bpath = node.nodePath;
			delete node.nodePath;
			node.__qpNormalized = true;
			return node;
		},*/

		getDatastore: function(datastore) {
			return this.options.datastore;
		},
		setDatastore: function(datastore) {
			this.options.datastore = datastore;
		},

		options: {
			datastore: null,
			url_base: '',
			img_base: '',
			target: null,
			target_type: '',
			use_cache: false,
			suggester: {}
		},
		
		getter: ['getDatastore']
	});


})(jQuery);
