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


/**
 * See QueryHandler_Abstract for input/output formats
 */

function QProcessor(options) {

	this.OUTPUT_JSON = 'json';
	this.OUTPUT_XML = 'xml';
	this.OUTPUT_DOM = 'dom';

	this.options = null;
	this.filters = null;
	this.sorts = null;

	this._init = function(options) {
		this.options = $.extend({
			parentid: 10000,
			depth: 0,
			items: 50,
			page: 0,
			condition: 'and',
			filters: [],
			sorts: []
		}, options);
	};

	this.checkFilters = function() {
		var filters = this.options.filters || [];
		var valid = false;
		for (var i=0; i<filters.length; i++) {
			var filter = filters[i];
			var content = filter.content ? filter.content : filter.from;
			if (content && content != undefined && content !==  null && content !== '') {
				valid = true;
				return valid;
			}
		}
		return valid;
	};
	
	this.getFullQuery = function(handler, inputFormat, outputFormat, filters) {
		
		handler = handler.toUpperCase();
		filters = filters || '';
		
		var query = this.getQuery(inputFormat);
		
		switch (inputFormat.toLowerCase()) {
			case 'json':
				query = 'handler=%s&output=%s&%s&filters=%s'.printf(
					handler,
					outputFormat,
					query,
					filters
				);
				break;
			case 'xml':
				query = {
					handler: handler,
					output: outputFormat,
					query: query,
					filters: filters
				};
				break;
		}
		
		return query;
	};

	this.getQuery = function(inputFormat) {
		
		if (this.options.filters.length == 0 || !this.checkFilters()) {
			// Need one filter at least
			return null;
		}

		inputFormat = inputFormat || this.OUTPUT_JSON;
		inputFormat = inputFormat.toLowerCase();

		if (inputFormat == this.OUTPUT_JSON) {
			var query = [];
			query.push('query[parentid]=' + this.options.parentid);
			query.push('query[depth]=' + this.options.depth);
			query.push('query[items]=' + this.options.items);
			query.push('query[page]=' + this.options.page);
			query.push('query[view]=' + this.options.view);
			query.push('query[condition]=' + this.options.condition);
			for (i=0; i<this.options.filters.length; i++) {
				var filter = this.options.filters[i];
				query.push('query[filters]['+i+'][field]=' + filter.field);
				query.push('query[filters]['+i+'][comparation]=' + filter.comparation);
				if (filter.content) query.push('query[filters]['+i+'][content]=' + filter.content);
				if (filter.from) query.push('query[filters]['+i+'][from]=' + filter.from);
				if (filter.to) query.push('query[filters]['+i+'][to]=' + filter.to);
			}
			for (i=0; i<this.options.sorts.length; i++) {
				var sort = this.options.sorts[i];
				query.push('query[sorts]['+i+'][field]=' + sort.field);
				query.push('query[sorts]['+i+'][order]=' + sort.order);
			}
			query = query.join('&');
			return query;
		}

		var $query = $('<search/>')
			.append($('<parentid/>').html(this.options.parentid))
			.append($('<depth/>').html(this.options.depth))
			.append($('<items/>').html(this.options.items))
			.append($('<page/>').html(this.options.page))
			.append($('<view/>').html(this.options.view))
			.append($('<condition/>').html(this.options.condition))
			.append(this._filtersToXML(this.options.filters))
			.append(this._sortsToXML(this.options.sorts));

		var query = (inputFormat == this.OUTPUT_DOM) ? $query : $('<query/>').append($query).html();
		return query;
	};

	this._filtersToXML = function(f) {
		var filters = $('<filters/>');
		$(f).each(function(id, item) {

			var filter = $('<filter/>')
				.attr('field', item.field)
				.attr('comparation', item.comparation);

			if (item.field == 'creation' || item.field == 'publication') {
				if (item.comparation == 'inrange') {
					if (item.from) filter.attr('from', item.from);
					if (item.to) filter.attr('to', item.to);
				} else {
					if (item.from) filter.attr('content', item.from);
				}
			} else {
				if (item.content) filter.attr('content', item.content);
			}

			$(filters).append(filter);

		}.bind(this));

		return filters;
	};

	this._sortsToXML = function(f) {
		var sorts = $('<sorts/>');
		$(f).each(function(id, item) {

			var sort = $('<sort/>')
				.attr('field', item.field)
				.attr('order', item.order);
			$(sorts).append(sort);

		}.bind(this));

		return sorts;
	};

	this.clearFilters = function() {
		this.options.filters = [];
	};

	this.addFilter = function(filter) {
		filter = $.extend({
			field: '',
			comparation: '',
			content: ''
		}, filter);
		this.options.filters.push(filter);
	};

	this.clearSort = function() {
		this.options.sorts = [];
	};

	this.addSort = function(sort) {
		sort = $.extend({
			field: '',
			order: ''
		}, sort);
		this.options.sorts.push(sort);
	};
	
	this.getOptions = function() {
		return Object.clone(this.options);
	};
	
	this.getFilters = function() {
		return Object.clone(this.options.filters);
	};
	
	this.getSorts = function() {
		return Object.clone(this.options.sorts);
	};

	this._init(options);
};
