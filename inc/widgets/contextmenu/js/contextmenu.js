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


(function($) {

	$.widget('ui.contextmenu', {

		items: null,

		_init: function() {
			var widget = this;
			var $this = this.element;
			$this.hide();
			this.items = [];
			$this.addClass('xim-contextmenu-container');
//			$(document)
//				.click(function() {
//					widget.hide();
//				})
//				.bind('contextmenu', function() {
//					widget.show();
//					return false;
//				});
			$(document)
				.click(this._onDocumentClick.bind(this))
				.bind('contextmenu', this._onDocumentContextMenu.bind(this));
		},
		_onDocumentClick: function() {
			this.hide();
		},
		_onDocumentContextMenu: function() {
			this.show();
			return false;
		},
		show: function(options) {
			var $this = this.element;
			options = $.extend({
				event: null,
				model: []
			}, options);
			if (options.model) this.setModel(options.model);
			if (options.event) {
				var left = options.event.pageX;
				var top = options.event.pageY;
				$this.css({
					top: top + 'px',
					left: left + 'px'
				});
			}
			$this.show();
		},
		hide: function() {
			var $this = this.element;
			//$this.hide();
			$this.contextmenu('destroy')
				.unbind()
//				.unbind('click', this._onDocumentClick)
//				.unbind('contextmenu', this._onDocumentContextMenu)
				.remove();
		},
		setModel: function(model) {

			var widget = this;
			var $this = this.element;
			if (!model) model = [];

			// Important! unbind()
			$this.unbind().empty();

			for (var i=0; i<model.length; i++) {

				var data = model[i];
				$this.append(
					$('<div></div>')
						.click(function (event) {
							$(this).trigger('menuItemClick', [{ui: widget, element: $(this), data: $(this).data('data')}]);
						})
						.data('data', data)
						.addClass('xim-contextmenu-item ui-widget ui-corner-all ui-widget-content main')
						.append($('<img></img>').attr('src', this.options.img_base + '/' + data.icon.value))
						.append($('<span></span>').html(data.name.value))
				);
//console.log($this);
				$('div, img, span', $this)
					.mouseenter(function (event) {
						$(event.target).closest('div').addClass('hover ui-state-hover');
					})
					.mouseout(function (event) {
						$(event.target).closest('div').removeClass('hover ui-state-hover');
					});
			}
		},
		getOptions: function() {
			return this.options;
		},
		
		options: {
			url_base: '',
			img_base: '',
			loading_icon: '/actions/browser3/resources/images/loading.gif',
			colModel: null
		},
		
		getter: ['getOptions']
	});

})(jQuery);
