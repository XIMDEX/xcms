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
 *  @version $Revision: 8529 $
 */


/**
 * Control the forms submission logic.
 */


X.FormsManager = Object.xo_create({

	buttons: null,

	_init: function(options) {

//		console.info('FormsManager: ', this);

		this.options = Object.extend({
			actionView: null,
			iframeId: '',
			actionContainer: null,
			form: null,
			searches: [
				'(not_empty)', '(is_url)', '(is_email)',
				'(field_equals)__([^\\s]+)', '(check_group)__([^\\s]+)'
			]
		}, options);

		// Don't want the jQuery object, want the DOMElement
		this.options.form = $(this.options.form).get(0);

		this.options.form.getFormMgr = function() {
			return this;
		}.bind(this);

		this._checkInputFields();
		this._registerButtons();
	},

	/**
	 * Checks and corrects inputs behaviours.
	 */
	_checkInputFields: function() {

		var $fields = $('input[type=text]', this.options.form);

		// Don't send a form when enter key pressed
		$fields.keydown(function(event) {
			if (event.keyCode == 13) {
				return false;
			}
		});
	},

	/**
	 * Register click events on all form buttons with the class "validate"
	 */
	_registerButtons: function() {

		this.buttons = [];

		// Register buttons that will send the form
		$('.validate', this.options.form).each(function(index, button) {
			this.registerButton(button);
		}.bind(this));

		// Register buttons for reseting the form
		$('.reset-button', this.options.form).each(function(index, button) {
			button.click(function() {
				this.options.form.reset();
				return false;
			});
		}.bind(this));
	},

	/**
	 * Extends a button with methods for retrieve the form and the FormManager object.
	 * Creates a collection that stores "beforeSubmit" callbacks.
	 * If one callback return TRUE the form submission will be aborted.
	 *
	 * button: the DOMElement that will act as a submit button when clicked.
	 * confirm: When TRUE a confirmation message will be shown.
	 * message: The confirmation message. When NULL, the value attribute of the button will be used.
	 * file: ???
	 */
	registerButton: function(button, options) {

		button = $(button).get(0);

		options = $.extend({
			confirm: true,
			message: null,
			file: 0
		}, options);

		button = $.extend(button, {
			getForm: function() {
				return this.options.form;
			}.bind(this),
			getFormMgr: function() {
				return this;
			}.bind(this),
			beforeSubmit: new X.Collection({unique: true})
		});

		$(button).click(function(event) {

			// Call the callbacks, if one of them returns TRUE the form will not be submitted
			var submit = true;
			var i = 0, l = this.beforeSubmit.size();
			while (submit && i<l) {
				var cb = this.beforeSubmit.get(i);
				if (Object.isFunction(cb)) {
					var abort = cb(event, this);
					submit = abort === true ? false : true;
				}
				i++;
			}

			if (!submit) {
				//console.log('[formsManager]: Aborting sendForm.');
				return;
			}

			return this.getFormMgr().sendForm($.extend({
				button: this
			}, options));
		});

		//console.log(button);
		this.buttons.push(button);
		return button;
	},

	getButtons: function() {
		return this.buttons;
	},

	getActionContainer: function() {
		return this.options.actionContainer;
	},

	setActionContainer: function(container) {
		this.options.actionContainer = container;
		return this;
	},

	/**
	 * Genera un iframe, envía el contenido sobre el iframe y por último carga el contenido en un tab.* button: the DOMElement that will act as a submit button when clicked.
	 *
	 * button: The clicked button.
	 * confirm: When TRUE a confirmation message will be shown.
	 * message: The confirmation message. When NULL the value attribute of the button will be used.
	 * file: ???
	 */
	sendForm: function(options) {

		options = $.extend({
			button: null,
			confirm: true,
			message: null,
			file: 0
		}, options);


		// Ensure the form and iframe IDs are unique
		var formId = $(this.options.form).attr('id');
		if (Object.isEmpty(formId)) {
			formId = 'form_' + X.getUID();
			$(this.options.form).attr('id', formId);
		}
		this.options.iframeId = 'form_sender_' + formId;

		// Validate the form
		if (!Object.isEmpty($(this.options.form).data('validator')) || $(this.options.form).data('validator') === null) {
			$(this.options.form).validate({rules: this._getConstraints(this.options.form)});
		}

		// Submit button
		var validateElement = options.button || $('.validate', this.options.form);
		var message = options.message || $('~ .submit_message', validateElement).attr('value');
		var dialog = this._getDialog(this.options.form, message);
		if (message == '' || message == undefined) {
			options.confirm = false;
		}

		// Send the form
		if (!options.confirm) {
			this._doSubmit(this.options.form, options.files);
		} else {

			$(dialog).dialog({
				title: 'Ximdex Notifications',
				modal: true,
				buttons: {
					_('Accept'): function() {
						this._doSubmit(this.options.form, options.files);
						$(dialog).dialog('destroy');
						$(dialog).remove();
					}.bind(this),
					_('Cancel'): function() {
						this._cancelSubmit(this.options.form);
						$(dialog).dialog('destroy');
						$(dialog).remove();
					}.bind(this)
				}
			});
		}

		return false;
	},

	_doSubmit: function(form, files) {

		// Obtains the iframe
		var iframe = this._getIFrame(form);

		// Form attributes
		$(form).addClass('ready_to_send');

		if (!isNaN(files) && files > 0) {
			$(form).attr('action', $(form).attr('action') + '&files=' + files);
		}

		// jQuery Bug: $(elem).attr('target') devuelve un nodo con name=target o id=target, no el atributo target de $(elem)
		$(form).attr('target', this.options.iframeId);
		$(iframe).load(this._reloadFrame.bind(this, iframe));

		if (Object.isObject(this.options.actionView)) {
			this.options.actionView.history.push($(form).attr('action'));
		}

		$(form).submit();
	},

	_cancelSubmit: function(form) {
		var iframe = this._getIFrame(form);
		$(iframe).unbind().remove();
	},

	_getIFrame: function(form) {

		var iframe = $('iframe#' + this.options.iframeId);

		if (iframe.length == 0) {

			iframe = $('<iframe></iframe>')
				.attr({
					id: this.options.iframeId,
					src: 'about:blank',
					name: this.options.iframeId
				})
				.addClass('form_sender ui-helper-hidden');
//			$(form).after(iframe);
			$(iframe).appendTo('body');
		}

		return iframe;
	},

	_getDialog: function(form, message) {

		var dialog = $(form).next('div.form_send_dialog');

		if (dialog.length == 0) {

			dialog = $('<div class="form_send_dialog"><div/>').html(message);
			$(form).after(dialog);
		}

		return dialog;
	},

	_getConstraints: function(form) {
		var elements = {};

		$('.validable', form).each(function(id, validable) {

			var elementClass = $(validable).attr('class');
			var constraints = {};

			for (var i=0, l=this.options.searches.length; i<l; i++) {
				var search = this.options.searches[i];
				var result = elementClass.match(search);

				if (result !== null) {

					switch (result[1]) {
						case 'not_empty':
							constraints.required = true;
							break;

						case 'is_url':
							constraints.url = true;
							break;

						case 'is_email':
							constraints.email = true;
							break;

						case 'field_equals':
							constraints.equalTo = '#' + result[2];
							break;

						case 'check_group':
							var allclass = $(validable).attr('class');
							var regex = new RegExp('check_group__([^\\s]+)');
							var matchs = regex.exec(allclass);
							var group = matchs[0];
							if(elements[group] != group) {
								elements[group] = group;
								constraints.checks = matchs[0];
							}
							break;
					}
				}
			}

			var name = $(validable).attr('name');
			elements[name] = constraints;
		}.bind(this));

		return elements;
	},

	/**
	 * Carga el contenido de un panel tras un envio de formulario
	 * @return NULL
	 */
	_reloadFrame: function(event, iframe) {

		//fix for FF4.0
		if( null != iframe.target ) {
		  var iframe = iframe.target;
		}

		var content = $(iframe).contents().find('body').html();
		var sessionInfo = content.match(/<xim:meta name="X-XIMDEX" content="([^"]*)"/);

		if (!Object.isEmpty(sessionInfo)) {
			if (X.checkSession(sessionInfo[1] || '')) {
				return;
			}
		}

		if (!this.options.actionContainer || !Object.isString(content) || content.length == 0) {
			return false;
		}

		$(this.options.actionContainer).html(content);

		// Remove the iframe from de DOM preventing the infinite page load
		$(iframe)
			.unbind()
			.attr('src', 'about:blank')
			.remove();

		$(this).trigger('form-loaded', []);
	}

});
