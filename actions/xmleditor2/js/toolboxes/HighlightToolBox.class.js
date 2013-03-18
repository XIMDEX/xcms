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
 *  @version $Revision: 8529 $
 */



/**
 * This class highlights the selected elements
 */
function HighlightToolBox() {

	this._lastParent = null;
	this._lastElement = null;
	this._lastElements = null;
	this._lastHighlight = null;

	this.initialize = function(tool, editor) {
    	this.tool = tool;
        this.editor = editor;
        this.editor.logMessage(_('HighlightToolBox tool initialized'));
	};

	this.beforeUpdateContent = function(options) {
		// clean all selections
		if (options.selNode) this.highlightElement(null);
	};

	/**
	 * Do the highlight on the selected element
	 */
	this.updateState = function(options) {
		if (!options.selNode || (options.event && !['click', 'keyup'].contains(options.event.type)))
			return;
		this.highlightElement(options.selNode);
	};

	this.highlightElement = function(element) {

		// If element is null all the previuos elements will be unselected

		var parent = null;
		var selectedNodes = [];
		if (element) {
			parent = this.editor.getParentWithUID(element);
			selectedNodes = $('[uid="'+element.getAttribute('uid')+'"]', this.editor.getBody());
		}

		/*if (!selectedNodes.contains(element)) {
			selectedNodes = [element];
		}*/

		$((this._lastElements || [])).each(
			function(index, elem) {
				$(elem).removeClass('rng-element-selected');
				$(elem).attr("contentEditable",false);
				if(element && $(elem).attr('uid') == element.getAttribute('uid'))
					return;
			}
		);
		if (this._lastParent) {
			$(this._lastParent).removeClass('rng-parent-selected');
		}

		$(selectedNodes).each(
			function(index, elem) {
				$(elem).addClass('rng-element-selected');
				$(elem).attr("contentEditable",true);
				var parentElem = $(elem).parent();
				while (parentElem[0] && parentElem[0].tagName != "HTML"){
				    parentElem.attr("contentEditable",false);
				    parentElem = parentElem.parent();
				}
			}
		);
		if (parent && parent !== element) {
			$(parent).addClass('rng-parent-selected');
		}

		this._lastElement = element;
		this._lastElements = selectedNodes;
		this._lastParent = parent;
	};

	this.onMouseOver = function(options) {
		if (this._lastHighlight) {
			this.onMouseOut({selNode: this._lastHighlight});
		}
		if(!options.selNode.ximElement.isSelectable(this.editor.nodeId)) {
			options.selNode = options.selNode.ximElement.getFirstSelectableParent(this.editor.nodeId);
		}
		options.selNode.__background = $(options.selNode).css('background-color');
		options.selNode.__cursor = $(options.selNode).css('cursor');
		if(options.event.shiftKey)
			$(options.selNode).css({'background-color': '#000', 'cursor': 'move'});
		else
			$(options.selNode).css('background-color', '#DEDEDE');
		this._lastHighlight = options.selNode;
	};

	this.onMouseOut = function(options) {
		$(options.selNode).css('background-color', options.selNode.__background);
		$(options.selNode).css('cursor', options.selNode.__cursor);
	};

};

HighlightToolBox.prototype = new XimdocToolBox();
