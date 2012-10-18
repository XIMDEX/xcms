/*****************************************************************************
 *
 * Copyright (c) 2003-2005 Kupu Contributors. All rights reserved.
 *
 * This software is distributed under the terms of the Kupu
 * License. See LICENSE.txt for license text. For a list of Kupu
 * Contributors see CREDITS.txt.
 *
 *****************************************************************************/

// $Id: kupusilvatools.js 22877 2006-01-30 15:57:00Z guido $

// a mapping from namespace to field names, here you can configure which 
// metadata fields should be editable with the property editor (needs to
// be moved to somewhere in Silva or something?)
EDITABLE_METADATA = {
    'http://infrae.com/namespaces/metadata/silva-news': 
            [['subjects', 'checkbox', 1, 'subjects'],
                ['target_audiences', 'checkbox', 1, 'target audiences'],
                ['start_datetime', 'datetime', 1, 'start date/time'],
                ['end_datetime', 'datetime', 0, 'end date/time'],
                ['location', 'text', 0, 'location']
            ]
}
 
function SilvaLinkTool() {
    /* redefine the contextmenu elements */
};

SilvaLinkTool.prototype = new LinkTool;

SilvaLinkTool.prototype.updateLink = function (linkel, url, type, 
                                                    name, target, title) {
    if (type && type == 'anchor') {
        linkel.removeAttribute('href');
        linkel.setAttribute('name', name);
    } else {
        linkel.href = url;
        linkel.setAttribute('silva_href', url);
        if (linkel.innerHTML == "") {
            var doc = this.editor.getInnerDocument();
            linkel.appendChild(doc.createTextNode(title || url));
        }
        if (title) {
            linkel.title = title;
        } else {
            linkel.removeAttribute('title');
        }
        if (target && target != '') {
            linkel.setAttribute('target', target);
        }
        else {
            linkel.removeAttribute('target');
        };
        linkel.style.color = this.linkcolor;
    };
};

SilvaLinkTool.prototype.createContextMenuElements = function(selNode, event) {
    /* create the 'Create link' or 'Remove link' menu elements */
    var ret = new Array();
    var link = this.editor.getNearestParentOfType(selNode, 'a');
    if (link) {
        ret.push(new ContextMenuElement('Delete link', this.deleteLink, this));
    } else {
        ret.push(new ContextMenuElement('Create link', getLink, this));
    };
    return ret;
};

function SilvaLinkToolBox(inputid, targetselectid, targetinputid, 
                            addbuttonid, updatebuttonid, delbuttonid, 
                            toolboxid, plainclass, activeclass) {
    /* create and edit links */
    
    this.input = getFromSelector(inputid);
    this.targetselect = getFromSelector(targetselectid);
    this.targetinput = getFromSelector(targetinputid);
    this.addbutton = getFromSelector(addbuttonid);
    this.updatebutton = getFromSelector(updatebuttonid);
    this.delbutton = getFromSelector(delbuttonid);
    this.toolboxel = getFromSelector(toolboxid);
    this.plainclass = plainclass;
    this.activeclass = activeclass;
};

SilvaLinkToolBox.prototype = new LinkToolBox;

SilvaLinkToolBox.prototype.initialize = function(tool, editor) {
    this.tool = tool;
    this.editor = editor;
    addEventHandler(this.targetselect, 'change', this.selectTargetHandler, this);
    addEventHandler(this.targetinput, 'change', this.selectTargetHandler, this);
    addEventHandler(this.addbutton, 'click', this.createLinkHandler, this);
    addEventHandler(this.updatebutton, 'click', this.createLinkHandler, this);
    addEventHandler(this.delbutton, 'click', this.tool.deleteLink, this);
    this.targetinput.style.display = 'none';
    this.editor.logMessage('Link tool initialized');
};

SilvaLinkToolBox.prototype.selectTargetHandler = function(event) {
    var select = this.targetselect;
    var input = this.targetinput;

    var selvalue = select.options[select.selectedIndex].value;
    if (selvalue != 'input') {
        input.style.display = 'none';
    } else {
        input.style.display = 'inline';
    };
};
 
SilvaLinkToolBox.prototype.createLinkHandler = function(event) {
    var url = this.input.value;
    var target = this.targetselect.options[
                    this.targetselect.selectedIndex].value;
    if (target == 'input') {
        target = this.targetinput.value;
     };
    this.tool.createLink(url, 'link', null, target);
};

SilvaLinkToolBox.prototype.updateState = function(selNode, event) {
    var currnode = selNode;
    var link = false;
    var href = '';
    while (currnode) {
        if (currnode.nodeName.toLowerCase() == 'a') {
            href = currnode.getAttribute('silva_href');
            if (!href) {
                href = currnode.getAttribute('href');
            };
            if (href) {
                if (this.toolboxel) {
                    this.toolboxel.className = this.activeclass;
                    if (this.toolboxel.open_handler) {
                        this.toolboxel.open_handler();
                    };
                };
                this.input.value = href;
                var target = currnode.getAttribute('target');
                if (!target) {
                    this.targetselect.selectedIndex = 0;
                    this.targetinput.style.display = 'none';
                } else {
                    var target_found = false;
                    for (var i=0; i < this.targetselect.options.length; i++) {
                        var option = this.targetselect.options[i];
                        if (option.value == target) {
                            this.targetselect.selectedIndex = i;
                            target_found = true;
                            break;
                        };
                    };
                    if (target_found) {
                        this.targetinput.value = '';
                        this.targetinput.style.display = 'none';
                    } else {
                        // XXX this is pretty hard-coded...
                        this.targetselect.selectedIndex = 
                                this.targetselect.options.length - 1;
                        this.targetinput.value = target;
                        this.targetinput.style.display = 'inline';
                    };
                };
                this.addbutton.style.display = 'none';
                this.updatebutton.style.display = 'inline';
                this.delbutton.style.display = 'inline';
                return;
            };
        };
        currnode = currnode.parentNode;
    };
    this.targetselect.selectedIndex = 0;
    this.targetinput.value = '';
    this.targetinput.style.display = 'none';
    this.updatebutton.style.display = 'none';
    this.delbutton.style.display = 'none';
    this.addbutton.style.display = 'inline';
    if (this.toolboxel) {
        this.toolboxel.className = this.plainclass;
    };
    this.input.value = '';
};
 
function SilvaImageTool(editelid, urlinputid, targetselectid, targetinputid, 
                        hireslinkcheckboxid, linkinputid, 
                        alignselectid, titleinputid, toolboxid, plainclass, 
                        activeclass) {
    /* Silva specific image tool */
    this.editel = getFromSelector(editelid);
    this.urlinput = getFromSelector(urlinputid);
    this.targetselect = getFromSelector(targetselectid);
    this.targetinput = getFromSelector(targetinputid);
    this.hireslinkcheckbox = getFromSelector(hireslinkcheckboxid);
    this.linkinput = getFromSelector(linkinputid);
    this.alignselect = getFromSelector(alignselectid);
    this.titleinput = getFromSelector(titleinputid);
    this.toolboxel = getFromSelector(toolboxid);
    this.plainclass = plainclass;
    this.activeclass = activeclass;
}

SilvaImageTool.prototype = new ImageTool;

SilvaImageTool.prototype.initialize = function(editor) {
    this.editor = editor;
    addEventHandler(this.targetselect, 'change', this.setTarget, this);
    addEventHandler(this.targetselect, 'change', this.selectTargetHandler, this);
    addEventHandler(this.targetinput, 'change', this.setTarget, this);
    addEventHandler(this.urlinput, 'change', this.setSrc, this);
    addEventHandler(this.hireslinkcheckbox, 'change', this.setHires, this);
    addEventHandler(this.linkinput, 'keypress', this.setLink, this);
    addEventHandler(this.linkinput, 'change', this.setLink, this);
    addEventHandler(this.alignselect, 'change', this.setAlign, this);
    addEventHandler(this.titleinput, 'change', this.setTitle, this);
    this.targetinput.style.display = 'none';
    this.editor.logMessage('Image tool initialized');
};

SilvaImageTool.prototype.createContextMenuElements = function(selNode, event) {
    return new Array(new ContextMenuElement('Create image', getImage, this));
};

SilvaImageTool.prototype.selectTargetHandler = function(event) {
    var select = this.targetselect;
    var input = this.targetinput;

    var selvalue = select.options[select.selectedIndex].value;
    if (selvalue != 'input') {
        input.style.display = 'none';
    } else {
        input.style.display = 'inline';
    };
};

SilvaImageTool.prototype.updateState = function(selNode, event) {
    var image = this.editor.getNearestParentOfType(selNode, 'img');
    if (image) {
        this.editel.style.display = 'block';
        var src = image.getAttribute('silva_src');
        if (!src) {
            src = image.getAttribute('src');
        };
        this.urlinput.value = src;
        var target = image.getAttribute('target');
        if (!target) {
            this.targetselect.selectedIndex = 0;
            this.targetinput.style.display = 'none';
        } else {
            var target_found = false;
            for (var i=0; i < this.targetselect.options.length; i++) {
                var option = this.targetselect.options[i];
                if (option.value == target) {
                    this.targetselect.selectedIndex = i;
                    target_found = true;
                    break;
                };
            };
            if (target_found) {
                this.targetinput.value = '';
                this.targetinput.style.display = 'none';
            } else {
                this.targetselect.selectedIndex = this.targetselect.options.length - 1;
                this.targetinput.value = target;
                this.targetinput.style.display = 'inline';
            };
        };
        var hires = image.getAttribute('link_to_hires') == '1';
        if (!hires) {
            var link = image.getAttribute('link');
            this.hireslinkcheckbox.checked = false;
            this.linkinput.value = link == null ? '' : link;
        } else {
            this.hireslinkcheckbox.checked = 'checked';
            this.linkinput.value = '';
            this.linkinput.disabled = 'disabled';
        };
        if (this.toolboxel) {
            if (this.toolboxel.open_handler) {
                this.toolboxel.open_handler();
            };
            this.toolboxel.className = this.activeclass;
        };
        var align = image.getAttribute('alignment');
        if (!align) {
            align = 'left';
        };
        var title = image.getAttribute('title');
        if (!title) {
            title = '';
        };
        this.titleinput.value = title;
        selectSelectItem(this.alignselect, align);
    } else {
        this.editel.style.display = 'none';
        this.urlinput.value = '';
        this.titleinput.value = '';
        if (this.toolboxel) {
            this.toolboxel.className = this.plainclass;
        };
        this.targetselect.selectedIndex = 0;
        this.targetinput.value = '';
        this.targetinput.style.display = 'none';
    };
};

SilvaImageTool.prototype.createImage = function(url, alttext, imgclass) {
    /* create an image */
    var img = this.editor.getInnerDocument().createElement('img');
    img.src = url;
    img.setAttribute('silva_src', url);
    img.removeAttribute('height');
    img.removeAttribute('width');
    if (alttext) {
        img.alt = alttext;
    };
    if (imgclass) {
        img.className = imgclass;
    };
    img = this.editor.insertNodeAtSelection(img, 1);
    this.editor.logMessage(_('Image inserted'));
    this.editor.updateState();
    return img;
};
    
SilvaImageTool.prototype.setTarget = function() {
    var target = this.targetselect.options[this.targetselect.selectedIndex].value;
    if (target == 'input') {
        target = this.targetinput.value;
    };
    var selNode = this.editor.getSelectedNode();
    var image = this.editor.getNearestParentOfType(selNode, 'img');
    if (!image) {
        this.editor.logMessage('No image selected!', 1);
    };
    image.setAttribute('target', target);
};

