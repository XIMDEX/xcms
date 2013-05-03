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
 *  @version $Revision: 8538 $
 */



var ToolbarTool = Object.xo_create(XimdocTool, {
	
	buttons: null,
	
//	_init: function(options) {
//		console.log(arguments);
//	},
	
	initialize: function(editor) {
		
		this.editor = editor;
		this.buttons = [];
		
		// NOTE: Buttons are defined in toolbar.tpl
		
		// Edition oprations
		this.registerButton('CutButton', 'kupu-cut-button');
		this.registerButton('CopyButton', 'kupu-copy-button');
		this.registerButton('PasteButton', 'kupu-paste-button');
		
//		this.registerButton('UndoButton', 'kupu-undo-button');
//		this.registerButton('RedoButton', 'kupu-redo-button');
		
		this.registerButton('ScrollUpButton', 'kupu-scrollup-button');
		this.registerButton('ScrollDownButton', 'kupu-scrolldown-button');
		
//		this.registerButton('RemoveButton', 'kupu-remove-button');
//		this.registerButton('SchemaValidatorButton', 'kupu-schemavalidator-button');
//		this.registerButton('XimletDrawerButton', 'kupu-ximletdrawer-button');
		
	},

	updateState: function(options) {
		
	},
	
	beforeUpdateContent: function(options) {
		
	},

	afterUpdateContent: function(options) {
		
	},
	
	registerButton: function(className, buttonId) {
		
		var buttonClass = null;
		try {
			buttonClass = eval(className);
			if (!Object.isFunction(buttonClass)) return;
		} catch(e) {
			return;
		}
		
		var button = new buttonClass(buttonId, this);
		
		if ($('#toolbar #'+button.buttonid).length == 0) return;
		button.initialize(this.editor);
		this.buttons.push(button);
	},
	
	getTool: function(toolName) {
//		console.log(this.editor);
	}

});


var ToolbarButton = Object.xo_create(XimdocButton, {

	_init: function(buttonid, tool) {
		ToolbarButton._construct(this, buttonid, this.commandfunc, tool);
	}
});

var CutButton = Object.xo_create(ToolbarButton, {

    commandfunc: function(event) {
		var ximElement = this.editor.getSelectedNode().ximElement;
		this.cutElement(ximElement);
    }
});

var CopyButton = Object.xo_create(ToolbarButton, {

    commandfunc: function(event) {
		var ximElement = this.editor.getSelectedNode().ximElement;
		this.copyElement(ximElement);
    }
});

var PasteButton = Object.xo_create(ToolbarButton, {

    commandfunc: function(event) {
		var ximElement = this.editor.getSelectedNode().ximElement;
		this.pasteElement(ximElement.parentNode, ximElement);
    }
});

var ScrollUpButton = Object.xo_create(ToolbarButton, {

	commandfunc: function(event) {
		var ximElement = this.editor.getSelectedNode().ximElement;
		if (ximElement.scrollUp()) {
			this.setActionDescription(_('Scroll up'));
			this.editor.updateEditor({caller: this});
		}
    }
});

var ScrollDownButton = Object.xo_create(ToolbarButton, {

	commandfunc: function(event) {
		var ximElement = this.editor.getSelectedNode().ximElement;
		if (ximElement.scrollDown()) {
			this.setActionDescription(_('Scroll down'));
			this.editor.updateEditor({caller: this});
		}
    }
});
