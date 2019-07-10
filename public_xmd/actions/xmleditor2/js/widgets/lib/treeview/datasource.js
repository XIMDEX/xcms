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

if (typeof(Function.prototype['bind']) != 'function') {
	Function.prototype.bind = function() {
		var args = arguments;
		var caller = args[0];
		var __method = this;
		delete(args[0]);
		return function() {
			return __method.apply(caller, arguments);
		}
	}
}

var DataSource = function(options) {

	this.options = null;

	this.init = function(options) {
		this.options = $.extend({
			url: null,
			cb_fetched: function(data, textStatus) {/*console.info(arguments);*/},
			type: 'xml'
		}, options);
	};

	this.fetch = function(params, callback) {
		callback = typeof(callback) == 'function'
			? callback
			: this.options.cb_fetched;

		$.get(
			this.options.url,
			params,
			callback,
			this.options.type
		);
	};

	this.init(options);
};

var Iter = function(path) {

	this.path = null;
	this.indexes = null;

	this.init = function(path) {
		if (path === null) path = 'root';
		this.path = path;
		this.indexes = this.path.split(':');
		if (this.indexes[0] != 'root') {
			this.path = 'root:' + path;
			this.indexes = this.path.split(':');
		}
	};

	this.get_row = function(model) {
		//console.log(this.path, model, model[this.path]);
		return model[this.path] || null;
	};

	this.init(path);
};

var RowModel = function(options) {

	this.colModel = null;
	this.queries = null;
	this.rows = null;
	this.index = null;
	this.ds = null;

	this.init = function(options) {
		options = $.extend({queries: null, ds: null}, options);
		this.colModel = options.colModel || {selector: '', columns: []};
		this.queries = options.queries;
		this.index = {root: {data: {}, children: this.rows}};
		this.rows = [this.index.root];
		this.ds = options.ds;
	};

	this.get_path = function(iter) {
		var it = this.get_iter(iter);
		var path = it.indexes.slice(1);
		return path;
	};

	this.get_iter = function(path) {
		var it = null;
		if (path === null || typeof(path) == 'string') {
			it = new Iter(path);
		} else if (typeof(path) == 'object' && path['path']) {
			it = new Iter(path.path);
		}
		return it;
	};

	this.iter_first = function() {
		return new Iter('root:0');
	};

	this.iter_next = function(iter) {
		if (iter == null) return null;
		var indexes = iter.indexes;
		indexes[indexes.length-1]++;
		var it = new Iter(indexes.join(':'));
		if (!it.get_row(this.index)) {
			it = null;
		}
		return it;
	};

	this.iter_previous = function(iter) {
		if (iter == null) return null;
		var indexes = iter.indexes;
		indexes[indexes.length-1]--;
		var it = new Iter(indexes.join(':'));
		if (!it.get_row(this.index)) {
			it = null;
		}
		return it;
	};

	this.iter_parent = function(iter) {
		if (iter == null) return null;
		var indexes = iter.indexes.slice(0, iter.indexes.length-1);
		if (indexes.length == 0) return null;
		//indexes.push('0');
		var it = new Iter(indexes.join(':'));
		if (!it.get_row(this.index)) {
			it = null;
		}
		return it;

	};

	this.iter_children = function(iter, options) {
	};

	this.has_children = function(iter) {
		var it = this.get_iter(iter);
		var row = it.get_row(this.index);
		return row === null ? false : true;
	};

	this.append = function(parent, data) {
		if (parent == null) {
			this.index.root = {
				data: data,
				children: []
			};
			return new Iter('root');
		}
		var it = this.get_iter(parent);
		var row = it.get_row(this.index);
		if (row) {
			var item = {
				data: data,
				children: []
			};
			row.children.push(item);
			var path = it.path + ':' + (row.children.length - 1);
			it = this.get_iter(path);
			this.index[path] = item;
			return it;
		}
		return null;
	};

	this.prepend = function(parent, data) {
		// Implement me!
	};

	this.insert = function(parent, position, data) {
		// Implement me!
	};

	this.insert_before = function(parent, sibling, data) {
		// Implement me!
	};

	this.insert_after = function(parent, sibling, data) {
		// Implement me!
	};

	this.remove = function(iter) {
		var it = this.get_iter(iter);
		var pit = this.iter_parent(it);
		delete this.index[pit.path].children[it.indexes[it.indexes.length-1]];
		delete this.index[it.path];
	};

	this.remove_children = function(iter) {
		var it = this.get_iter(iter);

		var indexes = it.indexes;
		indexes.push('0');
		var cit = new Iter(indexes.join(':'));
		while (cit) {
			this.remove(cit);
			cit = this.iter_next(cit);
		}

		this.index[it.path].children = [];
	};

	this.clear = function() {
		this.rows = [];
		this.index = {root: {data: {}, children: this.rows}};
		this.rows = [this.index.root];
	};

	this.get_value = function(iter, column) {
		var it = this.get_iter(iter);
		var row = it.get_row(this.index);
		if (row) {
			row = row.data;
			if (column != undefined) row = row[column];
			return row;
		}
		return null;
	};

	this.set_value = function(iter, value, column) {
		var it = this.get_iter(iter);
		var row = it.get_row(this.index);
		if (row) {
			if (column != undefined) {
				row.data[column] = value;
			} else if (value != null && typeof(value) == 'object' && value['push']) {
				row.data = value;
			}
			return row;
		}
		return null;
	};

	this._create_dsNode = function(options) {
		var node = options.node;
		var columns = options.columns;
		var newNode = {};
		var c = 0;
		for (var i=0; i<columns.length; i++) {
			var col = columns[i];
			var oCol = {};
			for (var o in col) {
				oCol[o] = col[o];
			}
			oCol.value = $(node).attr(oCol.name);
			oCol._index = c;
			newNode[col.name] = oCol;
			newNode[c++] = oCol;
		}
		return newNode;
	};

	this.init(options);
};

