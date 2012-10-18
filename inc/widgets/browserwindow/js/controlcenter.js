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
 *  @version $Revision: 7858 $
 */


(function(X) {

	var B = X.browser;
	B.panels.leftTabs[1] = 'ControlCenterView';

	var idCount = 0;

	B.ControlCenterView = Object.xo_create(B.DataView, {

		_init: function(options) {

			B.ControlCenterView._construct(this, options);

			this.id = 'browser-ccenter-view-' + (++idCount);
			this.className = 'browser-ccenter-view';
			this.label = _("Control center");
			this._tabId = 1;

			this.content = $('<div></div>')
				.addClass('browser-view browser-ccenter-view-content')
				.attr({id: this.id});

				this.setViewTitle(_("Control center"));

			Object.getWidgetConf({
				wn: 'treeview',
				a: 'browser3',
				onComplete: function(data) {
					data.root = {
						name: {value:_("Control center"), visible: true},
						nodeid: {value: 2, visible: false},
						icon: {value: 'controlcenter.png', visible: true, type: 'image'},
						children: {value: 1, visible: false},	// Non zero!
						isdir: {value: 1, visible: false},
						path: {value: '/'+ _("Control center"), visible: false}
					};
					this.treeviewConf = data;
					this.createTreeview(data, 'browser-ccenter-view-treecontainer');
					this.continueInit();
				}.bind(this)
			});

		},

		continueInit: function() {

			this.createMenubar();
			$(this.treeview).show();
			this.setSelectedNodes(
				X.session.get('%s.selectedItems'.printf($(this.treeview).treeview('getId'))) || []
			);
		},

		setSelectedNodes: function(nodes) {
			if (nodes.length > 0) {
				$(this.treeview).treeview('navigate_to_idnode', nodes[0].replace(/treeview-nodeid-/, ''));
			}
		},

		createMenubar: function() {

			this.menubar = $('<div></div>')
				.addClass('browser-projects-view-menubar')
				.appendTo(this.content);
		}

	});

})(com.ximdex);
