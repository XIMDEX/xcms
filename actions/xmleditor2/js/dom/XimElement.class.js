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
 * Representation of a XML ximdoc element, needs a RngElement as a template.
 * For the creation of an existing XML element use {@link XimDocument#importXmlElement}
 * For the creation of an existing HTML element use {@link XimDocument#importHtmlElement}
 * @constructor
 */
XimElement = function(rngElement, importChildElements, ancestors) {

	this.tagName = null;
	this.uid = null;
	this.parentNode = null;
	this.previousSibling = null;
	this.nextSibling = null;
	this.childNodes = [];
	this.attributes = {};
	this.defaultContent = '';
	this.value = [];
	this.schemaNode = null;
	this.isRoot = false;
	this._htmlElements = [];

	/**
	 * Function which initializes a new instance.
	 * @private
	 * @param {RngElement} rngElement A RngElement as a template for the new XimElement.
	 */
	this._initialize = function(rngElement, importChildElements, ancestors) {

		if (!rngElement) return;

		this.tagName = rngElement.tagName.toLowerCase();
		this.schemaNode = rngElement;
		this.defaultContent = rngElement.defaultContent;
		this.value = [rngElement.defaultContent];

		var attributes = rngElement.attributes;

		for (var attrName in attributes) {
			if (attributes[attrName] && typeof(attributes[attrName]) == 'object') {
				this.attributes[attrName] = '';
			}
		}

		if (importChildElements && (!ancestors || !ancestors.contains(rngElement.tagName))) {		
			this._importChildElements(this, rngElement, ancestors);
		}
	};

	this._importChildElements = function(ximElement, rngElement, ancestors) {
		var count = rngElement.childNodes.length;
		for (var i=0; i<count; i++) {
			if (ancestors) {
				var ancestorsbase = ancestors;
				if (!ancestors.contains(rngElement.tagName)) ancestorsbase.push(rngElement.tagName);
			} else {
				var ancestorsbase = [rngElement.tagName];
			}
			if (!rngElement.childNodes[i].isOptional){

			    var child = new XimElement(rngElement.childNodes[i], false, ancestorsbase);
			    ximElement.appendChild(child);
			}
		}
	};

	/**
	 * Function which inserts a XimElement as a child node of this instance.
	 * @param {XimElement} child The XimElement to insert.
	 */
	this.appendChild = function(child) {
		child.parentNode = this;
		this.childNodes.push(child);
		var childCount = this.childNodes.length;
		if (this.childNodes[childCount - 2]) {
			child.previousSibling = this.childNodes[childCount - 2];
			this.childNodes[childCount - 2].nextSibling = child;
		}
	};

	/**
	 * Function which inserts a XimElement as a child node of this instance and before the specified brother.
	 * @param {XimElement} child The XimElement to insert.
	 * @param {XimElement} brother The brother of the new child.
	 */
	this.insertBefore = function(child, brother) {
		child.parentNode = this;
		var newChildNodes = [];
		var count = this.childNodes.length;
		for (var i=0; i<count; i++) {
			var item = this.childNodes[i];
			if (item.uid == brother.uid) {
				newChildNodes.push(child);
				newChildNodes.push(item);
			} else {
				newChildNodes.push(item);
			}
		}
		child.nextSibling = brother;
		child.previousSibling = brother.previousSibling;
		if (brother.previousSibling) brother.previousSibling.nextSibling = child;
		brother.previousSibling = child;
		this.childNodes = newChildNodes;
	};

	/**
	 * Function which inserts a XimElement as a child node of this instance and after the specified brother.
	 * @param {XimElement} child The XimElement to insert.
	 * @param {XimElement} brother The brother of the new child.
	 */
	this.insertAfter = function(child, brother) {
		child.parentNode = this;
		var newChildNodes = [];
		var count = this.childNodes.length;
		for (var i=0; i<count; i++) {
			var item = this.childNodes[i];
			if (item.uid == brother.uid) {
				newChildNodes.push(item);
				newChildNodes.push(child);
			} else {
				newChildNodes.push(item);
			}
		}
		child.previousSibling = brother;
		child.nextSibling = brother.nextSibling;
		if (brother.nextSibling) brother.nextSibling.previousSibling = child;
		brother.nextSibling = child;
		this.childNodes = newChildNodes;
	};

	/**
	 * Function which removes a child node of this instance.
	 * @param {XimElement} child The XimElement to remove.
	 */
	this.removeChild = function(child) {
		var newChildNodes = [];
		var count = this.childNodes.length;
		for (var i=0; i<count; i++) {
			var item = this.childNodes[i];
			if (item.uid != child.uid) {
				newChildNodes.push(item);
			}
		}
		if(child.previousSibling) {
			child.previousSibling.nextSibling = child.nextSibling;
		}
		if(child.nextSibling) {
			child.nextSibling.previousSibling = child.previousSibling;
		}
		this.childNodes = newChildNodes;
	};

	/**
	 * Function which splits an element of the 'value' array in two elements based in a specific position.
	 * @param {int} position
	 */
	this.splitValue = function(position) {
		var key = this.getValueKeyByPosition(position);
		var relativePosition = position - this.getValueString(key).length;
		var pieces = [];
		pieces[0] = this.value[key].substring(0,relativePosition + 1);
		pieces[1] = this.value[key].substring(relativePosition + 1);
		this.value.splice(key, 1, pieces[1]);
		this.value.splice(key, 0, pieces[0]);
	}

	/**
	 * Function which returns a string that represents the real value of the element.
	 * If a key is given, returns string only since previous key.
	 * @param {int} key
	 * @returns {string}
	 */
	this.getValueString = function(key) {
		var val = '';
		var count = this.value.length;
		for (var i=0; i<count; i++) {
			if(i == key)
				return val;
			val += this.value[i];
		}
		return val;
	}

	/*
	 * Function which returns value key given a position.
	 * @param {int} position
	 * @returns {int}
	 */
	this.getValueKeyByPosition = function(position) {
		var length = 0;
		var count = this.value.length;
		for (var i=0; i<count; i++) {
			length += this.value[i].length;
			if(length > position)
				return i;
		}
		return (count - 1);
	}

	/*
	 * Function which returns position respect siblings.
	 * @returns {int}
	 */
	this.getSiblingPosition = function () {
		var position = 0;
		var sibling = this;
		while (sibling.previousSibling) {
			position ++;
			sibling = sibling.previousSibling;
		}
		return position;
	}

	/**
	 * Function which exports this XimElement instance into a DOMNode using the specified DOMDocument
	 * or the IFrame DOMDocument if not passed.
	 * @param {DOMDocument} doc Optional, DOMDocument to use for DOMNode creation.
	 * @returns {DOMNode}
	 */
	this.toDomElement = function(doc) {

		/** Export function **/
		doc = doc || document;
		var domelement = doc.createElement(this.tagName.toLowerCase());
		var attributes = this.attributes;
		for (var attrName in attributes) {
			var attrValue = attributes[attrName];
			try {
				domelement.setAttribute(attrName, attrValue);
			} catch(e) {
				//console.log(attrName, attrValue);
			}
		}

		var value = null;
		if (this.macro && this.schemaNode.type == 'ximlet') {
			value = [this.macro];
		} else {
			value = this.value ? this.value : '';
		}

		var l = value.length;
		for (var i=0 ; i<l; i++) {
			var textNode = doc.createTextNode(value[i] || '');
			domelement.appendChild(textNode);
		}

		return domelement;
	};

	/**
	 * Function which scrolls up the element in the hierachy.
	 * @returns {boolean} TRUE if scroll is allowed in the actual position.
	 */
	this.scrollUp = function() {
		var brotherPos = this.findNextAllowedPosition('up');
		if (brotherPos !== null) {
			var pos = this.getElementIndex();
			var aux = this.parentNode.childNodes[brotherPos];
			this.parentNode.childNodes[brotherPos] = this;
			this.parentNode.childNodes[pos] = aux;
			return true;
		}
		return false;
	},

	/**
	 * Function which scrolls down the element in the hierachy.
	 * @returns {boolean} TRUE if scroll is allowed in the actual position.
	 */
	this.scrollDown = function() {
		var brotherPos = this.findNextAllowedPosition('down');
		if (brotherPos !== null) {
			var pos = this.getElementIndex();
			var aux = this.parentNode.childNodes[brotherPos];
			this.parentNode.childNodes[brotherPos] = this;
			this.parentNode.childNodes[pos] = aux;
			return true;
		}
		return false;
	},

	/**
	 * Function which returns if an element can be copied or cutted.
	 * @returns {boolean} TRUE if ximElement can be copied or cutted.
	 */
	this.canBeCopied = function() {
		if(this.isSectionXimlet())
			return false;
		return true;
	},

	/**
	 * Function which returns if an element can be removed.
	 * @returns {boolean} TRUE if ximElement is removable.
	 */
	this.isRemovable = function() {
		if (this.isSectionXimlet() || this.isRoot) {
			return false;
		}
		return true;
	},

	/**
	 * Function which returns if an element is a 'section' ximlet.
	 * @returns {boolean} TRUE if ximElement is a 'section' ximlet.
	 */
	this.isSectionXimlet = function() {
		if(this.attributes['section_ximlet'] && this.attributes['section_ximlet'] == 'yes') {
			return true;
		}
		return false;
	},

	/**
	 * Function which returns if an element is selectable.
	 * @returns {boolean} TRUE if ximElement is selectable.
	 */
	this.isSelectable = function(docNodeId) {
		if(this.uid.indexOf(docNodeId) == -1)
			return false;
		return true;
	},

	/**
	 * Function which returns if an element is applyable.
	 * @returns {boolean} TRUE if ximElement is apply type.
	 */
	this.isApplyable = function() {
		if(this.schemaNode.type.contains('apply'))
			return true;
		return false;
	},

	this.disApply = function(preserveTextContent) {
		if(!this.isApplyable) return;

		// Joining previous and next string elements before disApplying ximElement

		var value = this.parentNode.value;
		var children = this.parentNode.childNodes;
		var length = value.length;
		var i = 0;
		while (i<length) {
			if (children[i] && children[i].uid == this.uid) {
				var val1 = value[i] || '';
				var val2 = value[i+1] || '';
				value.splice(i, 2, val1 + val2);

				var element = $('[uid="'+this.uid+'"]', kupu.getBody())[0];
				var parent = element.parentNode;
				var previous = element.previousSibling;
				var next = element.nextSibling;

				if ((previous && previous.nodeType == 3) || (next  && next.nodeType == 3)) {
					val1 = previous ? previous.nodeValue || '' : '';
					val2 = next ? next.nodeValue || '' : '';
					parent.insertBefore(document.createTextNode(val1 + (preserveTextContent ? this.value : '') + val2), previous);
					if (previous)
					    parent.removeChild(previous);
					if (next)
					    parent.removeChild(next);
				} //if the apply contains the whole word
                                else if(val1 == "" && val2 == ""){
                                        parent.insertBefore(document.createTextNode((preserveTextContent ? this.value : '')), previous);
                                        parent.removeChild(previous);
                                        parent.removeChild(next);
                                }
                                //Tree view FIX
                                else{
                                        element = $('[uid="'+this.uid+'"]', kupu.getBody())[0].parentNode;
                                        parent = element.parentNode;
                                        previous = element.previousSibling;
                                        next = element.nextSibling;

                                        if (previous && next && previous.nodeType == 3 && next.nodeType == 3) {
                                                val1 = previous.nodeValue;
                                                val2 = next.nodeValue;
                                                parent.insertBefore(document.createTextNode(val1 + (preserveTextContent ? this.value : '') + val2), previous);
                                                parent.removeChild(previous);
                                                parent.removeChild(next);
                                        }
                                }


				parent.removeChild(element);
				i = length;
			}
			i++;
		}

		return this;
	}

	/**
	 * Function which returns if an element is droppable.
	 * @returns {boolean} TRUE if ximElement is droppable.
	 */
	this.isDroppable = function(docNodeId) {
		return this.isSelectable(docNodeId);
	},

	/**
	 * Function which returns first selectable parent.
	 * @returns {ximElement} First selectable parent.
	 */
	this.getFirstSelectableParent = function(docNodeId) {
		if(!this.parentNode)
			return null;
		var parentNode = this.parentNode;
		if(!parentNode.isSelectable(docNodeId))
			return parentNode.getFirstSelectableParent(docNodeId);
		return parentNode;
	},

	/**
	 * Function which returns first parent with 'document' uid.
	 * @returns {ximElement} First parent with 'document' uid.
	 */
	this.getFirstParentWithDocumentUid = function(docNodeId) {
		if(!this.parentNode)
			return this;
		var parentNode = this.parentNode;
		var parentUid = parentNode.uid;
		if(!parentUid || parentUid.indexOf(docNodeId) == -1)
			return parentNode.getFirstParentWithDocumentUid(docNodeId);
		return parentNode;
	},

	/**
	 * Function which finds the next position where the XimElement is allowed, always under the same parent.
	 * TODO: It's needed an implementation of <choice/> and cardinallity in RngDocument!
	 * TODO: previousSibling and nextSibling is missed here
	 * @param {string} direction [up|down] string.
	 * @returns {int} The new position of the element.
	 */
	this.findNextAllowedPosition = function(direction) {
		if(this.isSectionXimlet())
			return null;

		if (!direction) direction = 'up';
		direction = direction.toLowerCase();

		var pos = this.getElementIndex();
		if (direction == 'up') {
			pos--;
		} else if (direction == 'down') {
			pos++;
		}

		var brother = this.parentNode.childNodes[pos];
		if(!brother || brother.isSectionXimlet())
			return null;
		var rngElement = this.schemaNode;
		var rngParent = this.parentNode.schemaNode;
		var rngBrother = brother ? brother.schemaNode : null;

		if (!brother || !rngElement.isAllowedUnder(rngParent, rngBrother)) {
			return null;
		}

		return pos;
	};

	/**
	 * Function which finds the index of this element in the parent childNodes array.
	 * NOTE: A replace for previousSibling and nextSibling???
	 * @returns {int}
	 */
	this.getElementIndex = function() {
		var parent = this.parentNode;
		var pos = -1;
		var i = 0;
		while (pos<0 && i<parent.childNodes.length) {
			if (parent.childNodes[i].uid == this.uid) pos = i;
			i++;
		}
		return pos;
	};

	this.getHtmlElements = function(editable) {
		// editable == undefined	-> All
		// editable == true			-> Editables
		// editable == false		-> No Editables
		var elems = $(this._htmlElements);
		if (editable === true) {
			elems = elems.not('[editable=no]');
		} else if (editable === false) {
			elems = elems.filter('[editable=no]');
		}
		return elems;
	};

	this.getIdNodeForUid = function(docNodeId, parent) {
		if(this.isRoot)
			return docNodeId;
		var parentNode = this.parentNode ? this.parentNode : parent;
		if(!parentNode)
			return docNodeId;
		var parentUid = parentNode.uid;
		if(!parentUid || parentUid.indexOf(docNodeId) == -1)
			return parentNode.getIdNodeForUid(docNodeId, (parentNode.parentNode) ? parentNode.parentNode : null);
		var ximletId = parentNode.attributes['ximlet_id'];
		return (ximletId) ? ximletId : docNodeId;
	}

	this._initialize(rngElement, importChildElements, ancestors);
};
