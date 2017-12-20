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


globalContext = null;
(function($) {
	$.widget('ui.canvas', {
		widgets: null,
		_init: function () {
			this.widgets = new Object();
			globalContext = this.element;
		},
		add: function(context, url, search_key) {
			if (url == null || url == 'undefined') {
				url = window.location.href;
			}
			qm = new query_manager(url);
			if (search_key) {
				var key = this._contextualize(context, search_key);
				if (key != '') {
					this.widgets[key] = $('#' + key).canvas_i({qm: qm});
					return true;
				} else {
					return false;
				}
			}
			this.widgets[$(context).attr('id')] = $('#' + $(context).attr('id')).canvas_i({qm: qm});
		},
		get_widget: function(element) {
			return this.widgets[this._contextualize(element)];
		},
		/* Contextualize chain to estimate a widget */
		_contextualize: function (element, search_key) {
			widget_key = this._check_for_key(element, search_key);
			if (widget_key.length > 0) {
				return widget_key;
			}
			key = null;
			// backtrace to find the widget 
			parents = $(element).parents();
			array_length = parents.length;
			for (i = 0; i < array_length; i++)  {
				key = this._check_for_key($(parents[i]), search_key);
				if (key != '') return key;
			}
			return '';
		},
		_check_for_key: function(element, search_key) {
			if (search_key) {
				if ($(element).attr('id') != '' && $(element).attr('id') != undefined) {
					return $(element).attr('id');
				} else {
					return '';
				}
			}
			for (key in this.widgets) {
				if (key == $(element).attr('id')) {
					return key;
				}
			}
			return '';
		}
		
	});
	
	$.ui.canvas.getter = ['get_widget'];
	
})(jQuery);


function addWidget(context) {
	if (globalContext == null) {
		$('body').canvas();
	}
	
	$(globalContext).canvas('add', context, '', true);
}

function getWidget(element) {
	if (globalContext == null) {
		$('body').canvas();
	}
	var widget = $(globalContext).canvas('get_widget', element);
	if (!widget) {
		addWidget(element);
		widget = $(globalContext).canvas('get_widget', element);
	}
	return widget;
}
