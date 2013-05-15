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
 *  @version $Revision: 8541 $
 */


function HoverTool() {

	this.editor = null;
	this._body = null;
	this._dispatchEvents = false;

    	this.initialize = function(editor) {
        	this.editor = editor;
        	this.toolboxes = {};
        	this._body = editor.getBody();
		this.afterUpdateContent(null);
	};

    	this.beforeUpdateContent = function(options) {
    		this._dispatchEvents = false;
		this.editor.elements.unbind('hover');
		for (id in this.toolboxes) {
			if (this.toolboxes[id]['beforeUpdateContent']) this.toolboxes[id].beforeUpdateContent(options);
		};
    	};

    	this.afterUpdateContent = function(options) {

    		// Important!
    		this._body = this.editor.getBody();

    		this.editor.elements = $('[uid]', this._body);
        	this.editor.elements.each(function(index, elem) {
        		$(elem).hover(
	        		function(e) {
		        		var target = e.currentTarget || e.target;
					this.updateState({caller: this, selNode: target, event: e});
	        		}.bind(this),
	        		function(e) {
		        		var target = e.currentTarget || e.target;
					this.updateState({caller: this, selNode: target, event: e});
	        		}.bind(this)
			);
	    	}.bind(this));
		
		for (id in this.toolboxes) {
			if (this.toolboxes[id]['afterUpdateContent']) this.toolboxes[id].afterUpdateContent(options);
		};
		this._dispatchEvents = true;
    	};

    	this.updateState = function(options) {

    		// NOTE: jQuery uses 'mouseenter' and 'mouseleave' ... ???
		if (!this._dispatchEvents || !options.selNode)
			return;
		if(options.event && 
			//!['mouseover', 'mouseenter', 'mouseout', 'mouseleave', 'click', 'keyup'].contains(options.event.type))
			!['mouseover', 'mouseout', 'click', 'keyup'].contains(options.event.type))
			return;

		// NOTE: event will be the jQuery event object at this point.
		// Registered object can obtain the original event object using event.originalEvent
		var ids=["draggabletoolbox","highlighttoolbox"];
		for (var i=0;i<ids.length;i++) {
			var toolbox = this.toolboxes[ids[i]];
			try {
				if (toolbox['updateState']) {
					toolbox.updateState(options);
				}
			} catch (e) {
				this.editor.logMessage(_('Exception while processing updateState on ${id}: ${msg}', {'id': id, 'msg': e.message}), 2);
			}
		};
    	};
};

HoverTool.prototype = new XimdocTool();
