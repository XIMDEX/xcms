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




var ToolContainerToolBox = Object.xo_create(new XimdocToolBox(), {

	_init: function(options) {

		var $container = $('.kupu-toolboxes-container');
		var $collapser = $('.kupu-toolboxes-collapser');
		var $element = $('.kupu-toolboxes-container-container');
		var $canvas = $('.iwrapper');
		
		var autohide = false;
		
		var hide = function () {
			$collapser.addClass('kupu-collapsed');
			$container.addClass('kupu-toolboxes-container-collapsed');
			$canvas.addClass('iwrapper-extended');	
		}
		var show = function () {
			$collapser.removeClass('kupu-collapsed');
			$container.removeClass('kupu-toolboxes-container-collapsed');
			$canvas.removeClass('iwrapper-extended');	
		}

		$element.droppable({
			accept: 'div',
			tolerance: 'pointer',
			drop: function(ev, ui) {
				this._onDrop($(ui.draggable[0]));
			}.bind(this)
		})/*.sortable()*/;
		
		$collapser.click(function(){
			if (autohide) {
				show();
			} else {
				hide();
			}
			autohide = !autohide;	
		});
		$container.mouseenter(function(){
			if (autohide)
				show();
		});
		$container.mouseleave(function(){
			if (autohide) 
				hide();
		});
	},
	
	_onDrop: function ($item) {
	    
	    var toolId = $('.ui-dialog-title', $item).attr('id');
	    if(!toolId) return this;
	    toolId = toolId.replace('ui-dialog-title-', '');
	    var title = $('.ui-dialog-title', $item).text();
    	var toolObj = this.editor.maintoolboxes[toolId];

	    if(toolObj !== null) {
    		toolObj.setElement({mode: toolObj.MODE_PANEL});
	    	$item.remove();
	    }

		return this;
	}

});