SilvaImageTool.prototype.setSrc = function() {
    var selNode = this.editor.getSelectedNode();
    var img = this.editor.getNearestParentOfType(selNode, 'img');
    if (!img) {
        this.editor.logMessage('Not inside an image!', 1);
    };
    
    var src = this.urlinput.value;
    img.setAttribute('src', src);
    img.setAttribute('silva_src', src);
    this.editor.logMessage('Image updated');
};

SilvaImageTool.prototype.setHires = function() {
    var selNode = this.editor.getSelectedNode();
    var image = this.editor.getNearestParentOfType(selNode, 'img');
    if (!image) {
        this.editor.logMessage('No image selected!', 1);
        return;
    };
    if (this.hireslinkcheckbox.checked) {
        image.setAttribute('link_to_hires', '1');
        image.removeAttribute('link');
        this.linkinput.value = '';
        this.linkinput.disabled = 'disabled';
    } else {
        image.setAttribute('link_to_hires', '0');
        image.setAttribute('link', this.linkinput.value);
        this.linkinput.disabled = false;
    };
};

SilvaImageTool.prototype.setLink = function() {
    var link = this.linkinput.value;
    var selNode = this.editor.getSelectedNode();
    var image = this.editor.getNearestParentOfType(selNode, 'img');
    if (!image) {
        this.editor.logMessage('No image selected!', 1);
        return;
    };
    image.setAttribute('link', link);
    image.setAttribute('link_to_hires', '0');
};

SilvaImageTool.prototype.setTitle = function() {
    var selNode = this.editor.getSelectedNode();
    var image = this.editor.getNearestParentOfType(selNode, 'img');
    if (!image) {
        this.editor.logMessage('No image selected!', 1);
        return;
    };
    var title = this.titleinput.value;
    image.setAttribute('title', title);
};

SilvaImageTool.prototype.setAlign = function() {
    var selNode = this.editor.getSelectedNode();
    var image = this.editor.getNearestParentOfType(selNode, 'img');
    if (!image) {
        this.editor.logMessage('Not inside an image', 1);
        return;
    };
    var align = this.alignselect.options[this.alignselect.selectedIndex].value;
    image.setAttribute('alignment', align);
};
 
function SilvaTableTool() {
    /* Silva specific table functionality
        overrides most of the table functionality, required because Silva requires
        a completely different format for tables
    */
}

SilvaTableTool.prototype = new TableTool;

SilvaTableTool.prototype.createTable = function(rows, cols, makeHeader, tableclass) {
    /* add a Silvs specific table, with an (optional) header with colspan */
    var doc = this.editor.getInnerDocument();

    var table = doc.createElement('table');
    table.style.width = "100%";
    table.className = tableclass;

    var tbody = doc.createElement('tbody');
    
    if (makeHeader) {
        this._addRowHelper(doc, tbody, 'th', -1, cols);
    }

    for (var i=0; i < rows; i++) {
        this._addRowHelper(doc, tbody, 'td', -1, cols);
    }

    table.appendChild(tbody);

    // call the _getColumnInfo() method, this will generate the colinfo on the
    // table
    this._getColumnInfo(table);

    var iterator = new NodeIterator(table);
    var currnode = null;
    var contentcell = null;
    while (currnode = iterator.next()) {
        var nodename = currnode.nodeName.toLowerCase();
        if (nodename == 'td' || nodename == 'th') {
            contentcell = currnode;
            break;
        };
    };
    
    var selection = this.editor.getSelection();
    var docfrag = selection.cloneContents();
    var setcursoratend = false;
    if (contentcell && docfrag.hasChildNodes()) {
        while (contentcell.hasChildNodes()) {
            contentcell.removeChild(contentcell.firstChild);
        };
        
        while (docfrag.hasChildNodes()) {
            contentcell.appendChild(docfrag.firstChild);
            setcursoratend = true;
        };
    };
    this.editor.insertNodeAtSelection(table);

    this._setTableCellHandlers(table);

    this.editor.logMessage('Table added');
};

SilvaTableTool.prototype.addTableRow = function() {
    /* add a table row or header */
    var currnode = this.editor.getSelectedNode();
    var doc = this.editor.getInnerDocument();
    var tbody = this.editor.getNearestParentOfType(currnode, 'tbody');
    if (!tbody) {
        this.editor.logMessage('No table found!', 1);
        return;
    }
    var cols = this._countCells(tbody);
    var currrow = this.editor.getNearestParentOfType(currnode, 'tr');
    if (!currrow) {
        this.editor.logMessage('Not inside a row!', 1);
        return;
    };
    var index = this._getRowIndex(currrow) + 1;
    // should check what to add as well
    this._addRowHelper(doc, tbody, 'td', index, cols);

    this.editor.logMessage('Table row added');
};

SilvaTableTool.prototype.delTableRow = function() {
    /* delete a table row or header */
    var currnode = this.editor.getSelectedNode();
    var currtr = this.editor.getNearestParentOfType(currnode, 'tr');

    if (!currtr) {
        this.editor.logMessage('Not inside a row!', 1);
        return;
    };

    currtr.parentNode.removeChild(currtr);

    this.editor.logMessage('Table row removed');
};

SilvaTableTool.prototype.addTableColumn = function() {
    /* add a table column */
    var currnode = this.editor.getSelectedNode();
    var doc = this.editor.getInnerDocument();
    var table = this.editor.getNearestParentOfType(currnode, 'table');
    if (!table) {
        this.editor.logMessage('Not inside a table!');
        return;
    };
    var body = table.getElementsByTagName('tbody')[0];
    var currcell = this.editor.getNearestParentOfType(currnode, 'td');
    if (!currcell) {
        var currcell = this.editor.getNearestParentOfType(currnode, 'th');
        if (!currcell) {
            this.editor.logMessage('Not inside a row!', 1);
            return;
        } else {
            var index = -1;
        };
    } else {
        var index = this._getColIndex(currcell) + 1;
    };
    var numcells = this._countCells(body);
    this._addColHelper(doc, body, index, numcells);
    table.removeAttribute('silva_column_info');
    this._getColumnInfo();

    this.editor.logMessage('Column added');
};

SilvaTableTool.prototype.delTableColumn = function() {
    /* delete a column */
    var currnode = this.editor.getSelectedNode();
    var table = this.editor.getNearestParentOfType(currnode, 'table');
    if (!table) {
        this.editor.logMessage('Not inside a table body!', 1);
        return;
    }
    var body = table.getElementsByTagName('tbody')[0];
    var currcell = this.editor.getNearestParentOfType(currnode, 'td');
    if (!currcell) {
        currcell = this.editor.getNearestParentOfType(currnode, 'th');
        if (!currcell) {
            this.editor.logMessage('Not inside a cell!');
            return;
        };
        var index = -1;
    } else {
        var index = this._getColIndex(currcell);
    };

    this._delColHelper(body, index);
    table.removeAttribute('silva_column_info');
    this._getColumnInfo();

    this.editor.logMessage('Column deleted');
};

SilvaTableTool.prototype.setColumnWidths = function(widths) {
    /* sets relative column widths */
    var selNode = this.editor.getSelectedNode();
    var table = this.editor.getNearestParentOfType(selNode, 'table');

    if (!table) {
        this.editor.logMessage('not a table');
        return;
    };

    var silva_column_info = this._getColumnInfo(table);
    widths = widths.split(',');
    if (widths.length != silva_column_info.length) {
        alert('number of widths doesn\'t match number of columns!');
        return;
    };
    for (var i=0; i < widths.length; i++) {
        silva_column_info[i][1] = widths[i];
    };
    this._setColumnInfo(table, silva_column_info);
    this._updateTableFromInfo(table);
};

SilvaTableTool.prototype.getColumnWidths = function(table) {
    var silvacolinfo = table.getAttribute('silva_column_info');
    var widths = new Array();
    silvacolinfo = silvacolinfo.split(' ');
    for (var i=0; i < silvacolinfo.length; i++) {
        var pair = silvacolinfo[i].split(':');
        if (pair[1] == '*') {
            widths.push('*');
        } else {
            widths.push(parseInt(pair[1]));
        };
    };
    widths = this._factorWidths(widths);
    return widths;
};

SilvaTableTool.prototype.setColumnAlign = function(align) {
    var currnode = this.editor.getSelectedNode();
    var currtd = this.editor.getNearestParentOfType(currnode, 'td');
    var index = 0;
    if (!currtd) {
        return; // might be we're not inside a table, else we're inside a th
    } else {
        var cols = this._getAllColumns(currtd.parentNode);
        for (var i=0; i < cols.length; i++) {
            if (cols[i] == currtd) {
                index = i;
                break;
            };
        };
    };
    var infos = this._getColumnInfo();
    infos[index][0] = align;
    var table = this.editor.getNearestParentOfType(currnode, 'table');
    this._setColumnInfo(table, infos);
    this._updateTableFromInfo(table);
};

SilvaTableTool.prototype._factorWidths = function(widths) {
    var highest = 0;
    for (var i=0; i < widths.length; i++) {
        if (widths[i] > highest) {
            highest = widths[i];
        };
    };
    var factor = 1;
    for (var i=0; i < highest; i++) {
        var testnum = highest - i;
        var isfactor = true;
        for (var j=0; j < widths.length; j++) {
            if (widths[j] % testnum != 0) {
                isfactor = false;
                break;
            };
        };
        if (isfactor) {
            factor = testnum;
            break;
        };
    };
    if (factor > 1) {
        for (var i=0; i < widths.length; i++) {
            widths[i] = widths[i] / factor;
        };
    };
    return widths;
};

SilvaTableTool.prototype._addRowHelper = function(doc, body, celltype, index, numcells) {
    /* actually adds a row to the table */
    var row = doc.createElement('tr');

    // fill the row with cells
    if (celltype == 'td') {
        for (var i=0; i < numcells; i++) {
            var cell = doc.createElement(celltype);
            var nbsp = doc.createTextNode("\u00a0");
            cell.appendChild(nbsp);
            row.appendChild(cell);
        }
    } else if (celltype == 'th') {
        var cell = doc.createElement(celltype);
        cell.setAttribute('colSpan', numcells);
        var nbsp = doc.createTextNode("\u00a0");
        cell.appendChild(nbsp);
        row.appendChild(cell);
    }

    // now append it to the tbody
    var rows = this._getAllRows(body);
    if (index == -1 || index >= rows.length) {
        body.appendChild(row);
    } else {
        var nextrow = rows[index];
        body.insertBefore(row, nextrow);
    }

    return row;
};

SilvaTableTool.prototype._addColHelper = function(doc, body, index, numcells) {
    /* actually adds a column to a table */
    var rows = this._getAllRows(body);
    for (var i=0; i < rows.length; i++) {
        var row = rows[i];
        var cols = this._getAllColumns(row);
        var col = cols[0];
        if (col.nodeName.toLowerCase() == 'th') {
            var colspan = col.getAttribute('colSpan');
            if (colspan) {
                colspan = parseInt(colspan);
            } else {
                colspan = 1;
            }
            col.setAttribute('colSpan', colspan + 1);
        } else {
            var cell = doc.createElement('td');
            var nbsp = doc.createTextNode('\u00a0');
            cell.appendChild(nbsp);
            if (index == -1 || index >= rows.length) {
                row.appendChild(cell);
            } else {
                row.insertBefore(cell, cols[index]);
            };
        };
    };
    var table = body.parentNode;
    table.removeAttribute('silva_column_info');
    this._getColumnInfo();
};

SilvaTableTool.prototype._delColHelper = function(body, index) {
    /* actually delete all cells in a column */
    var rows = this._getAllRows(body);
    for (var i=0; i < rows.length; i++) {
        var row = rows[i];
        var cols = this._getAllColumns(row);
        if (cols[0].nodeName.toLowerCase() == 'th') {
            // is a table header, so reduce colspan
            var th = cols[0];
            var colspan = th.getAttribute('colSpan');
            if (!colspan || colspan == '1') {
                body.removeChild(row);
            } else {
                colspan = parseInt(colspan);
                th.setAttribute('colSpan', colspan - 1);
            };
        } else {
            // is a table cell row, remove one
            if (index > -1) {
                row.removeChild(cols[index]);
            } else {
                row.removeChild(cols[cols.length - 1]);
            }
        }
    };
    var table = body.parentNode;
    table.removeAttribute('silva_column_info');
    this._getColumnInfo();
};

