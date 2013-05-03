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
 *  @version $Revision: 8535 $
 */




var FloatingToolBox = Object.xo_create(new XimdocToolBox(), {

	MODE_DIALOG: 'dialog',
	MODE_PANEL: 'panel',

	_init: function(options) {

		this.toolboxId = options.toolboxId || null;
		this.ctrlButtonId = options.ctrlButtonId || null;
		this.buttonActiveClass = options.buttonActiveClass || '';
		this.buttonInactiveClass = options.buttonInactiveClass || '';
		this.visible = Object.isBoolean(options.visible) ? options.visible.valueOf() : true;
		this.mode = options.mode || this.MODE_PANEL;
		this.buttons = options.buttons || {};
	},
	initialize: function(tool, editor) {

		this.setElement();
		this.tool = tool;
		this.editor = editor;
		this.ctrlButton = null;

//		if ($('#'+this.ctrlButtonId).length > 0) {
//			this.ctrlButton = new ToggleButton({
//				buttonid: this.ctrlButtonId,
//				commandfunc: this.toggle.bind(this),
//				tool: this.tool,
//				activeClass: this.buttonActiveClass,
//				inactiveClass: this.buttonInactiveClass
//			});
//			editor.registerTool(this.ctrlButtonId, this.ctrlButton);
//			this.ctrlButton.setActive(this.visible);
//		}

		if(this.mode != this.MODE_DIALOG) return this;

		if (this.visible) this.show();
	},
	_createAttributeInput: function(label, value) {
		$(value).addClass('kupu-attribute-value');
		$(this.element).append(
			$('<div></div>')
				.addClass('xedit-element-attribute')
				.append($('<div></div>').addClass('kupu-toolbox-label').html('%s:'.printf(label)))
				.append($('<div></div>').addClass('kupu-toolbox-attribute-value').append(value)/*.change(this.updateButtonHandler.bind(this))*/)
				.show()
		);
	},

	_addHtml: function(html) {
		$(this.element).append(html);
	},

	_insertHtml: function(html) {
		$(this.element).html(html);
	},

	_createTreeSelector: function(input_id) {
		if($('#tree_selector' + input_id).length)
			$('#tree_selector' + input_id).toggle();
		else {
			var ds = new DataSource({
				url: url_root + '/inc/widgets/treeview/helpers/treeselectordata.php',
			});
			var tm = new TreeModel({colModel: {
				selector: 'tree tree',
				columns: [
					{name: 'text', label: 'Name', visible: true, width: ''},
					{name: 'nodeid', label: 'NodeId', visible: true, width: ''},
					{name: 'icon', label: 'Icon', visible: false, width: ''},
					{name: 'openIcon', label: 'OpenIcon', visible: false, width: ''},
					{name: 'children', label: 'Children', visible: false, width: ''},
					{name: 'isdir', label: 'IsDir', visible: false, width: ''},
					{name: 'tipofiltro', label: 'filter', visible: false, width: ''},
				]
			}, ds: ds});
			$('#' + input_id).parent().parent().after(
				$('<div></div>').addClass('xedit-element-attribute').append(
					$('<div></div>').addClass('kupu-toolbox-attribute-value').append(
						$('<div></div>').attr('id', 'tree_selector' + input_id)
					)
				)
			);
		}
		$('#tree_selector' + input_id).treeview({rowModel: tm})
			.bind('expand', function(event, params) {})
			.bind('collapse', function(event, params) {})
			.bind('select', function(event, params) {if(arguments[1].data.isdir != 1) {$('#' + input_id).val(arguments[1].data.nodeid); this.updateButtonHandler(event);}}.bind(this));
	},
	setElement: function(options) {

		this.mode = options ? this.mode = options.mode : this.mode;

		if (options === null && this.toolboxId !== null && $('#'+this.toolboxId).length > 0) {
			this.element = $('#'+this.toolboxId)[0];
			return this;
		}

		this.element = this.element ? this.element : $('<div></div>')[0];

		if (this.toolboxId !== null) {
			$(this.element).attr('id', this.toolboxId);
		}

		this._setElementView();
		this.setTitle();
	},
	_setElementView: function() {

		if(this.mode != this.MODE_DIALOG) {

			var createButton = function(value, callback) {
				var $btn = $('<button class="ui-state-default ui-corner-all" type="button">%s</button>'.printf(value));
				$btn.click(callback);
				return $btn;
			};

			var container = $('<div class="kupu-toolbox-container"></div>');
			$(this.element).appendTo(container);
			$(container).appendTo('.kupu-toolboxes-container-container');

			$(this.element).addClass('ui-dialog-content ui-dialog-widget ui-widget-content');

//			$(this.element).wrap($('<div class="kupu-toolbox-scrollpanel"></div>'));

			var buttons = [];
			for (var o in this.buttons) {
				buttons.push(createButton(o, this.buttons[o]));
			}

			if (buttons.length > 0) {
				var $buttonBar = $('<div class="kupu-toolbox-buttonbar"></div>').appendTo(container);
				buttons.each(function(index, item) {
					$buttonBar.append(item);
				});
			}

			if (this.visible) {
				$(this.element).show();
			} else {
				$(this.element).hide();
			}
			return this;
		}

		var container = $(this.element).closest('div.kupu-toolbox-container');
		$('div.kupu-toolbox-buttonbar', container).unbind().remove();


		var check = $(this.element).data("dialog");

		if(check) {
			$(this.element).dialog("destroy");
		}


		$(this.element).appendTo('body');
		$(this.element).dialog({
			autoOpen: false,
			dialogClass: 'xedit-toolbox '+(this.toolboxId || ''),
			width: 400,
			resizable: true,
			closeOnEscape: false,
			modal: false,
			stack: true,
			position: 'center',
			buttons: this.buttons,
			close: function() {
				this.setElement({mode: this.MODE_PANEL});
				if (this.ctrlButton !== null) {
					this.ctrlButton.setActive(false);
				}
				$(this).dialog('destroy');
			}.bind(this)
		});
		this.dialog = $(this.element).closest('.ui-dialog').get(0);
		this.titlebar = $('.ui-dialog-titlebar', this.dialog)
			.click(this.collapse.bind(this))
			.get(0);
	},
	setOption: function(name, value) {
		if(this.mode != this.MODE_DIALOG) return this;
		if (Object.isString(name)) {
			$(this.element).dialog('option', name, value);
		} else if (Object.isObject(name)) {
			for (var o in name) {
				this.setOption(o, name[o]);
			};
		}
		return this;
	},
	setTitle: function(title) {

		if (Object.isString(title) && this.title != title) {
			this.title = title.trim();
		}

		if (Object.isEmpty(this.title)) {
			return;
		}

		if (this.mode != this.MODE_DIALOG) {

			var $title = $('<h3>' + this.title + '</h3>');
			$($title).addClass('kupu-toolbox-heading kupu-toolbox-heading-opened');
			$($title).prepend($('#' + this.ctrlButtonId));
			$(this.element).before($title);

			var container = $(this.element).closest('div.kupu-toolbox-container');

			if ($(this.element).is(':hidden')) {
				$('div.kupu-toolbox-buttonbar', container).hide();
				$($title).toggleClass('kupu-toolbox-heading-closed');
				$($title).toggleClass('kupu-toolbox-heading-opened');
			}

			$($title).click(function() {
				$('div.ui-dialog-content, div.kupu-toolbox-buttonbar', container).toggle();
				$(this).toggleClass('kupu-toolbox-heading-closed');
				$(this).toggleClass('kupu-toolbox-heading-opened');
				return false;
			});

			this._makeDraggable($title);
			return this;

		} else {

			$('div.ui-dialog-titlebar', this.dialog).prepend($('#' + this.ctrlButtonId));
		}

		this.setOption('title', _(this.title));
		return this;
	},
	show: function() {
		$(this.element).dialog('open');
		return this;
	},
	hide: function() {
		$(this.element).dialog('close');
		return this;
	},
	toggle: function() {
		if($(this.element).closest('.ui-dialog')[0]) {
			if (['', 'none'].contains($(this.element).closest('.ui-dialog').css('display'))) {
				this.show();
			} else {
				this.hide();
			}
		} else {
			var title = $(this.element).prev().text();
			// Don't show buttons in toolbar
//			$('#toolbar > span').children('.kupu-tb-buttongroup:last').append($('#' + this.ctrlButtonId));
			$(this.element).prev().remove();
			this.setElement({mode: this.MODE_DIALOG});
    		this.setTitle(title);
	    	this.show();
		}
		return this;
	},
	_clean: function() {
		$(this.element).unbind().empty();
		return this;
	},
	collapse: function() {
		if(this.mode != this.MODE_DIALOG) return this;
		if ($(this.dialog).hasClass('xedit-collapsed')) {
			$(this.dialog).animate({height: '150px'}, 400);
			$(this.element).show();
			$('.ui-dialog-buttonpane', this.dialog).show();
//			$(this.element).slideDown('fast');
//			$('.ui-dialog-buttonpane', this.dialog).slideDown('fast');
		} else {
			$(this.element).hide();
			$('.ui-dialog-buttonpane', this.dialog).hide();
			$(this.dialog).animate({height: '20px'}, 400);
//			$(this.element).slideUp('fast');
//			$('.ui-dialog-buttonpane', this.dialog).slideUp('fast');
		}
		$(this.dialog).toggleClass('xedit-collapsed');
	},
	beforeUpdateContent: function(options) {

	},
	updateState: function(options) {

	},
	updateButtonHandler: function(event) {

	},

	_makeDraggable: function(title) {

        $(title).draggable({
        	containment: $('.kupu-editorframe'),
			helper: 'clone',
        	appendTo: 'body',
        	iframeFix: false,
        	start: function(event, ui) {

        		var helper = $(ui.helper[0])
        			.clone()
					.addClass('ui-cloned-helper')
        			.css({
        				position: 'absolute',
        				width: '300px',
        				/*height: '26px',*/
        				/*background: 'url("../../gfx/fnd-tab.png") repeat scroll 0 0 transparent',
						border: '1px solid #ccc',
						'background-repeat': 'no-repeat'*/
        			})
        			.show()
        			.appendTo(this.editor.getBody());

        	}.bind(this),
        	drag: function(event, ui) {

				var view = this.editor.getBody().ownerDocument.defaultView;
				var p = {
//					top: ui.position.top - $('.kupu-editorframe').position().top + view.scrollY,
					top: ui.absolutePosition.top - $('.kupu-editorframe').position().top + view.scrollY,
//					left: ui.position.left
					left: ui.absolutePosition.left
				};
				$('.ui-cloned-helper', this.editor.getBody()).css({
					top: p.top,
					left: p.left
				});
        	}.bind(this),
        	stop: function(event, ui) {

        		// TODO: elementFromPoin will not work in all browsers
        		// See http://www.quirksmode.org/dom/w3c_cssom.html
        		// Parameters can be diferent in distinct browsers

        		$('.ui-cloned-helper', this.editor.getBody()).remove();
        		var p = {
//        			x: ui.position.top,
        			x: event.clientX,
//        			x: event.pageX,
//        			y: ui.position.left - $('.kupu-editorframe').position().top
        			y: event.clientY - $('.kupu-editorframe').position().top
//        			y: event.pageY - $('.kupu-editorframe').position().top
        		};

        		var element = this.editor.getBody().ownerDocument.elementFromPoint(p.x, p.y);
        		element = element && element.ximElement ? element.ximElement : null;

        		if (element != null) {
        			// helper has been dropped into the iframe
        			this.toggle();
        		}
        	}.bind(this)
        });
	}
});

