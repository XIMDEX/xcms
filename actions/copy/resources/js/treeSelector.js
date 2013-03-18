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
 *  @version $Revision: 8029 $
 */


function hndCopy_treeSelector(options) {

	var $ts = options.element;
	function getClosestInput(element, name) {
		return $(element).closest('form').find('input[name='+name+']');
	}

	var tcm = [
		// Node name
		{name: 'text', alias: 'name', label: 'Name', visible: true},
		// Node ID
		{name: 'nodeid', label: 'NodeId', visible: false, type: 'number', align: 'center'},
		// Node is Folder
		{name: 'isdir', label: 'IsFolder', visible: false, type: 'number', align: 'center'},
		// Node Path
		{name: 'path', label: 'Path', visible: false},
		// Parent ID
		{name: 'padre', alias: 'parentid', label: 'ParentId', visible: false, type: 'number', align: 'center'},
		// NPI
		{name: 'contenidotipo', alias: 'contenttype', label: 'ContentType', visible: false},
		// GET Request when node is expanded
		{name: 'action', label: 'Action', visible: false},
		// Icon
		{name: 'icon', label: '', visible: true, type: 'image', width: '24px'},
		// Open icon
		{name: 'openIcon', label: '', visible: true, type: 'image', width: '24px'},
		// NPI
		{name: 'state', label: 'State', visible: false},
		// Number of children
		{name: 'children', visible: false, type: 'number'},
		// Node must be shown expanded
		{name: 'open', visible: false},
		// Node must be selected by default
		{name: 'selected', visible: false},
		// NodeType NAME
		{name: 'tipoFiltro', alias: 'filter', visible: false},
		// Node ID for being copied
		{name: 'targetid', visible: false},
		// Pagination
		{name: 'startIndex', label: 'Inicio', visible: false},
		{name: 'endIndex', label: 'Fin', visible: false}
	];

	var tds = {
		ds: new DataSource({
			method: 'get',
			type: 'xml',
			url:  window.url_root + '/inc/widgets/treeview/helpers/treeselectordata.php'
		}),
		colModel: tcm,
		queryParams: function(params, options) {
			var p = {
				// Node to be expanded
				nodeid: params.nodeid.value,
				// NPI
				contenttype: params.contenttype ? params.contenttype.value : '',
				// Node to be copied
				targetid: params.targetid ? params.targetid.value : window.nodeId,
				// NodeType NAME
				filtertype: getClosestInput($ts, 'filtertype').val(),
				// NodeType ID
				nodetype: getClosestInput($ts, 'nodetypeid').val(),
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
			url_base: window.url_root + '/',
			img_base: window.url_root + '/xmd/images/icons/'
		})
		.bind('select', function(event, params) {
			getClosestInput(params.element, 'targetid').val(params.data.nodeid.value);
			getClosestInput(params.element, 'pathfield').val(params.data.path.value);
			checkNodeName(params.data.nodeid.value);
		});

	tds = $ts.treeview('getDatastore');
	tds.clear();
	tds.append({
		name: {value: 'ximdex', visible: true},
		nodeid: {value: 1, visible: false},
		isdir: {value: 1, visible: false},
		icon: {value: 'root.png', visible: true, type: 'image'},
		children: {value: 2, visible: false},
		path: {value: '/', visible: false},
		targetid: {value: getClosestInput($ts, 'nodeid').val()},
		nodetype: {value: getClosestInput($ts, 'nodetypeid').val()},
		filtertype: {value: getClosestInput($ts, 'filtertype').val()},
		contenttype: {value: ''},
		nelementos: {value: 50}
	});
	$ts.treeview('setModel', tds.get_model(), null, true);

	function checkNodeName(target) {

		var input = getClosestInput(options.element, 'nodeid');
		var nodeid = $(input).val();

		$.getJSON(X.restUrl,{'action':'copy','method':'checkNodeName','nodeid':nodeid,'targetid':target},
			function (data,textstatus,xhr){
				//Allows ximIO renames automatically all nodes copied to the same level. This is a improvement.
				//if(data.changeName==1){$('.changename').removeClass('hidden');}
				//else{$('.changename').addClass('hidden');}
				if(data.insert==0){$('.warning').removeClass('hidden');}
				else{$('.warning').addClass('hidden');}
			}
		);
	}
}
