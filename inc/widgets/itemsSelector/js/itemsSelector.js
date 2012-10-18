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

	$.widget('ui.itemsSelector', {
		_init: function() {
			var $this = $(this.element);
			$this.attr('id', this.options.name + '_itemsSelector-container');
			$this.addClass('xim-itemsSelector ' + this.options.className);
			$this.append(
				$('<span></span>')
					.addClass('xim-itemsSelector-legend')
					.html(this.options.legend)
			);
			this._createPaginator();
			this.selectItem(this.options.defaultValue);
		},
		_createPaginator: function() {
			var ret = null;
			var method = '_createPaginator_' + this.options.view;
			if (this[method]) {
				ret = this[method]();
			}
			return ret;
		},
		_createItem: function(item) {
			var ret = null;
			var method = '_createItem_' + this.options.view;
			if (this[method]) {
				ret = this[method](item);
			}
			return ret;
		},
		getSelectedItem: function() {
			var $this = $(this.element);
			if (this.options.view == 'radio') {
				return $('input[checked=true]', $this).val();
			} else if (this.options.view == 'select') {
				return $('option[selected=true]', $this).val();
			}
		},
		selectItem: function(value) {
			var $this = $(this.element);
			if (this.options.view == 'radio') {
				$('input', $this).attr('checked', false);
				$('input[value='+value+']', $this).attr('checked', true);
			} else if (this.options.view == 'select') {
				$('option', $this).attr('selected', false);
				$('option[value='+value+']', $this).attr('selected', true);
			}
		},
		_createPaginator_radio: function() {
			var $this = $(this.element);
			var l = this.options.items.length;
			for (var i=0; i<l; i++) {
				var item = this._createItem(this.options.items[i]);
				$this.append(item.input);
				$this.append(item.label);
			}
		},
		_createPaginator_select: function() {
			var widget = this;
			var $this = $(this.element);
			var select = $('<select></select>')
				.appendTo($this)
				.change(function(event) {
					$(widget.element).trigger('itemClick', [{ui: widget, element: this, data: {value: $(this).val()}}]);
				});
			var l = this.options.items.length;
			for (var i=0; i<l; i++) {
				var item = this._createItem(this.options.items[i]);
				select.append(item);
			}
		},
		_createItem_radio: function(item) {
			var widget = this;
			var input = $('<input/>')
				.addClass('xim-itemsSelector-option')
				.attr({
					type: 'radio',
					name: this.options.name + '_itemsSelector-item'
				})
				.val(item.value)
				.click(function(event) {
					$(widget.element).trigger('itemClick', [{ui: widget, element: this, data: {value: $(this).val()}}]);
				});
			var label = $('<span></span>').addClass('xim-itemsSelector-optionlabel').html(item.text);
			return {input: input, label: label};
		},
		_createItem_select: function(item) {
			var widget = this;
			var option = $('<option/>')
				.addClass('xim-itemsSelector-option')
				.attr({
					name: this.options.name + '_itemsSelector-item'
				})
				.val(item.value)
				.html(item.value);
			return option;
		},
		
		options: {
			name: 'pag1',
			className: '',
			legend: 'Paginador:',
			defaultValue: 50,
			view: 'radio',				// [radio | select]
			items: [
				{value: 25, text: '25'},
				{value: 50, text: '50'},
				{value: 75, text: '75'},
				{value: 100, text: '100'}
			]
		},
		
		getter: ['getSelectedItem']
	});

})(jQuery);
