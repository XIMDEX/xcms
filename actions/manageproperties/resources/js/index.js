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
	
	var properties = ['channels', 'languages', 'schemas'];
	// TODO: gettext method "_()" must translate these string!
	var _properties = {channels: 'canales', languages: 'idiomas', schemas: 'plantillas'};
	
	
	fn('.reset-button').click(function(event) {
		fn('input[name^=inherited][value=inherited]').change();
	});
	
	
	var btn = fn('.submit-button').get(0);
	
	btn.beforeSubmit.add(function(event, button) {
		
		var messages = [];
		
		properties.each(function(index, property) {
			
			var overwritten = fn('input.%s_overwritten'.printf(property)).attr('checked');
			if (overwritten) {
				var fields = fn('input.%s'.printf(property));
				var notempty = false;
				fields.each(function(item, field) {
					var checked = fn(field).attr('checked');
					notempty = notempty || checked;
				});
				if (!notempty) {
					var message = _('A value should be selected for <b>%s</b> property.<br />').printf(_properties[property]);
					message += _('Otherwise inherited values will be used.')
					messages.push(message);
				}
			}
		});
		
		var sendform = messages.length == 0;
		if (!sendform) {
			manageproperties_showDialog(messages, fn, params, function(send) {
				if (send) {
					var fm = btn.getFormMgr();
					fm.sendForm({
						button: btn,
						confirm: false,
						jsonResponse: true
					});
				}
			});
		}
		
		return !sendform;
	});
	
	properties.each(function(index, property) {
		
		// Inherited or overwritten values
		fn('input[name=inherited_%s]'.printf(property)).change(function(event) {
			var name = $(this).attr('name').replace(/inherited_/, '');
			var value = $(this).val();
			
			if (value == 'inherited') {
				
				fn('input.%s'.printf(name)).attr('disabled', true);
				fn('input.%s_recursive'.printf(property)).attr('disabled', true);
				fn('button.%s_apply'.printf(property)).attr('disabled', true);
			} else {

				fn('input.%s'.printf(name)).removeAttr('disabled');
				fn('input.%s_recursive'.printf(property)).removeAttr('disabled');
				fn('button.%s_apply'.printf(property)).removeAttr('disabled');
			}
		});
		
		// Applies recursively the property values
		fn('button.%s_apply'.printf(property)).click(function(event) {
			var propName = fn(this).attr('name').replace(/_apply$/, '');
			$.post(
				X.restUrl,
				{
					action: params.action.command,
					method: 'applyPropertyRecursively',
					nodes: params.nodes,
					values: fn(this).val(),
					property: propName
				},
				function(data, statusText, xhr) {
					if (data.result && data.result.nodes > 0) {
						var message = null;
						switch (data.property) {
							case 'Channel':
								message = _('Channel %s has been associated with %s documents.').printf(data.result.values.length, data.result.nodes);
								break;
							case 'Language':
								message = _('%s idiomatic versions have been created.').printf(data.result.nodes);
								break;
						}
						manageproperties_showDialog([message], fn, params, function(send) {
						});
					}
				}
			);
		});
		
		// Shows/hides recursive checkboxes/buttons
		if (fn('input.%s_inherited'.printf(property)).attr('checked')) {
			
			fn('input.%s'.printf(property)).change(function(event) {
				var value = fn(this).attr('checked');
				if (value) {
					fn('div.%s_recursive_%s'.printf(property, fn(this).val())).removeClass('novisible');
				} else {
					fn('div.%s_recursive_%s'.printf(property, fn(this).val())).addClass('novisible');
					fn('div.%s_recursive_%s input.%s_recursive'.printf(property, fn(this).val(), property)).removeAttr('checked');
				}
			});
			
		} else {
			
			fn('input.%s'.printf(property)).change(function(event) {
				if (!fn(this).attr('checked')) {
					fn('button.%s_apply_%s'.printf(property, fn(this).val())).attr('disabled', true);
				} else {
					fn('button.%s_apply_%s'.printf(property, fn(this).val())).removeAttr('disabled');					
				}
			});
		}
	});
	
});
