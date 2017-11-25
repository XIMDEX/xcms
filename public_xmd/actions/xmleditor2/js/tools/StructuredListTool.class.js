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
 * Base tool for tables and lists management
 */
function StructuredListTool() {

	this._current_uid = null;
	this._current_model = null;
	this._current_rngModel = null;
	this._allowed_uids = null;
	this._valid_tags = null;	// Overwrite in subclasses!

	this.initialize = function(editor) {
		this.editor = editor;
		this.parentClass.editor = editor;
		this._allowed_uids = [];
		this.afterUpdateContent(null);
	};

	this.updateState = function(options) {

		if (!options.selNode || !options.event || options.event.type != 'click') return;

		var ximElement = options.selNode.ximElement;
		var uid = ximElement.uid;
		var editable = options.selNode;
		var tagName = options.selNode.tagName.toLowerCase();

		// NOTE: Checking valid tags for this especific tool
		if (!editable || !this._valid_tags.contains(tagName) || (!this._allowed_uids.contains(uid) && !ximElement['structuredParent'])) {
			this._current_uid = null;
			this._current_model = null;
			this._current_rngModel = null;
			return;
		}

		// elem is the root node of the struct
		var elem = ximElement.structuredParent || ximElement;
		this._current_uid = elem.uid;
		this._current_model = {};
		this._parseModel(elem, 0);
		this._current_rngModel = ximElement.schemaNode;
	};

	/**
	 * Recursive
	 * Function which extracts the level from the hierachy for each element
	 */
	this._parseModel = function(item, level) {
		if (!level) level = 0;
		if (level > 2) return;
		this._current_model[item.tagName] = level;
		var childNodes = this.sanitizeChildNodes(item);
		var l = childNodes.length;
		for (var i=0; i<l; i++) {
			var child = childNodes[i];
			if(child.tagName != 'dextag')
				this._parseModel(child, level+1);
		}
	};

	/**
	 * Function which obtains the UIDs of all tables and lists
	 */
	this.afterUpdateContent = function(options) {

		this._allowed_uids = [];
		var frameBody = this.editor.getBody();

		$('table,ul,ol', frameBody).each(
			function(index, element) {
				var uid = element.getAttribute('uid')
				if (uid) {
					this._allowed_uids.push(uid);
					var ximElement = element.ximElement;
					this._parseChildrens(ximElement, ximElement);
				}
			}.bind(this)
		);
	};

	/**
	 * Recursive
	 * Function which sets the sctructuredParent attribute on all childrens of tables and lists
	 */
	this._parseChildrens = function(ximElement, ximParent) {
		var l = ximElement.childNodes.length;
		for (var i=0; i<l; i++) {
			var child = ximElement.childNodes[i];
			// Beware with elements already registered
			if (!child['structuredParent']) child.structuredParent = ximParent;
			this._parseChildrens(child, ximParent);
		}
	};


	// --- API ---

	this.getElement = function() {
		return this.editor.getXimDocument().getElement(this._current_uid);
	};

	this.getLevel = function(tagName) {
		return this._current_model[tagName];
	};

	this.getElementByLevel = function(level) {
		for (var elem in this._current_model) {
			if (this._current_model[elem] == level) return elem;
		}
		return null;
	};

	this.appendChild = function(ximElement, ximParent, updateEditor) {
		var elem = this.editor.getXimDocument().appendChild(ximElement, ximParent);
		this.setActionDescription(_('Insert element'));
		if (updateEditor) this.editor.updateEditor({caller: this});
		return elem;
	};

	this.insertBefore = function(ximElement, ximParent, ximBrother, updateEditor) {
		var elem = this.editor.getXimDocument().insertBefore(ximElement, ximParent, ximBrother);
		this.setActionDescription(_('Insert element'));
		if (updateEditor) this.editor.updateEditor({caller: this});
		return elem;
	};

	this.insertAfter = function(ximElement, ximParent, ximBrother, updateEditor) {
		var elem = this.editor.getXimDocument().insertAfter(ximElement, ximParent, ximBrother);
		this.setActionDescription(_('Insert element'));
		if (updateEditor) this.editor.updateEditor({caller: this});
		return elem;
	};

	this.removeChild = function(elem, ximElement, updateEditor) {
		var elem = this.editor.getXimDocument().removeChild(ximElement);
		this.setActionDescription(_('Remove element'));
		if (updateEditor) this.editor.updateEditor({caller: this});
		return elem;
	};


	// Events handlers that must be used from createContextMenuElements()

	this._appendChildHandler = function(elem, selNode) {
		var elem = elem;
		var selNode = selNode;
		return function() {
			this.appendChild(elem, selNode, true);
		};
	};

	this._removeChildHandler = function(elem, selNode) {
		var elem = elem;
		var selNode = selNode;
		return function() {
			this.removeChild(elem, selNode, true);
		};
	};

	this._showPropertiesHandler = function(selNode) {
		var selNode = selNode;
		return function() {
			this.showItemProperties(selNode);
		};
	};


	// --- abstract ---

	/**
	 * Returns an array of menu items. See kupubasetools.js
	 */
	this.createContextMenuElements = function(selNode, event) {
		this.editor.alert('overwrite me!');
	};

	this.showItemProperties = function(selNode) {
		this.editor.alert('showItemProperties: overwrite me!');
	};

	this._getFormalName = function(item) {
		this.editor.alert('_getFormalName: overwrite me!');
	};

	this.elementsForInsert = function(item) {
		this.editor.alert('elementsForInsert: overwrite me!');
	};

	this.elementsForRemove = function(item) {
		this.editor.alert('elementsForRemove: overwrite me!');
	};

};

