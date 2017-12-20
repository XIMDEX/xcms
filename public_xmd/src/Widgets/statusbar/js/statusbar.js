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

	$.widget('ui.panel', {
		_init: function() {
			var widget = this;
			var $this = $(this.element[0]);
			$this
				.attr(this.options.htmlOptions)
				.addClass('xim-statusbar-panel')
				.click(function(event) {
					event.stopPropagation();
					$(this).trigger('itemClick', [{ui: widget, element: this}]);
				})
				.dblclick(function(event) {
					event.stopPropagation();
					$(this).trigger('itemDblclick', [{ui: widget, element: this}]);
				});
			this.update(this.options.content, this.options.afterUpdate);
		},
		update: function(content, callback) {
			content = content || this.options.content;
			var $this = $(this.element[0]);
			if (typeof(content) == 'object' && content['url']) {
				$.get(
					content.url,
					content.params,
					function(data, status) {
						$(this.element[0]).html(data);
						if (typeof(callback) == 'function') {
							callback(this);
						}
					}.bind(this),
					'html'
				);
			} else {
				$this.html($(content));
			}
		},

		options: {content: '<div></div>', htmlOptions: {}, afterUpdate: null}
	});

	$.widget('ui.statusbar', {
		_init: function() {
			this.options = $.extend($.ui.statusbar.defaults, this.options);
			var $this = $(this.element[0]);
			$this.addClass('xim-statusbar-container').data('panels', $('.xim-statusbar-panel', $this));
		},
		panels: function() {
			return $(this.element[0]).data('panels', $('.xim-statusbar-panel', $this));
		},
		addPanel: function(options) {
			var $this = $(this.element[0]);
			var panel = $('<div></div>').panel(options);
			$this.append(panel);
			$this.data('panels', $('.xim-statusbar-panel', $this));
			return panel;
		},
		getPanel: function(panel_id) {
			var $this = $(this.element[0]);
			var panel = $('div#'+panel_id+'.xim-statusbar-panel', $this);
			return panel;
		},
		removePanel: function(panel_id) {
			var $this = $(this.element[0]);
			var panel = $('div#'+panel_id+'.xim-statusbar-panel', $this).unbind();
			panel.remove();
		},
		
		options: {},
		getter: ['panels', 'addPanel', 'getPanel']
	});

})(jQuery);
