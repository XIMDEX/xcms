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
 *  @version $Revision: 7831 $
 */


(function() {

	var colModel = [
		{name: 'name', label: 'Name', visible: true},
		{name: 'nodeid', label: 'NodeId', visible: false, type: 'number', align: 'center'},
		{name: 'nodetypeid', label: 'nodetypeid', visible: false, type: 'number', align: 'center'},
		{name: 'is_section_index', label: 'is_section_index', visible: false, type: 'boolean', align: 'center'},
		{name: 'parentid', label: 'ParentId', visible: false, type: 'number', align: 'center'},
		{name: 'icon', label: '', visible: true, type: 'image', width: '24px'},
		{name: 'path', label: 'Path', visible: false},
		{name: 'isdir', label: 'isDir', visible: false},
		{name: 'children', visible: false, type: 'number'},
		{name: 'startIndex', label: 'Inicio', visible: false},
		{name: 'endIndex', label: 'Fin', visible: false},
		{name: 'backend', label: 'backend', visible: false},
		{name: 'hasActions', label: 'HasActions', type: 'number', visible: false},
		{name: 'canUploadFiles', label: 'canUploadFiles', type: 'number', visible: false}
	];
	return {
		paginator: {
			show: false
		},
		url_base: window.url_root + '/',
		img_base: window.url_root + '/xmd/images/icons/',
		colModel: colModel,
		datastore: {
			ds: new DataSource({
				method: 'get',
				type: 'json',
				url:  X.restUrl + '?action=browser3&method=read'
			}),
			colModel: colModel,
			queryParams: function(params, options) {
				//console.info(params, options);
				var ret = null;
				if (params.startIndex && params.startIndex.value !== undefined
					&&
					params.endIndex && params.endIndex.value !== undefined) {

					ret = {
						nodeid: params.parentid.value,
						from: params.startIndex.value,
						to: params.endIndex.value
					};
				} else {
					ret = {
						nodeid: params.nodeid.value,
						items: options.paginator.defaultValue
					};
				}
				return ret;
			},
	//		selector: 'tree tree'
			selector: 'collection'
		},
		root: {
			name: {value: _('Projects'), visible: true},
			nodeid: {value: 10000, visible: false},
			nodetypeid: {value: 0},
			icon: {value: 'projects.png', visible: true, type: 'image'},
			children: {value: 1, visible: false},	// Non zero!
			isdir: {value: 1, visible: false},
 path: {value: '/'+ _('Projects'), visible: false},
			canUploadFiles: {value: 0},
			hasActions: {value: 1}
		}
	};

})();
