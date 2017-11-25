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

(function() {

	X.searchpanel.colmodels.search = [
		{name: 'icon', label: '', visible: true, type: 'image', width: '24px'},
		{name: 'isdir', label: '', visible: false},
		{name: 'nodeid', label: 'Id', visible: true, type: 'number', align: 'center', width: '30px', sortable: true},
		{name: 'parentid', label:  _('ParentId'), visible: false},
		{name: 'name', label: _('Name'), visible: true, sortable: true},
		{name: 'nodetype', label:_('Node type'),  visible: true, sortable: true},
		{name: 'abspath', alias: 'path', label: _('Path'),  visible: true, sortable: true},
		{name: 'creationformated', label:  _('Creation'), visible: true, align: 'center', sortable: true},
		{name: 'versionnumber', label:  _('Version'),  visible: true, align: 'center', sortable: true},
		{name: 'versiondateformated', label:  _('Modification'),  visible: true, align: 'center', sortable: true}
	];

	X.searchpanel.colmodels.nodetypes = [
		{name: 'idnodetype', label: 'NodeTypeId', visible: true, type: 'number'},
		{name: 'name', label: _('Name'), visible: true},
		{name: 'icon', label:_('Icon'), visible: true, type: 'image', width: '24px'}
	];

	X.searchpanel.datastores.search = {
		ds: new DataSource({
			method: 'post',
			type: 'json',
			url:  X.restUrl + '?action=browser3&method=search'
		}),
		colModel: X.searchpanel.colmodels.search,
		selector: function(data) {
			return data.data;
		}
	};

	X.searchpanel.datastores.nodetypes = {
		ds: new DataSource({
			method: 'get',
			type: 'json',
			url:  X.restUrl + '?action=browser3&method=nodetypes'
		}),
		colModel: X.searchpanel.colmodels.nodetypes,
		selector: function(data) {
			return data.nodetypes;
		}
	};

})();
