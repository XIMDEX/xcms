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
 *  @version $Revision: 8294 $
 */




(function(X) {

	var B = X.browser;
	B.panels.leftTabs[0] = 'ProjectsView';

	var idCount = 0;

	B.ProjectsView = Object.xo_create(B.DataView, {

		pendingConf: 2,
		currentView: null,

		_init: function(options) {

			B.ProjectsView._construct(this, options);

			this.id = 'browser-projects-view-' + (++idCount);
			this.className = 'browser-projects-view';
			this.label = _("Projects");
			this._tabId = 0;

			this.content = $('<div></div>')
				.addClass('browser-view browser-projects-view-content')
				.attr({id: this.id});

			this.setViewTitle(_("Projects"));

			Object.getWidgetConf({
				wn: 'listview',
				a: 'browser3',
				onComplete: function(data) {
					if (data.datastore) (--this.pendingConf);
					this.listviewConf = data;
					this.createListview(data, 'browser-projects-view-listcontainer');
					this.continueInit();
				}.bind(this)
			});

			Object.getWidgetConf({
				wn: 'treeview',
				a: 'browser3',
				onComplete: function(data) {
					if (data.datastore) (--this.pendingConf);
					this.treeviewConf = data;
					this.createTreeview(data, 'browser-projects-view-treecontainer');
					this.continueInit();
				}.bind(this)
			});

		},

		onLink: function() {
			var changeView = function(event, params) {
				if (!params.event.ctrlKey) return;
				var views = [null, 'Tree', 'Grid', 'List'];
				var view = views[parseInt(params.char)];
				this.setView(view);
			}.bind(this);
			$(this.browser).bind('project-view-tree', changeView);
			$(this.browser).bind('project-view-grid', changeView);
			$(this.browser).bind('project-view-list', changeView);
		},

		continueInit: function() {

			if (this.pendingConf > 0) return;
			this.createMenubar();
			this.setView(X.session.get('projectsview.view') || 'Tree');
			var nodes = X.session.get('%s.selectedItems'.printf($(this.treeview).treeview('getId'))) || [];
			this.setSelectedNodes(nodes);
//			    $.getJSON(
//				X.restUrl + '?method=getDefaultNode&ajax=json',
//				function(data) {
//
//				    	var node_list = data['nodes'];
//					nodes = new Array();
//
//					if(node_list && node_list.length>0 && node_list[0]["IdNode"]) {
//						nodes[0] = node_list[0]["IdNode"];
//				    	}
//					else{
//						nodes = X.session.get('%s.selectedItems'.printf($(this.treeview).treeview('getId'))) || [];
//					}
//					this.setSelectedNodes(nodes);
//				}.bind(this)
//			    );		

		},

		setView: function(view) {

			// tree, grid, list
			if (this.currentView == view) return;
			this.currentView = view;

			if (view == 'Tree') {

				//var model = $(this.listview).listview('getParent');
				//$(this.treeview).treeview('setModel', model);

				$(this.listview).hide();
				$(this.treeview).show();

			} else if (['Grid', 'List'].contains(view)) {

				//var model = $(this.treeview).treeview('getModel');
				//$(this.listview).listview('setModel', model);

				$(this.treeview).hide();
				$(this.listview).show();
				$(this.listview).listview('setRenderer', view);

			} else {
				this.currentView = null;
			}

			if (this.currentView !== null) {
				X.session.set('projectsview.view', this.currentView);
			}
		},

		setSelectedNodes: function(nodes) {
			if (nodes.length > 0) {
				$(this.treeview).treeview('navigate_to_idnode', nodes[0].replace(/treeview-nodeid-/, ''));
				$(this.listview).listview('loadByNodeId', nodes[0]);
			}

			$("#ximdex-splash .progress").width("85%");
		},

		createMenubar: function() {

			this.menubar = $('<div></div>')
				.addClass('browser-projects-view-menubar')
				.appendTo(this.content);

			var viewGroup = $('<div></div>')
				.addClass('window-toolbar-buttongroup view-group')
				.appendTo(this.menubar);

			[
				{text: _("Tree"), value: 'Tree'},
				{text: _("Grid"), value: 'Grid'},
				{text: _("List"), value: 'List'}
			]
			.each(function(index, item) {
				var b = this.createButton({
					text: item.text,
					value: item.value,
					data: item,
					className: 'view-button',
					icon: Object.isString(item.icon) ? '%s/%s'.printf(X.browser.imagesUrl, item.icon) : null,
					click: function(e) {
						this.setView($(e.currentTarget).val());
					}.bind(this),
					container: viewGroup
				});

			}.bind(this));
		}

	});

})(com.ximdex);
