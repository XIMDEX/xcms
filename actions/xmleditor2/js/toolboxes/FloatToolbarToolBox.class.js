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
 *  @version $Revision: 8093 $
 */





var FloatToolbarToolBox = Object.xo_create(new XimdocToolBox(), {

	toolbar: null,
	buttons: null,

	initialize: function(tool, editor) {

		this.toolbarId = 'float-toolbar';
	    	this.buttons = [];
	    	this.tool = tool;
	        this.editor = editor;
		this.afterUpdateContent(null);
	        this.editor.logMessage(_('FloatToolbarToolBox tool initialized'));
	
	        this.registerButton(new ScrollDownButton({id: 'scrolldown-button', className: 'kupu-scrolldown', title: _('Scroll Down')}));
	        this.registerButton(new ScrollUpButton({id: 'scrollup-button', className: 'kupu-scrollup', title: _('Scroll Up')}));
	        this.registerButton(new PasteButton({id: 'paste-button', className: 'kupu-paste', title: _('Paste')}));
	        this.registerButton(new CopyButton({id: 'copy-button', className: 'kupu-copy', title: _('Copy')}));
	        this.registerButton(new CutButton({id: 'cut-button', className: 'kupu-cut', title: _('Cut')}));
	},

	_elementIsallowed: function(element) {
		var rngElement = element['rngElement'] || element['schemaNode'];
		if (!rngElement) return false;
		var allowed = (!rngElement.type.contains('apply')) && (rngElement.tagName != 'docxap');
		//allowed = allowed && element.getAttribute('editable') != 'no';
		return allowed;
	},

	getToolbar: function() {
		return $('#'+this.toolbarId, this.editor.getBody())[0];
	},

	hasButtons: function() {
		return this.buttons.length > 0 ? true : false;
	},

	registerButton: function(button) {
		if ($('#'+button.id, this.toolbar).length > 0) return;
		button.initialize(this, this.editor);
		this.buttons.push(button);
	},

	unregisterButton: function(button) {
		var l = this.buttons.length;
		while (l--) {
			if (this.buttons[l].id == button.buttonid) {
				$(this.buttons[l].getButton()).unbind().remove();
				delete this.buttons[l];
				break;
			}
		}
	},

	beforeUpdateContent: function(options) {
		var l = this.buttons.length;
		while (l--) {
			this.buttons[l].beforeUpdateContent(options);
		}
		this.toolbar.unbind().empty().remove();
		this.toolbar = null;
	},

	/**
	 * Function which updates the events handlers after updating the editor content
	 */
	afterUpdateContent: function(options) {

		var body = this.editor.getBody();

		var html = '<div class="kupu-tb kupu-float-toolbar" id="'+this.toolbarId+'">' +
			'<span id="kupu-tb-floatbuttons" class="kupu-tb-buttons">' +
			'	<span id ="kupu-tb-floatbuttonsgroup" class="kupu-tb-buttongroup">' +
			'	</span>' +
			'</span>' +
		'</div>';

		// Appending the toolbar to the document body, it will be positioned later
		this.toolbar = $(html, body).appendTo(body);
		this.toolbar.mouseout(
			function(event) {
				this.onMouseOut({selNode: this.toolbar[0].ximElement, event: event});
			}.bind(this)
		);

		var l = this.buttons.length;
		while (l--) {
			$(this.buttons[l].renderize(), body).appendTo($('#kupu-tb-floatbuttonsgroup', this.toolbar));
			$(this.buttons[l].getButton())
				.click(
					function(event) {
						this.commandfunc(event);
					}.bind(this.buttons[l])
				)
				.mouseout(
					function(event) {
						this.onMouseOut({selNode: this.toolbar[0].ximElement, event: event});
					}.bind(this)
				);
		}

		// This doesn't works for images
		this.toolbar.designMode = 'Off';
		this.toolbar.hide();
	},

	/**
	 * Function which changes the menu position
	 */
	_moveToolbar: function(options) {

		var _elem = $(options.selNode);
		
//		var top = _elem.position().top; // + _elem.height() - 5;
//		var left = _elem.position().left + _elem.width(); // - this.toolbar.width();
		var top = options.event.pageY;
		var left = options.event.pageX;

		this.toolbar.css({
			position: 'absolute',
			top: top+'px',
			left: left+'px'
		});

		this.toolbar[0].ximElement = _elem[0].ximElement;
	},
	
	/**
	 * Function which returns if there is paste job pending
	 */
	pendingPaste: function() {
		if (!this.editor.clipboard) 
			return false;
		return true;
	},
	
	/**
	 * Function which makes visible all buttons
	 */
	showAllButtons: function() {
		$('.xedit-floatbutton', this.toolbar).show();
	},
	
	/**
	 * Function which hides all buttons
	 */
	hideAllButtons: function() {
		$('.xedit-floatbutton', this.toolbar).hide();
	},
	
	/**
	 * Function which hides buttons with no effect
	 */
	toggleButtons: function() {
		if(!this.toolbar[0].ximElement.isSelectable(this.editor.nodeId)) {
			this.hideAllButtons();
			return;
		}
		
		if(!this.toolbar[0].ximElement.findNextAllowedPosition('up'))
			$('#scrollup-button', this.toolbar).hide();
		if(!this.toolbar[0].ximElement.findNextAllowedPosition('down'))
			$('#scrolldown-button', this.toolbar).hide();
		if(!this.toolbar[0].ximElement.canBeCopied()) {
			$('#copy-button', this.toolbar).hide();
			$('#cut-button', this.toolbar).hide();
		}
		if(!this.pendingPaste())
			$('#paste-button', this.toolbar).hide();
	},

	/**
	 * Function which shows the scroller menu
	 */
	onMouseOver: function(options) {
		if (!this.toolbar || !this._elementIsallowed(options.selNode) || !this.hasButtons())
			return;
		this._moveToolbar(options);
		this.toggleButtons();
		this.toolbar.show();
	},

	/**
	 * Function which hides the scroller menu
	 */
	onMouseOut: function(options) {

		if (!this.toolbar || !this._elementIsallowed(options.selNode) || !this.hasButtons()) return;

		var hide = true;
		try {
			var related = options.event.relatedTarget;
			hide = $(related).parents('#'+this.toolbarId).length == 0;
		} catch (e) {
			//console.error(e);
		}

		if (!hide) return;
		
		this.showAllButtons();
		this.toolbar.hide();
		this.toolbar[0].isVisible = false;
	}

});


