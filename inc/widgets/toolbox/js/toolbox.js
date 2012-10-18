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

	var drag_options = {
		containment: '#xedit-container',
		snap: {
			items: '.xedit-toolbox, .xedit-groupbox'
			/*snap: function() {
				console.log('snap', arguments);
			},
			release: function() {
				console.log('release', arguments);
			}*/
		},
		snapMode: 'outer',
		snapTolerance: 15,
		opacity: 0.75,
		stack: { group: '.xedit-toolbox, .xedit-groupbox', min: 50 }	// =D
	};

	var resize_options = {
		minWidth: 100,
		minHeight: 60,
		handles: 'w, e, s, se'
	};

	$.fn.extend({
		toolbar: function(options) {
			$(this).each(function(idx, menu) {
				var menuOptions = $('ul:first', menu);
				$(menu).addClass('ui-tabs ui-widget ui-widget-content ui-corner-all');
				menuOptions.addClass('ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all');
				$('div', menu).hide();
				$('a', menuOptions).each(function(idx, item) {
					$(item).click(function(event) {
						id = item.href.split('#')[1];
						content = $('div#'+id, menu);
						$('div', menu).hide();
						content.show();
						return false;
					});
				});
			});
		}
	});

	$.widget('ui.xeditCanvas', {
		_init: function() {
			var $this = $(this.element[0]);
			$this
				.droppable({
					accept: '.xedit-groupbox-item',
					activeClass: '.ui-state-highlight',
					tolerance: 'pointer',
					drop: function(event, ui) {

						$('.xedit-groupbox').groupBox('removeBox', {toolbox: ui.draggable, event: event});
						return false;

						var elem = $('<div></div>').append($('div:first', ui.draggable).clone());
						elem.attr('id', ui.draggable.attr('id'));
						elem.attr('class', ui.draggable.attr('class'));
						$('body').append(elem.toolBox());
						elem.css({
							top: event.clientY,
							left: event.clientX
						});
						ui.draggable.remove();
						return false;
					}
				})
		}
	});

	$.widget('ui.toolBox', {
		_init: function() {

			var options = $.extend($.ui.toolBox.defaults, this.options);
			var $this = $(this.element[0]);

			$this
				.removeClass('xedit-groupbox-item')
				.addClass('ui-widget-content xedit-toolbox')
				.resizable('destroy').resizable(resize_options)
				.draggable('destroy').draggable($.extend({}, drag_options, {handle: $(options.header+':first', $this)}))
				.droppable({
					accept: '.xedit-toolbox',
					//activeClass: '.ui-state-highlight',
					tolerance: 'pointer',
					drop: function(event, ui) {

						var groupbox = $('.xedit-groupbox');
						if (groupbox.length == 0) {
							groupbox = $('<div id="xedit-groupbox-1" class="xedit-group">' +
								'	<div>' +
								'		<h3><a href="#">Properties Group</a></h3>' +
								'	</div>' +
								'</div>'
							).groupBox().appendTo('body');

							groupbox.css({
								top: $(this).position().top,
								left: $(this).position().left
							});
						}

						if (groupbox.data('items').length == 0) {
							groupbox.groupBox('appendBox', {toolbox: $(this)});
						}

						groupbox.groupBox('appendBox', {toolbox: ui.draggable});
						return false;
					}
				});

			$(options.header+':first', $this)
				.removeClass('ui-state-default')
				.addClass('ui-widget-header ui-state-active')
				.dblclick(function(event) {
					var parent = $(event.target).parents('div.xedit-toolbox:first');
					if ($(this).hasClass('ui-state-active')) {
						parent.data('_height', parent.height());
						parent.animate({height: 15}).resizable('destroy').resizable($.extend({}, resize_options, {handles: 'w, e'}));
						$(this).addClass('ui-state-default').removeClass('ui-state-active');
					} else {
						parent.animate({height: parent.data('_height')}).resizable('destroy').resizable($.extend({}, resize_options, {handles: 'w, e, s, se'}));
						$(this).removeClass('ui-state-default').addClass('ui-state-active');
					}
					$(this).next().toggle('slow');
					return false;
				})
				.siblings('div').addClass('xedit-toolbox-content');

			$('div:first', $this)
				.addClass('xedit-toolbox-container');

			$this.data('_height', $this.height());
		}
	});

	$.ui.toolBox.defaults = {header: 'h3'};

	$.widget('ui.groupBox', {
		_init: function() {

			var options = $.extend($.ui.groupBox.defaults, this.options);
			var $this = $(this.element[0]);

			$this.data('items', []);

			$(options.header+':first', $this)
				.addClass('ui-widget-header ui-state-active')
				.css('margin-bottom', '6px');

			$this
				.addClass('ui-widget-content xedit-groupbox')
				.resizable('destroy').resizable(resize_options)
				.draggable('destroy').draggable($.extend({}, drag_options, {handle: $(options.header+':first', $this)}))
				.droppable({
					accept: '.xedit-toolbox',
					activeClass: '.ui-state-highlight',
					tolerance: 'pointer',
					drop: function(event, ui) {
						$(this).groupBox('appendBox', {toolbox: ui.draggable});
						return false;
					}
				});
		},

		appendBox: function(options) {
			var $this = $(this.element[0]);
			var elem = $('<div></div>').append($('div:first', options.toolbox).clone());
			elem.attr('id', options.toolbox.attr('id'));
			elem.attr('class', options.toolbox.attr('class'));
			elem.css('height', options.toolbox.height());
			$this.append(elem.groupBoxItem({groupBox: $this}));
			$this.data('items', $('.xedit-groupbox-item', $this));
			options.toolbox.remove();
			$this.css('height', '');
			return elem;
		},

		removeBox: function(options) {
			var $this = $(this.element[0]);
			var elem = $('<div></div>').append($('div:first', options.toolbox).clone());
			elem.attr('id', options.toolbox.attr('id'));
			elem.attr('class', options.toolbox.attr('class'));
			$('body').append(elem.toolBox());
			elem.css({
				top: options.event.clientY,
				left: options.event.clientX
			});
			options.toolbox.remove();
			$this.data('items', $('.xedit-groupbox-item', $this));
			if ($this.data('items').length == 1) {
				var e = this.removeBox({toolbox: $($this.data('items')[0]), event: options.event});
				e.css({
					top: $this.position().top,
					left: $this.position().left
				});
				$this.groupBox('destroy').remove();
			}
			return elem;
		},

		items: function() {
			var $this = $(this.element[0]);
			return $this.data('items');
		}
	});

	$.ui.groupBox.defaults = {header: 'h3'};
	$.ui.groupBox.getter = ['items'];

	$.widget('ui.groupBoxItem', {
		_init: function() {

			var options = $.extend($.ui.groupBoxItem.defaults, this.options);
			$this = $(this.element[0]);

			$this
				.removeClass('xedit-toolbox')
				.addClass('ui-widget-content xedit-groupbox-item')
				.css('margin-top', '5px')
				.resizable('destroy')
				.draggable('destroy').draggable($.extend({}, drag_options, {
					revert: 'invalid',
					snap: true,
					snapMode: 'inner',
					snapTolerance: 5,
					handle: $(options.header+':first', $this)
				}))
				.show();

			$(options.header+':first', $this)
				.removeClass('ui-state-default')
				.addClass('ui-widget-header ui-state-active')
				.dblclick(function(event) {
					var parent = $(event.target).parents('div.xedit-groupbox-item:first');
					if ($(this).hasClass('ui-state-active')) {
						parent.data('_height', parent.height());
						parent.animate({height: 15}).resizable('destroy');
						$(this).addClass('ui-state-default');
						$(this).removeClass('ui-state-active');
					} else {
						parent.animate({height: parent.data('_height')}).resizable('destroy');
						$(this).removeClass('ui-state-default');
						$(this).addClass('ui-state-active');
					}
					$(this).next().toggle('slow');
					return false;
				});

		}
	});

	$.ui.groupBoxItem.defaults = {header: 'h3'};

})(jQuery);
