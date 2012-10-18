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

X.listview.ListviewRenderer_Details = Object.xo_create(X.listview.ListviewRenderer, {

	RENDER_TYPE: 'DETAILS_RENDERER',
	
	createView: function(model) {

		var widget = this.widget;
		var container = $('<div></div>').addClass('xim-listview-details-container');
		var imgContainer = $('<div></div>').addClass('xim-listview-details-imgContainer').appendTo(container);
		var detailsContainer = $('<div></div>').addClass('xim-listview-details-detailsContainer').appendTo(container);

		$('<img></img>')
			.attr('src', this.getImageURL(model))
			.appendTo(imgContainer);
		$('<div></div>')
			.html(model.name.value)
			.appendTo(detailsContainer);

		container
			.bind('click', function(event) {
				$(this).trigger('lv_itemClick', [{ui: this.widget, element: event.target, data: model, originalEvent: event}]);
			})
			.bind('dblclick', function(event) {
				$(this.widget).trigger('lv_itemDblclick', [{ui: this.widget, element: event.target, data: model, originalEvent: event}]);
			})
			.bind('contextmenu', function(event) {
				// NOTE: "contextmenu" widget needs the original event.
				$(this.widget).trigger('lv_itemContextmenu', [{ui: this.widget, element: event.target, data: model, originalEvent: event}]);
				return false;
			}.bind(this));
		
		return container;
	},
	
	getImageURL: function(model) {
	
		if (model.isdir.value == '1') return;
		
		var url = '';
		var ext = model.name.value.substr(-4);
		if (['.png', '.jpg', '.gif'].contains(ext)) {
			url = window.url_root + '/data/nodes' + model.path.value.replace(/\/Proyectos/, '');
		} else {
			url = this.widget.options.img_base + '/' + model.icon.value;
		}
		
		return url;
	}
});