SilvaTableTool.prototype._getRowIndex = function(row) {
    /* get the current rowindex */
    var rowindex = 0;
    var prevrow = row.previousSibling;
    while (prevrow) {
        if (prevrow.nodeName.toLowerCase() == 'tr') {
            rowindex++;
        };
        prevrow = prevrow.previousSibling;
    };
    return rowindex;
};

SilvaTableTool.prototype._countCells = function(body) {
    /* get the current column index */
    var numcols = 0;
    var cols = this._getAllColumns(this._getAllRows(body)[0]);
    for (var i=0; i < cols.length; i++) {
        var node = cols[i];
        if (node.nodeName.toLowerCase() == 'th') {
            var colspan = node.getAttribute('colSpan');
            if (colspan) {
                colspan = parseInt(colspan);
            } else {
                colspan = 1;
            };
            numcols += colspan;
        } else {
            numcols++;
        };
    };
    return numcols;
};

SilvaTableTool.prototype._getAllRows = function(body) {
    /* returns an Array of all available rows */
    var rows = new Array();
    for (var i=0; i < body.childNodes.length; i++) {
        var node = body.childNodes[i];
        if (node.nodeName.toLowerCase() == 'tr') {
            rows.push(node);
        };
    };
    return rows;
};

SilvaTableTool.prototype._getAllColumns = function(row) {
    /* returns an Array of all columns in a row */
    var cols = new Array();
    for (var i=0; i < row.childNodes.length; i++) {
        var node = row.childNodes[i];
        if (node.nodeName.toLowerCase() == 'td' || 
                node.nodeName.toLowerCase() == 'th') {
            cols.push(node);
        };
    };
    return cols;
};

SilvaTableTool.prototype._getColumnInfo = function(table) {
    var mapping = {'C': 'center', 'L': 'left', 'R': 'right'};
    var revmapping = {'center': 'C', 'left': 'L', 'right': 'R'};
    if (!table) {
        var selNode = this.editor.getSelectedNode();
        var table = this.editor.getNearestParentOfType(selNode, 'table');
    };
    if (!table) {
        return;
    };
    var silvacolinfo = table.getAttribute('silva_column_info');
    if (silvacolinfo) {
        var infos = silvacolinfo.split(' ');
        var ret = [];
        for (var i=0; i < infos.length; i++) {
            var tup = infos[i].split(':');
            tup[0] = mapping[tup[0]];
            ret.push(tup);
        };
        return ret;
    } else {
        var ret = [];
        var body = null;
        var iterator = new NodeIterator(table);
        var body = iterator.next();
        var colinfo = []; // to use as the table attribute later on
        while (body.nodeName.toLowerCase() != 'tbody') {
            body = iterator.next();
        };
        var rows = this._getAllRows(body);
        for (var i=0; i < rows.length; i++) {
            var cols = this._getAllColumns(rows[i]);
            if (cols[0].nodeName.toLowerCase() == 'td') {
                for (var j=0; j < cols.length; j++) {
                    var tup = [];
                    var className = cols[j].className;
                    tup[0] = 'left';
                    if (className.indexOf('align-') == 0) {
                        tup[0] = className.substr(6);
                    };
                    var width = cols[j].getAttribute('width');
                    if (!width) {
                        width = 1;
                    } else {
                        width = parseInt(width);
                    };
                    tup[1] = width;
                    colinfo.push(revmapping[tup[0]] + ':' + tup[1]);
                    ret.push(tup);
                };
                table.setAttribute('silva_column_info', colinfo.join(' '));
                return ret;
            };
        };
    };
};

SilvaTableTool.prototype._setColumnInfo = function(table, info) {
    var mapping = {'center': 'C', 'left': 'L', 'right': 'R'};
    var str = [];
    for (var i=0; i < info.length; i++) {
        str.push(mapping[info[i][0]] + ':' + info[i][1]);
    };
    table.setAttribute('silva_column_info', str.join(' '));
};

SilvaTableTool.prototype._updateTableFromInfo = function(table) {
    var colinfo = this._getColumnInfo(table);

    // convert the relative widths to percentages first
    var totalunits = 0;
    for (var i=0; i < colinfo.length; i++) {
        if (colinfo[i][1] == '*') {
            totalunits += 1;
        } else {
            totalunits += parseInt(colinfo[i][1]);
        };
    };

    var percent_per_unit = 100.0 / totalunits;

    // find the rows containing cells
    var rows = this._getAllRows(table.getElementsByTagName('tbody')[0]);
    for (var i=0; i < rows.length; i++) {
        var cols = this._getAllColumns(rows[i]);
        if (cols[0].nodeName.toLowerCase() == 'th') {
            continue;
        };
        for (var j=0; j < cols.length; j++) {
            var align = colinfo[j][0];
            cols[j].className = 'align-' + align;
            var width = colinfo[j][1];
            if (width != '*') {
                cols[j].setAttribute('width', '' + 
                            (width * percent_per_unit) + '%');
            } else {
                cols[j].removeAttribute('width');
            };
        };
    };
    return;



    // find the first cell, use its parent as the row
    // XXX note that this might potentially go wrong on nested tables!
    var firstrow = table.getElementsByTagName('td')[0].parentNode;
    var colinfo = this._getColumnInfo(table);

    // now convert the relative widths to percentages
    // first find the first row containing cells
    var totalunits = 0;
    for (var i=0; i < colinfo.length; i++) {
        if (colinfo[i][1] == '*') {
            totalunits += 1;
        } else {
            totalunits += parseInt(colinfo[i][1]);
        };
    };

    var percent_per_unit = 100.0 / totalunits;

    var children = firstrow.childNodes;
    var currcellindex = 0;
    for (var i=0; i < children.length; i++) {
        var child = children[i];
        if (child.nodeType != 1 || child.nodeName.toLowerCase() != 'td') {
            continue;
        };
        var align = colinfo[currcellindex][0];
        child.className = 'align-' + align;
        var width = colinfo[currcellindex][1];
        if (width != '*') {
            child.setAttribute('width', '' + 
                        (width * percent_per_unit) + '%');
        } else {
            child.removeAttribute('width');
        };
        currcellindex++;
    };
};

function SilvaTableToolBox(addtabledivid, edittabledivid, newrowsinputid, 
                        newcolsinputid, makeheaderinputid, classselectid, alignselectid, widthinputid,
                        addtablebuttonid, addrowbuttonid, delrowbuttonid, addcolbuttonid, delcolbuttonid, 
                        fixbuttonid, delbuttonid, toolboxid, plainclass, activeclass) {
    /* Silva specific table functionality
        overrides most of the table functionality, required because Silva requires
        a completely different format for tables
    */

    this.addtablediv = getFromSelector(addtabledivid);
    this.edittablediv = getFromSelector(edittabledivid);
    this.newrowsinput = getFromSelector(newrowsinputid);
    this.newcolsinput = getFromSelector(newcolsinputid);
    this.makeheaderinput = getFromSelector(makeheaderinputid);
    this.classselect = getFromSelector(classselectid);
    this.alignselect = getFromSelector(alignselectid);
    this.widthinput = getFromSelector(widthinputid);
    this.addtablebutton = getFromSelector(addtablebuttonid);
    this.addrowbutton = getFromSelector(addrowbuttonid);
    this.delrowbutton = getFromSelector(delrowbuttonid);
    this.addcolbutton = getFromSelector(addcolbuttonid);
    this.delcolbutton = getFromSelector(delcolbuttonid);
    this.fixbutton = getFromSelector(fixbuttonid);
    this.delbutton = getFromSelector(delbuttonid);
    this.toolboxel = getFromSelector(toolboxid);
    this.plainclass = plainclass;
    this.activeclass = activeclass;
}

SilvaTableToolBox.prototype = new TableToolBox;

SilvaTableToolBox.prototype.initialize = function(tool, editor) {
    /* attach the event handlers */
    this.tool = tool;
    this.editor = editor;
    addEventHandler(this.addtablebutton, "click", this.addTable, this);
    addEventHandler(this.addrowbutton, "click", this.tool.addTableRow, this.tool);
    addEventHandler(this.delrowbutton, "click", this.tool.delTableRow, this.tool);
    addEventHandler(this.addcolbutton, "click", this.tool.addTableColumn, this.tool);
    addEventHandler(this.delcolbutton, "click", this.tool.delTableColumn, this.tool);
    addEventHandler(this.fixbutton, "click", this.fixTable, this);
    addEventHandler(this.delbutton, "click", this.tool.delTable, this);
    addEventHandler(this.alignselect, "change", this.setColumnAlign, this);
    addEventHandler(this.classselect, "change", this.setTableClass, this);
    addEventHandler(this.widthinput, "change", this.setColumnWidths, this);
    this.addtablediv.style.display = "block";
    this.edittablediv.style.display = "none";
    this.editor.logMessage('Table tool initialized');
};

SilvaTableToolBox.prototype.updateState = function(selNode) {
    /* update the state (add/edit) and update the pulldowns (if required) */
    var table = this.editor.getNearestParentOfType(selNode, 'table');
    if (table) {
        this.addtablediv.style.display = "none";
        this.edittablediv.style.display = "block";
        var td = this.editor.getNearestParentOfType(selNode, 'td');
        if (!td) {
            td = this.editor.getNearestParentOfType(selNode, 'th');
            this.widthinput.value = '';
        } else {
            this.widthinput.value = this.tool.getColumnWidths(table);
        };
        if (td) {
            var align = td.className.split('-')[1];
            if (align == 'center' || align == 'left' || align == 'right') {
                selectSelectItem(this.alignselect, align);
            };
        };
        selectSelectItem(this.classselect, table.className);
        if (this.toolboxel) {
            if (this.toolboxel.open_handler) {
                this.toolboxel.open_handler();
            };
            this.toolboxel.className = this.activeclass;
        };
    } else {
        this.edittablediv.style.display = "none";
        this.addtablediv.style.display = "block";
        this.alignselect.selectedIndex = 0;
        this.classselect.selectedIndex = 0;
        if (this.toolboxel) {
            this.toolboxel.className = this.plainclass;
        };
    };
};

SilvaTableToolBox.prototype.addTable = function() {
    /* add a Silvs specific table, with an (optional) header with colspan */
    var rows = parseInt(this.newrowsinput.value);
    var cols = parseInt(this.newcolsinput.value);
    var makeHeader = this.makeheaderinput.checked;
    var classchooser = getFromSelector("kupu-table-classchooser-add");
    var tableclass = this.classselect.options[this.classselect.selectedIndex].value;
    this.tool.createTable(rows, cols, makeHeader, tableclass);
};

SilvaTableToolBox.prototype.setTableClass = function() {
    var cls = this.classselect.options[this.classselect.selectedIndex].value;
    this.tool.setTableClass(cls);
};

SilvaTableToolBox.prototype.setColumnWidths = function() {
    var widths = this.widthinput.value;
    this.tool.setColumnWidths(widths);
};

SilvaTableToolBox.prototype.setColumnAlign = function() {
    var align = this.alignselect.options[
                    this.alignselect.selectedIndex].value;
    this.tool.setColumnAlign(align);
};

