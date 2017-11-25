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

	$.widget('ui.treeview', {
		root: null,
		selected: null,
		_init: function() {
			var $this = this.element;
			if (!this.options['rowModel']) this.options.rowModel = new TreeModel(['col1']);
			this.options.rowModel.append(null, {
				text: 'Proyectos',
				nodeid: 10000
			});

			$this.addClass('xim-treeview-container');
			this.setModel(this.options.rowModel);
		},
		_createRoot: function() {
			var $this = this.element;
			var branch = $('<ul></ul>').addClass('xim-treeview-branch');
			var root = this._createNode('root', this.options.rowModel.get_value('root'));
			branch.append(root);
			$this.append(branch);
			return branch;
		},
		_createBranch: function(parent) {
			var $this = this.element;
			var model = this.options.rowModel;
			var iter = model.get_iter($(parent).data('path'));

			var branch = $('<ul></ul>').addClass('xim-treeview-branch');
			model.iter_children(iter, {
				params: {
					method: 'treedata',
					nodeid: $(parent).data('nodeid'),
					nelementos: '50'
				},
				callback: function(it) {
					while (it) {
						var data = model.get_value(it);
						if (data) branch.append(this._createNode(it.path, data));
						it = model.iter_next(it);
					}
					$(parent).append(branch);
				}.bind(this)
			});
		},
		_createNode: function(path, data) {
			var node = $('<li></li>')
				.addClass('xim-treeview-node xim-treeview-collapsed')
				.append(this._createLabel(data));

			node.data('widget', this);
			node.data('path', path);
			node.data('nodeid', data.nodeid);
			node
				.click(function(event) {event.stopPropagation();$(this).data('widget').select(this);})
				.dblclick(function(event) {event.stopPropagation();$(this).data('widget').toggle(this);node.focus();});
			return node;
		},
		_createImage: function(data) {
			image = $('<img></img>').attr('src', this.options.url_base + '../' + data['icon']);
			return image;
		},
		_createLabel: function(data) {
			var $this = $(this);
			var labelClass = (data['isdir'] == 1) ? ('ui-icon ' + this.options.collapsed_icon) : ('ui-icon ui-icon-arrow-1-e');
			var lbl = $('<div></div>');
			lbl.append(
				$('<span></span>')
					.addClass(labelClass)
					.click(function(event) {
						event.stopPropagation();
						var parent = $(this).parents('.xim-treeview-node:first');
						parent.data('widget').toggle(parent);
					})
			);
			lbl.append(this._createImage(data));
			lbl.append($('<span></span>').addClass('xim-treeview-label').html(data.text));
			return lbl;
		},
		getModel: function() {
			return this.options.rowModel;
		},
		setModel: function(model) {
			if (this.root) $(this.root).unbind().remove();
			this.options.rowModel = model;
			this.root = this._createRoot();
			this.expand($('li.xim-treeview-node:first', this.root));
		},
		toggle: function(node) {
			var widget = $(node).data('widget');
			if ($(node).hasClass('xim-treeview-expanded')) {
				widget.collapse(node);
			} else {
				widget.expand(node);
			}
		},
		expand: function(node) {
			var widget = $(node).data('widget');
			if ($('ul.xim-treeview-branch:first', node).length == 0) {
				widget._createBranch(node);
			}
			$('ul.xim-treeview-branch:first', node).show();
			$(node)
				.removeClass('xim-treeview-collapsed')
				.addClass('xim-treeview-expanded');
			$('.ui-icon:first', node)
				.removeClass(widget.options.collapsed_icon)
				.addClass(widget.options.expanded_icon);
			var data = widget.options.rowModel.get_value($(node).data('path'));
			$(node).trigger('expand', [{ui: widget, element: node, data: data}]);
		},
		collapse: function(node) {
			var widget = $(node).data('widget');
			$('ul.xim-treeview-branch:first', node).hide();
			$(node)
				.removeClass('xim-treeview-expanded')
				.addClass('xim-treeview-collapsed');
			$('.ui-icon:first', node)
				.removeClass(widget.options.expanded_icon)
				.addClass(widget.options.collapsed_icon);
			var data = widget.options.rowModel.get_value($(node).data('path'));
			$(node).trigger('collapse', [{ui: widget, element: node, data: data}]);
		},
		select: function(node) {
			var widget = $(node).data('widget');
			if (widget.selected) {
				$('.xim-treeview-label:first', widget.selected).removeClass('xim-treeview-selected');
			}
			widget.selected = node;
			$('.xim-treeview-label:first', widget.selected).addClass('xim-treeview-selected');
			var data = widget.options.rowModel.get_value($(node).data('path'));
			$(node).trigger('select', [{ui: widget, element: node, data: data}]);
		},
		getSelected: function() {
			return this.selected;
		}
	});

	$.ui.treeview.defaults = {
		rowModel: null,
		colModel: null,
		rootLabel: 'ximdex',
		collapsed_icon: 'ui-icon-triangle-1-e',
		expanded_icon: 'ui-icon-triangle-1-se',
		url_base: '',
		/*collapsed_icon: 'ui-icon-carat-1-e',
		expanded_icon: 'ui-icon-carat-1-se'*/
	};
	$.ui.treeview.getter = ['getSelected', 'getModel'];

})(jQuery);
