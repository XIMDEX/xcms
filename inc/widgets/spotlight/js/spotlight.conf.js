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

	var colModel = [
		{name: 'nodeid', label: 'NodeId', visible: true, type: 'number', align: 'center', width: '30px'},
		{name: 'parentid', label: 'ParentId', visible: false},
		{name: 'name', label: 'Name', visible: true},
		{name: 'nodetype', label: 'NodeType', visible: true},
		{name: 'relpath', label: 'Relative Path', visible: true},
		{name: 'isdir', label: 'Folder', visible: true},
		{name: 'icon', label: '', visible: true, type: 'image', width: '24px'},
		{name: 'children', label: 'Children', visible: false},
		{name: 'abspath', alias: 'path', label: 'Absolute Path', visible: true}
	];
	
	return {
		queryFormat: 'JSON', // [DOM | XML | JSON]
		queryHandler: 'SQL', // [SQL | SOLR | XVFS]
		outputFormat: 'JSON', // [XML | JSON]
		advanced: null,
		cbBeforeSearch: function(data, textStatus) {},
		cbSearch: function(data, textStatus) {},
		colModel: colModel,
		datastore: {
			ds: new DataSource({
				method: 'post',
				type: 'JSON',
				url:  X.restUrl
			}),
			selector: function(data) {
				//console.info(data.data);
				return data ? data.data : [];
			},
			colModel: colModel
		}
	};

})();