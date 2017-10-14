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

var XimimageDrawer = Object.xo_create (new Drawer(), {

	_init: function (elementid, tool) {

		this.element 	= getFromSelector (elementid); //object drawer
	    this.tool 		= tool;
		this.ximElement = null;
	},

	setXimElement: function (ximElement) {

		this.ximElement = ximElement;
	},

    createContent: function () {

		$("body").append ($("<div/>").addClass ("overlay js_overlay"));
		$('button.save-button', this.element).click (this.save.bind (this));
    	$('button.close-button', this.element).click (this.close.bind (this));

    	var div 	= $('#attributes-list-options', this.element);
    	var tagName = $('<p />').addClass ('tag-name').html (this.ximElement.tagName);

    	div.empty ();
    	div.append (tagName);

    	for (var attrName in this.ximElement.schemaNode.attributes) this._createInput (attrName, div);

		$(this.element).show();
        this.focusElement();
    },

    _createInput: function (attrName, div) {

    	if (attrName == 'uid') return;

    	var attribute 	= this.ximElement.schemaNode.attributes [attrName];
    	var label 		= $('<label />').attr ({for: 'ximimage-' + attrName}).addClass ('title').html (attrName);
    	var input 		= attribute.values.length ? $('<select />') : $('<input />');

    	input.attr ({id: 'ximimage-' + attrName}).addClass ('ximage-input');

    	if (attribute.values.length) {

    		var j = 0;

    		while (j < attribute.values.length) {

    			var option 	= $('<option />');
    			var valor 	= attribute.values [j];

    			if (valor == this.ximElement.attributes [attrName]) option.attr('selected', 'selected');
				input.append (option.attr ({value: valor}).html (valor));
				j++;
    		}
    	}else input.attr ({type: 'text', value: this.ximElement.attributes [attrName]});

        input.data('attribute-name', attrName);
    	div.append (label, input);
    },

    save: function () {

        var toolbox     = this.tool.toolboxes ['attributestoolbox'];
        var attributes  = {};

        //no conflict with toolbox
        $('.ximage-input', this.element).each (function (index, elem) {

            var attrName    = $(elem).data ('attribute-name');
            var attrValue   = $(elem).val ();

            attributes[attrName] = attrValue;
        });

        toolbox.tool.saveAttributes (attributes);
        this.editor.logMessage(_('Attributes updated!'));
        toolbox.setActionDescription(_('Update attributes'));
        toolbox._clean();
        this.editor.updateEditor({caller: this});
		this.close ();
    },

    close: function () {

    	/*this.data = null;

    	$('div.js_add_link_panel', this.element).hide(); ;
		$('div.js_add_link_panel', this.element).next("div.buttons").hide(); ;
		$('div.js_search_link_panel', this.element).show() ;
		$('div.js_search_link_panel', this.element).next("div.buttons").show() ;
		$('a.js_add_link', this.element).show() ;
		$("input", this.element).val("");
    	$('select.ximlink-list', this.element).unbind().empty();
    	$('input.ximlink-search', this.element).unbind();

    	$("div.xim-treeview-selector",this.element).treeview("destroy");
    	$("div.xim-treeview-selector",this.element).empty();
    	*/

    	var dt = this.tool.editor.getTool ('ximdocdrawertool');

    	dt.closeDrawer();
    	$('button.save-button', this.element).unbind();
    	$('button.close-button', this.element).unbind();
		$('div.js_overlay').remove();
    }
});