var TreeModel = function(options) {

	this.iter_children = function(iter, options) {
		if (iter == null) return null;
		var indexes = iter.indexes;
		indexes.push('0');
		var it = new Iter(indexes.join(':'));

		options = $.extend({params: {}, cache: false}, options);

		if (options.cache /*&& this.has_children(it)*/) {
			if (typeof(options.callback) == 'function') options.callback(it);
			return it;
		}

		this.ds.fetch(
			options.params,
			function(data, textStatus) {
				if (textStatus == 'success') {
					var nodes = $(this.colModel.selector, data);
					nodes.each(function(idx, node) {
						var fields = {};
						var f = 0;
						$(this.colModel.columns).each(function(idx, col) {
							fields[col.name] = $(node).attr(col.name);
							fields[f] = $(node).attr(col.name);
							f++;
						});
						this.append(iter, fields);
					}.bind(this));
					if (typeof(options.callback) == 'function') options.callback(it);
				} else {
					console.error(arguments);
				}
			}.bind(this)
		);

		return it;
	};

	this.init(options);
};
TreeModel.prototype = new RowModel();

var ButtonBarModel = function(options) {

	this.data = null;

	this.load_data = function(options) {
		this.ds.fetch(
			options.params,
			function(data, textStatus) {
				if (textStatus == 'success') {
					this.data = data;
					if (typeof(options.callback) == 'function') options.callback();
				} else {
					console.error(arguments);
				}
			}.bind(this)
		);
	};

	this.query = function(options) {
		if (this.data === null) return null;
		this.clear();
		var query = this.queries[options.query.query];
		var nodes = $(query.selector, this.data);
			console.log(nodes, this.data);
		for (var i=0; i<nodes.length; i++) {
			var node = nodes[i];
			var row = this._create_dsNode({
				node: node,
				columns: query.columns
			});
			this.append('root', row);
		}
		return this.iter_first();
	};

	this.init(options);
};
ButtonBarModel.prototype = new RowModel();

var MenuBarModel = function(options) {

	this.load_data = function(options) {
		this.ds.fetch(
			options.params,
			function(data, textStatus) {
				if (textStatus == 'success') {
					this.data = data;

					var data = this.query({
						selector: 'menubar > menu > menuitem',
						columns: [
							{name: 'id'},
							{name: 'text'},
							{name: 'accel'}
						]
					});
					this._createMenuItems('root', data);

					if (typeof(options.callback) == 'function') options.callback();
				} else {
					console.error(arguments);
				}
			}.bind(this)
		);
	};

	this._createMenuItems = function(parent, items) {
		parent = this.get_iter(parent);
		if (parent === null) return;
		for (var i=0; i<items.length; i++) {
			var it = this.append(parent, items[i]);
			var children = this.query({
				selector: 'menuitem[id="'+items[i].id+'"] > menu > menuitem',
				columns: [
					{name: 'id'},
					{name: 'text'},
					{name: 'accel'}
				]
			});
			this._createMenuItems(it, children);
		}
	};

	this.query = function(options) {
		if (this.data === null) return null;
		var data = [];
		var nodes = $(options.selector, this.data);
		for (var i=0; i<nodes.length; i++) {
			var node = nodes[i];
			var row = {};
			var f = 0;
			for (var c=0; c<options.columns.length; c++) {
				var alias = options.columns[c].name;
				var value = $(node).attr(alias);
				row[alias] = value;
				row[f] = value;
				f++;
			}
			data.push(row);
		}
		return data;
	};

	this.iter_children = function(iter, options) {
		iter = this.get_iter(iter);
		if (iter == null) return null;
		var indexes = iter.indexes;
		indexes.push('0');
		var it = new Iter(indexes.join(':'));
		return it;
	};

	this.init(options);
};
MenuBarModel.prototype = new RowModel();
