/*
 * Panel Draft 0.3.1
 * for jQuery UI
 *
 * Copyright (c) 2009 Igor 'idle sign' Starikov
 * Dual licensed under the MIT (MIT-LICENSE.txt)
 * and GPL (GPL-LICENSE.txt) licenses.
 *
 * http://code.google.com/p/ist-ui-panel/
 *
 * Depends:
 *	ui.core.js
 */
(function($) {

	$.widget('ui.panel', {

		// create panel
		_init: function() {
			
			if (this.element.is('div')) {
				var self = this,
					o = this.options;

				this.panelBox = this.element;
				o.width = this.panelBox.css('width');
				this.panelBox.attr('role', 'panel');
				o.id = this.panelBox.attr('id');
				this.headerBox = this.element.children().eq(0);
				this.contentBox = this.element.children().eq(1);
				o.content = this.contentBox.html();
				// wrap content to prevent padding issue
				this.contentBox.wrapInner('<div></div>');
				this.contentTextBox = this.contentBox.children().eq(0).addClass(o.contentTextClass);
				this.headerBox.wrapInner('<div><span></span></div>');
				// need separate titleBox and titleTextBox to avoid possible collapse/draggable issues
				this.titleBox = this.headerBox.children().eq(0);
				this.titleTextBox = this.titleBox.children().eq(0);
				this.titleText = this.titleTextBox.html();
				this.headerBox.prepend('<span></span>')
				this.rightBox = this.headerBox.children().eq(0).addClass(o.rightboxClass);

				// setting up controls
				if (o.controls!=false){
					// suppose 'o.controls' should be a ui.toolbar control
					this.rightBox.append('<span></span>');
					this.controlsBox = this.rightBox.children().eq(0).addClass(o.controlsClass).html(o.controls);
				}

				// styling
				this.panelBox.addClass(o.widgetClass);
				this.headerBox.addClass(o.headerClass);
				this.titleBox.addClass(o.titleClass);
				this.titleTextBox.addClass(o.titleTextClass);
				this.contentBox.addClass(o.contentClass);

				// collapsibility
				if (o.collapsible){

					switch (o.collapseType) {
						case 'slide-right':
							var childIndex = 0;
							// there is a shift of child element index if controls are enabled
							if (o.controls) childIndex = 1;
							this.rightBox.append('<span><span/></span>');
							this.collapsePanel = this.rightBox.children().eq(childIndex).addClass(o.collapsePnlClass);
							this.collapseButton =  this.collapsePanel.children().eq(0).addClass(o.slideRIcon);
							this.iconBtnClpsd = o.slideRIconClpsd;
							this.iconBtn = o.slideRIcon;
							this.ctrlBox = this.controlsBox;
							break;
						case 'slide-left':
							this.headerBox.prepend('<span><span/></span>');
							this.collapsePanel = this.headerBox.children().eq(0).addClass(o.collapsePnlClass);
							this.collapseButton =  this.collapsePanel.children().eq(0).addClass(o.slideLIcon);
							this.iconBtnClpsd = o.slideLIconClpsd;
							this.iconBtn = o.slideLIcon;
							this.ctrlBox = this.rightBox;
							break;
						default:
							this.headerBox.prepend('<span><span/></span>');
							this.collapseButton = this.headerBox.children().eq(0).addClass(o.headerIcon);
							this.iconBtnClpsd = o.headerIconClpsd;
							this.iconBtn = o.headerIcon;
							this.ctrlBox = this.controlsBox;
							break;
					}

					this._buttonHover(this.collapseButton);
					this.collapseButton.addClass(o.iconClass);
					if (o.event) {
						this.collapseButton.bind((o.event) + ".panel", function(event) { return self._clickHandler.call(self, event, this); });
						this.titleTextBox.bind((o.event) + ".panel", function(event) { return self._clickHandler.call(self, event, this); });
					}
					// collapse panel if 'accordion' option is set
					if (o.accordion) {
						o.collapsed = true;
					}
					// restore state from cookie
					if (o.cookie) {
						if (self._cookie()==0) {
							o.collapsed = false;
						} else {
							o.collapsed = true;
						}
					}
					// store state as attribute
					this.panelBox.attr('collapsed', o.collapsed);
					
					// panel collapsed - trigger action
					if (o.collapsed) {
						self.toggle(0, true);
					}
				}
				this.panelBox.show();
			}

		},

		_cookie: function() {
			var cookie = this.cookie || (this.cookie = this.options.cookie.name || 'ui-panel-' + $.data(this.options.id));
			return $.cookie.apply(null, [cookie].concat($.makeArray(arguments)));
		},

		_clickHandler: function(event, target){
			var o = this.options;
			
			if (o.disabled) return false;
			this.toggle(o.collapseSpeed);
			return false;
		},
		
		toggle: function (collapseSpeed, innerCall){
			this.options.callFunctionToggle;
			eval(this.options.callFunctionToggle);
			var self = this,
				o = this.options,
				btn = this.collapseButton,
				ibc = this.iconBtnClpsd,
				ib = this.iconBtn,
				panelBox = this.panelBox,
				contentBox = this.contentBox,
				headerBox = this.headerBox,
				titleTextBox = this.titleTextBox,
				titleText = this.titleText,
				ctrlBox = this.ctrlBox,
				ie = '';

			// that's IE 6-8 for sure, use appropriate style for vertical text
			if (!jQuery.support.leadingWhitespace) ie="-ie";

			// split toggle into 'fold' and 'unfold' actions and handle callbacks
			if (contentBox.css('display')=='none'){
				this._trigger("unfold");
			}else{
				this._trigger("fold");
			}

			if (ctrlBox){
				ctrlBox.toggle(0);
			}

			// vaious content sliding animations
			if (o.collapseType=='default'){				
				if (collapseSpeed==0) {
					if (ctrlBox){
						ctrlBox.hide();
					}
					contentBox.hide();
				} else contentBox.slideToggle(collapseSpeed);
			} else {
				if (collapseSpeed==0) {
					// reverse collapsed option for immediate folding
					o.collapsed=false;
					if (ctrlBox) ctrlBox.hide();
					contentBox.hide();
				} else contentBox.toggle();

				if (o.collapsed==false){
					if (o.trueVerticalText){
						// true vertical text - svg or filter
						headerBox.toggleClass('ui-panel-vtitle').css('height', o.vHeight);
						if (ie==''){
							titleTextBox.
								empty().
								// put transparent div over svg object for object onClick simulation
								append('<div style="height:90%;width:100%;position:absolute;bottom:0;"></div><object type="image/svg+xml" data="data:image/svg+xml;charset=utf-8,<svg xmlns=\'http://www.w3.org/2000/svg\'><text x=\'-190\' y=\'13\' style=\'font-weight:bold;font-family:verdana;font-size:0.7em;\' transform=\'rotate(-90)\' text-rendering=\'optimizeSpeed\'>'+titleText+'</text></svg>"></object>').
								css('height', o.vHeight);
						}
						
						titleTextBox.toggleClass('ui-panel-vtext'+ie);
					} else {
						// vertical text workaround
						headerBox.attr('align','center');
						titleTextBox.html(titleTextBox.text().replace(/(.)/g, '$1<BR>'));
					}
					panelBox.animate( {width: '2.4em'}, collapseSpeed );
				} else {
					if (o.trueVerticalText){
						headerBox.toggleClass('ui-panel-vtitle').css('height', 'auto');
						titleTextBox.empty().append(titleText);
						
						titleTextBox.toggleClass('ui-panel-vtext'+ie);
					} else {
						headerBox.attr('align','left');
						titleTextBox.html(titleTextBox.text().replace(/<BR>/g, ' '));
					}
					panelBox.animate( {width: o.width}, collapseSpeed );
				}
			}

			// only if not initially folded
			if (collapseSpeed!=0 || o.trueVerticalText) {
				o.collapsed = !o.collapsed;
			}

			panelBox.attr('collapsed', o.collapsed);

			// save state in cookie if allowed
			if (o.cookie) {
				self._cookie(Number(o.collapsed), o.cookie);
			}

			// inner toggle call to show only one unfolded panel if 'accordion' option is set
			if (o.accordion && !innerCall){
				$("."+o.accordion+"[role='panel'][collapsed='false'][id!='"+(o.id)+"']").panel('toggle', collapseSpeed, true);
			}						

			// css animation for header and button
			btn.toggleClass(ibc).toggleClass(ib);
			headerBox.toggleClass('ui-corner-all');
		},

		content: function(content){
			this.contentBox.html(content);
		},

		destroy: function(){
			var o = this.options;

			this.headerBox
				.html(this.titleText)
				.removeClass(o.headerClass);
			this.contentBox
				.removeClass(o.contentClass)
				.html(o.content);
			this.panelBox
				.removeAttr('role')
				.removeAttr('collapsed')
				.unbind('.panel')
				.removeClass(o.widgetClass);
				
			if (o.cookie) {
				this._cookie(null, o.cookie);
			}
		},

		_buttonHover: function(el){
			var o = this.options;

			el
				.bind('mouseover', function(){ $(this).addClass(o.hoverClass); })
				.bind('mouseout', function(){ $(this).removeClass(o.hoverClass); })
		}

	});

	$.extend($.ui.panel, {
		version: '0.3.1',
		defaults: {
			event: 'click',
			collapsible: true,
			collapseType: 'default',
			collapsed: false,
			accordion: false,
			collapseSpeed: 'fast',
			// true vertical text with svg or filter rendering
			trueVerticalText: false,
			// neccessary for true vertical text
			vHeight: '220px',
			// suppose that we need ui.toolbar with controls here
			controls: false,
			// store panel state in cookie (jQuery cookie Plugin needed - http://plugins.jquery.com/project/cookie)
			cookie: null, // accepts cookie plugin options, e.g. { name: 'myPanel', expires: 7, path: '/', domain: 'jquery.com', secure: true }
			// styling
			widgetClass: 'ui-helper-reset ui-widget ui-panel',
			headerClass: 'ui-helper-reset ui-widget-header ui-panel-header ui-corner-top',
			contentClass: 'ui-helper-reset ui-widget-content ui-panel-content ui-corner-bottom',
			contentTextClass: 'ui-panel-content-text',
			rightboxClass: 'ui-panel-rightbox',
			controlsClass: 'ui-panel-controls',
			titleClass: 'ui-panel-title',
			titleTextClass: 'ui-panel-title-text',
			iconClass: 'ui-icon',
			hoverClass: 'ui-state-hover',
			collapsePnlClass: 'ui-panel-clps-pnl',
			//icons
			headerIconClpsd: 'ui-icon-triangle-1-e',
			headerIcon: 'ui-icon-triangle-1-s',
			slideRIconClpsd: 'ui-icon-arrowthickstop-1-w',
			slideRIcon: 'ui-icon-arrowthickstop-1-e',
			slideLIconClpsd: 'ui-icon-arrowthickstop-1-e',
			slideLIcon: 'ui-icon-arrowthickstop-1-w',
			callFunctionToggle: function(){}
		}
	});


})(jQuery);