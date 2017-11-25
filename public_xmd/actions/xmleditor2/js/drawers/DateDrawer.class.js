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

var DateDrawer = Object.xo_create(new Drawer(), {
    _init: function(elementid, tool){
        this.element = getFromSelector(elementid);
        this.tool = tool;
        
        this._registerEvents();
    },
    
    createContent: function(){
        
        this.htmlElement = $("[uid='"+this.tool.editor.ximElement.uid+"']", this.tool.editor.getBody())
        var that = this;
        $("body").append($("<div/>").
                    addClass("overlay js_overlay").
                    on("click", function(){
                        that.close();
                    })
                );
        $(this.element).show();
        $(this.element).find(".datepicker")
                .val(this.htmlElement.text())
                .datepicker({"dateFormat":"dd/mm/yy"})
                .datepicker("show");        
    },
    close: function() {
        var dt = this.tool.editor.getTool('ximdocdrawertool');
        dt.closeDrawer();
        $("body div.js_overlay").remove();
        $(this.element).hide();
    },
    
    _registerEvents: function(){
        var that = this;
        $(this.element)
                .find(".save-button")
                .on("click",function(){
                    var dateSelected = $(that.element).find(".datepicker").val()
                    that.htmlElement.text(dateSelected);
                    that.close();
                    
                });
        $(this.element).
                find(".close-button")
                .on("click", function(){
                    that.close();
                });
    }
    
    
});