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
 * Two states button, changes icon automatically
 */
var ToggleButton = Object.xo_create(XimdocButton, {

	_init: function(options) {
		ToggleButton._construct(this, options.buttonid, options.commandfunc, options.tool);
		this.activeClass = options.activeClass;
		this.inactiveClass = options.inactiveClass;
		this.active = false;
	},

	execCommand: function() {
		this.setActive(!this.active);
		ToggleButton._super(this, 'execCommand');
	},

	getActive: function() {
		return this.active;
	},

	setActive: function(active) {
		this.active = active;
		if (this.active) {
			$(this.button).addClass(this.activeClass);
			$(this.button).removeClass(this.inactiveClass);
		} else {
			$(this.button).addClass(this.inactiveClass);
			$(this.button).removeClass(this.activeClass);
		}
	}
});

