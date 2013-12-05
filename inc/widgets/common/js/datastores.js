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


function buttonbar_ColModel() {
	return [
			{name: 'actionid', label: 'ActionId', visible: true, type: 'number'},
			{name: 'name', label: 'Name', visible: true},
			{name: 'icon', label: '', visible: true, type: 'image'},
			{name: 'description', label: 'Description', visible: false}
		];
}

function buttonbar_DataStore() {
	if (!widgetsVars.getValue('action_buttons', 'useXVFS')) {
		return new DataStore({
			ds: new DataSource({
				method: 'get',
				type: 'xml',
				url:  X.restUrl + '?method=toolbardata'
			}),
			colModel: buttonbar_ColModel(),
			queryParams: function(params, options) {
				return {
					nodeid: params.nodeid.value
				};
			},
			selector: 'selection node role action'
		});
	} else {
		return new DataStore({
			ds: new DataSource({
				method: 'get',
				type: 'json',
				url:  X.restUrl + '?action=browser&method=searchActions'
			}),
			colModel: buttonbar_ColModel(),
			queryParams: function(params, options) {
				return {
					idnode: params.nodeid.value
				};
			},
			selector: function(source) {
				var actions = [];
				var _actions = $(source).attr('actions');
				if (!_actions) return actions;
				$($(source).attr('actions')).each(function(id, item) {
					var row = {
						actionid: item.id,
						name: item.name,
						icon: item.icon || '',
						//parentid: item.idparent,
						description: item.description,
						__xvfs: true
					};
					actions.push(row);
				});
				return actions;
			}
		});
	}
}

function menubar_ColModel() {
	return [
			{name: 'id', label: 'Id', visible: true, type: 'number'},
			{name: 'text', label: 'Name', visible: true},
			{name: 'icon', label: '', visible: true, type: 'image'},
			{name: 'accel', label: 'Accelerator', visible: true}
		];
}

function menubar_DataStore() {
	return new DataStore({
		ds: new DataSource({
			method: 'get',
			type: 'xml',
			url:  window.url_root + '/xmd/loadaction.php?method=ximmenu'
		}),
		colModel: menubar_ColModel(),
		// First level
		selector: 'menubar > menu > menuitem'
	});
}