SilvaTableToolBox.prototype.fixTable = function(event) {
    /* fix the table so it is Silva (and this tool) compliant */
    // since this can be quite a nasty creature we can't just use the
    // helper methods
    
    // first we create a new tbody element
    var currnode = this.editor.getSelectedNode();
    var table = this.editor.getNearestParentOfType(currnode, 'TABLE');
    if (!table) {
        this.editor.logMessage('Not inside a table!');
        return;
    };
    var doc = this.editor.getInnerDocument();
    var tbody = doc.createElement('tbody');

    var allowed_classes = new Array('plain', 'grid', 'list', 'listing', 'data');
    if (!allowed_classes.contains(table.getAttribute('class'))) {
        table.setAttribute('class', 'plain');
    };
    
    table.setAttribute('cellpadding', '0');
    table.setAttribute('cellspacing', '0');

    // now get all the rows of the table, the rows can either be
    // direct descendants of the table or inside a 'tbody', 'thead'
    // or 'tfoot' element
    var rows = new Array();
    var parents = new Array('thead', 'tbody', 'tfoot');
    for (var i=0; i < table.childNodes.length; i++) {
        var node = table.childNodes[i];
        if (node.nodeName.toLowerCase() == 'tr') {
            rows.push(node);
        } else if (parents.contains(node.nodeName.toLowerCase())) {
            for (var j=0; j < node.childNodes.length; j++) {
                var inode = node.childNodes[j];
                if (inode.nodeName.toLowerCase() == 'tr') {
                    rows.push(inode);
                };
            };
        };
    };
    
    // now find out how many cells our rows should have
    var numcols = 0;
    for (var i=0; i < rows.length; i++) {
        var row = rows[i];
        var currnumcols = 0;
        for (var j=0; j < row.childNodes.length; j++) {
            var node = row.childNodes[j];
            if (node.nodeName.toLowerCase() == 'td' ||
                    node.nodeName.toLowerCase() == 'th') {
                var colspan = 1;
                if (node.getAttribute('colSpan')) {
                    colspan = parseInt(node.getAttribute('colSpan'));
                };
                currnumcols += colspan;
            };
        };
        if (currnumcols > numcols) {
            numcols = currnumcols;
        };
    };

    // now walk through all rows to clean them up
    for (var i=0; i < rows.length; i++) {
        var row = rows[i];
        var newrow = doc.createElement('tr');
        var currcolnum = 0;
        var inhead = -1;
        while (row.childNodes.length > 0) {
            var node = row.childNodes[0];
            if (node.nodeName.toLowerCase() == 'td') {
                if (inhead == -1) {
                    inhead = 0;
                    node.setAttribute('colSpan', '1');
                };
            } else if (node.nodeName.toLowerCase() == 'th') {
                if (inhead == -1) {
                    inhead = 1;
                    newrow.appendChild(node);
                    node.setAttribute('colSpan', '1');
                    node.setAttribute('rowSpan', '1');
                    continue;
                } else if (inhead == 0) {
                    var td = doc.createElement('td');
                    while (node.childNodes.length) {
                        td.appendChild(node.childNodes[0]);
                    };
                    row.removeChild(node);
                    node = td;
                };
            } else {
                row.removeChild(node);
                continue;
            };
            node.setAttribute('rowspan', '1');
            if (inhead) {
                while (node.childNodes.length) {
                    newrow.childNodes[0].appendChild(node.childNodes[0]);
                };
                var colspan = node.getAttribute('colSpan');
                if (colspan) {
                    colspan = parseInt(colspan);
                } else {
                    colspan = 1;
                }
                var current_colspan = parseInt(newrow.childNodes[0].getAttribute('colSpan'));
                newrow.childNodes[0].setAttribute('colSpan', (current_colspan + colspan).toString());
                row.removeChild(node);
            } else {
                node.setAttribute('colSpan', 1);
                node.setAttribute('rowSpan', 1);
                newrow.appendChild(node);
            };
        };
        if (newrow.childNodes.length) {
            tbody.appendChild(newrow);
        };
    };

    // now make sure all rows have the correct length
    for (var i=0; i < tbody.childNodes.length; i++) {
        var row = tbody.childNodes[i];
        if (row.childNodes.length && row.childNodes[0].nodeName.toLowerCase() == 'th') {
            row.childNodes[0].setAttribute('colSpan', numcols);
        } else {
            while (row.childNodes.length < numcols) {
                var td = doc.createElement('td');
                var nbsp = doc.createTextNode('\u00a0');
                td.appendChild(nbsp);
                row.appendChild(td);
            };
        };
    };
    
    // now remove all the old stuff from the table and add the new tbody
    var tlength = table.childNodes.length;
    for (var i=0; i < tlength; i++) {
        table.removeChild(table.childNodes[0]);
    };
    table.appendChild(tbody);

    this.editor.getDocument().getWindow().focus();

    this.editor.logMessage('Table cleaned up');
};

SilvaTableToolBox.prototype._fixAllTables = function() {
    /* fix all the tables in the document at once */
    return;
    var tables = this.editor.getInnerDocument().getElementsByTagName('table');
    for (var i=0; i < tables.length; i++) {
        this.fixTable(tables[i]);
    };
};

function SilvaIndexTool(inputid, addbuttonid, updatebuttonid, deletebuttonid, toolboxid, plainclass, activeclass) {
    /* a tool to manage index items (named anchors) for Silva */
    this.input = getFromSelector(inputid);
    this.addbutton = getFromSelector(addbuttonid);
    this.updatebutton = getFromSelector(updatebuttonid);
    this.deletebutton = getFromSelector(deletebuttonid);
    this.toolboxel = getFromSelector(toolboxid);
    this.plainclass = plainclass;
    this.activeclass = activeclass;
};

SilvaIndexTool.prototype = new KupuTool;

SilvaIndexTool.prototype.initialize = function(editor) {
    /* attach the event handlers */
    this.editor = editor;
    addEventHandler(this.input, 'blur', this.updateIndex, this);
    addEventHandler(this.addbutton, 'click', this.addIndex, this);
    addEventHandler(this.updatebutton, 'click', this.updateIndex, this);
    addEventHandler(this.deletebutton, 'click', this.deleteIndex, this);
    if (this.editor.getBrowserName() == 'IE') {
        // need to catch some additional events for IE
        addEventHandler(editor.getInnerDocument(), 'keyup', this.handleKeyPressOnIndex, this);
        addEventHandler(editor.getInnerDocument(), 'keydown', this.handleKeyPressOnIndex, this);
    };
    addEventHandler(editor.getInnerDocument(), 'keypress', this.handleKeyPressOnIndex, this);
    this.updatebutton.style.display = 'none';
    this.deletebutton.style.display = 'none';
};

SilvaIndexTool.prototype.addIndex = function(event) {
    /* create an index */
    var name = this.input.value;
    var currnode = this.editor.getSelectedNode();
    var indexel = this.editor.getNearestParentOfType(currnode, 'A');
    
    if (indexel && indexel.getAttribute('href')) {
        this.editor.logMessage('Can not add index items in anchors');
        return;
    };
    
    if (!indexel) {
        var doc = this.editor.getDocument();
        if (!name) {
            var selection = this.editor.getSelection();
            var cloned = selection.cloneContents();
            var iterator = new NodeIterator(cloned);
            var name = '';
            var currnode = null;
            while (currnode = iterator.next()) {
                if (currnode.nodeValue) {
                    name += currnode.nodeValue;
                };
            };
            if (name) {
                this.input.value = name;
            };
        };
        var docel = doc.getDocument();
        indexel = docel.createElement('a');
        var text = docel.createTextNode('[' + name + ']');
        indexel.appendChild(text);
        indexel = this.editor.insertNodeAtSelection(indexel, true);
        indexel.className = 'index';
    };
    
    indexel.setAttribute('name', name);
    var sel = this.editor.getSelection();
    sel.collapse(true);
    this.editor.logMessage('Index added');
};

SilvaIndexTool.prototype.updateIndex = function(event) {
    /* update an existing index */
    var currnode = this.editor.getSelectedNode();
    var indexel = this.editor.getNearestParentOfType(currnode, 'A');
    if (!indexel) {
        return;
    };

    if (indexel && indexel.getAttribute('href')) {
        this.editor.logMessage('Can not add an index element inside a link!');
        return;
    };

    var name = this.input.value;
    indexel.setAttribute('name', name);
    while (indexel.hasChildNodes()) {
        indexel.removeChild(indexel.firstChild);
    };
    var text = this.editor.getInnerDocument().createTextNode('[' + name + ']')
    indexel.appendChild(text);
    this.editor.logMessage('Index modified');
};

SilvaIndexTool.prototype.deleteIndex = function() {
    var selNode = this.editor.getSelectedNode();
    var a = this.editor.getNearestParentOfType(selNode, 'a');
    if (!a || a.getAttribute('href')) {
        this.editor.logMessage('Not inside an index element!');
        return;
    };
    a.parentNode.removeChild(a);
    this.editor.logMessage('Index element removed');
};

SilvaIndexTool.prototype.handleKeyPressOnIndex = function(event) {
    var selNode = this.editor.getSelectedNode();
    var a = this.editor.getNearestParentOfType(selNode, 'a');
    if (!a || a.getAttribute('href')) {
        return;
    };
    var keyCode = event.keyCode;
    if (keyCode == 8 || keyCode == 46) {
        a.parentNode.removeChild(a);
    } else if (keyCode == 9 || keyCode == 39) {
        var next = a.nextSibling;
        while (next && next.nodeName.toLowerCase() == 'br') {
            next = next.nextSibling;
        };
        if (!next) {
            var doc = this.editor.getInnerDocument();
            next = doc.createTextNode('\xa0');
            a.parentNode.appendChild(next);
        };
        var selection = this.editor.getSelection();
        // XXX I fear I'm working around bugs here... because of a bug in 
        // selection.moveStart() I can't use the same codepath in IE as in Moz
        if (this.editor.getBrowserName() == 'IE') {
            selection.selectNodeContents(a);
            // XXX are we depending on a bug here? shouldn't we move the 
            // selection one place to get out of the anchor? it works,
            // but seems wrong...
            selection.collapse(true);
        } else {
            selection.selectNodeContents(next);
            selection.collapse();
            var selection = this.editor.getSelection();
        };
        this.editor.updateState();
    };
    if (event.preventDefault) {
        event.preventDefault();
    } else {
        event.returnValue = false;
    };
    return false;
};

SilvaIndexTool.prototype.updateState = function(selNode) {
    var indexel = this.editor.getNearestParentOfType(selNode, 'A');
    if (indexel && !indexel.getAttribute('href')) {
        if (this.toolboxel) {
            if (this.toolboxel.open_handler) {
                this.toolboxel.open_handler();
            };
            this.toolboxel.className = this.activeclass;
        };
        this.input.value = indexel.getAttribute('name');
        this.addbutton.style.display = 'none';
        this.updatebutton.style.display = 'inline';
        this.deletebutton.style.display = 'inline';
    } else {
        if (this.toolboxel) {
            this.toolboxel.className = this.plainclass;
        };
        this.input.value = '';
        this.updatebutton.style.display = 'none';
        this.deletebutton.style.display = 'none';
        this.addbutton.style.display = 'inline';
    };
};

SilvaIndexTool.prototype.createContextMenuElements = function(selNode, event) {
    var indexel = this.editor.getNearestParentOfType(selNode, 'A');
    if (indexel && !indexel.getAttribute('href')) {
        return new Array(new ContextMenuElement('Delete index', this.deleteIndex, this));
    } else {
        return new Array();
    };
};

function SilvaTocTool(depthselectid, addbuttonid, delbuttonid, toolboxid, plainclass, activeclass) {
    this.depthselect = getFromSelector(depthselectid);
    this.addbutton = getFromSelector(addbuttonid);
    this.delbutton = getFromSelector(delbuttonid);
    this.toolbox = getFromSelector(toolboxid);
    this.plainclass = plainclass;
    this.activeclass = activeclass;
    this._inside_toc = false;
};

SilvaTocTool.prototype = new KupuTool;

