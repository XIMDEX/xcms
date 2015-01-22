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

/**
 * Control the forms submission logic.
 */

X.FormsManager = Object.xo_create({

	buttons: null,

	_init: function(options) {

		this.options = Object.extend({
			actionView: null,
            tabId: '',
			iframeId: '',
			actionContainer: null,
			form: null,
			searches: [
				'(not_empty)', '(is_url)', '(is_email)', '(js_val_min)__([^\d]+)',
				'(field_equals)__([^\\s]+)', '(check_group)__([^\\s]+)', '(js_val_alphanumeric)',
				'(js_val_unique_name)', '(js_val_unique_url)'
			]
		}, options);

		// Don't want the jQuery object, want the DOMElement
		$(this.options.form).get(0)['getFormMgr'] = function() {
			return this;
		}.bind(this);

		this._checkInputFields();
		this._registerButtons();
	},

	/**
	 * Checks and corrects inputs behaviours.
	 */
	_checkInputFields: function() {

		// var $fields = $('input[type=text]', this.options.form);

		// // // Don't send a form when enter key pressed
		// // $fields.keydown(function(event) {
		// // 	if (event.keyCode == 13) {
		// // 		return false;
		// // 	}
		// // });
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
			file: 0,
			jsonResponse: true
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

		$(button).click(trySubmit);
		$(this.options.form).keydown(function(event){
			if (event.target.nodeName !== 'TEXTAREA') {
				if (event.keyCode == 13) {
					return trySubmit(event);
					// return false;
				}
			}
		});
		this.buttons.push(button);
		
		function trySubmit(event) {
			if (!this.blockSubmit) {	
				// Call the callbacks, if one of them returns TRUE the form will not be submitted
				var submit = true;
				var i = 0, l = button.beforeSubmit.size();
				while (submit && i<l) {
					var cb = button.beforeSubmit.get(i);
					if (Object.isFunction(cb)) {
						var abort = cb(event, button);
						submit = abort === true ? false : true;
					}
					i++;
				}

				if (!submit) return false;

				button.getFormMgr().sendForm($.extend({
					button: button
				}, options));

				return false;
			}	
		}

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
	 * button: the DOMElement that will act as a submit button when clicked.
	 * confirm: When TRUE a confirmation message will be shown.
	 * message: The confirmation message. When NULL the value attribute of the button will be used.
	 * file: ???
	 */
	sendForm: function(options) {

		options = $.extend({
			button: null,
			confirm: true,
			message: null,
			file: 0,
			jsonResponse: false
		}, options);

		// Ensure the form and iframe IDs are unique
		var formId = $(this.options.form).attr('id');
		if (Object.isEmpty(formId)) {
			formId = 'form_' + X.getUID();
			$(this.options.form).attr('id', formId);
		}
		this.options.iframeId = 'form_sender_' + formId;

		// Validate the form
		if (!Object.isEmpty($(this.options.form).data('validator')) || $(this.options.form).data('validator') == null) {			
			$(this.options.form).validate(
				{
					rules: this._getConstraints(this.options.form),
					messages: this._getMessages(this.options.form),
					errorElement: "span",
					errorPlacement: function(error, element) {
						
						$span = $("<span/>").addClass("error_block");
						if (element[0].tagName.toLowerCase()=="select" && element.parent().hasClass("input-select"))
							$span.addClass("select_block");
						element.wrap($span);
						element.before(error);
					}
				});
		}

		// Submit button
		var submitButton = options.button || $('.validate', this.options.form);
		var message = options.message || $('~ .submit_message', submitButton).attr('value');
		var dialog = this._getDialog(this.options.form, message);
		if (message == '' || message == undefined) {
			options.confirm = false;
		}

		// Send the form
		if (!options.confirm) {
			this._doSubmit(this.options.form, options.files, submitButton, options.jsonResponse);
		} else {
			_this = this;
			var dialogButtons = {};
			dialogButtons[_('Cancel')] = function() {
				_this._cancelSubmit(_this.options.form);
				$(dialog).dialog('destroy');
				$(dialog).remove();
			};
			dialogButtons[_('Accept')] = function() {
				_this._doSubmit(_this.options.form, options.files, submitButton, options.jsonResponse);
				$(dialog).dialog('destroy');
				$(dialog).remove();
			};
			$(dialog).dialog({
				title: 'Ximdex Notifications',
				modal: true,
				buttons: dialogButtons
			});
		}

		return false;
	},

	_doSubmit: function(form, files, button, jsonResponse) {

		// Obtains the iframe
		var iframe = this._getIFrame(form);
		$form = $(form);
		// Form attributes
		$form.addClass('ready_to_send');

		if (!isNaN(files) && files > 0) {
			$form.attr('action', $form.attr('action') + '&files=' + files);
		}

		// jQuery Bug: $(elem).attr('target') returns a node with name=target o id=target, not the target attribute of $(elem)
		$form.attr('target', this.options.iframeId);
		$(iframe).load(this._reloadFrame.bind(this, iframe));

		/*if (Object.isObject(this.options.actionView)) {
			this.options.actionView.history.push($form.attr('action'));
		}*/
		
		if (jsonResponse && X.ActionTypes.reload.indexOf(this.options.actionView.action.command) == -1) {
			if ($(form).valid()) {
				button = button[0] || button;
				if (button) var loader = Ladda.create(button).start();//Start button loading animation
				//this.blockSubmit = true;
				var _this = this;

                angular.element('#angular-content').scope().submitForm({
                    reload: false,
                    tabId: this.options.tabId,
                    url: $form.attr('action'),
                    data: $form.serialize(),
                    callback: function (args) {
                            if (loader) loader.stop();
                            if (args.error) {
                                _this.actionNotify([_('Internal server error')], $form, true);
                            } else {
                                _this.actionDoneCallback(args.data, form, args.tab);
                            }
                        }
                });
				/*$.ajax({
			        url: $form.attr('action'),
			        type: 'post',
			        dataType: 'json',
			        contentType: "application/x-www-form-urlencoded",
			        data: $form.serialize(),
			        success: function(data) {	
			        	if(loader) loader.stop();//Stop loading animation
			        	if (data && data.messages) {
			        		_this.options.actionView.actionDoneCallback(data, form);
			        	}
			        	this.blockSubmit = false;
			        },
			        error: function(error) {
			        	_this.options.actionView.actionNotify([_('Internal server error')], $form, true);
			        	loader.stop();
			        	this.blockSubmit = false;
			        }
			    });*/
			}
		} else {
			button = button[0] || button;
			if (button) var loader = Ladda.create(button).start();//Start button loading animation
			//form.submit();
            angular.element('#angular-content').scope().submitForm({
                reload: true,
                tabId: this.options.tabId,
                url: $form.attr('action'),
                data: $form.serialize(),
                callback: function () {
                    if (loader) loader.stop();
                }
            });
		}
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
						case 'js_val_min':
							constraints.minlength = result[2];
							break;
						case 'js_val_alphanumeric':
							constraints.alphanumeric = true;
							break;
						case 'js_val_unique_name':
							var idnode = $(validable).attr("data-idnode");
							constraints["remote"] = {
							    url: X.restUrl+"?action=browser3",
                                data: {
									inputName:$(validable).attr("name"),
									nodeid: idnode,
									method:"validation",
									validationMethod:"isUniqueName"
								},
                      			type: "post",                      			
                      		};                   		
							
							break;
						case 'js_val_unique_url':
							var idnode = $(validable).attr("data-idnode");
							constraints["remote"] = {
							    url: X.restUrl+"?action=browser3",
                                data: {
                                    nodeid: idnode,
									inputName:$(validable).attr("name"),
									method:"validation",
									validationMethod:"isUniqueUrl"
								},
                      			type: "post",                      			
                      		};                   		
							
							break;

						case 'check_group':
							var allclass = $(validable).attr('class');
							var regex = new RegExp('check_group__([^\\s]+)');
							var matchs = regex.exec(allclass);
							var group = matchs[0];
							constraints.checks = matchs[0];
							break;
					}
				}
			}

			var name = $(validable).attr('name');
			elements[name] = constraints;
		}.bind(this));
		return elements;
	},

	_getMessages: function(form) {
		var elements = {};

		$('.validable', form).each(function(id, validable) {

			var elementClass = $(validable).attr('class');
			var messages = {};

			for (var i=0, l=this.options.searches.length; i<l; i++) {
				var search = this.options.searches[i];
				var result = elementClass.match(search);

				if (result !== null) {

					switch (result[1]) {
						case 'js_val_unique_name':
							messages["remote"] = _("A node with this name already exists for current parent node.");
							break;
						case 'js_val_unique_url':
							messages["remote"] = _("A link with this url already exists.");							
							break;
					}
				}
			}

			var name = $(validable).attr('name');
			elements[name] = messages;
		}.bind(this));

		return elements;
	},

	/**
	 * Loads the content of a panel after sending the form
	 * @return NULL
	 */
	_reloadFrame: function(event, iframe) {

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
	},
    actionDoneCallback: function(result, form, tab) {
        $form = $(form);

        //Messaging

        var submitError = false;
        var messages = [];
        $.each(result.messages, function(key, message){
            messages.push(message.message);

            if (message.type === 0) submitError = true;
        });
        var nodeId = result.parentID || result.nodeID || result.idNode;
        //Refresh node
        if (!submitError && nodeId) $("#"+tab.id+"_content").trigger('nodemodified', nodeId);
        if (!submitError && result.oldParentID) $("#"+tab.id+"_content").trigger('nodemodified', result.oldParentID);

        if (!submitError && X.ActionTypes.create.indexOf(tab.action.command) != -1 ) form.get(0).reset();
        if (!submitError && X.ActionTypes.remove.indexOf(tab.action.command) != -1) {
            $('#angular-content').scope().closeTabById(tab.id);
            humane.log(messages, {addnCls: 'notification-success'});
        } else {
            this.actionNotify(result.messages, $form, submitError);
        }
    },

    actionNotify: function(messages, $form, error) {
        for (var i = messages.length - 1; i >= 0; i--) {
            (function(msg,form){
                var message = $('<div class="message" style="display: none;"><p>'+msg.message+'</p></div>');
                switch (msg.type){
                    case 0:
                        message.addClass('message-error');
                        break;
                    case 1:
                        message.addClass('message-warning');
                        break;
                    default:
                        message.addClass('message-success');
                }
                form.find('.action_header').after(message);
                message.slideDown();
                setTimeout(function(){message.slideUp(400, function(){
                    message.remove();
                })}, 4000);
            })(messages[i],$form);
        };

    }

});
