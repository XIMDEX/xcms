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
 *  @version $Revision: 8069 $
 */


(function($) {

	var PanelSeparator = Object.xo_create({

		_init: function(options) {

//			console.info('PanelSeparator: ', this);

			this.options = Object.extend({
				parent: null,
				top: '0px',
				left: '0px',
				index: null,
				dragStart: function() {},
				drag: function() {},
				dragStop: function() {}
			}, options);

			this.sep = $('<div></div>')
				.addClass('hbox-panel-sep')
				.data('data', {index: this.options.index});

			if (this.options.index !== null) $(this.sep).addClass('hbox-panel-separator-'+this.options.index);

			if (this.options.parent !== null) {
				$(this.sep).appendTo(this.options.parent);
			}

			var containment = $(this.options.parent).parent();

			$(this.sep)
				.draggable({
					axis: 'x',
					appendTo: containment,
					containment: containment,
					helper: 'clone',
					opacity: 1,
					cursor: 'w-resize',
					zIndex: 3000,
					addClasses: false,
					start: function(event, ui) {
						this.options.dragStart(event, ui.helper.context, ui.position);
					}.bind(this),
					drag: function(event, ui) {
						this.options.drag(event, ui.helper.context, ui.position);
					}.bind(this),
					stop: function(event, ui) {
						this.options.dragStop(event, ui.helper.context, ui.position);
					}.bind(this)
				});

			this.position(this.options);
		},

		element: function() {
			return this.sep;
		},

		position: function(pos) {

			/*pos = pos || null;
			if (Object.isObject(pos)) {
				if (pos.top) $(this.sep).css('top', parseInt(pos.top));
				if (pos.left) $(this.sep).css('left', parseInt(pos.left));
				return this;
			}*/
			var actualPos = {
				top: $(this.sep).css('top').replace(/\D/ig, ''),
				left: $(this.sep).css('left').replace(/\D/ig, '')
			};

			return actualPos;
		},

		dimension: function(dim) {

			var actualDim = {
				width: $(this.sep).width(),
				height: $(this.sep).height()
			};

			dim = dim || null;
			if (Object.isObject(dim)) {

				$(this.container).data('lastDimension', Object.clone(actualDim));

				if (dim.width) $(this.sep).width(parseInt(dim.width));
				//if (dim.height) $(this.sep).height(parseInt(dim.height));
				return this;
			}

			return actualDim;
		},

		lastDimension: function() {
			return $(this.container).data('lastDimension');
		}
	});

	var Panel = Object.xo_create({

		container: null,
		panel: null,
		sep: null,

		_init: function(options) {

//			console.info('Panel: ', this);

			this.options = Object.extend({
				parent: null,
				top: '0px',
				left: '0px',
				width: '0px',
				height: '0px',
				separator: true,
				index: null,
				dragStart: function() {},
				drag: function() {},
				dragStop: function() {}
			}, options);

			this.container = $('<div></div>').addClass('hbox-panel-container');
			if (this.options.index !== null) $(this.container).addClass('hbox-panel-container-'+this.options.index);
			if (this.options.parent !== null) {
				$(this.container).appendTo(this.options.parent);
			}

			this.panel = $('<div></div>').addClass('hbox-panel').appendTo(this.container);
			this.sep = this.options.separator
				? new PanelSeparator({
					parent: this.container,
					dragStart: this.options.dragStart,
					drag: this.options.drag,
					dragStop: this.options.dragStop,
					index: this.options.index
				  })
				: null;

			this.position(this.options);
			this.dimension(this.options);

		},

		element: function() {
			return this.container;
		},

		content: function() {
			return this.panel;
		},

		separator: function(sep) {
			sep = sep || null;
			if (Object.isObject(sep)) {
				this.sep = sep;
				return this;
			}
			return this.sep;
		},

		position: function(pos) {

			/*pos = pos || null;
			if (Object.isObject(pos)) {
				if (pos.top) $(this.container).css('top', parseInt(pos.top));
				if (pos.left) $(this.container).css('left', parseInt(pos.left));
				this.render();
				return this;
			}*/

			var actualPos = {
				top: $(this.container).css('top').replace(/\D/ig, ''),
				left: $(this.container).css('left').replace(/\D/ig, '')
			};

			return actualPos;
		},

		dimension: function(dim, animate) {

			var actualDim = {
				width: $(this.container).width(),
				height: $(this.container).height()
			};

			animate = Object.isBoolean(animate) ? animate : false;
			dim = dim || null;
			if (Object.isObject(dim)) {

				$(this.container).data('lastDimension', Object.clone(actualDim));

				var sepBorder = 0;
				if (this.sep !== null) {
					sepBorder = this.sep.dimension().width;
				}
				if (dim.width) {
					$(this.container).width(parseInt(dim.width));
					$(this.panel).width(parseInt(dim.width) - sepBorder);
					actualDim.width = dim.width;
				}
				/*if (dim.height) $(this.container).height(parseInt(dim.height));
				$(this.container).css({
					top: '0px',
					bottom: '0px'
				});
				$(this.panel).css({
					top: '0px',
					bottom: '0px'
				});*/
				this.render();
				return this;
			}

			return actualDim;
		},

		lastDimension: function() {
			return $(this.container).data('lastDimension');
		},

		render: function() {

			if (this.sep === null) return;

			var top = parseInt($(this.panel).css('top').replace(/\D/ig, ''));
			var left = parseInt($(this.panel).css('left').replace(/\D/ig, ''));
			var width = parseInt($(this.panel).width());
			var height = parseInt($(this.panel).height());

			/*this.sep
				.position({
					top: top,
					left: width
				})
				.dimension({
					height: height
				});*/

			return this;
		}
	});

	$.widget('ui.hbox', {

		panels: null,

		_init: function() {

//			console.info('HBox: ', this);

			this.panels = [];
			this.options.borderSize = $(this.element).css('padding-top');

			var hboxDim = this.dimension();
			var width = hboxDim.width / this.options.panels;

			var auxPanels = [];

			for (var i=0, l=this.options.panels; i<l; i++) {

				var p = new Panel({
					parent: this.element,
					separator: (i < (l-1)),
					dragStart: this.onDragStart.bind(this),
					drag: this.onDrag.bind(this),
					dragStop: this.onDragStop.bind(this),
					width: width,
//					height: hboxDim.height,
					index: i
				});

				if (p.separator()) {

					// NOTE: Wrap the index value
					var f = (function(idx) {
						return function() {
							var index = idx;
							this.togglePanel(index);
						}.bind(this);
					}.bind(this))(i);

					$(p.separator().element()).dblclick(f);

					if (i == 0) {
						// default size
						p.dimension({width: 315});
					}
				}

				auxPanels.push(i);
				this.panels.push(p);


				$(window).resize(function() {
					this.render();
				}.bind(this) );
			}


			var ci = function() {
				if (auxPanels.length > 0) return;
				this.render();
			}.bind(this);


			for (var i=0,l=auxPanels.length; i<l; i++) {
				var key = 'hbox.sep.%s'.printf(i);
				var value = X.session.get(key);
				auxPanels.shift();
				if (this.panels[i].separator() && value !== null) {
					this.panels[i].dimension({width: value});
				}
				ci();
			}
		},

		dimension: function() {
			return {
				width: $(this.element).width(),
				height: $(this.element).height()
			}
		},

		getPanel: function(index) {
			return this.panels[index] || null;
		},

		length: function() {
			return this.panels.length;
		},

		render: function(animate) {

			animate = Object.isBoolean(animate) ? animate : false;
			var hboxDim = this.dimension();
			var totalWidth = 0;

			this.panels.each(function(index, item) {
				action_dimension = null;

				var dim = item.dimension();
				var width = parseInt(dim.width);

				if (totalWidth + width > hboxDim.width) {
					width = hboxDim.width - totalWidth;
					item.dimension({width: width}, animate);
				}

				if (index < this.length() - 1) {
					totalWidth += width;
				} else {
					totalWidth = hboxDim.width - totalWidth;
					item.dimension({width: totalWidth}, animate);
					action_dimension =  item.dimension();
				}

				$(window).trigger("action_resize", [{dimension: item.dimension(), panel: this}]);
				$(item.element()).removeClass('hbox-panel-hidden');

			}.bind(this));

			return;

//			if (!animate) {
//				$(this.getPanel(0)).css(leftPanel);
//				$('.hbox-panel-sep', this.element).css(separator);
//				$(this.getPanel(1)).css(rightPanel);
//			} else {
//				$(this.getPanel(0)).animate(leftPanel, 'fast');
//				$('.hbox-panel-sep', this.element).animate(separator, 'fast');
//				$(this.getPanel(1)).animate(rightPanel, 'fast');
//			}
		},

		onDrag: function(event, element, position) {

			var index = $(element.className.split(' ')).map(function(index, item) {
				var ret = parseInt(item.replace(/^hbox-panel-separator-/i, ''));
				return isNaN(ret) ? null : ret;
			}).get(0);

			var p = this.getPanel(index);
			if (p === null) return;
			var borderSize = parseInt(p.separator().dimension().width);

			var offset = this.getPanel(index-1) ? this.getPanel(index-1).dimension().width : 0;
			position.left = position.left <= 0 ? 4 : position.left;
			p.dimension({width: (position.left - offset) + borderSize});

			this.render();
		},

		onDragStart: function(event, element, position) {
			$(this.element).trigger('dragstart', [{event: event, sep: element, pos: position}]);
		},

		onDragStop: function(event, element, position) {
			var data = $(element).data('data');
			X.session.set('hbox.sep.%s'.printf(data.index), position.left);
			$(this.element).trigger('dragstop', [{event: event, sep: element, pos: position}]);
		},

		togglePanel: function(panelIndex) {
			if (this.isHidden(panelIndex)) {
				this.showPanel(panelIndex);
			} else {
				this.hidePanel(panelIndex);
			}
		},

		showPanel: function(panelIndex) {
			var p = this.getPanel(panelIndex);
			if (p === null) return;
			p.dimension(p.lastDimension());
			this.render(true);
			$(p.element()).removeClass('hbox-panel-hidden');
			this.onDragStop({}, p.separator().element(), {left: p.dimension().width});
		},

		hidePanel: function(panelIndex) {
			var p = this.getPanel(panelIndex);
			if (p === null) return;
			var dim = p.separator().dimension();
			p.dimension({width: dim.width});
			this.render(true);
			$(p.element()).addClass('hbox-panel-hidden');
			this.onDragStop({}, p.separator().element(), {left: p.dimension().width});
		},

		isHidden: function(panelIndex) {
			var p = this.getPanel(panelIndex);
			if (p === null) return null;
			return $(p.element()).hasClass('hbox-panel-hidden');
		},

		options: {
			panels: 2
		},

		getter: ['getPanel', 'isPanelExpanded', 'length']

	});

})(jQuery);
