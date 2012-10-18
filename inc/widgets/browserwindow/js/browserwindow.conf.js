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
 *  @version $Revision: 7740 $
 */


(function() {

	var datasource = new DataSource({
		method: 'post',
		type: 'json',
		url:  X.restUrl
	});

	X.browser.datastores = {

		sets: {
			ds: datasource,
			queryParams: function(params, options) {
				return {
					setid: params.setid,
					action: 'browser3',
					method: 'getSet'
				};
			},
			selector: 	function(data) {
				return data;
			},
			colModel: [
				{name: 'icon', label: '', visible: true, type: 'image', width: '24px'},
				{name: 'nodeid', label: 'NodeId', visible: true, type: 'number', align: 'center', width: '70px'},
				{name: 'parentid', label: 'ParentId', visible: false, type: 'number', align: 'center'},
				{name: 'text', alias: 'name', label: 'Name', visible: true, width: '200px'},
				{name: 'path', label: 'Path', visible: true},
				{name: 'isdir', label: 'isDir', visible: false},
				{name: 'children', visible: false, type: 'number'}
			]
		},

		filters: {
			ds: datasource,
			queryParams: function(params, options) {
				return {
					filterid: params.filterid,
					action: 'browser3',
					method: 'getFilter'
				};
			},
			selector: 	function(data) {
				return data.data;
			},
			colModel: [
				{name: 'icon', label: '', visible: true, type: 'image', width: '24px'},
				{name: 'nodeid', label: 'NodeId', visible: true, type: 'number', align: 'center', width: '70px'},
				{name: 'parentid', label: 'ParentId', visible: false, type: 'number', align: 'center'},
				{name: 'name', label: 'Name', visible: true, width: '200px'},
				{name: 'path', label: 'Path', visible: true},
				{name: 'isdir', label: 'isDir', visible: false},
				{name: 'children', visible: false, type: 'number'}
			]
		},

		searches: {
			ds: datasource,
			selector: function(data) {
				return data.data;
			},
			queryParams: function(params, options) {
				params = 'action=browser3&method=search&output=JSON&' + params;
				return params;
			},
			colModel: [
				{name: 'nodeid', label: 'NodeId', visible: true, type: 'number', align: 'center', width: '30px'},
				{name: 'parentid', label: 'ParentId', visible: false},
				{name: 'name', label: 'Name', visible: true},
				{name: 'nodetype', label: 'NodeType', visible: true},
				{name: 'relpath', label: 'Relative Path', visible: true},
				{name: 'isdir', label: 'Folder', visible: true},
				{name: 'icon', label: '', visible: true, type: 'image', width: '24px'},
				{name: 'children', label: 'Children', visible: false},
				{name: 'abspath', alias: 'path', label: 'Absolute Path', visible: true}
			]
		},

		parents: {
			ds: new DataSource({
				method: 'get',
				type: 'json',
				url:  X.restUrl + '?action=browser3&method=parents'
			}),
			selector: function(data) {
				return data.node.parents || [];
			},
			colModel: [
				{name: 'name', label: 'Name', visible: true},
				{name: 'nodeid', label: 'NodeId', visible: true, type: 'number'},
				{name: 'isdir', label: 'IsFolder', visible: true}
			]
		},

		cmenu: {
			ds: new DataSource({
				method: 'get',
				type: 'json',
				url:  X.restUrl + '?action=browser3&method=cmenu'
			}),
			selector: function(data) {
				return data;
			},
			colModel: [
				{name: 'callback', visible: true},
				{name: 'command', visible: true},
				{name: 'icon', visible: true, type: 'image'},
				{name: 'module', visible: true},
				{name: 'name', visible: true},
				{name: 'params', visible: true},
				{name: 'bulk', visible: false}
			]
		}
	};
	
	return X.browser.datastores;

})();