StructuredListTool.prototype = new XimdocTool();


/**
 *  Class from tables management. Extends StructuredListTool
 */
function TableManagerTool() {

	// See levels on StructuredList._parseModel()
	this.ELEMENT_TABLE = 0;
	this.ELEMENT_ROW = 1;
	this.ELEMENT_CELL = 2;

	this.parentClass = TableManagerTool.prototype;
	this._valid_tags = ['table', 'tbody', 'tr', 'th', 'td'];
	this.editor = null;

	/**
	 * Calculates where to place the new element depending on the selected element.
	 * TODO: This method could be inteligent and add cells or colspans automatically.
	 */
	this.appendChild = function(tagName, selNode, updateEditor) {

		var ximTable = this.getElement();
		var selNodeLevel = this.getLevel(selNode.tagName);
		var itemLevel = this.getLevel(tagName);
		var newElem = null;

		if (itemLevel == this.ELEMENT_ROW) {

	   		var rngRow = this.editor.getRngDocument().getElement(tagName);
	    	var newRow = new XimElement(rngRow, true);

			if (selNodeLevel == this.ELEMENT_TABLE) {

				// Adding a new row from the table element
				newElem = this.parentClass.appendChild(newRow, selNode, updateEditor);

			} else {

				// Adding a new row from a row or cell element
				var ximRow = selNode;
				if (selNodeLevel == this.ELEMENT_CELL) ximRow = selNode.parentNode;
				newElem = this.parentClass.insertAfter(newRow, ximTable, ximRow, updateEditor);
			}

		} else if (itemLevel == this.ELEMENT_CELL) {

	   		var rngCell = this.editor.getRngDocument().getElement(tagName);
	    	var newCell = new XimElement(rngCell, true);

	    	if (selNodeLevel == this.ELEMENT_ROW) {

	    		// Adding a cell from a row element
	    		newElem = this.parentClass.appendChild(newCell, selNode, updateEditor);

	    	} else if (selNodeLevel == this.ELEMENT_CELL) {

	    		// Adding a cell from a cell element
	    		var ximRow = selNode.parentNode;
	    		newElem = this.parentClass.insertAfter(newCell, ximRow, selNode, updateEditor);
	    	}
		}

		//console.log(selNode);
		return newElem;
	};

	this.removeChild = function(elem, ximElement, updateEditor) {

		var selNodeLevel = this.getLevel(ximElement.tagName);
		var itemLevel = this.getLevel(elem);
		var newElem = null;

		if (itemLevel == this.ELEMENT_TABLE) {

			// Removing the table
			newElem = this.parentClass.removeChild(elem, this.getElement(), updateEditor);

		} else if (itemLevel == this.ELEMENT_ROW) {

			// Removing a row
			var ximCell = ximElement;
			if (selNodeLevel == this.ELEMENT_CELL) ximCell = ximElement.parentNode;
			newElem = this.parentClass.removeChild(elem, ximCell, updateEditor);

		} else if (itemLevel == this.ELEMENT_CELL && selNodeLevel == this.ELEMENT_CELL) {

			// Removing a cell
			newElem = this.parentClass.removeChild(elem, ximElement, updateEditor);
		}

		//console.log(ximElement);
		return newElem;
	};

	/**
	 * Function which creates a table with especific options
	 */
	this.createTable = function(rngTable, selNode, options) {

   		var rngTable = this.editor.getRngDocument().getElement(rngTable.tagName);
    	var newTable = new XimElement(rngTable, true);
		this._current_model = {};
		this._parseModel(rngTable, 0);
    	newTable = this.editor.getXimDocument().insertAfter(newTable, selNode.parentNode, selNode);

    	// ... so we have to complete rows and cells.
    	for (var r=0; r<options.rows; r++) {
			row = this.appendChild(this.getElementByLevel(this.ELEMENT_ROW), newTable, false);
    		for (var c=0; c<options.cols; c++) {
				col = this.appendChild(this.getElementByLevel(this.ELEMENT_CELL), row, false);
    		}
    	}

		this.setActionDescription(_('Create table'));
		// Be carefull, don't update the editor until all elements have been created, see FALSE on appendChild() calls.
		this.editor.updateEditor({caller: this});
	};

	this.sanitizeChildNodes = function(item) {
		var childNodes = [];
		var c = 0;
		for (var i=0; i<item.childNodes.length; i++) {
			if(item.childNodes[i].tagName == 'dextag')
				continue;
			childNodes[c] = item.childNodes[i];
			c++;
		}

		return childNodes;
	}


	/**
	 * Function which removes a table
	 */
	this.removeTable = function(selNode) {
		if (!selNode) return;
		var ximTable = selNode.structuredParent || selNode;
		this.removeChild(ximTable.tagName, ximTable, true);
	};

	/**
	 * selNode is a ximElement
	 */
	this.createContextMenuElements = function(selNode, event) {

		if (!selNode) return [];
		var ximNode = selNode.ximElement;
		if (!this._current_rngModel || !ximNode['structuredParent']) return [];

		var cm = [];
		cm.push(new ContextMenuElement(_('-----'), function(){}, this));
		cm.push(new ContextMenuElement(_('Remove table'), function() {this.removeTable(ximNode);}, this));
		cm.push(new ContextMenuElement(_('Table properties'), this._showPropertiesHandler(ximNode), this));
		cm.push(new ContextMenuElement(_('-----'), function(){}, this));

		var elements = this.elementsForInsert(ximNode.tagName);
		var l = elements.length;
		for (var i=0; i<l; i++) {
			var elem = elements[i];
			var name = this._getFormalName(elem);
			cm.push(new ContextMenuElement(_('Add item: ') + name, this._appendChildHandler(elem, ximNode), this));
			cm.push(new ContextMenuElement(_('Properties of ') +name, this._showPropertiesHandler(ximNode), this));
		}

		cm.push(new ContextMenuElement(_('-----'), function(){}, this));

		elements = this.elementsForRemove(ximNode.tagName);
		l = elements.length;
		for (var i=0; i<l; i++) {
			var elem = elements[i];
			var name = this._getFormalName(elem);
			cm.push(new ContextMenuElement(_('Remove item: ') + name, this._removeChildHandler(elem, ximNode), this));
		}

		return cm;
	};

	this._getFormalName = function(item) {
		var name = '';
		switch (this.getLevel(item)) {
			case this.ELEMENT_ROW:
				name = 'Row';
				break;
			case this.ELEMENT_CELL:
				name = 'Cell';
				break;
			default:
				name = '-- Unknown --';
		}
		return name;
	};

	/**
	 * Needs the ximElement tagName of the selected node for return the items allowed for insert.
	 * In a table, the elements permited are rows and cells, regardless of the selected item
	 * except a cell when selNode is the table element.
	 */
	this.elementsForInsert = function(selNodeTagName) {
		var elements = [];
		var itemLevel = this.getLevel(selNodeTagName);
		for (var o in this._current_model) {
			var level = this._current_model[o];
			// Can't insert a table
			// Can't insert a cell if selNode is a table element
			if (!(itemLevel == this.ELEMENT_TABLE && level == this.ELEMENT_CELL) && level > this.ELEMENT_TABLE) {
				elements.push(o);
			}
		}
		return elements;
	};

	/**
	 * Needs the ximElement tagName of the selected node for return the items allowed for remove.
	 * In a table, the elements permited are rows (if selected item is a row or cell)
	 * and cells (if selected item is a cell).
	 */
	this.elementsForRemove = function(selNodeTagName) {
		var elements = [];
		var itemLevel = this.getLevel(selNodeTagName);
		for (var o in this._current_model) {
			var level = this._current_model[o];
			if (itemLevel == this.ELEMENT_ROW && level == this.ELEMENT_ROW) {
				// selNode is a row, only allow to delete rows
				elements.push(o);
			} else if (itemLevel == this.ELEMENT_CELL && [this.ELEMENT_ROW, this.ELEMENT_CELL].contains(level)) {
				// selNode is a cell, we can delete the cell or the parent row
				elements.push(o);
			}
		}
		return elements;
	};

};

