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
 *  @version $Revision: 8170 $
 */




/**
 * Drawer for ximlets management
 */

function ximletDrawer(elementid, tool, wrap) {

    this.element = getFromSelector(elementid);
    this.tool = tool;
    this.input = getBaseTagClass(this.element, 'input', 'kupu-ximletdrawer-input');
    this.selectedNode = null;
    this.ximletId = null;

    /*
    *	Display the drawer
    */
    this.createContent = function() {

		// Workaround!: Firefox 3 disables the "Update Attribute" button, why?
		kupu.enableButtonFF3(this.element);

        // TODO: take from this.selectedNode value from id
        this.selectedNode = this.editor.getSelectedNode();

        // Be sure that selectedNode is a ximlet
        var rngXimlet = this.selectedNode.rngElement;
        if (!rngXimlet || rngXimlet.type != 'ximlet') {
        	this.drawertool.closeDrawer();
        	return;
        }

        this.input.value = this.selectedNode.ximElement.getMacroId();
        this.ximletId = this.input.value;
        $(this.element).show();
        this.focusElement();
    };

    /*
    *	Add or modify a ximlet
    */
    this.save = function() {

        if(this.input.value != this.ximletId && this.tool._existsXimlet(this.input.value)) {
        	if(!confirm(_('The document already contains a ximlet with ID ') + this.input.value + '. ' + _('Do yo want to continue?')))
        		return;
        }
        
        if(this.input.value != this.ximletId) {
	        // Update Ximlet content
	        this.tool.updateXimlet(this.selectedNode, this.input.value);
		}

		// Close Drawer
        this.drawertool.closeDrawer();
    };

};

ximletDrawer.prototype = new Drawer();