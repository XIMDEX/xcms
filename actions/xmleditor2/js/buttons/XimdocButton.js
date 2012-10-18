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
 *  @version $Revision: 7842 $
 */



/**
 * Base prototype for ximdex buttons tools
 * Implements KupuButton and extends XimdocTool
 */
var XimdocButton = Object.xo_create(new XimdocTool(), {

	_init: function(buttonid, commandfunc, tool) {
		this.buttonid = buttonid;
		this.button = getFromSelector(buttonid);
		this.commandfunc = commandfunc || this.commandfunc;
		this.tool = tool;
		this._enable = true;
	},

	initialize: function(editor) {
		this.editor = editor;
		this._fixTabIndex(this.button);
		addEventHandler(this.button, 'click', this.execCommand, this);
	},

	execCommand: function() {
		/* exec this button's command */
		if (this._enable) {
			this.commandfunc(this, this.editor, this.tool);
		}
	},

	commandfunc: function(button, editor, tool) {
		// do nothing
	},

	disable: function() {
		$(this.button).addClass('disabled');
		this._enable = false;
	},

	enable: function() {
		$(this.button).removeClass('disabled');
		this._enable = true;
	},

	isEnable: function() {
		return this._enable;
	}

});

