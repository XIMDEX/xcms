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

(function(X) {

	X.browser.DataView = Object.xo_create(X.browser.AbstractView, {

		treeview: null,
		listview: null,

		/*_init: function(options) {
			X.browser.DataView._construct(this, options);
		}*/

		checkNodeHasActions: function(data) {
			var ret = false;
			data.hasActions = data.hasActions || {value: 0};
			ret = parseInt(data.hasActions.value) > 0;
			return ret;
		},

		checkNodeIntersectionHasActions: function(nodes) {

			$('.xim-actions-dropdown', this.content).css('display', '');
			var actions = true;

			for (var i=0,l=nodes.length; i<l; i++) {

				if (Object.isEmpty(nodes[i])) continue;

				var data = $(nodes[i]).data('data');
				if (Object.isEmpty(data)) continue;

				data.hasActions = data.hasActions || {value: 0};
				if (parseInt(data.hasActions.value) == 0) {
					actions = false;
					break;
				}
			};

			if (!actions) {
				nodes.each(function(index, node) {
					$('.xim-actions-dropdown', node).css('display', 'none');
				});
			}
		},

		createTreeview: function(conf, className) {

			this.treeview = $('<div></div>')
				.addClass(className)
				.appendTo(this.content)
				.hide();

			this.treeview
				.treeview(conf)
				.bind('itemClick', function(event, params) {

					var selection = this.treeview.treeview('getSelection').get();
					this.checkNodeIntersectionHasActions(selection);
					var nodes = [];

					nodes = $.map(selection, function(item, index) {
						return $(item).attr('id');
					});

					X.session.set(
						'%s.selectedItems'.printf(this.treeview.treeview('getId')),
						nodes
					);
					// NOTE: Connecting listview and treeview
					$(this.listview).listview('loadByNodeId', params.element);

				}.bind(this))
				.bind('select', function(event, params) {
					$('.destroy-on-click').unbind().remove();
				}.bind(this))
				.bind('actionsDropdown', function(event, params) {

					var selector = params.event.target;
					var selection = params.selection;

					var nodes = [];
					selection.each(function(index, item) {
						nodes.push($(item).data('data'));
					});

					// Get the selector position before the Ajax request...
					var pos = $(selector).offset();
					pos = {x: pos.left + $(selector).width(), y: pos.top};

					this.browser.browserwindow('getActions', {
						nodes: nodes,
						cb: this.createFloatMenu.bind(this),
						data: $(params.element).data('data'),
						selector: selector,
						menuPos: pos
					});
				}.bind(this))
				.bind('itemContextmenu', function(event, params){
					var selector = params.event.target;
					var selection = params.selection.collection;

					var nodes = [];
					selection.each(function(index, item) {
						nodes.push($(item).data('data'));
					});

					// Get the selector position before the Ajax request...
					var pos = $(selector).offset();
					//pos = {x: pos.left + $(selector).width(), y: pos.top};
					pos = {x: params.event.pageX+10, y: pos.top};

					this.browser.browserwindow('getActions', {
						nodes: nodes,
						cb: this.createFloatMenuList.bind(this),
						data: $(params.element).data('data'),
						selector: selector,
						menuPos: pos
					});
					//return false;
			}.bind(this)
			);

			$(this.treeview).treeview('addCreateNodeListener', function(widget, node) {

				var data = $(node).data('data');
				if (!this.checkNodeHasActions(data)) {
					return node;
				}

				$('<span></span>')
					.addClass('xim-actions-dropdown xim-treeview-actions-dropdown')
					.addClass('ui-icon ui-icon-triangle-1-e')
					.click(function(event) {

						var w = this.element;
						var target = event.target;
						var isSelected = $(target).closest('div').hasClass('xim-treeview-container-selected');
						var selection = [node];

						if (isSelected) {
							// Stoping propagation here prevent the clear of selected nodes
							event.stopPropagation();
							selection = $(w).treeview('getSelection').get();
						}

						w.trigger('actionsDropdown', [{
							ui: w,
							element: node,
							data: data,
							event: event,
							selection: selection
						}]);
					}.bind(widget))
					.appendTo($('div', node));

				return node;
			}.bind(this));

			var tds = $(this.treeview).treeview('getDatastore');
			tds.clear();
			tds.append(Object.clone(conf.root));
			$(this.treeview).treeview('setRootModel', tds.get_model(), false, true);
		},

		createListview: function(conf, className) {

			this.listview = $('<div></div>')
				.addClass(className)
				.appendTo(this.content)
				.hide();

			this.listview
				.listview(conf)
				.bind('itemClick', function(event, params) {
					//$('div.xim-contextmenu-container').remove();
					var selection = this.listview.listview('getSelection').get();
					this.checkNodeIntersectionHasActions(selection);
				}.bind(this))
				.bind('itemDblclick', function(event, params) {
					//this.history.add(params.data);
					$(this.listview).listview('loadByNodeId', params.element);
					// NOTE: Connecting listview and treeview
					$(this.treeview).treeview('navigate_to_idnode', params.data.nodeid.value);
				}.bind(this))
				.bind('actionsDropdown', function(event, params) {

					var selector = params.event.target;
					var selection = params.selection;

					var nodes = selection;

					// Get the selector position before the Ajax request...
					var pos = $(selector).offset();
					pos = {x: pos.left + $(selector).width(), y: pos.top};

					this.browser.browserwindow('getActions', {
						nodes: nodes,
						cb: this.createFloatMenu.bind(this),
						data: $(params.element).data('data'),
						selector: selector,
						menuPos: pos
					});
				}.bind(this))//;
//				.bind('afterSetModel', function(event, params) {
					//params.data = $(params.element).listview('getParent');
					//$(this.element).trigger(this.LISTVIEW_LOADED, [params]);
//				}.bind(this))
//				.bind('itemDrop', function(event, params) {
					/*var setid = $(this.listview).data('setid');
					if (setid !== false) {
						$(this.element).trigger(
							this.SETBUTTON_DROP,
							[{
								setid: setid,
								selection: [params.data.nodeid.value]
							}]
						);
					}*/
//				}.bind(this))
				.bind('itemContextmenu', function(event, params) {

					var nodes = [];
					var selection = $(this.listview).listview('getSelection').asArray();
					$.each(selection, function(index, item) {
						nodes.push(item.nodeid.value);
					});

					$(this.element).trigger(
						this.CONTEXTMENU_CLICK,
						[{
							ui: event.target,
							caller: 'actions',
							model: [],
							nodes: nodes,
							originalEvent: params.originalEvent
						}]
					);
			}.bind(this));
			$(this.listview).listview('addCreateNodeListener', function(widget, node) {

				var data = $(node).data('data');
				if (!this.checkNodeHasActions(data)) {
					return node;
				}

				$('<span></span>')
					.addClass('xim-actions-dropdown xim-listview-actions-dropdown')
					.addClass('ui-icon ui-icon-triangle-1-e')
					.mousedown(function(event) {

						var w = this.element;
						var target = event.target;
						var isSelected = $(target).closest('.xim-listview-selected').length > 0 ? true : false;
						var selection = [$(node).data('data')];

						if (isSelected) {
							// Stoping propagation here prevent the clear of selected nodes
							event.stopPropagation();
							selection = $(w).listview('getSelection').get();
						}

						w.trigger('actionsDropdown', [{
							ui: w,
							element: node,
							data: data,
							event: event,
							selection: selection
						}]);
					}.bind(widget))
					.appendTo($('.xim-listview-icon', node).parent());

				return node;
			}.bind(this));

			var tds = $(this.listview).listview('getDatastore');
			var data = Object.clone(conf.root);
			tds.clear();
			tds.append(data);
			$(this.listview).listview('loadFromSource', tds, data);
		}

	});

})(com.ximdex);
