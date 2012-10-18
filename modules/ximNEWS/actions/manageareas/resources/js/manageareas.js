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

	function createNewManager(form) {
		return new X.FormsManager({
			actionContainer: params.context,
			form: form
		});
	}

	fn('.add_area').click(function() {

		$.get(fn('#index_form').attr('action') + '&method=create', function(data) {

			fn('#index_form').replaceWith(data);
			var formManager = createNewManager(fn('#ca_form'));
			$(formManager.options.actionContainer).labelWidth();

		});
	});

	fn('.update_area').click(function() {

		$.get(fn('#index_form').attr('action') + '&method=edit&area_id=' +
			fn(this).attr('id'), function(data) {

				fn('#index_form').replaceWith(data);
				createNewManager(fn('#ca_form'));
		});
	});

	fn('.delete_area').click(function() {

		var id_area = fn(this).attr('id');
 		var div_dialog = $("<div>").attr('id', 'dialog').appendTo('#index_form', this);
		div_dialog.html(_("Do you want to delete the category?"));

		div_dialog.dialog({
			buttons: {
				_("Accept"): function() {

				fn(this).dialog("destroy");

				$.get(fn('#index_form').attr('action') + '&method=delete&area_id=' +
					id_area, function(data) {

					fn('#index_form').replaceWith(data);
				});
			},
			 _("Cancel"): function() { fn(this).html('');fn(this).dialog("destroy"); }
			}
		});
	});
});
