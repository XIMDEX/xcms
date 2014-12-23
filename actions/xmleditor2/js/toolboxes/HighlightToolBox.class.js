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


function FormViewToolBox() {

	this.element = false;
    this.initialize = function(tool,editor){

		this.tool = tool;
		this.editor = editor;

    };
    this.beforeUpdateContent = function(options){	
	
    };
    this.updateState = function(options){
	
		if(this.editor.view=="form"){

			this.element = this.editor.ximElement;
			this.formElement = $("[uid='"+this.element.uid+"']", this.editor.getBody()).closest(".js-edition-block");
			if (this.formElement.length){
				 $(".btn-toolbar button", this.formElement).addClass("disabled");
				var $elementSiblingButtons = $(".btn-toolbar .js-siblings button", this.formElement);
				var $elementApplyButtons = $(".btn-toolbar .js-applies button", this.formElement);
				var that = this;
				var parentElement = this.element.parentNode;
				var uid=this.element.uid;		

				if (this.editor.selectedTextLength > 0){
			    	$(".btn-toolbar .js-applies button", this.formElement).removeClass("disabled");
				}else{
					$(".btn-toolbar .js-siblings button", this.formElement).removeClass("disabled");
                                        $(".btn-toolbar .js-extra button", this.formElement).removeClass("disabled");
				}		
				
				$elementApplyButtons.each(function(index, button){
					var elementTypes = that.element.schemaNode.type;
					var childName = $(button).data("element");
					if (elementTypes && elementTypes.length && elementTypes.indexOf(childName) !== -1){
						$(button).removeClass("disabled")
					}
					var xeditButton = that.editor.tools[childName + '_rngbutton'];  
					$(button).off("click").on("click",function(){
						if (childName == that.element.tagName){
							that.editor.tools.ximdoctool.disApplyElement(that.element);
						}else{
							var ximElement = that.editor.getXimDocument().getElement(uid);
						    that.editor.ximElement = ximElement
						    that.editor.selNode = $(button).parent().parent().siblings("[uid='"+uid+"']")[0];
						    xeditButton.commandfunc(null,null,null,parentElement);
						    that.editor._setSelectionData( ximElement );
						}
					    that.editor.updateEditor({caller: that, target: ximElement});	
					});
				});

				$elementSiblingButtons.each(function(index, button){
					var childName = $(button).data("element");
					var xeditButton = that.editor.tools[childName + '_rngbutton']; 
                                        var currentElement = that.element;
					$(button).off("click").on("click",function(){
						var found = false;
						
						var availableSiblingElements;
						while(!found && currentElement.parentNode){
							availableSiblingElements = currentElement.parentNode.schemaNode.childNodes;
							for (var i = 0; i < availableSiblingElements.length; i++){
								if (availableSiblingElements[i].tagName == childName){
									found = true;
									parentElement = currentElement.parentNode? currentElement.parentNode:currentElement;
									break;
								}
							}
							
							if (!found)
								currentElement = currentElement.parentNode;
						}
						
					    var ximElement = that.editor.getXimDocument().getElement(uid);
					    that.editor.ximElement = ximElement
					    that.editor.selNode = $(button).parent().parent().siblings("[uid='"+uid+"']")[0];
					    if (parentElement){
					    	xeditButton.commandfunc(null,null,null,parentElement, currentElement);
					    	that.editor._setSelectionData( ximElement );
					    	that.editor.updateEditor({caller: that, target: ximElement});
					    }
				    
					});
				});
                                
			}
                        
                        this.enableNewElementButton();
		}		
	
    };
    
    this.enableNewElementButton =function(){
        that = this;
        $(".js-add-more", this.editor.getBody())
                .off("click")
                .on("click", function(){
                    var uid = that.editor.nodeId+"."+$(this).data("uid");
                    var lastElement = that.editor.getXimDocument().getElement(uid);
                    that.editor.tools["ximdoctool"].createElement(lastElement.tagName, lastElement.parentNode, lastElement);
                })
    }
}

FormViewToolBox.prototype = new XimdocToolBox();