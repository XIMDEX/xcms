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
 * Drawer for tables management
 */
function TableWizardDrawer(tool, rngElement, selXimElement) {
    /* Table drawer */
    this.element = getFromSelector('kupu-tabledrawer');
    this.tool = tool;
    this.rngElement = rngElement;
    this.selXimElement = selXimElement;

    this.addpanel = getBaseTagClass(this.element, 'div', 'kupu-tabledrawer-addtable');
    this.editpanel = getBaseTagClass(this.element, 'div', 'kupu-tabledrawer-edittable');
    var editclassselect = getBaseTagClass(this.element, 'select', 'kupu-tabledrawer-editclasschooser');
    var addclassselect = getBaseTagClass(this.element, 'select', 'kupu-tabledrawer-addclasschooser');
    var alignselect = getBaseTagClass(this.element, 'select', 'kupu-tabledrawer-alignchooser');
    var newrowsinput = getBaseTagClass(this.element, 'input', 'kupu-tabledrawer-newrows');
    var newcolsinput = getBaseTagClass(this.element, 'input', 'kupu-tabledrawer-newcols');
    var makeheadercheck = getBaseTagClass(this.element, 'input', 'kupu-tabledrawer-makeheader');

    this.createContent = function() {

        var editor = this.editor;
        var selNode = editor.getSelectedNode();

        /*function fixClasses(classselect) {
            if (editor.config.table_classes) {
                var classes = editor.config.table_classes['class'];
                while (classselect.hasChildNodes()) {
                    classselect.removeChild(classselect.firstChild);
                };
                for (var i=0; i < classes.length; i++) {
                    var classinfo = classes[i];
                    var caption = classinfo.xcaption || classinfo;
                    var classname = classinfo.classname || classinfo;

                    var option = document.createElement('option');
                    var content = document.createTextNode(caption);
                    option.appendChild(content);
                    option.setAttribute('value', classname);
                    classselect.appendChild(option);
                };
            };
        };
        fixClasses(addclassselect);
        fixClasses(editclassselect);*/

		// Workaround!: Firefox 3 disables the "Update Attribute" button, why?
		kupu.enableButtonFF3(this.element);

        var table = editor.getNearestParentOfType(selNode, 'table');
        //console.info(editor.getNearestParentWithAttribute(selNode, 'table'));

		table = selNode.ximElement;
		if (!table['structuredList']) table = false;

        if (!table) {
            // show add table drawer
            show = this.addpanel;
            hide = this.editpanel;
        } else {
            // show edit table drawer

            // TODO: May we connect this "edit panel" with AttributesTool?

            show = this.editpanel;
            hide = this.addpanel;
            /*var align = this.tool._getColumnAlign(selNode);
            selectSelectItem(alignselect, align);
            selectSelectItem(editclassselect, table.className);*/
        };

		  // For now, overriting to only creation:
		  show = this.addpanel;
		  hide = this.editpanel;

        hide.style.display = 'none';
        show.style.display = 'block';
        this.element.style.display = 'block';
        this.focusElement();
    };

    this.createTable = function() {
        //this.editor.resumeEditing();
        var options = {
        	rows: newrowsinput.value || 1,
        	cols: newcolsinput.value || 1
        };
		  this.tool.createTable(this.rngElement, this.editor.selNode.ximElement, options);
		  this.drawertool.closeDrawer();
    };
};

TableWizardDrawer.prototype = new Drawer();