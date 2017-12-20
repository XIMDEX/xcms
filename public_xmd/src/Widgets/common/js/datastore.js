
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

var DataStore = Object.xo_create({

	_init: function(options) {
		this.options = Object.extend({
			ds: null,
			colModel: null,
			queryParams: null,
			selector: null
		}, options);
		this.source = null;
		this.data = null;
		this.it = 0;
	},

	datasource: function(ds) {
		if (!Object.isEmpty(ds)) {
			this.options.ds = ds;
			return this;
		} else {
			return this.options.ds;
		}
	},

	colModel: function(colModel) {
		if (!Object.isEmpty(colModel)) {
			this.options.colModel = colModel;
			return this;
		} else {
			return this.options.colModel;
		}
	},

	queryParams: function(queryParams) {
		if (!Object.isEmpty(queryParams)) {
			this.options.queryParams = queryParams;
			return this;
		} else {
			return this.options.queryParams;
		}
	},

	selector: function(selector) {
		if (!Object.isEmpty(selector)) {
			this.options.selector = selector;
			return this;
		} else {
			return this.options.selector;
		}
	},
	
	extractColModel: function(data) {

		if (data['__indexes']) {
			return data;
		}

		var fields = {};
		fields.__indexes = {};
		var f = 0;

		$(this.options.colModel).each(function(idx, col) {

			var props = Object.extend({
				name: null,
				label: null,
				visible: false,
				width: null,
				type: 'string',			// string | number | image
				align: 'left',			// left | center | right
				value: null
			}, col);

			var alias = col['alias'] ? col.alias : col.name;
			props.name = alias;
			delete props.alias;
			props.value = $(data).attr(col.name);

			fields[alias] = props;
			fields[f] = props;
			fields.__indexes[alias] = f;
			f++;

		});
		fields = this.normalize_node(fields, data);
		return fields;
	},

	normalize_node: function(fields, data) {
		if (fields['path'] && fields.path['value']) {
			//fields.path.value = fields.path.value + '/ximDEX';
			if (null !== fields.path.value.match(/^\/ximDEX/i)) {
				fields.path.value = fields.path.value.replace(/^\/ximDEX/i, '');
				if (fields.path.value.length == 0) {
					fields.path.value = '/';
				}
			}
			//console.log(fields.path.value);
		}
		return fields;
	},

	loaded: function() {
		return (this.data !== null);
	},

	createQueryParams: function(queryParams, params) {
		var _params = {};
		if (queryParams === null) {
			if (typeof(params.params) == 'string') {
				_params = params.params;
			} else {
				for (var o in params.params) {
					if (params.params[o]['value']) {
						_params[o] = params.params[o].value;
					} else {
						_params[o] = params.params[o];
					}
				}
			}
		} else if (typeof(queryParams) == 'function') {
			_params = queryParams(params.params, params.options);
		} else if (typeof(queryParams) == 'object' && queryParams['filter'] && typeof(queryParams['filter']) == 'function') {
			_params = queryParams.filter(params.params, params.options);
		}
		return _params;
	},

	load_data: function(params, callback) {
		this.clear();
		params = params || {};
		callback = callback || function() {};
		params = this.createQueryParams(this.options.queryParams, params);
		if (this.options.ds['fetch'] && typeof(this.options.ds['fetch']) == 'function') {
			this.options.ds.fetch(
				params,
				function(data, textStatus) {
					this.source = data;
					var filteredData = this.query(this.options.selector) || data;
					for (var i=0; i<filteredData.length; i++) {
						this.append(filteredData[i]);
					}
					if (typeof(callback) == 'function') callback(this);
				}.bind(this)
			);
		} else {
			this.source = this.options.ds;
			var filteredData = this.query(this.options.selector);
			for (var i=0; i<filteredData.length; i++) {
				this.append(filteredData[i]);
			}
			if (typeof(callback) == 'function') callback(this);
		}
	},

	clear: function() {
		this.data = [];
	},

	append: function(item) {
		var _item = item; //this.extractColModel(item);
		if (this.data == null) this.data = [];
		this.data.push(_item);
	},

	query: function(selector) {
		var filteredData = [];
		if (typeof(selector) == 'string') {
			// Try to filter by attribute (JSON) or by element (XML)
			filteredData = $(this.source).attr(selector) || $(selector, this.source);
		} else if (typeof(selector) == 'function') {
			filteredData = selector(this.source);
		} else if (typeof(selector) == 'object' && selector['filter'] && typeof(selector['filter']) == 'function') {
			filteredData = selector.filter(this.source);
		}
		filteredData = filteredData || [];
		for (var i=0; i<filteredData.length; i++) {
			filteredData[i] = this.extractColModel(filteredData[i]);
		}
		return filteredData;
	},

	get_model: function() {
		return this.data;
	},

	first: function() {
		if (!this.data) this.load_data();
		return this.data[0] ? this.data[0] : null;
	},

	fetch: function() {
		if (!this.data) this.load_data();
		if (!this.data || this.it >= this.data.length) return null;
		var ret = this.data[this.it];
		this.it++;
		return ret;
	},

	length: function() {
		if (!this.data) return 0;
		return this.data.length;
	}
	
});

