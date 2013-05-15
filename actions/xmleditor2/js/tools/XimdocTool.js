
/**
 * Base class for all ximdex actions
 * @class
 */
function XimdocTool() {
    /*
        Tools must implement at least an initialize method and an
        updateState method, and can implement other methods to add
        certain extra functionality (e.g. createContextMenuElements).

        XimdocTool knowns how to operate with XML and RNG documents.
    */

	this.toolboxes = {};

	this.updateState = function(options) {
		/*
			Is called when user moves cursor to other element

			Calls the updateState for all toolboxes and may want perform
			some actions itself
		*/
		for (id in this.toolboxes) {
			try {
				if (this.toolboxes[id]['updateState']) this.toolboxes[id].updateState(options);
			} catch (e) {
				this.editor.logMessage(_('Exception while processing updateState on ${id}: ${msg}', {'id': id, 'msg': e.message}), 2);
			}
		}
	};

	this.beforeUpdateContent = function(options) {
		// Called before the XSL transformation
		for (id in this.toolboxes) {
			if (this.toolboxes[id]['beforeUpdateContent']) this.toolboxes[id].beforeUpdateContent(options);
		}
	};

	this.afterUpdateContent = function(options) {
		// Called before the XSL transformation
		for (id in this.toolboxes) {
			if (this.toolboxes[id]['afterUpdateContent']) this.toolboxes[id].afterUpdateContent(options);
		}
	};

	/**
	 * <p>Function that is called before creating an element.</p>
	 * <p>Each subclass of XimdocTool can override this behaviour
	 * 	  when it calls the inherited createElement method</p>
	 *
	 */
	this.beforeCreateElement = function() {
		loadingImage.showLoadingImage();
	};
	
	/**
	 * <p>Function that is called after creating an element.</p>
	 * <p>Each subclass of XimdocTool can override this behaviour
	 * 	  when it calls the inherited createElement method</p>
	 *
	 */
	this.afterCreateElement = function(options) {
		loadingImage.hideLoadingImage();    		
	};

    	// Deactivates all ximdex buttons
    	this.disableAllButtons = function() {
		var model = this.editor.getRngDocument().getModel();
		for(var rngElement in model) {
			if(rngElement != "docxap") {
				rngElement = rngElement.replace(":", "_");
				var button = this.editor.getTool('%s_rngbutton'.printf(rngElement));
				button.disable();
			}
		}
    	};

    	this.getActionDescription = function() {
    		return this._actionDescription || '';
    	};

    	this.setActionDescription = function(description) {
    		this._actionDescription = description || '';
    	};

	this.createElementWrapper = function(nodeType, parent, brother) {
		// Function which returns a function reference that will be used in events calls (ie: ContentMenu)
		return function() {
			this.createElement(nodeType, parent, brother);
		}.bind(this);
	};

	/**
	 * nodeType is the tagName of the element we want to create
	 * parent and brother are instances of XimElement
	 */
    	this.createElement = function(nodeType, parent, brother, oSel) {
		this.beforeCreateElement();

    	/**

    	 TODO:
	 The system to create elemetns should be improved in several points:

    	 1.	An XimElement element should be created when calling this funcion, following instructions indicated in (1), from RNG
    	 2.	The cardinality should be taken into account when instantiating an XimElement from RNG. Create its children depending on it.
    	 3.	The previous step is not valid if the XimElement element already exists in the XML. In this case, only the childen defined by the XML will be created.
		In fact, the XimElelment doesn't have to be instantiated, but DOMElement via XimDocument.importXmlElement() should.
	 4.	In the XimElement, it should be distinguished between 'defaultValue', 'value' and the HTML representation of the element.
    	 	a.	The attribute 'defaultValue' is a string obtained from the RNG while creating the element; it will be used just whe instantiating a new XimElement.
    	 	b.	The attribute 'value' es string type value which represents the current 'nodeValue' of the HTML node.
    	 	c.	The HTML representation will be obtained by calling a specific function of XimElement or XimDocument.importHtmlElement().
			This would return a HTML node tree wich root is the XimElement element. This methos would be used in the XML exportation (XimDocument.saveXML())
    	 5.     The method XimDocument.importXmlElement() should not use any RNGElement to obtain values, just as it's being done now.
    	 */
   		var ximdoc = this.editor.getXimDocument();
   		var rngElement = ximdoc.getRngDocument().getElement(nodeType);
	    	var ximElement = new XimElement(rngElement, true);

		if (this.editor.selectedTextLength > 0) {

			var selection = oSel ? oSel : this.getSelection();
			if (selection === null) {
				this.editor.logMessage(_('ERROR: Trying to apply an element to more than one selected elements.'));
				return;
			}

			var res;
			res = this.wrapTextElement(ximElement, parent, selection);
			ximElement=res[0];
			brother=res[1];
			/*if (selection.startPosition == 0) {
				// Correcting bug with apply elements when they are at the start of the string
				parent.value.splice(0, 0, '');
			}*/
		}

    		// If brother = null append the element as the last child node
    		if (!brother) {
    			//console.log('appendChild(%s, %s)', ximElement.tagName, parent.tagName);
    			ximdoc.appendChild(ximElement, parent);
    		} else if (this.editor.selectedTextLength > 0) {
 			/*console.log('insertBefore(%s, %s, %s)',
    			ximElement.tagName + ' (' + ximElement.uid + ')',
    			parent.tagName + ' (' + parent.uid + ')',
    			brother.tagName + ' (' + brother.uid + ')');*/
    			ximdoc.insertBefore(ximElement, parent, brother);
    		} else {
			//console.log('insertAfter(%s, %s, %s)', ximElement.tagName, parent.tagName, brother.tagName);
	    		ximdoc.insertAfter(ximElement, parent, brother);
		}

		var tagName = ximElement.tagName || _("element");
		this.setActionDescription(_('Create ')+" "+tagName);
		this.editor._setSelectionData( ximElement );
    		this.editor.updateEditor({caller: this, target: ximElement});
		this.afterCreateElement();
    		return ximElement;
    	};

    	this.getSelection = function() {

		var selNode = this.editor.selNode;
		var selection = this.editor.getSelection();


		if (!selNode){
         	       var arraySelNode = this.editor.ximElement.getHtmlElements();
                	for(var i=0; i< arraySelNode.length;i++){
                        	if (arraySelNode[i].className.indexOf("-selected") > -1){
                                	selNode = arraySelNode[i];
	                        }
        	        }
        	}	

		// temporal asignement
		var startPos = selection.startOffset();
		var endPos = startPos + selection.getContentLength();

		var focusNode = null;
		var startNode = null;
		var endNode = null;

		if (IS_IE) {

			// We need to find the element in which the selection has been made.
			// This element is a "text node", child of range.parentElement(),
			// but there isn't a direct way to do that.

			var rng1 = selection.getRange();
			var parent = rng1.parentElement();
			var text = $(parent).text();
			text = text.substring(0, startPos);

			var child = null;
			var it = new DOMNodeIterator(parent);
			while ((child = it.next()) && focusNode === null) {

				var childText = null;
				if (child.nodeType == 3) {
					childText = child.nodeValue;
				} else {
					childText = $(child).text();
				}

				var aux = text.substr(0, childText.length);
				text = text.substr(childText.length);

				if (aux.length < childText.length) {
					focusNode = child;
					// FIXME: Is range over more than one element?
					startNode = focusNode;
					endNode = focusNode;
					startPos = aux.length;
					endPos = startPos + selection.getContentLength();
				}
			}

		} else {
			startNode = selection.startNode();
			startPos = selection.selection.anchorOffset;
			endNode = selection.endNode();
			endPos = selection.selection.focusOffset;
			focusNode = startNode;
		}

		// TODO: Implement ranges between multiple elements?
		if (startNode !== endNode) return null;

		//Iterating for every node. We are interesting in text (nodetype=3)
		//and nodes with uid attribute
		var it = new DOMNodeIterator(selNode);
		var child = null;
		var childPos = -1;
		while (it.hasNext() && child !== startNode) {
			child = it.next();
			if (child.nodeType == 3 || child.getAttribute("uid"))
			    childPos++;
			childPos++;
		}

		if (selNode.childNodes[0] && selNode.childNodes[0].nodeType != 3 && selNode.childNodes[0].getAttribute("uid")) {
			// Corrects bug with apply elements when they are at the start of the string
			childPos++;
		}

		// If selection is done by dragging from right to left, start & end position must be switched
		if(startPos > endPos) {
			var oldEndPos = endPos;
			endPos = startPos;
			startPos = oldEndPos;
		}

		var oSel = {
			focusNode: focusNode,
			focusNodeIndex: childPos,
			parentNode: selNode,
			startPosition: startPos,
			endPosition: endPos,
			ranges: {
				textBefore: focusNode.nodeValue.substring(0, startPos),
				text: focusNode.nodeValue.substring(startPos, endPos),
				textAfter: focusNode.nodeValue.substring(endPos)
			}
		};

		return oSel;
    	};

    	this.wrapTextElement = function(ximElement, parent, selection) {

		if(null != contextmenu_selection ) {
			var selection = contextmenu_selection;
			contextmenu_selection = null;
		}
		var selNode = this.editor.getSelectedNode() || contextmenu_selNode;


    		// Needed for editor.updateEditorContent()
		var pre = this.editor.getInnerDocument().createTextNode(selection.ranges.textBefore);
		var post = this.editor.getInnerDocument().createTextNode(selection.ranges.textAfter);


		selNode.insertBefore(pre, selection.focusNode);
		selNode.insertBefore(post, selection.focusNode);
		selNode.removeChild(selection.focusNode);

		brother = parent.childNodes[selection.focusNodeIndex];
		ximElement.value = [selection.ranges.text];


		return [ximElement,brother];
    	};

    	this.removeElementWrapper = function(ximElement) {
    		// Returns a function reference that will be used in events calls (ie: ContentMenu)
    		return function() {
    			this.removeElement(ximElement);
    		}.bind(this);
    	};

	/**
	 * ximElement is the node we want to remove.
	 */
	this.removeElement = function(ximElement) {
		if(!ximElement.isRemovable())
			return;

		if (ximElement.schemaNode.type.contains('apply')) {
			ximElement.disApply();
		}

		this.setActionDescription(_('Remove element'));
		this.editor.getXimDocument().removeChild(ximElement);
		this.editor.selNode = ximElement.getFirstSelectableParent(this.editor.nodeId);
    		this.editor.updateEditor({caller: this});
	};

    	this.disApplyElementWrapper = function(ximElement) {
    		// Returns a function reference that will be used in events calls (ie: ContentMenu)
    		return function() {
    			this.disApplyElement(ximElement);
    		}.bind(this);
    	};

	/**
	 * ximElement is the node we want to disApply.
	 */
	this.disApplyElement = function(ximElement) {
		if(!ximElement.isApplyable())
			return;

		ximElement.disApply(true);

		this.setActionDescription(_('Disapply element'));
		this.editor.getXimDocument().removeChild(ximElement);
		this.editor.selNode = ximElement.getFirstSelectableParent(this.editor.nodeId);
    		this.editor.updateEditor({caller: this});
	};

    	this.cutElementWrapper = function(ximElement) {
    		return function() {
    			return this.cutElement(ximElement);
    		}.bind(this);
    	};

	this.cutElement = function(ximElement) {
		this.editor.clipboard = this.editor.getXimDocument().cloneElement(ximElement);
		this.removeElement(ximElement);
		return this.editor.clipboard;
	};

    	this.copyElementWrapper = function(ximElement) {
    		return function() {
    			return this.copyElement(ximElement);
    		}.bind(this);
    	};

	this.copyElement = function(ximElement) {
		this.editor.clipboard = this.editor.getXimDocument().cloneElement(ximElement);
		return this.editor.clipboard;
	};

    	this.pasteElementWrapper = function(parent, brother) {
    		return function() {
    			return this.pasteElement(parent, brother);
    		}.bind(this);
    	};

	this.pasteElement = function(parent, brother) {
		if (!this.editor.clipboard) return;
		var newElement = this.editor.getXimDocument().cloneElement(this.editor.clipboard);
		newElement = this.editor.getXimDocument().insertAfter(newElement, parent, brother);
		this.setActionDescription(_('Paste element'));
		this.editor.updateEditor({caller: this});
		return newElement;
	};

};

XimdocTool.prototype = new KupuTool();

