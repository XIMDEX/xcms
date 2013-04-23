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


(function(X) {

	var P = window.com.ximdex.browser.panels;

	P.TabbedPanel = {

		tabs: null,
		name: null,
		browser: null,
		container: null,
		tabsItems: null,
		saveActive: null,
		lastSavedTab: null,
		closeable: null,

		_init: function(options) {

			this.name = options.name;
			this.browser = options.browser || null;
			this.container = options.container || null;
			this.saveActive = options.saveActive || false;
			this.closeable = options.closeable || false;

			this.tabs = $('<div></div>').appendTo(this.container);
			$('<ul></ul>').addClass('ul').appendTo(this.tabs);

			this.tabs.tabs({
				spinner: 'Retrieving data...',
				select: function(event, ui) {
					$('.destroy-on-click').off().remove();
					$('.hide-on-click').hide();
				},
				hbox: $('.browser-hbox')
			});

			if (this.closeable) {
				var tabTemplate = '<li><div class="ui-tab-close"></div><a href="#{href}"><span>#{label}</span></a></li>';
				this.tabs.tabs('option', 'tabTemplate', tabTemplate);
				$(document).on('click','.ui-tabs ul.ui-tabs-nav li .ui-tab-close',  function(event) {
					event.stopPropagation();
					var tabId = $(event.target).closest('li').data('tabId');
					this.closeTab(tabId);
					return false;
				}.bind(this));
			}

			this.registerEvents();
		},

		addTab: function(c) {
			/* Added if else flow, and id to the anchors of tabs in the form IdNode_actionCommand in order to not open a new tab
			for the same action*/
			var theTabId = c.nodes ? c.nodes.join("-")+"_"+c.action.command : "";
			var theNodes = c.nodes ? c.nodes.join("-") : "";
			if(theNodes != "" && theTabId != "" && $("#"+theTabId).length) {
				var theTab = $("#"+theTabId);
				var index = theTab.closest("li").index();
				this.tabs.tabs('select', index);
			}
			else {
				c.link(this.browser, this.tabs);

				this.tabs
					.tabs('add', c.getUrl(), c.getLabel(), (c.tabId() || undefined))
					.bind('tabsselect', function(event, ui) {

						this.tabs.tabs('updateTabsNav', ui);
						if (!this.saveActive) return;
						// This event is fired more than once, this is not necessary...
						if (this.lastSavedTab == ui.index) return;
						this.lastSavedTab = ui.index;
						X.session.set('%s.tab'.printf(this.name), ui.index);
					}.bind(this));

				var tabId = (this.tabs.tabs('length') - 1) || 0;
				$('a[href=#'+c.getId()+']', this.tabs)
					.addClass(c.getClass())
									.attr('id', theTabId)

					.closest('li')
					.data('tabId', tabId)
					c.tabId(tabId);

				this.tabs.tabs('select', tabId);
				this.tabs.tabs('adjustTabClasses');
			}
		},

		loadTabs: function() {
			this.tabsItems.each(function(index, item) {
				var Constructor = eval('X.browser.'+item);
				if (Object.isFunction(Constructor)) {
					var c = new Constructor({});
					this.addTab(c);
				}
			}.bind(this));
		},

		activeTab: function(index) {

			if (index === undefined) {
				return this.tabs.tabs('select');
			}

			if (isNaN(index = parseInt(index))) return this;

			this.tabs.tabs('select', index);
			return this;
		},

		closeTab: function(tabId) {
			if (!this.closeable) return;
			this.tabs.tabs('remove', tabId);
			// Reorder the ids
			$('ul.ui-tabs-nav li', this.tabs).each(function(index, item) {
				$(item).data('tabId', index);
			});
		},

		registerEvents: function() {

			var getListener = function(name) {
				return Object.isFunction(this[name]) ? this[name].bind(this) : function() {};
			}.bind(this);

			this.tabs.bind('tabsadd', getListener('onTabsAdd'));
			this.tabs.bind('tabsremove', getListener('onTabsRemove'));

			$(this.browser).bind('tab-close', getListener('onTabClose'));

			$(this.browser).bind('tab-left', getListener('onTabLeft'));
			$(this.browser).bind('tab-right', getListener('onTabRight'));
		},

		onTabClose: function(event, params) {

			if (!this.closeable) return;

			var tab = $(this.tabs).tabs('selected');
			if (tab === null) return;
			var tabId = tab.data('tabId');
			event.stopPropagation();
			this.closeTab(tabId);

			if( null != event.originalEvent)
				event.originalEvent.preventDefault();

			return false;
		},

		onTabLeft: function(event, params) {

			var tab = $(this.tabs).tabs('selected');
			if (tab === null) return;
			var tabId = tab.data('tabId');
			event.stopPropagation();
			$('ul.ui-tabs-nav a').focus();
			var id = tab.prev('li').data('tabId');
			if (id === undefined) {
				id = this.tabs.tabs('length') - 1;
			}
			this.tabs.tabs('select', id);

			if( null != event.originalEvent)
				event.originalEvent.preventDefault();

			return false;
		},

		onTabRight: function(event, params) {


			var tab = $(this.tabs).tabs('selected');

			if (tab === null) return;
			var tabId = tab.data('tabId');
			event.stopPropagation();
			$('ul.ui-tabs-nav a').focus();
			var id = tab.next('li').data('tabId');
			if (id === undefined) {
				id = 0;
			}

			if( null != event.originalEvent)
				event.originalEvent.preventDefault();
			this.tabs.tabs('select', id);

			return false;
		},

		onTabAllClose: function(event, params) {
			var list = $(this.tabs).tabs("getTabsList");
			var total = list.length;

			for(i= total-1; i>= 0; i--) {
				$(this.tabs).tabs("remove", i);
			}


			this.tabs.tabs('dummyTab');

			if( null != event.originalEvent)
				event.originalEvent.preventDefault();

			return false;
		},

	};

	P.LeftPanel = Object.xo_create(P.TabbedPanel, {
		_init: function(options) {
			this.tabsItems = P.leftTabs.clone();
			P.LeftPanel._construct(this, $.extend(options, {saveActive: true, closeable: false}));
			this.loadTabs();
			this.activeTab(options.activeTab);
		},

		onTabLeft: function(event, params) {
		},

		onTabRight: function(event, params) {
		},
	});


	P.RightPanel = Object.xo_create(P.TabbedPanel, {

		_init: function(options) {
			this.tabsItems = P.rightTabs.clone();
			P.RightPanel._construct(this, $.extend(options, {saveActive: false, closeable: true}));
			this.loadTabs();
			this.activeTab(options.activeTab);
			$(this.browser).bind('tab-dummy-action', this.onTabDummyAction.bind(this));
			$(this.browser).bind('tab-all-close', this.onTabAllClose.bind(this) );

			this.tabs.tabs('dummyTab');

			$('#header .selector_language li').click(function (e) {
				var lang = e.target.innerHTML;
				var code = $(':input', e.target).val();

				this.openAction({
				label: lang,
				name: lang,
				command: 'changelang',
				params: 'code='+code,
				bulk: '0'
			}, 10000) }.bind(this) );

			$("#ximdex-splash .progress").width("50%");
			this.openAction({
                                                label: _("Welcome to new Ximdex 3.3!"),
                                                name:  _("Welcome to new Ximdex 3.3!"),
                                                command: 'welcome',
                                                params: '',
                                                bulk: '0'
                                        }, 10000);



//			if (load_welcome ) {
//
//				$.getJSON(
//                                X.restUrl + '?method=getDefaultNode&ajax=json',
//                                function(data) {
//					var node_list = data["nodes"];
//
//					this.openAction({
//                                                label: _("Welcome to new Ximdex 3.2!"),
//                                                name:  _("Welcome to new Ximdex 3.2!"),
//                                                command: 'welcome',
//                                                params: '',
//                                                bulk: '0'
//                                        }, 10000);
//
//					if (node_list && node_list.length && node_list[0]["IdNode"]){
//						this.openAction({
//                	                        	label: _("Edit XML document"),
//	                                	        name: _("Edit XML document"),
//        	                	                command:'xmleditor2',
//                	                        	params:'',
//	                        	                bulk:'0'
//	                                	},data["nodes"][0]["IdNode"]);
//					}
//
//
//                                }.bind(this)
//
//                            );
//
//
//
//			}
		},

		onTabDummyAction: function(event, params) {
			this.openAction({
				label: 'Dummy - ' + (new Date()).getTime(),
				name: 'Dummy',
				command: 'dummy',
				icon: 'create_proyect.png',
				module: '',
				params: '',
				callback: 'callAction',
				bulk: '0'
			}, 10000);
		},

		openAction: function(action, nodes) {

			var bulk = parseInt(action.bulk) == 1 ? true : false;
			var _nodes = !Object.isArray(nodes) ? [nodes] : nodes;
			var xnodes = bulk ? [_nodes] : _nodes;

			for (var i=0,l=xnodes.length; i<l; i++) {

				var options = {
					label: action.name,

					action: action,
					nodes: xnodes[i]
				};

				var view = new X.browser.ActionView(options);
				this.addTab(view);

			}
		},

		onTabsAdd: function() {
			this.tabs.tabs('dummyTab');
		}

	});

 })(com.ximdex);