TableManagerTool.prototype = new StructuredListTool();


/**
 *  Class from lists management. Extends StructuredList
 */
function ListManagerTool() {

	// See levels on StructuredList._parseModel()
	this.ELEMENT_LIST = 0;
	this.ELEMENT_ITEM = 1;

	this.parentClass = ListManagerTool.prototype;
	this._valid_tags = ['ul', 'ol', 'li'];

	/**
	 * Calculates where to place the new element depending on the selected element.
	 */
	this.appendChild = function(tagName, selNode, updateEditor) {

		var ximList = this.getElement();
		var selNodeLevel = this.getLevel(selNode.tagName);
		var itemLevel = this.getLevel(tagName);
		var newElem = null;

		if (itemLevel == this.ELEMENT_ITEM) {

			var rngItem = this.editor.getRngDocument().getElement(tagName);
	    	var newItem = new XimElement(rngItem, true);

			if (selNodeLevel == this.ELEMENT_LIST) {

				// Adding a new item from the list element
				newElem = this.parentClass.appendChild(newItem, selNode, updateEditor);

			} else if (selNodeLevel == this.ELEMENT_ITEM) {

				// Adding a new item from a item element
				newElem = this.parentClass.insertAfter(newItem, ximList, selNode, updateEditor);
			}

		}

		//console.log(selNode);
		return newElem;
	};

	this.removeChild = function(elem, ximElement, updateEditor) {

		var selNodeLevel = this.getLevel(ximElement.tagName);
		var itemLevel = this.getLevel(elem);
		var newElem = null;

		if (itemLevel == this.ELEMENT_LIST) {

			// Removing the list
			newElem = this.parentClass.removeChild(elem, this.getElement(), updateEditor);

		} else if (itemLevel == this.ELEMENT_ITEM && selNodeLevel == this.ELEMENT_ITEM) {

			// Removing an item
			newElem = this.parentClass.removeChild(elem, ximElement, updateEditor);

		}

		//console.log(ximElement);
		return newElem;
	};

	/**
	 * Function which creates a list with a default list item
	 */
	this.createList = function(rngList, selNode, options) {
		this.createElement(rngList.tagName, selNode.parent, selNode, null).apply();
	};

	/**
	 * Function which removes a list
	 */
	this.removeList = function(selNode) {
		if (!selNode) return;
		var ximList = selNode.structuredParent || selNode;
		this.removeChild(ximList.tagName, ximList, true);
	};

	/**
	 * selNode is a ximElement
	 */
	this.createContextMenuElements = function(selNode, event) {

		if (!selNode) return [];
		var ximNode = selNode.ximElement;
		if (!this._current_rngModel || !ximNode['structuredParent']) return [];

		var cm = [];
		cm.push(new ContextMenuElement(_('-----'), function(){}, this));
		//cm.push(new ContextMenuElement(_('Remove List'), this._removeChildHandler(this._current_rngModel.tagName, ximNode), this));
		cm.push(new ContextMenuElement(_('Remove list'), function() {this.removeList(ximNode);}, this));
		cm.push(new ContextMenuElement(_('List properties'), this._showPropertiesHandler(ximNode), this));
		cm.push(new ContextMenuElement(_('-----'), function(){}, this));

		var elements = this.elementsForInsert(ximNode.tagName);
		var l = elements.length;
		for (var i=0; i<l; i++) {
			var elem = elements[i];
			var name = this._getFormalName(elem);
			cm.push(new ContextMenuElement(_('Add item: ') + name, this._appendChildHandler(elem, ximNode), this));
			cm.push(new ContextMenuElement(_('Properties of ') + name, this._showPropertiesHandler(ximNode), this));
		}

		cm.push(new ContextMenuElement(_('-----'), function(){}, this));

		elements = this.elementsForRemove(ximNode.tagName);
		l = elements.length;
		for (var i=0; i<l; i++) {
			var elem = elements[i];
			var name = this._getFormalName(elem);
			cm.push(new ContextMenuElement(_('Remove item: ') + name, this._removeChildHandler(elem, ximNode), this));
		}

		return cm;
	};

	this._getFormalName = function(item) {
		var name = '';
		switch (this.getLevel(item)) {
			case this.ELEMENT_ITEM:
				name = 'List item';
				break;
			default:
				name = '-- ERROR IN getFormalName() --';
		}
		return name;
	};

	/**
	 * Needs the ximElement tagName of the selected node for return the items allowed for insert.
	 * In a list, the elements permited are only list items
	 */
	this.elementsForInsert = function(selNodeTagName) {
		var elements = [];
		for (var o in this._current_model) {
			var level = this._current_model[o];
			// Can't insert a list...
			if (level == this.ELEMENT_ITEM) {
				elements.push(o);
			}
		}
		return elements;
	};

	/**
	 * Needs the ximElement tagName of the selected node for return the items allowed for remove.
	 * In a list, the elements permited are list items and the list itself
	 */
	this.elementsForRemove = function(selNodeTagName) {
		var elements = [];
		for (var o in this._current_model) {
			var level = this._current_model[o];
			// We can delete a list item
			if (level == this.ELEMENT_ITEM) {
				elements.push(o);
			}
		}
		return elements;
	};

};

ListManagerTool.prototype = new StructuredListTool();