var FloatButton = Object.xo_create(new XimdocButton(), {
    
    _init: function(options) {
    	this.options = options;
	this.editor = null;
	this.toolbox = null;
	this.buttonid = null;
	this.className = null;
	this.title = null;
    },

    initialize: function(toolbox, editor) {
	    this.toolbox = toolbox;
	    this.editor = editor;
	    this.buttonid = this.options.id;
	    this.className = this.options.className;
	    this.title = this.options.title;
    },

    commandfunc: function(event) {
		this.editor.alert(_('Implement commandfunc method!'));
    },

    getButton: function() {
    	return $('#'+this.buttonid, this.toolbox.getToolbar())[0];
    },

    renderize: function() {
    	return '<button type="button" class="'+this.className+' xedit-floatbutton" id="'+this.buttonid+'" title="'+this.title+'">&#xA0;</button>';
    }
});


var ScrollUpButton = Object.xo_create(FloatButton, {

	commandfunc: function(event) {
		var ximElement = this.toolbox.getToolbar().ximElement;
		if (ximElement.scrollUp()) {
			this.setActionDescription(_('Scroll up'));
			this.editor.updateEditor({caller: this});
		}
    }
});

var ScrollDownButton = Object.xo_create(FloatButton, {

	commandfunc: function(event) {
		var ximElement = this.toolbox.getToolbar().ximElement;
		if (ximElement.scrollDown()) {
			this.setActionDescription(_('Scroll down'));
			this.editor.updateEditor({caller: this});
		}
    }
});

var CutButton = Object.xo_create(FloatButton, {

    commandfunc: function(event) {
		var ximElement = this.toolbox.getToolbar().ximElement;
		this.cutElement(ximElement);
    }
});

var CopyButton = Object.xo_create(FloatButton, {

    commandfunc: function(event) {
		var ximElement = this.toolbox.getToolbar().ximElement;
		this.copyElement(ximElement);
    }
});

var PasteButton = Object.xo_create(FloatButton, {

    commandfunc: function(event) {
		var ximElement = this.toolbox.getToolbar().ximElement;
		this.pasteElement(ximElement.parentNode, ximElement);
    }
});

