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

	var classes = {
		TAB_FIRST: 'xim-first-tab',
		TAB_LAST: 'xim-last-tab',
		TAB_HIDDEN: 'xim-hidden-tab',
		TAB_NAV: 'xim-tabs-nav',
		TAB_LIST: 'xim-tabs-list',
		TAB_LIST_SEL: 'xim-tabs-list-selector',
		TAB_LIST_ITEM: 'xim-tabs-list-item',
		TAB_LIST_HITEM: 'xim-tabs-list-item-hidden',
		TAB_LIST_SITEM: 'xim-tabs-list-item-selected'
	};

	$.extend($.ui.tabs.prototype, {

		tabsContainer: null,
		tabsNav: null,
		closingtab: false,

		_init: function() {

			this.tabsContainer = $(this.element).parent();
			this.tabsNav = $('<div></div>').addClass(classes.TAB_NAV);
			this.tabsContainer.prepend(this.tabsNav);

			$('<div></div>')
				.addClass(classes.TAB_LIST_SEL + ' ' + classes.TAB_HIDDEN)
				.appendTo(this.tabsNav)
				.click(function(e) {
					$(this).next('ul.'+classes.TAB_LIST).toggle(100, function() {
						// Assigns the 'hide-on-click' class after the transition
						// because of the listener that will hide it when click
						// event occurs.
						$(this).next('ul.'+classes.TAB_LIST).addClass('hide-on-click');
					}.bind(this));
				})
				.bind("contextmenu", function(e) {
					this.show_contextual();
				}.bind(this));

			$('<ul></ul>')
				.addClass(classes.TAB_LIST)
				.appendTo(this.tabsNav);


			$(this.element)
				.bind('tabsadd', this.onTabAdded.bind(this))
				.bind('tabsremove', this.onTabRemoved.bind(this));

			// The hbox element is passed from the TabbedPanel object
			$(this.options.hbox)
				.bind('dragstart', function() {$(this.tabs).removeClass(classes.TAB_HIDDEN);}.bind(this))
				.bind('dragstop', this.refreshTabs.bind(this));


		},

		/**
		 * Returns the selected tab.
		 */
		selected: function() {
			var selected = $('.ui-tabs-selected', this.tablist);
			selected = selected.length == 0 ? null : selected;
			return selected;
		},

		/**
		 * Returns TRUE if the tab with the specified ID is selected.
		 */
		isSelected: function(tabId) {
			var item = this.tabs[tabId] || null;
			if (item === null) return false;
			var ret = $(item).hasClass('ui-tabs-selected');
			return ret;
		},

		/**
		 * Adds the TAB_FIRST and TAB_LAST classes.
		 */
		adjustTabClasses: function() {
			$(this.tabs).removeClass(classes.TAB_FIRST).removeClass(classes.TAB_LAST);
			$('li:first', this.tablist).addClass(classes.TAB_FIRST);
			var last = $('li:last', this.tablist);
			if (!last.hasClass(classes.TAB_LAST)) {
				last.addClass(classes.TAB_LAST);
			}
			$(this.tablist).closest('div').addClass('tabs-container');
		},

		/**
		 * Creates a dummy tab for testing purposes.
		 */
		dummyTab: function() {

			var c = this.tabs.length;
			if (c > 0) {
				$(this.tablist).next('div.dummy-tab').remove();
			} else {
				var dummy = $(this.tablist).next('div.dummy-tab');
				if (dummy.length == 0) {
					this.tablist.after($('<div></div>').addClass('dummy-tab browser-action-view-content ui-tabs-panel ui-widget-content ui-corner-bottom'));
				}
			}
		},

		/**
		 * Returns the title of a tab
		 */
		getTabTitle: function(tabId) {
			var item = this.tabs[tabId] || null;
			if (item === null) return false;
			var title = $('a span', item).html();
			return title;
		},

		/**
		 * Returns the position of an object.
		 */
		getItemPos: function(item) {
			var pos = $(item).position();
			pos.w = $(item).width();
			pos.left = Math.floor(pos.left);
			pos.right = Math.floor(pos.left + pos.w);
			return pos;
		},

		/**
		 * Returns the position of an object relative to the tabs container.
		 */
		getTabPos: function(item) {
			var cPos = this.getItemPos(this.tabsContainer);
			var uPos = this.getItemPos(this.tablist);
			uPos.left = cPos.left - Math.abs(uPos.left);
			uPos.right = uPos.left + uPos.w;
			var iPos = this.getItemPos(item);
			iPos.left = Math.floor(uPos.left + iPos.left);
			iPos.right = iPos.left + iPos.w;
			return iPos;
		},

		/**
		 * Called when a new tab is added
		 */
		onTabAdded: function(event, ui) {
			this._scrollToTab(ui.index);
			this._hideOutsideTabs();
			event.stopPropagation();
		},

		/**
		 * Called when a tab is removed
		 */
		onTabRemoved: function(event, ui) {
			this._scrollToTab(this.options.selected);
			this._hideOutsideTabs();
			this.dummyTab();
		},

		/**
		 * Called when the tabs container has been resized
		 */
		refreshTabs: function(event, ui) {
			this._scrollToTab(this.options.selected);
//			this._hideOutsideTabs();
		},

		/**
		 * Traverses the tabs list and shows or hides them if are outside the tabs container.
		 */
		_hideOutsideTabs: function() {

			var cPos = this.getItemPos(this.tabsContainer);

			$('.'+classes.TAB_LIST_SEL, this.tabsNav).addClass(classes.TAB_HIDDEN);
			$(this.tabs).each(function(index, item) {

				var pos = this.getTabPos(item);

				if (pos.left < cPos.left) {
//					$(item).addClass(classes.TAB_HIDDEN);
					$('.'+classes.TAB_LIST_SEL, this.tabsNav).removeClass(classes.TAB_HIDDEN);
				} else if (pos.right > cPos.right) {
					$(item).addClass(classes.TAB_HIDDEN);
					$('.'+classes.TAB_LIST_SEL, this.tabsNav).removeClass(classes.TAB_HIDDEN);
				} else {
					$(item).removeClass(classes.TAB_HIDDEN);
				}
			}.bind(this));
		},

		/**
		 * If the tab is outside the tabs container,
		 * this method bring it to a visible position
		 * using an animation and selects it.
		 */
		_scrollToTab: function(tabId) {
	
			var item = this.tabs[tabId] || null;
			if (item === null) return false;

//			if (!$(item).hasClass(classes.TAB_HIDDEN)) {
//				// If it is not hidden, selects it
//				this.select(tabId);
//				return;
//			}

			var nWidth = 0;
			var selector = $('.'+classes.TAB_LIST_SEL, this.tabsContainer);
			if (selector.length > 0) {
				var nPos = this.getItemPos(selector);
				nWidth = nPos.w * 2;
			}

			var cPos = this.getItemPos(this.tabsContainer);
			var pos = this.getTabPos(item);

			var offset = 0;
			if (pos.left < cPos.left) offset = '0';
			if (pos.right > cPos.right) offset = '-=' + Math.abs(pos.right - cPos.right + nWidth);

			// Show all tabs first for a smoothness effect
			$(this.tabs).removeClass(classes.TAB_HIDDEN);

			$(this.tablist).animate({left: offset}, 400, function() {
				this.select(tabId);
				this._hideOutsideTabs();
				this._updateTabsNav();
			}.bind(this));
		},

		/**
		 * The tabs navigator is a list of all tabs that are shown
		 * when there are hidden tabs.
		 * When an element is clicked the method scrollToTab is called.
		 */
		_updateTabsNav: function() {
			if(this.closingtab) return ;

			var ul = $('.'+classes.TAB_LIST, this.tabsNav).unbind().empty();
			var last_index = 1;

			$(this.tabs).each(function(index, item) {

				var sel = this.isSelected(index);

				var l = $('<li></li>')
					.html(this.getTabTitle(index))
					.addClass(classes.TAB_LIST_ITEM + ' %s-%s'.printf(classes.TAB_LIST_ITEM, index))
					.appendTo(ul);

				if ($(item).hasClass(classes.TAB_HIDDEN)) {
					l.addClass(classes.TAB_LIST_HITEM);
				}

				if (sel) {
					l.addClass(classes.TAB_LIST_SITEM);
				}

				l.click(function(event) {

					if (!sel) {
						// There's no need for a 'hide' algorithm because of the 'hide-on-click' class.
						this._scrollToTab(index);
					}
				}.bind(this));

				last_index++;

			}.bind(this));



			var l = $('<li></li>')
			.html(_("Close all tabs") )
			.addClass(classes.TAB_LIST_ITEM + ' %s-%s'.printf(classes.TAB_LIST_ITEM, last_index))
			.addClass("tabswidget-close-all")
			.appendTo(ul)
			.click(function(event) {
					this.closingtab = true;
					for(var i=last_index-2; i>=0;i--) {
						$(this.element).tabs("remove", i);
					}
					this.closingtab = false;
			}.bind(this));


		},

		getTabsList: function() {
				return this.tabs;
		},

		updateTabsNav: function(ui) {

			var $ul = $('.'+classes.TAB_LIST, this.tabsNav);
			if ($ul.length == 0) return;

			$('li', $ul).removeClass(classes.TAB_LIST_SITEM);

			$('li.%s-%s'.printf(classes.TAB_LIST_ITEM, ui.index), $ul).addClass(classes.TAB_LIST_SITEM);

		},

		getter: ['selected', 'isSelected', 'updateTabsNav', 'getTabsList']
	});

})(jQuery);
