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
 *  @version $Revision: 7839 $
 */

(function() {

	X.searchpanel.colmodels.search = [
		{name: 'icon', label: '', visible: true, type: 'image', width: '24px'},
		{name: 'isdir', label: 'isDir', visible: false},
		{name: 'nodeid', label: 'NodeId', visible: false, type: 'number', align: 'center', width: '70px'},
		{name: 'tolid', label: 'DocId', visible: false, type: 'number', align: 'center', width: '70px'},
		{name: 'name', label: 'Nombre', visible: true, width: '160px'},
		{name: 'titulo', label: 'Titulo', visible: true, width: '180px'},
		{name: 'nombretipo', label: 'Tipo Doc.', visible: true, width: '40px'},
		{name: 'publicado', label: 'Publicado', visible: true, width: '40px'},
		{name: 'fechaalta', label: 'Fecha Alta', align: 'center', visible: true, width: '40px'},
		{name: 'fecharevision', label: 'Fecha Rev.', align: 'center', visible: true, width: '40px'},
		{name: 'fechadocumento', label: 'Fecha Doc.', visible: true, align: 'center', width: '40px'}
	];

	X.searchpanel.colmodels.nodetypes = [
		{name: 'idnodetype', label: 'NodeTypeId', visible: true, type: 'number'},
		{name: 'name', label: 'Name', visible: true},
		{name: 'icon', label: 'Icon', visible: true, type: 'image', width: '24px'}
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

	X.searchpanel.options = {
		queryHandler: 'TOL'
	};

})();
