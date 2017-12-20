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

X.listview.ListviewRenderer_List = Object.xo_create(X.listview.ListviewRenderer, {

	RENDER_TYPE: 'LIST_RENDERER',
	
	createView: function(model, listeners) {
		
		var container = $('<ul></ul>').addClass('xim-listview');
		for (var i=0; i<model.length; i++) {

			var data = model[i];
//			var row = $('<ul></ul>').appendTo(container);
			$(container).append(this._createCell(data, listeners));
		}
		
		return container;
	},
	
	_createCell: function(data, listeners) {

		var widget = this.widget;
		var cell = null;

		cell = $('<li></li>')
			.addClass('xim-listview-node xim-listview-list-view')
			.attr('id', 'listview-nodeid-'+data.nodeid.value);
		
		var icon = widget.options.img_base + data.icon.value;
		var cadena = data.icon.value.substring(0, data.icon.value.length - 4 );
		cell
			.append(
				//$('<div></div>')
					//.addClass('xim-listview-item-icon')
					//.append(
						$('<span></span>')
							//.attr('src', icon)
							.addClass('xim-listview-icon xim-listview-icon-list')
							.addClass('icon-' + cadena)
					//)
				)
			.append(
				//$('<div></div>')
					//.addClass('xim-listview-item-label')
					//.append(
						$('<span></span>')
							.html(data.name.value)
						//)
				);

		$(cell)
			.data('data', data)
			.mousedown(function(event) {
				event.stopPropagation();
				widget.selected = this;
				$(widget).trigger(
					'lv_itemMousedown',
					[{
						ui: widget,
						element: this,
						data: $(this).data('data'),
						originalEvent: event
					}]);
			})
			.mouseup(function(event) {
				$(widget).trigger(
					'lv_itemMouseup',
					[{
						ui: widget,
						element: this,
						data: $(this).data('data'),
						originalEvent: event
					}]);
			})
			.click(function(event) {
				event.stopPropagation();
				widget.selected = this;
				$([widget, this]).trigger(
					'lv_itemClick',
					[{
						ui: widget,
						element: this,
						data: $(this).data('data'),
						originalEvent: event
					}]);
			})
			.dblclick(function(event) {
				event.stopPropagation();
				var data = $(this).data('data');
				$([widget, this]).trigger(
					'lv_itemDblclick',
					[{
						ui: widget,
						element: this,
						data: data,
						originalEvent: event
					}]);
			})
			.bind('contextmenu', function(event) {
				// NOTE: "contextmenu" widget needs the original event.
				$([widget, this]).trigger(
					'lv_itemContextmenu',
					[{
						ui: widget,
						element: event.target,
						data: data,
						originalEvent: event
					}]);
				return false;
			}.bind(this));

			
		cell = this._callCreateNodeListeners(cell, listeners);
		return cell;
	}

});
