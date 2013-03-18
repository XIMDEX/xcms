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
 *  @version $Revision: 8360 $
 */


/*
 *Toolbox for use the element navigation bar.
 **/
function NavBarToolBox (){
    
	this._element; //Dom navbar element
        this._body = null; //String, html document after xslt transformation
	this.buttons; //Array, with buttos for each element in tree way as far as selector node
        
        this.initialize = function(tool, editor) {
                //Get navbar element
		this._element = $(".kupu-tb-navbar");
		this.editor = editor;
        };
        
        this.beforeUpdateContent = function(options) {
		
	};

	/**
	 * Updates the events handlers after update the editor content
	 */
	this.afterUpdateContent = function(options) {
            
        };

	
        /*
         *After every change in doc
         **/
	this.updateState = function(options){
                //Initializing editor attribute just once.
		if (!this.editor)
                    this.editor=options.caller;
                
                //Get current selector element.
		var ximElement = this.editor.ximElement;

                //Initializing vars
		this._element.empty();
		this.nav = []
		this.buttons = [];
                
                //Appending all ancestor of ximElement in this.nav property
		this._buildPath(ximElement);
                //Start in 1 to avoid docxap element
		for (var i = 1; i < this.nav.length;i++){

			var newTag = $("<span>").text(this.nav[i].tagName).attr("id","tag_"+this.nav[i].uid);

			if (i == this.nav.length -1){
				newTag.addClass("current-tag");
			}else{
				newTag.addClass("selector-tag");
			}
	
			var that = this;
                        newTag.bind('contextmenu', function(e) {
                                        e.preventDefault();
                                        /* Force click to update the clicked node before showing the context menu */
                                        var id = $(this).attr("id");
                                        uid = id.replace("tag_","");
                                        var elements = $('[uid="'+uid+'"]', that.editor.getBody());
                                        if (elements[0]){
                                         	       $(elements[0]).click();
					}
                                        var innerDocument = that.editor.getInnerDocument();
                                        var clonedEvent = innerDocument.createEvent('MouseEvents');
                                        clonedEvent.initMouseEvent(e.type, false, false, window, e.detail, e.screenX, e.screenY, e.clientX, 0, e.ctrlKey, e.altKey, e.shiftKey, e.metaKey, e.button, e.relatedTarget);
                                        innerDocument.dispatchEvent(clonedEvent);
                                });
                        this._element.append(newTag);

                        //Creating button for newTag
                        var button = new NavBarButton("tag_"+this.nav[i].uid, this);

                        button.initialize(this.editor);
                        this.buttons.push(button);


                }
	};

        /*
         *Construct the elements way to params element.
         *Recursive method.
         **/
	this._buildPath = function(ximElement){
		
		if (ximElement.parentNode){
			this._buildPath(ximElement.parentNode);
		}		
		this.nav.push(ximElement);
	};
	
    
}
NavBarToolBox.prototype = new XimdocToolBox();

var NavBarButton = Object.xo_create(XimdocButton, {

	_init: function(buttonid, tool) {
		NavBarButton._construct(this, buttonid, this.commandfunc, tool);
	},
	
	commandfunc: function(event) {
		var newUid = event.buttonid.substring(4);
		var elements = $('[uid="'+newUid+'"]', this.editor.getBody());
		if (elements[0])
			$(elements[0]).click();
    }
});
