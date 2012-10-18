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

	var submitButton = fn('.validate').get(0);
	submitButton.beforeSubmit.add(function(event, button) {

		var data = {
			lotename: fn('input[name=lotename]').val(),
			tipo: fn('select[name=tipo] option:selected').val(),
			boxfecha: fn('input[name=boxfecha]').val()
		};

		$.post(
			params.url + '&method=add_node',
			data,
			function(data, textStatus, xhr) {

				if (!Object.isObject(data)) {
					// Show errors
					params.actionView.setActionContent(data);
					return;
				}

				$('li#treeview-nodeid-' + data.idParent)
					.closest('div.xim-treeview-container')
					.treeview('refresh', data.idParent);

				params.actionView.openAction({
					action: {
						bulk: 0,
						callback: 'callAction',
						command: 'fileupload_common_multiple',
						method: 'index',
						name: _('Add images')
					},
					label: _('Add images'),
					nodes: data.idLote,
					url: X.restUrl + '?action=%s&method=%s&nodes[]=%s&type=%s'.printf(
						'fileupload_common_multiple',
						'index',
						data.idLote,
						data.type
					)
				});
			}
		);

		// Important!
		return true;
	});

});