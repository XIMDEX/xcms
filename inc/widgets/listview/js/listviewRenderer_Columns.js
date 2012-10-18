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
 *  @version $Revision: 7740 $
 */

X.listview.ListviewRenderer_Columns = Object.xo_create(X.listview.ListviewRenderer, {

	RENDER_TYPE: 'COLUMNS_RENDERER',
	
	_init: function(widget) {
		X.listview.ListviewRenderer_Columns._construct(this, widget);
		this.renderer = new X.listview.ListviewRenderer_List(this.widget);
		this.columns = [];
	},
	
	createView: function(model, event) {
		
		// TODO: Parametrize this option
		var cols = 3;
		var container = $('<table></table>').addClass('xim-listview-table xim-listview-table-columns');
		var row = $('<tr></tr>').appendTo(container);
		
		for (var c=0; c<cols; c++) {
			var cell = $('<td></td>')
				.addClass('xim-listview-table-column')
				.appendTo(row);
			this.columns[c] = $('<div></div>')
				.addClass('xim-listview-columns-col xim-listview-columns-col-'+c)
				.data('column_id', c)
				.appendTo(cell);
		}

		this._loadModel(model, -1);
		$('.xim-listview-table-container', this.widget.element).css('overflow', 'hidden');
		return container;
	},
	
	_on_itemClick: function(event, params) {
	
		var container = $(params.element).closest('.xim-listview-columns-col');
		this._loadFromSource(params.data, container.data('column_id'));
		$(this.widget).trigger('lv_itemClick', [params]);
		return false;
	},
	
	_on_itemDblClick: function(event, params) {
	
//		console.log('dblclick', params);
		var container = $(params.element).closest('.xim-listview-columns-col');
		var column_id = container.data('column_id') - 1;
		
		if (params.data.isdir == 0) {
//			console.log(params.data);
			return;
		}

		// FIXME: Maybe there's a better way to do this load...
		$(this)
			.unbind('modelLoaded')
			.bind('modelLoaded', function(event, params) {
				var column = params.column - 1;
				this._loadModel(params.model, column);
				$(this).unbind('modelLoaded');
			}.bind(this));
		
		$(this.widget).trigger('lv_itemDblclick', [params]);		
		return false;
	},
	
	_on_itemContextmenu: function(event, params) {
//		console.log('contextmenu', params);
		$(this.widget).trigger('lv_itemContextmenu', [params]);
//		return false;
	},
	
	_loadModel: function(model, column) {

		var newColumn = column  + 1;
		if (!this.columns[newColumn]) return;
		
		var view = null;		
		if (model.length == 1 && model[0].isdir.value == '0') {
			
			var details = new X.listview.ListviewRenderer_Details(this.widget);
			view = $(details.createView(model[0]))
				.bind('lv_itemClick', this._on_itemClick.bind(this))
				.bind('lv_itemDblclick', this._on_itemDblClick.bind(this))
				.bind('lv_itemContextmenu', this._on_itemContextmenu.bind(this));
		} else {
		
			view = $(this.renderer.createView(model))
				.bind('lv_itemClick', this._on_itemClick.bind(this))
				.bind('lv_itemDblclick', this._on_itemDblClick.bind(this))
				.bind('lv_itemContextmenu', this._on_itemContextmenu.bind(this));
				
			$('td.xim-listview-item', view).each(function(id, item) {
			
				var data = $(item).data('data');
				if (data.isdir.value == '1') {
					
					$(item)
						.append(
							$('<div></div>')
								.addClass('xim-listview-item-dir-icon')
								.append(
									$('<span></span>')
										.addClass('ui-icon ui-icon-triangle-1-e')
								)
						);
				}
			});
		}

		for (var c=newColumn; c<this.columns.length; c++) {
			this.columns[c].empty();
		}
		this.columns[newColumn].append(view);
		
		if (column == 0) {
			this.widget._generateBrowser(model[0]);
		}
	},
	
	_loadFromSource: function(data, column) {

		if (!this.columns[column + 1]) return;
		var dstore = this.widget.options.datastore;
		
		var callback = function(store, data) {
			var model = data || store.get_model();
			this._loadModel(model, column);
			$(this).trigger('modelLoaded', [{model: model, column: column}]);
		}.bind(this);
		
		if (data.isdir.value == 0) {
			callback(dstore, [data]);
			return;
		}
		
		if (dstore.options.ds.running) return;

		dstore.load_data(
			{
				params: data,
				options: this.widget.options
			},
			callback
		);
	}

});