SilvaTocTool.prototype.initialize = function(editor) {
    this.editor = editor;
    addEventHandler(this.addbutton, 'click', this.addOrUpdateToc, this);
    addEventHandler(this.depthselect, 'change', this.updateToc, this);
    addEventHandler(this.delbutton, 'click', this.deleteToc, this);
    addEventHandler(editor.getInnerDocument(), 'keypress', this.handleKeyPressOnToc, this);
    if (this.editor.getBrowserName() == 'IE') {
        addEventHandler(editor.getInnerDocument(), 'keydown', this.handleKeyPressOnToc, this);
        addEventHandler(editor.getInnerDocument(), 'keyup', this.handleKeyPressOnToc, this);
    };
};

SilvaTocTool.prototype.handleKeyPressOnToc = function(event) {
    if (!this._inside_toc) {
        return;
    };
    var keyCode = event.keyCode;
    if (keyCode == 8 || keyCode == 46) {
        var selNode = this.editor.getSelectedNode();
        var toc = this.getNearestToc(selNode);
        toc.parentNode.removeChild(toc);
    };
    if (keyCode == 13 || keyCode == 9 || keyCode == 39) {
        var selNode = this.editor.getSelectedNode();
        var toc = this.getNearestToc(selNode);
        var doc = this.editor.getInnerDocument();
        var selection = this.editor.getSelection();
        if (toc.nextSibling) {
            var sibling = toc.nextSibling;
            selection.selectNodeContents(toc.nextSibling);
            selection.collapse();
        } else {
            var parent = toc.parentNode;
            var p = doc.createElement('p');
            parent.appendChild(p);
            var text = doc.createTextNode('\xa0');
            p.appendChild(text);
            selection.selectNodeContents(p);
        };
        this._inside_toc = false;
    };
    if (event.preventDefault) {
        event.preventDefault();
    } else {
        event.returnValue = false;
    };
};

SilvaTocTool.prototype.updateState = function(selNode, event) {
    var toc = this.getNearestToc(selNode);
    if (toc) {
        var depth = toc.getAttribute('toc_depth');
        selectSelectItem(this.depthselect, depth);
        this.addbutton.style.display = 'none';
        this.delbutton.style.display = 'inline';
        this._inside_toc = true;
        if (this.toolbox) {
            if (this.toolbox.open_handler) {
                this.toolbox.open_handler();
            };
            this.toolbox.className = this.activeclass;
        };
    } else {
        this.depthselect.selectedIndex = 0;
        this.delbutton.style.display = 'none';
        this.addbutton.style.display = 'inline';
        this._inside_toc = false;
        if (this.toolbox) {
            this.toolbox.className = this.plainclass;
        };
    };
};

SilvaTocTool.prototype.addOrUpdateToc = function(event, depth) {
    var selNode = this.editor.getSelectedNode();
    var depth = depth ? depth : this.depthselect.options[this.depthselect.selectedIndex].value;
    var toc = this.getNearestToc(selNode);
    var doc = this.editor.getInnerDocument();
    var toctext = this.getTocText(depth);
    if (toc) {
        // there's already a toc, just update the depth
        toc.setAttribute('toc_depth', depth);
        while (toc.hasChildNodes()) {
            toc.removeChild(toc.firstChild);
        };
        toc.appendChild(doc.createTextNode(toctext));
    } else {
        // create a new toc
        var div = doc.createElement('div');
        div.setAttribute('toc_depth', depth);
        div.setAttribute('is_toc', 1);
        div.className = 'toc';
        var text = doc.createTextNode(toctext);
        div.appendChild(text);
        this.editor.insertNodeAtSelection(div);
    };
};

SilvaTocTool.prototype.createDefaultToc = function() {
    // XXX nasty workaround, entering null as the event...
    this.addOrUpdateToc(null, '-1');
};

SilvaTocTool.prototype.updateToc = function() {
    var selNode = this.editor.getSelectedNode();
    var toc = this.getNearestToc(selNode);
    if (toc) {
        var depth = this.depthselect.options[this.depthselect.selectedIndex].value;
        var toctext = this.getTocText(depth);
        toc.setAttribute('toc_depth', depth);
        while (toc.hasChildNodes()) {
            toc.removeChild(toc.firstChild);
        };
        doc = this.editor.getInnerDocument();
        toc.appendChild(doc.createTextNode(toctext));
    };
};

SilvaTocTool.prototype.deleteToc = function() {
    var selNode = this.editor.getSelectedNode();
    var toc = this.getNearestToc(selNode);
    if (!toc) {
        this.editor.logMessage('Not inside a toc!', 1);
        return;
    };
    toc.parentNode.removeChild(toc);
};

SilvaTocTool.prototype.getNearestToc = function(selNode) {
    var currnode = selNode;
    while (currnode) {
        if (currnode.nodeName.toLowerCase() == 'div' &&
                currnode.getAttribute('is_toc')) {
            return currnode;
        };
        currnode = currnode.parentNode;
    };
    return false;
};

SilvaTocTool.prototype.createContextMenuElements = function(selNode, event) {
    /* create the 'Delete TOC' menu elements */
    var ret = new Array();
    if (this.getNearestToc(selNode)) {
        ret.push(new ContextMenuElement('Delete TOC', this.deleteToc, this));
    } else {
        ret.push(new ContextMenuElement('Create TOC', this.createDefaultToc, this));
    };
    return ret;
};

SilvaTocTool.prototype.getTocText = function(depth) {
    var toctext = 'Table of Contents ';
    switch (depth) {
        case '-1':
            toctext += '(unlimited levels)';
            break;
        case '1':
            toctext += '(1 level)';
            break;
        default:
            toctext += '(' + depth + ' levels)';
            break;
    };
    return toctext;
};

function SilvaAbbrTool(abbrradioid, acronymradioid, radiocontainerid, titleinputid,
                            addbuttonid, updatebuttonid, delbuttonid,
                            toolboxid, plainclass, activeclass) {
    /* tool to manage citation elements */
    this.abbrradio = getFromSelector(abbrradioid);
    this.acronymradio = getFromSelector(acronymradioid);
    this.radiocontainer = getFromSelector(radiocontainerid);
    this.titleinput = getFromSelector(titleinputid);
    this.addbutton = getFromSelector(addbuttonid);
    this.updatebutton = getFromSelector(updatebuttonid);
    this.delbutton = getFromSelector(delbuttonid);
    this.toolbox = getFromSelector(toolboxid);
    this.plainclass = plainclass;
    this.activeclass = activeclass;
};

SilvaAbbrTool.prototype = new KupuTool;

SilvaAbbrTool.prototype.initialize = function(editor) {
    this.editor = editor;
    addEventHandler(this.addbutton, 'click', this.addElement, this);
    addEventHandler(this.updatebutton, 'click', this.updateElement, this);
    addEventHandler(this.delbutton, 'click', this.deleteElement, this);
    
    this.updatebutton.style.display = 'none';
    this.delbutton.style.display = 'none';
};

SilvaAbbrTool.prototype.updateState = function(selNode, event) {
    var element = this.getNearestAbbrAcronym(selNode);
    if (element) {
        this.addbutton.style.display = 'none';
        this.updatebutton.style.display = 'inline';
        this.delbutton.style.display = 'inline';
        this.titleinput.value = element.getAttribute('title');
        this.radiocontainer.style.display = 'none';
        if (this.toolbox) {
            if (this.toolbox.open_handler) {
                this.toolbox.open_handler();
            };
            this.toolbox.className = this.activeclass;
        };
    } else {
        this.addbutton.style.display = 'inline';
        this.updatebutton.style.display = 'none';
        this.delbutton.style.display = 'none';
        this.titleinput.value = '';
        if (this.editor.getBrowserName() == 'IE' || this.radiocontainer.nodeName.toLowerCase() != 'tr') {
            this.radiocontainer.style.display = 'block';
        } else {
            this.radiocontainer.style.display = 'table-row';
        };
        if (this.toolbox) {
            this.toolbox.className = this.plainclass;
        };
    };
};

SilvaAbbrTool.prototype.getNearestAbbrAcronym = function(selNode) {
    var current = selNode;
    while (current && current.nodeType != 9) {
        if (current.nodeType == 1) {
            var nodeName = current.nodeName.toLowerCase();
            if (nodeName == 'abbr' || nodeName == 'acronym') {
                return current;
            };
        };
        current = current.parentNode;
    };
};

SilvaAbbrTool.prototype.addElement = function() {
    var type = this.abbrradio.checked ? 'abbr' : 'acronym';
    var doc = this.editor.getInnerDocument();
    var selNode = this.editor.getSelectedNode();
    if (this.getNearestAbbrAcronym(selNode)) {
        this.editor.logMessage('Can not nest abbr and acronym elements');
        return;
    };
    var element = doc.createElement(type);
    element.setAttribute('title', this.titleinput.value);

    var selection = this.editor.getSelection();
    var docfrag = selection.cloneContents();
    var placecursoratend = false;
    if (docfrag.hasChildNodes()) {
        for (var i=0; i < docfrag.childNodes.length; i++) {
            element.appendChild(docfrag.childNodes[i]);
        };
        placecursoratend = true;
    } else {
        var text = doc.createTextNode('\xa0');
        element.appendChild(text);
    };
    this.editor.insertNodeAtSelection(element, 1);
    var selection = this.editor.getSelection();
    selection.collapse(placecursoratend);
    this.editor.getDocument().getWindow().focus();
    var selNode = selection.getSelectedNode();
    this.editor.updateState(selNode);
    this.editor.logMessage('Element ' + type + ' added');
};

SilvaAbbrTool.prototype.updateElement = function() {
    var selNode = this.editor.getSelectedNode();
    var element = this.getNearestAbbrAcronym(selNode);
    if (!element) {
        this.editor.logMessage('Not inside an abbr or acronym element!', 1);
        return;
    };
    var title = this.titleinput.value;
    element.setAttribute('title', title);
    this.editor.logMessage('Updated ' + element.nodeName.toLowerCase() + ' element');
};

SilvaAbbrTool.prototype.deleteElement = function() {
    var selNode = this.editor.getSelectedNode();
    var element = this.getNearestAbbrAcronym(selNode);
    if (!element) {
        this.editor.logMessage('Not inside an abbr or acronym element!', 1);
        return;
    };
    element.parentNode.removeChild(element);
    this.editor.logMessage('Deleted ' + element.nodeName.toLowerCase() + ' deleted');
};

function SilvaCitationTool(authorinputid, sourceinputid, addbuttonid, updatebuttonid, delbuttonid, 
                            toolboxid, plainclass, activeclass) {
    /* tool to manage citation elements */
    this.authorinput = getFromSelector(authorinputid);
    this.sourceinput = getFromSelector(sourceinputid);
    this.addbutton = getFromSelector(addbuttonid);
    this.updatebutton = getFromSelector(updatebuttonid);
    this.delbutton = getFromSelector(delbuttonid);
    this.toolbox = getFromSelector(toolboxid);
    this.plainclass = plainclass;
    this.activeclass = activeclass;
    this._inside_citation = false;
};

SilvaCitationTool.prototype = new KupuTool;

SilvaCitationTool.prototype.initialize = function(editor) {
    this.editor = editor;
    addEventHandler(this.addbutton, 'click', this.addCitation, this);
    addEventHandler(this.updatebutton, 'click', this.updateCitation, this);
    addEventHandler(this.delbutton, 'click', this.deleteCitation, this);
    if (editor.getBrowserName() == 'IE') {
        addEventHandler(editor.getInnerDocument(), 'keyup', this.cancelEnterPress, this);
        addEventHandler(editor.getInnerDocument(), 'keydown', this.handleKeyPressOnCitation, this);
    } else {
        addEventHandler(editor.getInnerDocument(), 'keypress', this.handleKeyPressOnCitation, this);
    };
    
    this.updatebutton.style.display = 'none';
    this.delbutton.style.display = 'none';
};

