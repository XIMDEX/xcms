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


window.com.ximdex = Object.extend(window.com.ximdex, {
	responses: {
		YES: 1,
		NO: 2,
		OK: 3,
		CANCEL: 4,
		CLOSE: 5,
	}
});

window.com.ximdex = Object.extend(window.com.ximdex, {
	buttons: {
		BTN_YES: {text: 'Yes', value: com.ximdex.responses.YES, onPress: null},
		BTN_NO: {text: 'No', value: com.ximdex.responses.NO, onPress: null},
		BTN_OK: {text: 'Ok', value: com.ximdex.responses.OK, onPress: null},
		BTN_CANCEL: {text: 'Cancel', value: com.ximdex.responses.CANCEL, onPress: null},
		BTN_CLOSE: {text: 'Close', value: com.ximdex.responses.CLOSE, onPress: null}
	},
	dialogs: {}
});


X.dialogs.XimdexDialog = Object.xo_create({
	
	_init: function(options) {
	
		this._pressedButton = X.responses.CANCEL;
		
		var _b = options.buttons || [X.buttons.BTN_OK];
		var buttons = {};
		
		$.each(_b, function(index, item) {
			buttons[item.text] = (function(item, context) {
				var cb = null;
				if (Object.isFunction(item.onPress)) {
					cb = item.onPress;
				} else {
					cb = function() {
						this._pressedButton = item.value;
						this.close();
					};
				}
				return cb.bind(context);
			})(item, this);
		}.bind(this));
		
		options.buttons = buttons;
	
		this.options = Object.extend({
			id: 'ximdexdialog',
			owner: window,
			container: 'body',
			title: 'Browser dialog',
			width: 400,
			height: 200,
			close: function(event, ui) {
				//$(this).trigger('onclose', [{value: this.getValue(), dialog: this}]);
				if (Object.isFunction(this.options.onclose)) {
					this.options.onclose(event, {value: this.getValue(), dialog: this});
				}
			}.bind(this),
			bgiframe: true,
			autoOpen: false,
			modal: true,
			draggable: false,
			// Buttons callbacks
			onYes: null,
			onNo: null,
			onOk: null,
			onCancel: null,
			onClose: null,
			onError: null
		},
		options);
	
		this.dialog = $('<div/>')
						.addClass(this.options.id)
						.append(
							$('<div/>').addClass('dialog-content')
						)
						.appendTo(this.options.container)
						.dialog(this.options);
		
	},
	
	bind: function(eventType, eventData, handler) {
		$(this.dialog).bind(eventType, eventData, handler);
		return this;
	},
	
	trigger: function(eventType, params) {
		if (Object.isFunction(this.options[eventType])) {
			this.options[eventType](this, params);
		}
	},
	
	setContent: function(content) {
		$('.dialog-content', this.dialog).unbind().empty().append(content);
	},
	
	getValue: function() {
		return this._pressedButton;
	},
	
	open: function() {
		$(this.dialog).dialog('open');
	},
	
	close: function() {
		this.clear();
		$(this.dialog).dialog('close');
	},
	
	clear: function() {
		// Overwrite me!
	},
	
	destroy: function() {
		$(this.dialog).dialog('destroy').unbind().remove();
	}
});

X.dialogs.MessageDialog = Object.xo_create(X.dialogs.XimdexDialog, {

	_init: function(options) {
	
		options.message = options.message || '';
		
		X.dialogs.MessageDialog._construct(this, Object.extend({
			id: 'messagedialog',
			title: 'Message dialog'
		}, options));
		
		var content = $('.dialog-content', this.dialog);
		
		if (Object.isString(this.options.message)) {
		
			$(content).html(this.options.message);
		} else if (Object.isObject(this.options.message)) {

			$(content).append(this.options.message);
		} else if (Object.isArray(this.options.message)) {

			// message is an array of objects in the form [{type: INT, message: STRING} [, ...]]

			$.each(this.options.message, function(index, item) {
				$(content).append(
					$('<div/>')
						.addClass('dialog-message')
						.html(item.message)
					// TODO: Show an icon depending on item.type ??????
				);
			}.bind(this));
		}
		
		if (this.options.icon) {
			// Do something with the icon...
		}
	}
	
});

X.dialogs.InputDialog = Object.xo_create(X.dialogs.MessageDialog, {

	_init: function(options) {
	
		X.dialogs.InputDialog._construct(this, Object.extend({
			id: 'inputdialog',
			title: 'Input dialog',
			keypress: function(event) {this.close();}
		}, options));
		
		this.input = $('<input/>')
						.attr({
							type: 'text'
						})
						.addClass('dialog-input')
						.keypress(function(event) {
							if (event.keyCode == 13) {
								this._pressedButton = X.responses.OK;
								if (Object.isFunction(this.options.keypress)) {
									this.options.keypress(event);
								}
								
							}
						}.bind(this));
		
		$(this.dialog)
			.append(
				$('<div/>')
					.addClass('dialog-input-container')
					.append(
						this.input
					)
			);
	},
	
	getInput: function() {
		return $(this.input).val();
	},
	
	open: function() {
		X.dialogs.InputDialog._super(this, 'open');
		$(this.input).focus();
	},
	
	close: function() {
		X.dialogs.InputDialog._super(this, 'close');
	}
	
});

