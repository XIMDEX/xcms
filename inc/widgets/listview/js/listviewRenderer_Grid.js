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
 *  @version $Revision: 7842 $
 */

X.listview.ListviewRenderer_Grid = Object.xo_create(X.listview.ListviewRenderer, {

	RENDER_TYPE: 'GRID_RENDERER',
	
	createView: function(model, listeners) {

		$('.t_fixed_header_main_wrapper').remove();
		var widget = this.widget;
		var container = $('<table></table>').addClass('xim-listview-table');//.addClass('resizable').attr('id','xim-listview-table');
		container.attr('id','xim-listview-table');
		/*var colgroups = $('<colgroup></colgroup>');
		for (var i=0; i<this.widget.options.colModel.length; i++) {
			colgroups.append('<col/>');
		}
		colgroups.appendTo(container);*/
		var theader = $('<thead></thead>').appendTo(container);
		//tbody = $('<tbody></tbody>').appendTo(container);
		var header = $('<tr></tr>')
			.addClass('xim-listview-item')
			.appendTo(theader);
		
		for (var i=0; i<this.widget.options.colModel.length; i++) {
			
			var col = this.widget.options.colModel[i];
			
			if (col.visible) {
				
				var th = null;
				
				if (!col.sortable) {
					th = $('<th></th>').html(col.label);
				} else {
					
					var $a = $('<a></a>').html(col.label).attr('href', '#').bind("click", {columna: col}, function(e) {
						
						// NOTE: Sort order is TRUE = ASC, FALSE = DESC
						
						var order = widget.sorts[e.data.columna.name];
						order = Object.isEmpty(order) ? true : !order;
						widget.sorts = {};
						widget.sorts[e.data.columna.name] = order;
						
						$(widget).trigger('lv_columnSort', [{column: e.data.columna, order: order}]);
						
						return false;
					});
					
					th = $('<th></th>').append($a);
				}
				
				if (col.width) $(th).css('width', col.width);
				if (col.align) $(th).css('text-align', col.align);
				header.append(th);
				
				if (!Object.isEmpty(widget.sorts[col.name])) {
					var cname = widget.sorts[col.name] ? 'sort-asc' : 'sort-desc';
					th.addClass(cname);
				}
			}
		}
		
		var tbody = $('<tbody></tbody>').appendTo(container);

		for (var i=0; i<model.length; i++) {

			var data = model[i];
			var row = $('<tr></tr>')
				.data('data', data)
				.addClass('xim-listview-item')
				.attr('id', 'listview-nodeid-'+data.nodeid.value);

			for (var c=0; c<widget.options.colModel.length; c++) {
				var col = widget.options.colModel[c];
				var field = data[col.name] || data[col.alias];
				if (col.visible) {
//					console.log(field.name, field);
					$(row).append(this._createCell(field));
				}
			}
			
			row = this._callCreateNodeListeners(row, listeners);

			$(row)
				.mousedown(function(event) {
					event.stopPropagation();
					widget.selected = this;
					var data = $(this).data('data');
					$(widget).trigger(
						'lv_itemMousedown',
						[{
							ui: widget,
							element: this,
							data: data,
							originalEvent: event
						}]);
				})
				.mouseup(function(event) {
					var data = $(this).data('data');
					$(widget).trigger(
						'lv_itemMouseup',
						[{
							ui: widget,
							element: this,
							data: data,
							originalEvent: event
						}]);
				})
				.click(function(event) {
					event.stopPropagation();
//						$('div.xim-contextmenu-container').remove();
					widget.selected = this;
					var data = $(this).data('data');
					$(widget).trigger(
						'lv_itemClick',
						[{
							ui: widget,
							element: this,
							data: data,
							originalEvent: event
						}]);
				})
				.dblclick(function(event) {
					event.stopPropagation();
					var data = $(this).data('data');
					$(widget).trigger(
						'lv_itemDblclick',
						[{
							ui: widget,
							element: this,
							data: data,
							originalEvent: event
						}]);
				})
				.bind('contextmenu', function(event) {
					var data = $(this).data('data');
					// NOTE: "contextmenu" widget needs the original event.
					$(event.target).trigger('itemContextmenu', [{ui: widget, element: event.target, data: data, originalEvent: event}]);
					$(widget).trigger(
						'lv_itemContextmenu',
						[{
							ui: widget,
							element: event.target,
							data: data,
							originalEvent: event
						}]);
					return false;
				}.bind(this))
				.appendTo(tbody);
		}
		
		if(model.length==0 && listeners.length>0){
			var res = $('<div></div>').html('<span class="ui-icon ui-icon-info"></span><p style="padding-top: 5px;"> &nbsp;No hay coincidencias.</p>');
			return res;
		}
		else if(model.length==0 && listeners.length==0){
			var res = $('<div></div>');
			return res;
		}

		return container;
	},
	
	_createCell: function(field) {

		if (!field) return $('<td></td>').addClass('xim-listview-grid-view');
			
		var widget = this.widget;
		var cell = null;

		if (field.visible) {
			cell = $('<td></td>').addClass('xim-listview-grid-view');
			if (field.type == 'image') {
				var icon = widget.options.img_base + field.value;
				var cadena = field.value.substring(0, field.value.length - 4 );
				$('<span></span>')
					//.attr('src', icon)
					.addClass('xim-listview-icon xim-listview-grid-icon')
					.addClass('icon-' + cadena)
					.appendTo(cell);
			} else {
				// Workaround?
				// Avoid to show a XVFS path as a nodeId
				var text = (field.name == 'nodeid' && isNaN(field.value)) ? '&nbsp;' : field.value;
				if(text!=null){
					cell.html('<span>'+text+'</span>');
				}
				else{
					cell.html('<span></span>');
				}
			}
			$(cell).css('text-align', field.align);
			$(cell).data('data', field);
			
			// NOTE: Necesary for sets dran&drops
			if (field.name == 'name') {	
				cell.addClass('xim-listview-label');
			} else if (field.name == 'icon') {	
				cell.addClass('xim-listview-grid-icon');
			}
		}

		return cell;
	}
});