SilvaCitationTool.prototype.cancelEnterPress = function(event) {
    if (!this._inside_citation || (event.keyCode != 13 && event.keyCode != 9)) {
        return;
    };
    if (event.preventDefault) {
        event.preventDefault();
    } else {
        event.returnValue = false;
    };
};

SilvaCitationTool.prototype.handleKeyPressOnCitation = function(event) {
    if (!this._inside_citation) {
        return;
    };
    var keyCode = event.keyCode;
    var citation = this.getNearestCitation(this.editor.getSelectedNode());
    var doc = this.editor.getInnerDocument();
    var selection = this.editor.getSelection();
    if (keyCode == 13 && this.editor.getBrowserName() == 'IE') {
        var br = doc.createElement('br');
        var currnode = selection.getSelectedNode();
        selection.replaceWithNode(br);
        selection.selectNodeContents(br);
        selection.collapse(true);
        event.returnValue = false;
    } else if (keyCode == 9) {
        var next = citation.nextSibling;
        if (!next) {
            next = doc.createElement('p');
            next.appendChild(doc.createTextNode('\xa0'));
            citation.parentNode.appendChild(next);
        };
        selection.selectNodeContents(next);
        selection.collapse();
        if (event.preventDefault) {
            event.preventDefault();
        };
        event.returnValue = false;
        this._inside_citation = false;
    };
};

SilvaCitationTool.prototype.updateState = function(selNode, event) {
    var citation = this.getNearestCitation(selNode);
    if (citation) {
        this.addbutton.style.display = 'none';
        this.updatebutton.style.display = 'inline';
        this.delbutton.style.display = 'inline';
        this.authorinput.value = citation.getAttribute('author');
        this.sourceinput.value = citation.getAttribute('source');
        this._inside_citation = true;
        if (this.toolbox) {
            if (this.toolbox.open_handler) {
                this.toolbox.open_handler();
            };
            this.toolbox.className = this.activeclass;
        };
    } else {
        this.addbutton.style.display = 'inline';
        this.updatebutton.style.display = 'none';
        this.delbutton.style.display = 'none';
        this.authorinput.value = '';
        this.sourceinput.value = '';
        this._inside_citation = false;
        if (this.toolbox) {
            this.toolbox.className = this.plainclass;
        };
    };
};

SilvaCitationTool.prototype.addCitation = function() {
    var selNode = this.editor.getSelectedNode();
    var citation = this.getNearestCitation(selNode);
    if (citation) {
        this.editor.logMessage('Nested citations are not allowed!');
        return;
    };
    var author = this.authorinput.value;
    var source = this.sourceinput.value;
    var doc = this.editor.getInnerDocument();
    var div = doc.createElement('div');
    div.className = 'citation';
    div.setAttribute('author', author);
    div.setAttribute('source', source);
    div.setAttribute('is_citation', '1');
    var selection = this.editor.getSelection();
    var docfrag = selection.cloneContents();
    var placecursoratend = false;
    if (docfrag.hasChildNodes()) {
        for (var i=0; i < docfrag.childNodes.length; i++) {
            div.appendChild(docfrag.childNodes[i]);
        };
        placecursoratend = true;
    } else {
        var text = doc.createTextNode('\xa0');
        div.appendChild(text);
    };
    this.editor.insertNodeAtSelection(div, 1);
    var selection = this.editor.getSelection();
    selection.collapse(placecursoratend);
    this.editor.getDocument().getWindow().focus();
    var selNode = selection.getSelectedNode();
    this.editor.updateState(selNode);
};

SilvaCitationTool.prototype.updateCitation = function() {
    var selNode = this.editor.getSelectedNode();
    var citation = this.getNearestCitation(selNode);
    if (!citation) {
        this.editor.logMessage('Not inside a citation element!');
        return;
    };
    citation.setAttribute('author', this.authorinput.value);
    citation.setAttribute('source', this.sourceinput.value);
};

SilvaCitationTool.prototype.deleteCitation = function() {
    var selNode = this.editor.getSelectedNode();
    var citation = this.getNearestCitation(selNode);
    if (!citation) {
        this.editor.logMessage('Not inside citation element!');
        return;
    };
    citation.parentNode.removeChild(citation);
};

SilvaCitationTool.prototype.getNearestCitation = function(selNode) {
    var currnode = selNode;
    while (currnode) {
        if (currnode.nodeName.toLowerCase() == 'div' &&
                currnode.getAttribute('is_citation')) {
            return currnode;
        };
        currnode = currnode.parentNode;
    };
    return false;
};

SilvaCitationTool.prototype.createContextMenuElements = function(selNode, event) {
    /* create the 'Delete citation' menu elements */
    var ret = new Array();
    if (this.getNearestCitation(selNode)) {
        ret.push(new ContextMenuElement('Delete cite', this.deleteCitation, this));
    };
    return ret;
};

function SilvaExternalSourceTool(idselectid, formcontainerid, addbuttonid, cancelbuttonid,
                                    updatebuttonid, delbuttonid, toolboxid, plainclass, activeclass) {
    this.idselect = getFromSelector(idselectid);
    this.formcontainer = getFromSelector(formcontainerid);
    this.addbutton = getFromSelector(addbuttonid);
    this.cancelbutton = getFromSelector(cancelbuttonid);
    this.updatebutton = getFromSelector(updatebuttonid);
    this.delbutton = getFromSelector(delbuttonid);
    this.toolbox = getFromSelector(toolboxid);
    this.plainclass = plainclass;
    this.activeclass = activeclass;

    this._editing = false;
    this._url = null;
    this._id = null;
    this._form = null;
    this._insideExternalSource = false;

    // store the base url, this will be prepended to the id to form the url to
    // get the codesource from (Zope's acquisition will make sure it ends up on
    // the right object)
    var urlparts = document.location.pathname.toString().split('/')
    var urlparts_to_use = [];
    for (var i=0; i < urlparts.length; i++) {
        var part = urlparts[i];
        if (part == 'edit') {
            break;
        };
        urlparts_to_use.push(part);
    };
    this._baseurl = urlparts_to_use.join('/');
};

SilvaExternalSourceTool.prototype = new KupuTool;

SilvaExternalSourceTool.prototype.initialize = function(editor) {
    this.editor = editor;
    addEventHandler(this.addbutton, 'click', this.startExternalSourceAddEdit, this);
    addEventHandler(this.cancelbutton, 'click', this.resetTool, this);
    addEventHandler(this.updatebutton, 'click', this.startExternalSourceAddEdit, this);
    addEventHandler(this.delbutton, 'click', this.delExternalSource, this);
    addEventHandler(editor.getInnerDocument(), 'keypress', this.handleKeyPressOnExternalSource, this);
    if (this.editor.getBrowserName() == 'IE') {
        addEventHandler(editor.getInnerDocument(), 'keydown', this.handleKeyPressOnExternalSource, this);
        addEventHandler(editor.getInnerDocument(), 'keyup', this.handleKeyPressOnExternalSource, this);
    };
    
    // search for a special serialized identifier of the current document
    // which is used to send to the ExternalSource element when sending
    // requests so the ExternalSources know their context
    this.docref = null;
    var metas = this.editor.getInnerDocument().getElementsByTagName('meta');
    for (var i=0; i < metas.length; i++) {
        var meta = metas[i];
        if (meta.getAttribute('name') == 'docref') {
            this.docref = meta.getAttribute('content');
        };
    };
     
    this.updatebutton.style.display = 'none';
    this.delbutton.style.display = 'none';
    this.cancelbutton.style.display = 'none';
};

SilvaExternalSourceTool.prototype.updateState = function(selNode) {
    var extsource = this.getNearestExternalSource(selNode);
    var heading = this.toolbox.getElementsByTagName('h1')[0];
    if (extsource) {
        this._insideExternalSource = true;
        selectSelectItem(this.idselect, extsource.getAttribute('source_id'));
        this.addbutton.style.display = 'none';
        this.cancelbutton.style.display = 'none';
        this.updatebutton.style.display = 'inline';
        this.delbutton.style.display = 'inline';
        this.startExternalSourceUpdate(extsource);
        if (this.toolbox) {
            this.toolbox.className = this.activeclass;
        };
        heading.replaceChild(
            document.createTextNode('external source \xab' + 
                extsource.getAttribute('source_id') + '\xbb'),
            heading.firstChild
        );
    } else {
        this._insideExternalSource = false;
        this.resetTool();
        if (this.toolbox) {
            this.toolbox.className = this.plainclass;
        };
        heading.replaceChild(
            document.createTextNode('external source'),
            heading.firstChild
        );
    };
};

SilvaExternalSourceTool.prototype.handleKeyPressOnExternalSource = function(event) {
    if (!this._insideExternalSource) {
        return;
    };
    var keyCode = event.keyCode;
    var selNode = this.editor.getSelectedNode();
    var div = this.getNearestExternalSource(selNode);
    var doc = this.editor.getInnerDocument();
    if (keyCode == 13 || keyCode == 9 || keyCode == 39) {
        if (div.nextSibling) {
            var selection = this.editor.getSelection();
            selection.selectNodeContents(div.nextSibling);
            selection.collapse();
        } else {
            var p = doc.createElement('p');
            var nbsp = doc.createTextNode('\xa0');
            p.appendChild(nbsp);
            div.parentNode.appendChild(p);
            var selection = this.editor.getSelection();
            selection.selectNodeContents(p);
            selection.collapse();
        };
        this._insideExternalSource = false;
    } else if (keyCode == 8) {
        var selectnode = div.nextSibling;
        if (!selectnode) {
            selectnode = doc.createElement('p');
            selectnode.appendChild(doc.createTextNode('\xa0'));
            doc.appendChild(selectnode);
        };
        var selection = this.editor.getSelection();
        selection.selectNodeContents(selectnode);
        div.parentNode.removeChild(div);
        selection.collapse();
    };
    if (event.preventDefault) {
        event.preventDefault();
    } else {
        event.returnValue = false;
    };
};

SilvaExternalSourceTool.prototype.getUrlAndContinue = function(id, handler) {
    if (id == this._id) {
        // return cached
        handler.call(this, this._url);
        return;
    };
    var request = new XMLHttpRequest();
    var url = this._baseurl + '/edit/get_extsource_url?id=' + id;
    request.open('GET', url, true);
    var callback = new ContextFixer(function() {
                if (request.readyState == 4) {
                    if (request.status.toString() == '200') {
                        var returl = request.responseText;
                        this._id = id;
                        this._url = returl;
                        handler.call(this, returl);
                    } else {
                        alert('problem: url ' + url + 
                                ' could not be loaded (status ' +
                                request.status + ')');
                    };
                };
            }, this);
    request.onreadystatechange = callback.execute;
    request.send('');
};

SilvaExternalSourceTool.prototype.startExternalSourceAddEdit = function() {
    // get the appropriate form and display it
    if (!this._editing) {
        var id = this.idselect.options[this.idselect.selectedIndex].value;
        this.getUrlAndContinue(id, this._continueStartExternalSourceEdit);
    } else {
        // validate the data and take further actions
        var formdata = this._gatherFormData();
        var doc = window.document;
        var request = new XMLHttpRequest();
        request.open('POST', this._url + '/validate_form_to_request', true);
        var callback = new ContextFixer(this._addExternalSourceIfValidated, request, this);
        request.onreadystatechange = callback.execute;
        request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        request.send(formdata);
    };
};

