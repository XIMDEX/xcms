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

X.listview.ListviewRenderer_Icon = Object.xo_create(X.listview.ListviewRenderer, {

	RENDER_TYPE: 'ICON_RENDERER',
	
	createView: function(model, listeners) {

		// TODO: Parametrize this option
		var cols = 5;
		var col = cols - 1;

		var widget = this.widget;
		var container = $('<table></table>').addClass('xim-listview-table');
		var row = null;
		
		for (var i=0; i<model.length; i++) {

			var data = model[i];
			col++;
			if (col == cols) {
				row = $('<tr></tr>').appendTo(container);
				col = 0;
			}
			
			$(row).append(this._createCell(data));
			row = this._callCreateNodeListeners(row, listeners);
		}
		
		return container;
	},
	
	_createCell: function(data) {

		var widget = this.widget;
		var cell = null;

		cell = $('<td></td>')
			.addClass('xim-listview-item xim-listview-icon-view')
			.attr('id', 'listview-nodeid-'+data.nodeid.value);
		
		var icon = widget.options.img_base + data.icon.value;
		var cadena = data.icon.value.substring(0, data.icon.value.length - 4 );
		cell
			.append(
				$('<div></div>')
					.addClass('xim-listview-item-icon')
					
					.append(
						$('<span></span>')
							//.attr('src', icon)
							.addClass('xim-listview-icon xim-listview-icon-icon')
							.addClass('icon-' + cadena)
					)
				)
			.append(
				$('<div></div>')
							.addClass('xim-listview-item-label')
					.append(
						$('<div></div>')
							.html(data.name.value)
					)
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
				$(widget).trigger(
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
				// NOTE: "contextmenu" widget needs the original event.
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

		return cell;
	}
	
});
