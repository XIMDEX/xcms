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

	fn('.sortable').sortable({
		cancel: '.first, .last',
		items: 'li:not(.nosortable)'
	});

	fn('img.modifyrolesstate').click(function() {

		var state = fn(this).closest('li').find('input[name="id_status"]').val();
		var nodeId = fn('input[name="idNode"]').val();
		var action = {
			bulk: 0,
			command: 'modifyrolesstate',
			method: 'index',
			name: 'Modifica los Roles Asociados',
			params:'state='+state
		};

		$(params.browser).browserwindow('openAction', action, nodeId);
	});

	fn('.open_report').click(function() {

		var nodeId = fn('input[name="idNode"]').val();
		var action = {
			bulk: 0,
			command: 'modifystates',
			method: 'checkNodeDependencies',
			name: 'Dependencias'
		};

		$(params.browser).browserwindow('openAction', action, [nodeId]);
	});

});