SilvaExternalSourceTool.prototype._continueStartExternalSourceEdit = function(url) {
    url = url + '/get_rendered_form_for_editor?docref=' + this.docref;
    var request = new XMLHttpRequest();
    request.open('GET', url, true);
    var callback = new ContextFixer(this._addFormToTool, request, this);
    request.onreadystatechange = callback.execute;
    request.send(null);
    while (this.formcontainer.hasChildNodes()) {
        this.formcontainer.removeChild(this.formcontainer.firstChild);
    };
    var text = document.createTextNode('Loading...');
    this.formcontainer.appendChild(text);
    this.updatebutton.style.display = 'none';
    this.cancelbutton.style.display = 'inline';
    this.addbutton.style.display = 'inline';
    this._editing = true;
};

SilvaExternalSourceTool.prototype.startExternalSourceUpdate = function(extsource) {
    var id = extsource.getAttribute('source_id');
    this.getUrlAndContinue(id, this._continueStartExternalSourceUpdate);
};

SilvaExternalSourceTool.prototype._continueStartExternalSourceUpdate = function(url) {
    url = url + '/get_rendered_form_for_editor';
    var formdata = this._gatherFormDataFromElement();
    formdata += '&docref=' + this.docref;
    var request = new XMLHttpRequest();
    request.open('POST', url, true);
    request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    var callback = new ContextFixer(this._addFormToTool, request, this);
    request.onreadystatechange = callback.execute;
    request.send(formdata);
    this._editing = true;
    while (this.formcontainer.hasChildNodes()) {
        this.formcontainer.removeChild(this.formcontainer.firstChild);
    };
    var text = document.createTextNode('Loading...');
    this.formcontainer.appendChild(text);
};

SilvaExternalSourceTool.prototype._addFormToTool = function(object) {
    if (this.readyState == 4) {
        if (this.status != '200') {
            // element not found, return without doing anythink
            object.resetTool();
            return;
        };
        while (object.formcontainer.hasChildNodes()) {
            object.formcontainer.removeChild(object.formcontainer.firstChild);
        };
        // XXX Somehow appending the XML to the form using DOM doesn't 
        // work correctly, it looks like the elements aren't HTMLElements 
        // but XML elements, don't know how to fix now so I'll use string 
        // insertion for now, needless to say it should be changed to DOM
        // manipulation asap...
        // XXX why is this.responseXML.documentElement.xml sometimes 'undefined'?
        object.formcontainer.innerHTML = this.responseText;
        object.idselect.style.display = 'none';
        // the formcontainer will contain a table with a form
        var form = null;
        var iterator = new NodeIterator(object.formcontainer);
        while (form == null) {
            var next = iterator.next();
            if (next.nodeName.toLowerCase() == 'form') {
                form = next;
            };
        };
        object._form = form;
    };
};

SilvaExternalSourceTool.prototype._addExternalSourceIfValidated = function(object) {
    if (this.readyState == 4) {
        if (this.status == '200') {
            // success, add the external source element to the document
            var selNode = object.editor.getSelectedNode();
            var currsource = object.getNearestExternalSource(selNode);
            var doc = object.editor.getInnerDocument();
            
            var extsource = doc.createElement('div');
            extsource.setAttribute('source_id', object._id);
            var header = doc.createElement('h4');
            extsource.appendChild(header);
            extsource.className = 'externalsource';
            var metatype = 'Silva Code Source'; // a default just in case
            for (var i=0; i < this.responseXML.documentElement.childNodes.length; i++) {
                var child = this.responseXML.documentElement.childNodes[i];
                if (child.nodeName.toLowerCase() == 'parameter') {
                    var key = child.getAttribute('key');
                    var value = '';
                    for (var j=0; j < child.childNodes.length; j++) {
                        value += child.childNodes[j].nodeValue;
                    };
                    if (key == 'metatype') {
                        metatype = value;
                        continue;
                    };
                    extsource.setAttribute(key, value);
                    key = key.replace(/_/g, ' ');
                    var textel = doc.createTextNode(key + ': ' + value.toString());
                    extsource.appendChild(textel);
                    extsource.appendChild(doc.createElement('br'));
                };
            };
            var htext = doc.createTextNode(metatype + ' \xab' + object._id + '\xbb');
            header.insertBefore(htext, header.firstChild);
            extsource.appendChild(doc.createElement('br'));
            if (!currsource) {
                object.editor.insertNodeAtSelection(extsource);
            } else {
                currsource.parentNode.replaceChild(extsource, currsource);
                var selection = object.editor.getSelection();
                selection.selectNodeContents(extsource);
                selection.collapse(true);
            };
            object.resetTool();
            object.editor.updateState();
        } else if (this.status == '400') {
            // failure, provide some feedback and return to the form
            alert('Form could not be validated, error message: ' + this.responseText);
        } else {
            alert('POST failed with unhandled status ' + this.status);
            throw('Error handling POST, server returned ' + this.status + ' HTTP status code');
        };
    };
};

SilvaExternalSourceTool.prototype.delExternalSource = function() {
    var selNode = this.editor.getSelectedNode();
    var source = this.getNearestExternalSource(selNode);
    if (!source) {
        this.editor.logMessage('Not inside external source!', 1);
        return;
    };
    var nextsibling = source.nextSibling;
    source.parentNode.removeChild(source);
    if (nextsibling) {
        var selection = this.editor.getSelection();
        selection.selectNodeContents(nextsibling);
        selection.collapse();
    };
};

SilvaExternalSourceTool.prototype.resetTool = function() {
    while (this.formcontainer.hasChildNodes()) {
        this.formcontainer.removeChild(this.formcontainer.firstChild);
    };
    this.idselect.style.display = 'inline';
    this.addbutton.style.display = 'inline';
    this.cancelbutton.style.display = 'none';
    this.updatebutton.style.display = 'none';
    this.delbutton.style.display = 'none';
    //this.editor.updateState();
    this._editing = false;
};

SilvaExternalSourceTool.prototype._gatherFormData = function() {
    /* walks through the form and creates a POST body */
    // XXX we may want to turn this into a helper function, since it's 
    // quite useful outside of this object I reckon
    var form = this._form;
    if (!form) {
        this.editor.logMessage('Not currently editing');
        return;
    };
    // first place all data into a dict, convert to a string later on
    var data = {};
    for (var i=0; i < form.elements.length; i++) {
        var child = form.elements[i];
        var elname = child.nodeName.toLowerCase();
        if (elname == 'input') {
            var name = child.getAttribute('name');
            var type = child.getAttribute('type');
            if (!type || type == 'text' || type == 'hidden' || type == 'password') {
                data[name] = child.value;
            } else if (type == 'checkbox' || type == 'radio') {
                if (child.checked) {
                    if (data[name]) {
                        if (typeof data[name] == typeof('')) {
                            var value = new Array(data[name]);
                            value.push(child.value);
                            data[name] = value;
                        } else {
                            data[name].push(child.value);
                        };
                    } else {
                        data[name] = child.value;
                    };
                };
            };
        } else if (elname == 'textarea') {
            data[child.getAttribute('name')] = child.value;
        } else if (elname == 'select') {
            var name = child.getAttribute('name');
            var multiple = child.getAttribute('multiple');
            if (!multiple) {
                data[name] = child.options[child.selectedIndex].value;
            } else {
                var value = new Array();
                for (var i=0; i < child.options.length; i++) {
                    if (child.options[i].checked) {
                        value.push(options[i].value);
                    };
                    if (value.length > 1) {
                        data[name] = value;
                    } else if (value.length) {
                        data[name] = value[0];
                    };
                };
            };
        };
    };
    
    // now we should turn it into a query string
    var ret = new Array();
    for (var key in data) {
        var value = data[key];
        // XXX does IE5 support encodeURIComponent?
        ret.push(encodeURIComponent(key) + '=' + encodeURIComponent(value));
    };
    
    return ret.join("&");
};

SilvaExternalSourceTool.prototype._gatherFormDataFromElement = function() {
    var selNode = this.editor.getSelectedNode();
    var source = this.getNearestExternalSource(selNode);
    if (!source) {
        return '';
    };
    var ret = new Array();
    for (var i=0; i < source.attributes.length; i++) {
        var attr = source.attributes[i];
        var name = attr.nodeName;
        var value = attr.nodeValue;
        if (name != 'class' && name != 'source_id' && name != 'id') {
            ret.push(encodeURIComponent(name) + '=' + encodeURIComponent(value));
        };
    };
    return ret.join('&');
};

SilvaExternalSourceTool.prototype.getNearestExternalSource = function(selNode) {

    var currnode = selNode;
    while (currnode) {
        if (currnode.nodeName.toLowerCase() == 'div' && currnode.className == 'externalsource') {
            return currnode;
        };
        currnode = currnode.parentNode;
    };
};

function SilvaKupuUI(textstyleselectid) {
    this.tsselect = getFromSelector(textstyleselectid);
};

SilvaKupuUI.prototype = new KupuUI;

SilvaKupuUI.prototype.initialize = function(editor) {
    this.editor = editor;
    this._fixTabIndex(this.tsselect);
    this._selectevent = addEventHandler(this.tsselect, 'change', this.setTextStyleHandler, this);
};

SilvaKupuUI.prototype.updateState = function(selNode) {
    /* set the text-style pulldown */

    // first get the nearest style
    var styles = {}; // use an object here so we can use the 'in' operator later on
    for (var i=0; i < this.tsselect.options.length; i++) {
        // XXX we should cache this
        styles[this.tsselect.options[i].value] = i;
    }
    
    // search the list of nodes like in the original one, break if we encounter a match,
    // this method does some more than the original one since it can handle commands in
    // the form of '<style>|<classname>' next to the plain '<style>' commands
    var currnode = selNode;
    var index = -1;
    while (index==-1 && currnode) {
        var nodename = currnode.nodeName.toLowerCase();
        for (var style in styles) {
            if (style.indexOf('|') < 0) {
                // simple command
                if (nodename == style.toLowerCase() && !currnode.className) {
                    index = styles[style];
                    break;
                };
            } else {
                // command + classname
                var tuple = style.split('|');
                if (nodename == tuple[0].toLowerCase() && currnode.className == tuple[1]) {
                    index = styles[style];
                    break;
                };
            };
        };
        currnode = currnode.parentNode;
    }
    this.tsselect.selectedIndex = Math.max(index,0);
};

SilvaKupuUI.prototype.setTextStyle = function(style) {
    /* parse the argument into a type and classname part
    
        generate a block element accordingly 
    */
    // XXX somehow this method always gets called twice... I would
    // really like to know why, but can't find it right now and don't
    // have time for a full investigation, so fiddle-fixed it this
    // way. Needless to say this needs some investigation at some point...
    if (this._cancel_update) {
        this._cancel_update = false;
        return;
    };
    
    var classname = "";
    var eltype = style;
    if (style.indexOf('|') > -1) {
        style = style.split('|');
        eltype = style[0];
        classname = style[1];
    };

    var command = eltype;
    // first create the element, then find it and set the classname
    if (this.editor.getBrowserName() == 'IE') {
        command = '<' + eltype + '>';
    };
    this.editor.getDocument().execCommand('formatblock', command);

    // now get a reference to the element just added
    var selNode = this.editor.getSelectedNode();
    var el = this.editor.getNearestParentOfType(selNode, eltype);

    // now set the classname
    if (classname) {
        el.className = classname;
        el.setAttribute('silva_type', classname);
    };
    this._cancel_update = true;
    this.editor.updateState();
    this.editor.getDocument().getWindow().focus();
};

function SilvaPropertyTool(tablerowid, formid) {
    /* a simple tool to edit metadata fields

        the fields' contents are stored in Silva's metadata sets
    */
    this.tablerow = document.getElementById(tablerowid);
    this.form = document.getElementById(formid);
    this.table = this.tablerow.parentNode;
    while (!this.table.nodeName.toLowerCase() == 'table') {
        this.table = this.table.parentNode;
    };
    // remove current content from the fields
    var tds = this.tablerow.getElementsByTagName('td');
    for (var i=0; i < tds.length; i++) {
        while (tds[i].hasChildNodes()) {
            tds[i].removeChild(tds[i].childNodes[0]);
        };
    };
};

