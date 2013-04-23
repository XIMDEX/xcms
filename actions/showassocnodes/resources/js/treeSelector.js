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


X.actionLoaded(function(event, fn, params) {

	var $ts = fn('.xim-treeview-selector');

	var tcm = [
		{name: 'idaction', alias: 'actionid', label: 'ActionId', visible: false, type: 'number', align: 'center'},
		{name: 'action', label: 'Action', visible: false, type: 'number', align: 'center'},
		{name: 'text', alias: 'name', label: 'Name', visible: true},
		{name: 'nodeid', label: 'NodeId', visible: false, type: 'number', align: 'center'},
		{name: 'nodetypeid', label: 'NodeTypeId', visible: false, type: 'number', align: 'center'},
		{name: 'isdir', label: 'IsFolder', visible: false, type: 'number', align: 'center'},
		{name: 'path', label: 'Path', visible: false},
		{name: 'padre', alias: 'parentid', label: 'ParentId', visible: false, type: 'number', align: 'center'},
		{name: 'icon', label: '', visible: true, type: 'image', width: '24px'},
		{name: 'openIcon', label: '', visible: true, type: 'image', width: '24px'},
		{name: 'children', visible: false, type: 'number'},
		{name: 'idximlet', alias: 'ximletid', visible: false, type: 'number'},
		{name: 'showMenu', visible: false, type: 'number'},
		{name: 'startIndex', label: 'Inicio', visible: false},
		{name: 'endIndex', label: 'Fin', visible: false},
		{name: 'canSelected', label: 'canSelected', type: 'number', visible: false,},
		{name: 'already_selected', label: 'already_selected',  type: 'number', visible: false,}

	];

	var tds = {
		ds: new DataSource({
			method: 'get',
			type: 'xml',
			url:  X.restUrl
		}),
		colModel: tcm,
		queryParams: function(params, options) {

			var p = {
				// Action
				action: params.action.value,
				// Method
				method: 'treedata',
				// Node to be expanded
				nodeid: params.nodeid.value,
				// ximlet id
				ximletid: params.ximletid.value,
				// Elements per page
				nelementos: options.paginator.defaultValue
			};
			return p;
		},
		selector: 'tree tree'
	};

	$ts
		.treeview({
			datastore: tds,
			paginator: {
				show: true
			},
			colModel: tcm,
			url_base: X.baseUrl + '/',
			img_base: X.baseUrl + '/xmd/images/icons/'
		});

	tds = $ts.treeview('getDatastore');
	tds.clear();
	tds.append({
		name: {value: _('Projects'), visible: true},
		nodeid: {value: fn('input[name=treeroot]').val(), visible: false},
		isdir: {value: 1, visible: false},
		icon: {value: 'root.png', visible: true, type: 'image'},
		children: {value: 2, visible: false},
		path: {value: '/'+_('Projects'), visible: false},
		action: {value: 'showassocnodes'},
		ximletid: {value: fn('input[name=ximletid]').val()},
		nelementos: {value: 50},
		canSelected: {value: 0},
		already_selected: 0
	});
	$ts.treeview('setRootModel', tds.get_model(), false, true);

	$ts.bind('itemClick', function (event, ui) {

		var nt = fn('input[name=searchednodetype]').val().split(',');

		fn('input[name=path]').val('');
		fn('input[name=targetid]').val('');

		var ximlet_already_selected = parseInt(ui.data.already_selected.value,10);
		var nodetype_valid = parseInt(ui.data.canSelected.value,10);
		if (Object.isEmpty(ui.data.nodetypeid) || !nt.contains(ui.data.nodetypeid.value) || ximlet_already_selected || !nodetype_valid ) {
			$(".warning").show();
			if(ximlet_already_selected) {
				$(".ximlet_already_selected").show();
			}
			return;
		}

		$(".warning").hide();
		$(".ximlet_already_selected").hide();

		fn('input[name=path]').val(ui.data.path.value);
		fn('input[name=targetid]').val(ui.data.nodeid.value);
	});


	if (!Object.isEmpty(fn('.createrel-button').get(0))) {
		fn('.createrel-button').get(0).beforeSubmit.add(function(event, button) {
			var targetid = fn('input[name=targetid]').val();
			if (Object.isEmpty(targetid)) {
				console.warn(_('Select a container'));
				return true;
			}
		});
	}

	if (!Object.isEmpty(fn('.deleterel-button').get(0))) {
		fn('.deleterel-button').get(0).beforeSubmit.add(function(event, button) {
			var sections = fn('input.sections_lists:checked');
			if (sections.length == 0) {
				console.warn(_('Select a container'));
				return true;
			}
		});
	}

});
