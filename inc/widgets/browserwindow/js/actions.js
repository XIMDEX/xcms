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


(function(X) {

	var B = X.browser;

	X.ActionTypes = {
		create: [	
			'addfoldernode', 
			'addsectionnode',
			'createlink',
			'newemptynode', 
			'copy', 
			'createrole', 
			'createuser',
			'createxmlcontainer'
		],
		remove: [
			'deletenode',
			'movenode', 
			'copy', 
			'expiresection',
			'publicatesection'
		],
		reload: [
			'addximlet', 
			'linkreport', 
			'workflow_forward',
			'modifyserver',
			'modifygroupsnode'
		]
	};

	var idCount = 0;

	B.ActionView = Object.xo_create(B.AbstractView, {

		url: null,
		action: null,
		nodes: null,
		forms: null,
		history: null,

		_init: function(options) {
			B.ActionView._construct(this, options);

			this.id = 'browser-action-view-' + (++idCount);
			this.className = 'browser-action-view';
			this.content = $('<div></div>')
				.addClass('browser-action-view-content')
				.attr({id: this.id});
			this.forms = {};
			this.history = [];

			this.openAction(options);
		},

		openAction: function(options) {

			this.url = options.url || null;
			this.action = options.action || null;
			this.nodes = options.nodes || [];
			this.nodes = (!Object.isArray(this.nodes) ? [this.nodes] : this.nodes);
			this.label = options.label;

			if (this.url === null && this.action !== null) {
				this._createUrl();
			}

			this.history.push(this.url);

			this.content.load(this.url, function(data, textStatus, xhr) {
				this.processAction();
			}.bind(this));
		},

		actionDoneCallback: function(result, form) {
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
    		if (!submitError && nodeId) $(this.container).trigger('nodemodified', nodeId);
    		if (!submitError && result.oldParentID) $(this.container).trigger('nodemodified', result.oldParentID);

    		if (!submitError && X.ActionTypes.create.indexOf(this.action.command) != -1 ) form.reset();
    		if (!submitError && X.ActionTypes.remove.indexOf(this.action.command) != -1) {
    			this.close();
    			humane.log(messages, {addnCls: 'xim-notice'});
			} else {
				this.actionNotify(messages, $form, submitError);
			}
		},

		actionNotify: function(messages, $form, error) {
			var $message = $('<div class="message" style="display: none;"></div>');
			$message.addClass(error ? 'message-error':'message-success');
			for (var i = messages.length - 1; i >= 0; i--) {
				$message.html($message.html()+'<p>'+messages[i]+'</p>');
			};
			$form.find('.action_header').after($message);
			$message.slideDown();
			setTimeout(function(){$message.slideUp(400, function(){
				$message.remove();
			})}, 4000);	
		},

		getForm: function(name) {
			name = name || null;
			return name === null ? this.forms : (this.forms[name] || null);
		},

		_createUrl: function() {

			var action = '';
			if (this.action['actionid']) {

				action = 'actionid=' + this.action.actionid;
			} else if (this.action['command']) {

				action = 'action=' + this.action.command;
			} else {

				// No defined action
				action = 'action=_NONE_';
			}

			var nodes = [];
			for (var i=0,l=this.nodes.length; i<l; i++) {
				nodes.push('nodes[]=' + this.nodes[i]);
			};

			var actionParams = '';
			if (this.action.params && this.action.params.length > 0) {
				actionParams = this.action.params;
			}

			var qs = [
				nodes.join('&'),
				action,
				'noCacheVar='+(new Date().getTime())
			];

			if (!Object.isEmpty(this.action.method)) qs.push('method='+this.action.method);
			if (!Object.isEmpty(this.action.module)) qs.push('mod='+this.action.module);
			if (!Object.isEmpty(actionParams)) qs.push(actionParams);


			qs = qs.join('&');
			this.url = '%s?%s'.printf(X.restUrl, qs);
		},

		close: function() {
			$(this.container).tabs('remove', this.tabId());
		},

		//onLink: function() {
		//},

		redirect: function(url, params) {
			$('form', this.content).attr('action', url);
		},

		setActionContent: function(content) {
			$(this.content).unbind().empty().append(content);
			this.forms = {};
			this.processAction();
		},

		processAction: function() {

			// jQuery plugin in extensions folder
			$(this.content).labelWidth();

			this._loadAssets();
			this._bindFormEvents();
		},

		_bindFormEvents: function() {

			// For each form create a FormsManager instance and register events for assets management
			$('form', this.content).each(function(index, form) {

				var name = !Object.isEmpty($(form).attr('id'))
					? $(form).attr('id')
					: (!Object.isEmpty($(form).attr('name'))
						? $(form).attr('name')
						: 'form_' + X.getUID()
						);

				this.forms[name] = form;

				var fm = new X.FormsManager({
					actionView: this,
					actionContainer: this.content,
					form: form
				});

				$(fm).bind('form-loaded', function(event) {
					this.processAction();
				}.bind(this));

			}.bind(this));

			// Close the action tab when the 'Close' button is clicked
			$('fieldset.buttons-form .close-button', this.content).click(function(e) {
				this.close();
				return false;
			}.bind(this));

			// Controls the logic when the 'Go back' button is clicked
			$('fieldset.buttons-form .goback-button', this.content).click(function(e) {

				var history = $('fieldset.buttons-form .history-value', this.content);
				history = history.html() || 1;

				while (this.history.length > 0 && history > 0) {
					this.history.pop();
					history--;
				}

				var url = this.history[this.history.length-1];
				this.content.load(url, function(data, textStatus, xhr) {
					this.processAction();
				}.bind(this));
				return false;
			}.bind(this));
		},

		_loadAssets: function() {

			var css = $('ul.css_to_include li', this.content).map(function(index, item) {
				return $(item).html();
			});
			css = $.makeArray(css);

			var js = $('ul.js_to_include li', this.content).map(function(index, item) {
				var url = $(item).html();
				url = Object.urldecode(url.replace(/&amp;/g, '&', url));
				return url;
			}.bind(this));

			js = {
				onComplete: this._onAssetsCompleted.bind(this),
				//onLoad: this._onScriptLoaded.bind(this),
				js: js
			};

			Object.loadCss(css);

			if (js.js.length > 0) {
				Object.loadScript(js);
			} else {
				this._onAssetsCompleted();
			}
		},

		_onAssetsCompleted: function() {
			console.log(this.action);
			X.triggerActionLoaded({
				actionView: this,
				browser: this.browser,
				context: this.content,
				url: this.url,
				action: this.action,
				nodes: this.nodes,
				tabId: this.tabId()
			});
		}
	});

})(com.ximdex);
