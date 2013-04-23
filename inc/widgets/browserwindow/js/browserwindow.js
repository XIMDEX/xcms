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


window.com.ximdex = Object.extend(window.com.ximdex, {

	browser: {
		actionUrl: X.baseUrl + window.base_action,
		imagesUrl: X.baseUrl + window.base_action + 'resources/images',
		cssUrl: X.baseUrl + window.base_action + 'resources/css',
		jsUrl: X.baseUrl + window.base_action + 'resources/js',
		panels: {
			leftTabs: [],
			rightTabs: []
		},
		actionEventSelector: document
	},
	actionLoaded: function(cb) {
		$(X.browser.actionEventSelector).one('action-loaded', cb);
	},
	triggerActionLoaded: function(params) {
		// IMPORTANT: Contextualized wrapper for jQuery function.
		// For use inside any action javascript.
		var fn = (function(jq, context) {
			return function(selector, c) {
				c = (c === undefined) ? context : $(c, context);
				return jq(selector, c);
			}
		})(jQuery, params.context);
		$(X.browser.actionEventSelector).trigger('action-loaded', [fn, params]);
	}
});


(function(X) {

	var B = X.browser;

	var defaultAccels = [
		{event: 'searchpanel-open', ctrlKey: true, altKey: true, char: 's'},
		{event: 'tab-close', ctrlKey: true, altKey: true, char: 't'},
		{event: 'tab-all-close', ctrlKey: true, altKey: true, char: 'c'},
		{event: 'tab-left', ctrlKey: true, altKey: true, keyCode: 37},
		{event: 'tab-right', ctrlKey: true, altKey: true, keyCode: 39},
		{event: 'project-view-tree',  ctrlKey: true, altKey: true, char: '1'},
		{event: 'project-view-grid',  ctrlKey: true, altKey: true, char: '2'},
		{event: 'project-view-list',  ctrlKey: true, altKey: true, char: '3'},
		{event: 'tab-dummy-action', ctrlKey: true, char: '0'},
		{event: 'dialog-close', keyCode: 27}
	];

	function getConstructor(cname) {
		var Constructor = null;
		try {
			Constructor = eval('window.com.ximdex.browser.panels.' + cname);
		} catch (e) {
			console.error('[Browser] No se encuentra el constructor %s.'.printf(constructor));
		}
		return Constructor;
	}

	var browserwindow = {

		hbox: null,
		panels: null,
		eh: null,
		cachedActions: new Object(),

		_init: function() {

//	console.info('Browser: ', this);
			X.ximModules.init();

			/** ************************* INIT SPLASH ************************** */
			$("#ximdex-splash")
			.width($(document).width() )
			.height($(document).height() );

			$("#ximdex-splash .progress").width("100%");
			//max progress width
			var max = $("#ximdex-splash .progress").width();

			$("#ximdex-splash .progress").width("7%");

			// Increment splash percent every 1 seconds
			intervalProgress = setInterval(function() {
				var width = $("#ximdex-splash .progress").width();
				if(0 != width && width < max ) {
					width = parseInt(width, 10) + 35;
					$("#ximdex-splash .progress").width(width);
				}else {
					clearInterval(intervalProgress);
					$("#ximdex-splash").fadeOut(1300);
				}
			}, 300);


			/** ****************************************************************** */

			this.panels = {};
			this.eh = new X.EventHandler(this.options.accels);

			$('.browser-window-content .browser-hbox', this.element).hbox({panels: this.options.panels});
			this.hbox = $('.browser-window-content .browser-hbox', this.element);


			for (var i=0,l=this.hbox.hbox('length'); i<l; i++) {

				var panel = this.hbox.hbox('getPanel', i);
				var constructor = this.options.panelsContent[i];
				var Constructor = getConstructor(constructor);
				var key = '%s.tab'.printf(constructor);

				var value = X.session.get(key);
				var c = new Constructor({
					name: constructor,
					browser: this.element,
					container: panel.content(),
					activeTab: value || 0
				});

				this.panels[constructor] = c;
			}

			
			//contextmenu in logo
			$('.xim-contextmenu-container',  this.element).contextmenu();

			this.registerTriggers();
			this.registerEvents();
			this.registerGlobalAjaxEvents();


		},

		appendView: function(panelName, view) {
			var panel = this.panels[panelName] || null;
			if (panel === null) return;
			panel.addTab(view);
		},

		openAction: function(action, nodes) {
			var panel = this.panels['RightPanel'];
			if (Object.isFunction(panel.openAction)) panel.openAction(action, nodes);
		},

		getActions: function(params) {
			var that = this;
			params = $.extend({
				nodes: [],
				cb: function() {}
			}, params);

			var ids = [];
			for (var i=0,l=params.nodes.length; i<l; i++) {
				try {
					ids.push(params.nodes[i].nodeid.value);
				} catch(e) {}
			}

			var cachedKey = ids.sort().join("-");
			
			// NOTE: Wrapping the callback function and the variables
			// will not produce collisions with others asynchronous calls.
			var success = (function(params, fn) {
				return function(data) {
					//Cache the result for this node (or nodes) using cachedKey and data
					if(cachedKey != "") {
						that.cachedActions[cachedKey] = data;
					}
					if (Object.isFunction(fn)) {
						fn($.extend({}, params, {actions: data}));
					}
				}
			})($.extend({ids: ids}, params, {cb: null}), params.cb);

			//If actions were already requested, use them.
			//If actions were already requested, use them.
			if(cachedKey != "" && that.cachedActions.hasOwnProperty(cachedKey)) {
				// setTimeout is neccesary in order to prevent the modal dialog with actions dissapears
				// after it appears
				
				setTimeout(function() {
						if (Object.isFunction(params.cb)) {
							params.cb($.extend({ids: ids}, params, {cb:null, actions: that.cachedActions[cachedKey]})); 
						}
					  },100);
			}
			//Otherwise make the request
			else {
				$.get(
					'?action=browser3&method=cmenu'.printf(X.restUrl),
					{'nodes[]': ids},
					success
				);
			}
		},

		registerTriggers: function() {

			$(document).keydown(function(event) {

				var accel = false;

				if ((accel = this.eh.match(event)) !== false) {
					$(this.element).trigger(accel.event, [{event: event, char: accel.char}]);
				}

			}.bind(this));

			$(document).click(function(event) {
				$('.hide-on-click', document).hide();
				$('.destroy-on-click', document).unbind().remove();
			});
		},

		registerEvents: function() {

			$(this.element).bind('dialog-close', function() {
				$('.hide-on-click', document).hide();
				$('.destroy-on-click', document).unbind().remove();
			});
		},

		registerGlobalAjaxEvents: function() {

			$(this.element)
				.ajaxError(function(event, xhr, ajaxOptions, thrownError) {
					var xheader = xhr.getResponseHeader('X-XIMDEX');
					X.checkSession(xheader);
				})
				.ajaxComplete(function(event, xhr, ajaxOptions) {
					var xheader = xhr.getResponseHeader('X-XIMDEX');
					X.checkSession(xheader);
				});
		},

		options: {
			title: '',
			conf: {},
			panels: 2,
			panelsContent: ['LeftPanel', 'RightPanel'],
			accels: defaultAccels
		},

		getter: []
	};


	$.widget('ui.browserwindow', browserwindow);

})(com.ximdex);
