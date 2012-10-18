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


X.actionLoaded(function (event, fn, params) {

	var btn = fn('.submit-button').get(0);
	btn.beforeSubmit.add(function(event, button) {

		var delete_link = fn('input[name=delete_link]').attr('checked');
		if (!delete_link) {
			return false;
		}

		if (fn('input.delete_method:checked').length == 0) {
			//alert('Debe seleccionar una de las opciones antes de eliminar el enlace.');
			var div_dialog = $("<div/>").attr('id', 'dialog').appendTo(btn);
			div_dialog.html(_("You must select an option before you delete the link"));
			div_dialog.dialog({
				modal: true,
				buttons: {
					_("Accept"): function() {
						div_dialog.dialog('destroy');
					}
				}
			});
			return true;
		}

		var method = 'method=' + fn('input.delete_method:checked').attr('value');
		var action = fn('.sl_form').attr('action').replace(/method=setlink/, method);
		fn('.sl_form').attr('action', action);
	});

	var sharewf_flag = fn('input[name=sharewf]').attr('checked');

	fn('input[name=delete_link]').change(function(event) {
		fn('div.translation_box').toggle();
		if (fn(this).attr('checked')) {
			fn('input[name=sharewf]').removeAttr('checked');
		} else {
			if (sharewf_flag) {
				fn('input[name=sharewf]').attr('checked', true);
			} else {
				fn('input[name=sharewf]').removeAttr('checked');
			}
		}
	});

	fn('input[name=sharewf]').change(function(event) {
		sharewf_flag = fn(this).attr('checked');
		if (sharewf_flag) {
			var div_dialog = $("<div/>").attr('id', 'dialog').appendTo(this);
			div_dialog.html(_("Intervals are erased publication of this node from the moment of the sending, and will be published the same intervals that the master node."));
			div_dialog.dialog({
				modal: true,
				buttons: {
					_("Accept"):function() {
						div_dialog.dialog('destroy');
					}
				}
			});
			//alert('Se borrarán los intervalos de publicación de este nodo a partir del momento del enví­o, y pasará a tener los mismos intervalos de publicación que el nodo maestro.');
		}
	});

});
