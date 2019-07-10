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

	X.browser.AbstractView = Object.xo_create({

		browser: null,
		container: null,
		_tabId: undefined,
		menubar: null,
		content: null,
		id: null,
		classIdentifier: null,
		label: null,

		_init: function(options) {
		},
		link: function(browser, container) {
			this.browser = browser;
			this.container = container;
			this.content.appendTo(container);
			if (Object.isFunction(this.onLink)) this.onLink();
		},
		getBrowser: function() {
			return this.browser;
		},
		getContainer: function() {
			return this.container;
		},
		tabId: function(id) {
			if (id >= 0) {
				this._tabId = id;
				return this;
			}
			return this._tabId;
		},
		getContent: function() {
			return this.content;
		},
		getId: function() {
			return this.id;
		},
		getClass: function() {
			return this.className;
		},
		getUrl: function() {
			return '#'+this.id;
		},
		getLabel: function() {
			return this.label;
		},
		isSelected: function() {
			return $(this.container).tabs('isSelected', this.tabId());
		},
		setViewTitle: function(title) {
			var c = $('.browser-view-title', this.content);
			if (c.length == 0) {
				c = $('<div></div>')
					.addClass('browser-view-title');
			}
			c
				.html(title)
				.appendTo(this.content);
		},
		createButton: function(options) {
			options = $.extend({
				text: '',
				title: '',
				value: '',
				data: {},
				className: '',
				click: function() {},
				icon: null,
				container: null
			}, options);

			var b = $('<button></button>')
				.addClass('window-toolbar-button '+options.className + ' ' +options.value)
				//.html($('<span></span><span class="tooltip"></span>').html(options.text))
				.val(options.value)
				.data('data', options.data)
//				.attr('title', options.title)
				.click(options.click);

			$(b)
				.append($('<span>Icono</span>'))
				.append($('<span class="tooltip"></span>').html(options.text));

			if (!Object.isEmpty(options.icon)) {

				var icon = options.icon.substr(0, options.icon.length-4);
				b.addClass('icon-'+icon);
			}

			if (options.container !== null) {
				b.appendTo(options.container);
			}
			$(b).wrap('<div class="button-container"/>');
			return b;
		},
		createButtonList: function(options) {
			options = $.extend({
				text: '',
				title: '',
				value: '',
				data: {},
				className: '',
				click: function() {},
				icon: null,
				container: null
			}, options);


			var ndiv = $('<div/>').addClass('button-container-list');
			var ndiv = $('<div/>').addClass('button-container-list icon').addClass(options.value).html(options.text);
			ndiv.data('data',options.data).click(options.click);

			/*if (!Object.isEmpty(options.icon)) {

				var icon = options.icon.substr(0, options.icon.length-4);
				ndiv.append($('<span/>').addClass(options.value).addClass('list-icon').html("Icono"));
			}

			ndiv.append($('<span/>').addClass('button-text icon').addClass(options.value).html(options.text));*/

			if (options.container !== null) {
				ndiv.appendTo(options.container);
			}

			return ndiv;
		},
		createFloatMenu: function(params) {

			var nodeid = params.data.nodeid.value;
			var cmenu = $('div.xim-actions-menu').unbind().empty();
			var show = true;

			if (cmenu.length == 0) {

				cmenu = $('<div></div>')
					.addClass('xim-actions-menu destroy-on-click')
					.attr('id', 'cmenu-'+nodeid);

			} else if ($(cmenu).attr('id') == 'cmenu-'+nodeid) {
				show = false;
			}

			show = show & (params.actions.length > 0);

			if (!show) {
				$(cmenu).unbind().remove();
				return;
			}

			if (Object.isFunction(params.actions.each)) {

				params.actions.each(function(index, item) {
					item.params = item.params.replace('%3D', '=');
					this.createButton({
						text: item.name,
						title: item.name,
						value: item.command,
						data: {
							action: item,
							nodes: params.nodes,
							ids: params.ids
						},
						className: 'view-action',
						icon: item.icon,
						click: function(e) {
							var data = $(e.currentTarget).data('data');
							this.browser.browserwindow('openAction', data.action, params.ids);
						}.bind(this),
						container: cmenu
					});
				}.bind(this));

				cmenu
					.css({
						position: 'absolute',
						left: params.menuPos.x,
						top: params.menuPos.y
					})
					.appendTo('body');

			}
		},
		createFloatMenuList: function(params) {

			var nodeid = params.data.nodeid.value;
			var cmenu = $('div.xim-actions-menu').unbind().empty();
			var show = true;

			if (cmenu.length == 0) {

				cmenu = $('<div></div>')
					.addClass('xim-actions-menu xim-actions-menu-list destroy-on-click')
					.attr('id', 'cmenu-'+nodeid);

			} else if ($(cmenu).attr('id') == 'cmenu-'+nodeid) {
				show = false;
			}

			show = show & (params.actions.length > 0);

			if (!show) {
				$(cmenu).unbind().remove();
				return;
			}

			if (Object.isFunction(params.actions.each)) {

				params.actions.each(function(index, item) {
					item.params = item.params.replace('%3D', '=');
					this.createButtonList({
						text: item.name,
						title: item.name,
						value: item.command,
						data: {
							action: item,
							nodes: params.nodes,
							ids: params.ids
						},
						className: 'view-action',
						icon: item.icon,
						click: function(e) {
							var data = $(e.currentTarget).data('data');
							this.browser.browserwindow('openAction', data.action, params.ids);
						}.bind(this),
						container: cmenu
					});
				}.bind(this));




				cmenu
					.css({
						position: 'absolute',
						left: params.menuPos.x,
						top: params.menuPos.y
					})
					.appendTo('body');

				//Detect End Page Collision
				var windowY = window.innerHeight;
				var menuY = $(cmenu).height();
				var finY  = menuY + params.menuPos.y;

				if(finY > windowY){
					params.menuPos.y = windowY - menuY - 20;
					$(cmenu).css({
						top: params.menuPos.y
					});
				}

			}
		}
	});

})(com.ximdex);
