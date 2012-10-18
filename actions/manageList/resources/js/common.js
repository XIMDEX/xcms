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


(function($) {
	$.widget('ui.canvas_i', {
		_init: function() {
			this.hide_properties_list();
		},
		add: function() {
			var name = $('.name_updater', this.element).val();
			var description = $('.description_updater', this.element).val();
			var url = window.url_root +	'/xmd/loadaction.php';
			this.hide_properties_list();
			var msg = _("Wait, please...");
			$.blockUI({ message: '<h1> '+msg+'</h1>' });

			$.getJSON(
					url,
					{
						action: 'manageList',
						method: 'add',
						name: name,
						description: description,
						ajax: 'json',
						type: $('input:hidden[name^=type]', this.element).val()
					},
					function(data) {
						if (data.result > 0) {
							$('.list', this.element)
								.append('<tr class="list_' + data.result + '"></tr>');
							$('.list_' + data.result, this.element).load(window.url_root +
									'/xmd/loadaction.php?action=manageList&method=loadElement&id='
									+ data.result + '&name=' + $('.name_updater', this.element).attr('value')
									+ '&description=' + $('.description_updater', this.element).attr('value')
							);
						}
						$.unblockUI();
					}.bind(this)
			);
		},
		load_add: function() {
			$('.name_updater', this.element).attr('value', '');
			$('.description_updater', this.element).attr('value', '');
			$('.add_button', this.element).show();
			$('.update_button', this.element).hide();

			this.show_properties_list();
		},
		update: function(id, element) {
			var id = $('.id_updater', this.element).val();
			var name = $('.name_updater', this.element).val();
			var description = $('.description_updater', this.element).val();

			this.hide_properties_list();

			var url = window.url_root + '/xmd/loadaction.php';
			$.blockUI({ message: _('<h1> Please wait...</h1>') });
			$.getJSON(
					url,
					{
						action: 'manageList',
						method: 'update',
						id: id,
						name: name,
						description: description,
						ajax: 'json',
						type: $('input:hidden[name^=type]', this.element).val()
					},
					function(data) {
						if (data.result > 0) {
							var element_to_update = $('.list_' + id, this.element);
							$('.name', element_to_update).html($('.name_updater', this.element).attr('value'));
							$('.description', element_to_update).html($('.description_updater', this.element).attr('value'));
						}
						$.unblockUI();
					}.bind(this)
			);
		},
		load_update: function(id, element) {
			var element_context = $(element).closest('tr');

			$('.add_button', this.element).hide();
			$('.update_button', this.element).show();

			$('.id_updater', this.element).attr('value', id);

			$('.name_updater', this.element).attr('value',
					$('.name', $(element_context)).html());

			$('.description_updater', this.element).attr('value',
					$('.description', $(element_context)).html());

			this.show_properties_list();
		},
		remove: function(id, element) {
			var url = window.url_root + '/xmd/loadaction.php';
			$.blockUI({ message: '<h1> Please wait...</h1>' });
			$.getJSON(
					url,
					{
						action: 'manageList',
						method: 'remove',
						id: id,
						ajax: 'json',
						type: $('input:hidden[name^=type]', this.element).val()
					},
					function(data) {
						if (data.result > 0) {
							var element_context = $(element).closest('tr');
							$(element_context).remove();
						}
						$.unblockUI();
					}
			);
		},
		show_properties_list: function() {
			$('.list_info_manager', this.element).show();
		},
		hide_properties_list: function() {
			$('.list_info_manager', this.element).hide();
		},
		cancel: function() {
			this.hide_properties_list();
		}
	});
})(jQuery);
