
/**
 * ximdex v.3 --- A Semantic CMS
 * Copyright (C) 2010, Open Ximdex Evolution SL <dev@ximdex.org>
 *
 * This program is commercial software.
 * Check version 2 of ximdex for the open source version.
 *
 * @author XIMDEX Team <dev@ximdex.org>
 *
 * @version $Revision: $
 *
 *
 * @copyright (C) Open Ximdex Evolution SL, {@link http://www.ximdex.org}
 * @license Commercial (check ximdex version 2 for the open source software)
 *
 * $Id$
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
