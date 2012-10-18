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

	$.widget('ui.paginator', {
		_init: function() {
			var $this = $(this.element);
			$this.addClass('xim-paginator-container');
		},
		loadOptions: function(options) {
			this.element.unbind().empty();
			this.options = $.extend({}, this.options, options);
			this.options.pages = parseInt(this.options.pages);
			this.options.selected = parseInt(this.options.selected);
			this._createPaginator();
		},
		_createPaginator: function() {

			if (this.options.pages < 2) return;

			var first = this.options.selected - 2;
			first = first < 1 ? 1 : first;
			var last = this.options.selected + 2;
			last = last > this.options.pages ? this.options.pages : last;
			if (last-first > 3) last--;

//console.log('first: %s, last: %s, pages: %s, selected: %s', first, last, this.options.pages, this.options.selected);

			this._createPrevious();
			if (first > 1) {
				this._createItem('...', first-1);
			}
			for (var i=first; i<=last; i++) {
				this._createItem(i, i);
			}
			if (last < this.options.pages) {
				this._createItem('...', last+1);
			}
			this._createNext();
		},
		_createPrevious: function() {
			if (this.options.selected-1 < 1) return;
			this._createItem('<', (this.options.selected-1));
		},
		_createNext: function() {
			if (this.options.selected+1 > this.options.pages) return;
			this._createItem('>', (this.options.selected+1));
		},
		_createItem: function(text, value, active) {

			active = active || true;
			var widget = this;
			var $this = $(this.element);

			var page = $('<div></div>')
				.addClass('xim-paginator-item xim-paginator-page ui-state-default')
				.html(text)
				.data('page', value)
				.appendTo($this);

			if (this.options.selected == value) {
				page.addClass('xim-paginator-selected-item ui-state-hover');
				active = false;
			}

			page.hover(
				function() {
					page.addClass('ui-state-hover');
				},
				function() {
					if (!page.hasClass('xim-paginator-selected-item')) page.removeClass('ui-state-hover');
				}
			);

			if (active) {
				page.click(function() {
					$this.trigger('pageClick', [{ui: widget, element: widget.element, page: $(this).data('page')}]);
				});
			}
		},
		getSelectedPage: function() {
			return this.options.selected;
		},
		selectPage: function(value) {

		},
		
		options: {
			name: 'pag1',
			className: '',
			pages: 0,
			selected: 0
		},
		
		getter: ['getSelectedPage']
	});


})(jQuery);
