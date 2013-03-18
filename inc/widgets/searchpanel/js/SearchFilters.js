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

	X.searchpanel.SearchFilters = Object.xo_create({
		
		filtersSet: null,
		nodeTypesList: null,
		filters: null,
		filterKeys: null,
		
		_init: function(options) {
			this.filtersSet = options.filtersSet;
			this.nodeTypesList = options.nodeTypesList;
			this.filtersKeys = [];
		},
		
		loadFilters: function(container, cb) {
						
			if (this.filters !== null) {
				var filters = this.createSearchFilter(container);
				if (Object.isFunction(cb)) cb(data, filters);
				return;
			}
			
			var data = {};
			if (!Object.isEmpty(this.filtersSet)) data.filters = this.filtersSet;
			
			$.getJSON(
				X.searchpanel.filtersUrl,
				data,
				function(data, textStatus) {
								
					this.filters = data;
					this.filterKeys = [];
					for (var o in this.filters) {
						this.filterKeys.push(o);
					}			
					var filters = this.createSearchFilter(container);

					$(document).on('click','.xim-search-filters .xim-filter-add', function(event) {
						this.createSearchFilter(container);
					}.bind(this));
		
					$(document).on('click','.xim-search-filters .xim-filter-remove', function(event) {
						if ($('.xim-search-filter').length == 1) {
							return;
						}
						var filter = $(event.target).parent('.xim-search-filter');
						filter.unbind().remove();
					});
					
					if (Object.isFunction(cb)) cb(data, filters);
		
				}.bind(this)
			);
		},
		
		clearFilters: function(container) {
			$('.xim-search-filter', container).unbind().remove();
		},
		
		hideMasterFilter: function(masterFilter) {
			$('.xim-search-filters .xim-filter-field option').show();
			$.each(masterFilter, function(index, filter) {
				$('.xim-search-filters .xim-filter-field option[value="%s"]'.printf(filter.field)).hide();
			});
		},
		
		createSearchFilter: function(container) {
		
			var filter = $('<div></div>')
				.addClass('xim-search-filter')
				.appendTo(container);
			
			['field', 'comparation', 'nodetype-comparation', 'date-comparation', 'boolean-comparation']
		 		.each(function(index, item) {
			
				var field = $('<select></select>')
					.addClass('xim-filter-%s'.printf(item))
					.appendTo(filter);
				
				
				if (item in this.filters) {
					var fieldData = this.filters[item];

					$(field)
						.data('field:data', fieldData)
						.empty();
					
					fieldData.each(function(index, item) {
						var options = $('<option value="%s">%s</option>'.printf(item.key, item.value));
						// Wrap item to obtain a clone
						options.data('option:data', (function(A) { return A;})(item));
						field.append(options);
					});
				}

				if (Object.isEmpty(item.match('comparation'))) {
					//$(field).change(this.onFieldTypeChange.bind(this, filter));
					$(field).change(function(event){
						//this.onFieldTypeChange.bind(this, filter);
						X.searchpanel.SearchFilters.prototype.onFieldTypeChange(event,filter);
					});
				} else {
					//$(field).change(this.onComparationChange.bind(this, filter));
					$(field).change(function(event){
						//this.onComparationChange.bind(this, filter);
						X.searchpanel.SearchFilters.prototype.onComparationChange(event,filter);
					});
				}
			
			}.bind(this));
			
			$('<input type="text" class="xim-filter-content" />').appendTo(filter);
			$('<select class="xim-filter-nodetype-content"></select>').appendTo(filter);
			$('<input type="text" class="xim-filter-date-content" />').appendTo(filter);
			$('<input type="text" class="xim-filter-date-to-content" />').appendTo(filter);
			
			var b = $('<select class="xim-filter-boolean-content"></select>').appendTo(filter);
			['true', 'false'].each(function(index, element) {
				b.append('<option value="%s">%s</option>'.printf(element, element));
			});
			
			$(this.nodeTypesList).each(function(id, nodetype) {
				var img = '%s/xmd/images/icons/%s'.printf(X.baseUrl, nodetype.icon.value);
				$('<option style="background: url(%s) no-repeat;"></option>'.printf(img))
					.val(nodetype.idnodetype.value)
					.html('('+nodetype.idnodetype.value+') ' + nodetype.name.value)
					.css({
						'padding-left': '20px'
					})
					.appendTo($('.xim-filter-nodetype-content', filter));
			});
			
			$('<button class="xim-filter-add">+</button>').appendTo(filter);
			$('<button class="xim-filter-remove">-</button>').appendTo(filter);
			
			this.createDatepicker($('.xim-filter-date-content, .xim-filter-date-to-content', filter));
			this.onFieldTypeChange({target: $('.xim-filter-field', filter)}, filter);
			
			return filter;
		},
		
		createDatepicker: function(elements) {
			$(elements)
				.datepicker('destroy')
				.datepicker({
					//altField: '#xim-qpsearch-datefield',
					//altFormat: 'dd-mm-yy',
					dateFormat: 'dd/mm/yy',
					changeYear: true,
					changeMonth: true
				});
		},
		
		onFieldTypeChange: function(event, filterContainer) {

			var value = $(event.target).val();
			var data = $('option[value=%s]'.printf(value), event.target).data('option:data');

			if (Object.isArray(data.comparation)) {
				var filter = [];
				data.comparation.each(function(index, item) {
					filter.push('.xim-filter-%s'.printf(item));
				});
				filter = filter.join(', ');
			} else {
				var filter = '.xim-filter-%s'.printf(data.comparation);
			}
			
			$('.xim-filter-comparation, .xim-filter-nodetype-comparation, .xim-filter-date-comparation, .xim-filter-boolean-comparation', filterContainer)
				.removeClass('xim-filter-active-comparation')
				.hide();
			$(filter, filterContainer)
				.addClass('xim-filter-active-comparation')
				.show();
			this.onComparationChange({target: $(filter, filterContainer)}, filterContainer);
		},
		
		onComparationChange: function(event, filterContainer) {

			var value = $(event.target).val();
			var data = $('option[value=%s]'.printf(value), event.target).data('option:data');

			if (Object.isArray(data.content)) {
				var filter = [];
				data.content.each(function(index, item) {
					filter.push('.xim-filter-%s'.printf(item));
				});
				filter = filter.join(', ');
			} else {
				var filter = '.xim-filter-%s'.printf(data.content);
			}
			$('.xim-filter-content, .xim-filter-nodetype-content, .xim-filter-date-content, .xim-filter-date-to-content, .xim-filter-boolean-content', filterContainer)
				.removeClass('xim-filter-active-content')
				.hide();
			$(filter, filterContainer)
				.addClass('xim-filter-active-content')
				.show();
		}
		
	});

})(X);