SilvaPropertyTool.prototype = new KupuTool;

SilvaPropertyTool.prototype.initialize = function(editor) {
    this.editor = editor;
    
    // walk through all metadata fields and expose them to the user
    var metas = this.editor.getInnerDocument().getElementsByTagName('meta');
    for (var i=0; i < metas.length; i++) {
        var meta = metas[i];
        var name = meta.getAttribute('name');
        if (!name) {
            // http-equiv type
            continue;
        };
        var rowcopy = this.tablerow.cloneNode(true);
        this.tablerow.parentNode.appendChild(rowcopy);
        // create the form elements, pass in the rowcopy so the row can be
        // rendered real-time, this because IE doesn't select checkboxes that
        // arent' visible(!!)
        this.parseFormElIntoRow(meta, rowcopy);
        /*
        if (tag) {
            this.tablerow.parentNode.appendChild(tag);
        };
        */
    };
    // throw away the original row: we don't need it anymore...
    this.tablerow.parentNode.removeChild(this.tablerow);
};

SilvaPropertyTool.prototype.parseFormElIntoRow = function(metatag, tablerow) {
    /* render a field in the properties tool according to a metadata tag

        returns some false value if the meta tag should not be editable
    */
    var scheme = metatag.getAttribute('scheme');
    if (!scheme || !(scheme in EDITABLE_METADATA)) {
        return;
    };
    var name = metatag.getAttribute('name');
    var namespace = metatag.getAttribute('scheme');
    var nametypes = EDITABLE_METADATA[scheme];
    var type = 'text';
    var mandatory = false;
    var namefound = false;
    var fieldtitle = '';
    for (var i=0; i < nametypes.length; i++) {
        var nametype = nametypes[i];
        var elname = nametype[0];
        var type = nametype[1];
        var mandatory = nametype[2];
        var fieldtitle = nametype[3];
        if (elname == name) {
            namefound = true;
            break;
        };
    };
    if (!namefound) {
        return;
    };
    
    var titlefield = document.createElement('span');
    var title = document.createTextNode(fieldtitle);
    titlefield.appendChild(title);
    tablerow.getElementsByTagName('td')[0].appendChild(titlefield);
    titlefield.className = 'metadata-field';
    
    var value = metatag.getAttribute('content');
    var parentvalue = metatag.getAttribute('parentcontent');
    var td = tablerow.getElementsByTagName('td')[1]
    if (type == 'text' || type == 'textarea' || type == 'datetime') {
        this._createSimpleItemHTML(type, value, name, 
                                    namespace, mandatory, td);
    } else if (type == 'checkbox') {
        var titlecell = tablerow.getElementsByTagName('td')[0];
        this._createCheckboxItemHTML(titlecell, value, name, namespace, 
                                        mandatory, td);
    };
    if (parentvalue && parentvalue != '') {
        td.appendChild(document.createElement('br'));
        td.appendChild(document.createTextNode('acquired value:'));
        td.appendChild(document.createElement('br'));
        td.appendChild(document.createTextNode(parentvalue));
    };

    return tablerow;
};

// just to make the above method a bit more readable
SilvaPropertyTool.prototype._createSimpleItemHTML = function(type, value, 
                                                            name, namespace,
                                                            mandatory, td) {
    var input = null;
    if (type == 'text' || type == 'datetime') {
        input = document.createElement('input');
        input.setAttribute('type', 'text');
        input.value = value;
        if (type == 'datetime') {
            input.setAttribute('widget:type', 'datetime');
        };
    } else if (type == 'textarea') {
        input = document.createElement('textarea');
        var content = document.createTextNode(value);
        input.appendChild(content);
    };
    input.setAttribute('name', name);
    input.setAttribute('namespace', namespace);
    input.className = 'metadata-input';
    if (mandatory) {
        input.setAttribute('mandatory', 'true');
    };
    td.appendChild(input);
};

SilvaPropertyTool.prototype._createCheckboxItemHTML = function(titlecell, 
                                                    value, name, namespace, 
                                                    mandatory, td) {
    // elements are seperated by ||
    var infos = value.split('||');

    // messy stuff coming up, that make the checkboxes appear in some
    // 'foldable' div
    var outerdiv = document.createElement('div');
    outerdiv.className = 'kupu-properties-checkbox-outerdiv';

    // the arrow and 'items' label
    var itemsdiv = document.createElement('div');
    outerdiv.appendChild(itemsdiv);
    var img = document.createElement('img');
    // XXX would be nice if this would be absolute...
    img.src = 'kupu_silva/closed_arrow.gif'; 
    outerdiv.image = img; // XXX memory leak!!
    itemsdiv.appendChild(img);
    itemsdiv.appendChild(document.createTextNode(_('items')));
    itemsdiv.setAttribute('title', _('click to unfold'));

    // handler for showing/hiding the checkbox divs
    var handler = function(evt) {
        if (this.lastChild.style.display == 'none') {
            this.image.src = 'kupu_silva/opened_arrow.gif';
            this.image.setAttribute('title', _('click to fold'));
            this.lastChild.style.display = 'block';
        } else {
            this.image.src = 'kupu_silva/closed_arrow.gif';
            this.image.setAttribute('title', _('click to unfold'));
            this.lastChild.style.display = 'none';
        }
    };
    addEventHandler(itemsdiv, 'click', handler, outerdiv);
    addEventHandler(titlecell, 'click', handler, outerdiv);

    // innerdiv is where the actual checkboxes are displayed in, and what
    // is collapsed/uncollapsed
    var innerdiv = document.createElement('div');
    innerdiv.className = 'kupu-properties-checkbox-innerdiv';
    outerdiv.appendChild(innerdiv);
    td.appendChild(outerdiv);

    for (var i=0; i < infos.length; i++) {
        // in certain cases the value you want to display is different
        // from that you want to store, in that case seperate id from
        // value with a |, there should always be a value|checked, but
        // in some cases you may want a value|title|checked set...
        var info = infos[i].split('|');
        var itemvalue = info[0];
        var title = info[0];
        var checked = (info[1] == 'true' || info[1] == 'yes');
        if (info.length == 3) {
            title = info[1];
            checked = (info[2] == 'true' || info[2] == 'yes');
        };
        var div = document.createElement('div');
        div.className = 'kupu-properties-checkbox-line';
        innerdiv.appendChild(div);

        var cbdiv = document.createElement('div');
        cbdiv.className = 'kupu-properties-checkbox-input';
        div.appendChild(cbdiv);
        
        var checkbox = document.createElement('input');
        checkbox.setAttribute('name', name);
        checkbox.setAttribute('namespace', namespace);
        checkbox.type = 'checkbox';
        checkbox.value = itemvalue;
        cbdiv.appendChild(checkbox);
        if (checked) {
            checkbox.checked = 'checked';
        };
        checkbox.className = 'metadata-checkbox';
        // XXX a bit awkward to set this on all checkboxes
        if (mandatory) {
            checkbox.setAttribute('mandatory', 'true');
        };
        var textdiv = document.createElement('div');
        textdiv.className = 'kupu-properties-checkbox-item-title';
        textdiv.appendChild(document.createTextNode(title));
        div.appendChild(textdiv);
    };
    // we can not hide the checkboxes earlier because IE requires them
    // to be *visible* in order to check them from code :(
    innerdiv.style.display = 'none';
};

SilvaPropertyTool.prototype.beforeSave = function() {
    /* save the metadata to the document */
    if (window.widgeteer) {
        widgeteer.widget_registry.prepareForm(this.form);
    };
    var doc = this.editor.getInnerDocument();
    var inputs = this.table.getElementsByTagName('input');
    var textareas = this.table.getElementsByTagName('textarea');
    var checkboxdata = {}; // name: value for all checkboxes checked
    var errors = [];
    var okay = [];
    for (var i=0; i < inputs.length; i++) {
        var input = inputs[i];
        if (!input.getAttribute('namespace')) {
            continue;
        };
        var name = input.getAttribute('name');
        var scheme = input.getAttribute('namespace');
        if (input.getAttribute('type') == 'text') {
            var value = input.value;
            if (input.getAttribute('mandatory') && value.strip() == '') {
                errors.push(name);
                continue;
            };
            okay.push([name, scheme, value]);
        } else if (input.getAttribute('type') == 'checkbox') {
            if (checkboxdata[name] === undefined) {
                checkboxdata[name] = [];
                // XXX yuck!!
                checkboxdata[name].namespace = scheme;
                checkboxdata[name].mandatory = 
                    input.getAttribute('mandatory') ? true : false;
            };
            if (input.checked) {
                checkboxdata[name].push(
                    input.value.replace('|', '&pipe;', 'g'));
            };
        };
    };
    for (var i=0; i < textareas.length; i++) {
        var textarea = textareas[i];
        var name = textarea.getAttribute('name');
        var scheme = textarea.getAttribute('namespace');
        var value = textarea.value;
        if (textarea.getAttribute('mandatory') && value.strip() == '') {
            errors.push(name);
            continue;
        };
        okay.push([name, scheme, value]);
    };
    for (var name in checkboxdata) {
        if (checkboxdata[name].mandatory && checkboxdata[name].length == 0) {
            errors.push(name);
        } else {
            var data = checkboxdata[name];
            okay.push([name, data.namespace, data.join('|')]);
        };
    };
    if (errors.length) {
        throw('Error in properties: fields ' + errors.join(', ') + 
                ' are required but not filled in');
    };
    for (var i=0; i < okay.length; i++) {
        this._addMetaTag(doc, okay[i][0], okay[i][1], okay[i][2]);
    };
};

SilvaPropertyTool.prototype._addMetaTag = function(doc, name, scheme, 
                                                    value, parentvalue) {
    var head = doc.getElementsByTagName('head')[0];
    if (!head) {
        throw('The editable document *must* have a <head> element!');
    };
    // first find and delete the old one
    // XXX if only we'd have XPath...
    var metas = doc.getElementsByTagName('meta');
    for (var i=0; i < metas.length; i++) {
        var meta = metas[i];
        if (meta.getAttribute('name') == name && 
                meta.getAttribute('scheme') == scheme) {
            meta.parentNode.removeChild(meta);
        };
    };
    var tag = doc.createElement('meta');
    tag.setAttribute('name', name);
    tag.setAttribute('scheme', scheme);
    tag.setAttribute('content', value);

    head.appendChild(tag);
};

function SilvaCharactersTool(charselectid) {
    /* a tool to add non-standard characters */
    this._charselect = document.getElementById(charselectid);
};

SilvaCharactersTool.prototype = new KupuTool;

SilvaCharactersTool.prototype.initialize = function(editor) {
    this.editor = editor;
    addEventHandler(this._charselect, 'change', this.addCharacter, this);
    var chars = this.editor.config.nonstandard_chars.split(' ');
    for (var i=0; i < chars.length; i++) {
        var option = document.createElement('option');
        option.value = chars[i];
        var text = document.createTextNode(chars[i]);
        option.appendChild(text);
        this._charselect.appendChild(option);
    };
};

SilvaCharactersTool.prototype.addCharacter = function() {
    var select = this._charselect;
    var c = select.options[select.selectedIndex].value;
    if (!c.strip()) {
        return;
    };
    var selection = this.editor.getSelection();
    var textnode = this.editor.getInnerDocument().createTextNode(c);
    var span = this.editor.getInnerDocument().createElement('span');
    span.appendChild(textnode);
    selection.replaceWithNode(span);
    var selection = this.editor.getSelection();
    selection.selectNodeContents(span);
    selection.moveEnd(1);
    selection.collapse(true);
    this.editor.logMessage('Character ' + c + ' inserted');
    this.editor.getDocument().getWindow().focus();
    select.selectedIndex = 0;
};
