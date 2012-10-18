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
 *  @version $Revision: 8084 $
 */





/**
 * A tool that knows how to add menu items to the context menu depending on the selected element
 */
function XimdocContextMenuTool() {

    	this.initialize = function(editor) {
        	this.editor = editor;
        	this.editor.logMessage(_('XimdocContextMenuTool tool initialized'));
    	};

    	this.createContextMenuElements = function(selNode, event) {
    	/**
    	 * Allowed elements:
    	 * 1.	Elements of the same type than the selected one.
    	 * 2.	Elements of the same type than the first descendent of the selected one.
    	 * 3.	Elements of the same type than the next brother of the selected one.
    	 *
    	 * TODO: Also, it would be needed to verify the cardinality with the schema... in future
    	 *
    	 * a) It should be kept the posibility of creating brothers of the same nodetype than the current one, but allowing to insert before or later.
    	 * b) The children insertion should be allowed checking against the schema.
    	 * c) The brother insertion should be allowed checking against the schema, otherwise, we should be as permissive as possible about order and cardinality.
    	 */

		var ret = new Array();

		if (this.editor.ximElement) {

			var rngElement = this.editor.ximElement.schemaNode;
			var rngParent = null;
			if (this.editor.ximParent) {
				rngParent = this.editor.ximParent.schemaNode;
			} else if (!this.editor.ximElement.isRoot) {
				return;
			}

			// Cutting, copying, pasting items
			this.createEditItems(ret, rngElement, rngParent);

			// If some text is selected: show "apply" type items.
			if (this.editor.selectedTextLength > 0) {
				// Create 'Apply' context menu elements.
				this.createApplyItems(ret, rngElement, rngParent);

			} else {

				// Creating 'Correct Spelling' context menu elements.
				this.createSpellcheckItems(ret, rngElement, rngParent);

				// Creating 'Annotation' context menu elements.
				this.createAnnotationItems(ret, rngElement, rngParent);

				// Creating 'Remove' context menu element.
				this.createRemoveItems(ret, rngElement, rngParent);

				// Creating 'Add Sibling' context menu elements.
				this.createSiblingItems(ret, rngElement, rngParent);

				// Creating 'Add Child' context menu elements.
				this.createChildItems(ret, rngElement, rngParent);

				// Creating 'ximlets' context menu elements.
				this.createXimletItems(ret, rngElement, rngParent, this.editor.ximElement.uid);

				// Creating 'dis-apply' context menu element.
				this.createDisapplyItems(ret, rngElement, rngParent);
			}
		}

		return ret;
    	};

	// Cutting, copying, pasting items
    	this.createEditItems = function(menu, rngElement, rngParent) {
    		if(this.editor.ximElement.isRoot) return;

		if(this.editor.ximElement.canBeCopied()) {
			menu.push(new ContextMenuElement(
							_('Cut') + ' ' + this.editor.ximElement.tagName,
							this.cutElementWrapper(this.editor.ximElement),
							this
							));
			menu.push(new ContextMenuElement(
							_('Copy') + ' ' + this.editor.ximElement.tagName,
							this.copyElementWrapper(this.editor.ximElement),
							this
							));
		}

		if (this.editor.clipboard) {
			menu.push(new ContextMenuElement(
							_('Paste') + ' ' + this.editor.clipboard.tagName,
							this.pasteElementWrapper(this.editor.ximParent, this.editor.ximElement),
							this
							));
		}

    	};

	// Creating 'Apply' context menu elements.
    	this.createApplyItems = function(menu, rngElement, rngParent) {
		var i = 0;
		var count = rngElement.childNodes.length;
		while (i < count) {
			if (i == 0) menu.push(new ContextMenuElement(_('-----'), function(){}, this));
			childElement = rngElement.childNodes[i];
			if (childElement.type.contains('apply')) {
				childName = childElement.tagName;
				menu.push(new ContextMenuElement(_('Apply:') + ' ' + childName, this.createElementWrapper(childName, this.editor.ximElement, null), this));
			}
			i++;
		}
    	};

	// Create 'Correct Spelling' context menu elements.
    	this.createSpellcheckItems = function(menu, rngElement, rngParent) {
		var clickedNode = this.editor.getClickedNode();
		var clickedNodeParent = this.editor.getClickedNode().parentNode;
		var clickedNodeParentText = clickedNodeParent.innerHTML;
		var owner = clickedNode.getAttribute('owner');
		if (owner == 'spellchecker') {
			menu.push(new ContextMenuElement(_('--------------'), function(){}, this));
			menu.push(new ContextMenuElement(_('Spell checking'), function(){}, this));
			menu.push(new ContextMenuElement(_('--------------'), function(){}, this));

			var word = clickedNode.textContent;
			count = this.editor.tools.ximdocspellcheckertool._spellCheckWordsArray.length;
			var checker = null;
			for (var i = 0; i < count; i ++) {
				if (this.editor.tools.ximdocspellcheckertool._spellCheckWordsArray[i].nodeValue == word) {
					checker = this.editor.tools.ximdocspellcheckertool._spellCheckWordsArray[i];
					break;
				}
			}

			menu.push(new ContextMenuElement(_('Leave word') + ' ' + word + ' ' + _('as it.'), this.editor.tools.ximdocspellcheckertool.replaceWord(word, word, clickedNodeParentText, clickedNodeParent, 'leave'), this.editor.tools.ximdocspellcheckertool));

			var suggestion = checker.parentNode.nextSibling;
			while(suggestion) {
				menu.push(new ContextMenuElement(_('Replace') + ' ' + word + ' ' + _('by') + ' ' + suggestion.textContent, this.editor.tools.ximdocspellcheckertool.replaceWord(word, suggestion, clickedNodeParentText, clickedNodeParent), this.editor.tools.ximdocspellcheckertool));
				suggestion = suggestion.nextSibling;
			}
		}
    	};

	// Create 'Annotation' context menu elements.
    	this.createAnnotationItems = function(menu, rngElement, rngParent) {
		//TODO: fundir con spellchecker?
		var clickedNode = this.editor.getClickedNode();
		var clickedNodeParent = this.editor.getClickedNode().parentNode;
		var clickedNodeParentText = clickedNodeParent.innerHTML;
		var owner = clickedNode.getAttribute('owner');
		if (owner == 'annotator') {
			menu.push(new ContextMenuElement(_('--------------'), function(){}, this));
			menu.push(new ContextMenuElement(_('ANNOTATION MENU'), function(){}, this));
			menu.push(new ContextMenuElement(_('--------------'), function(){}, this));

			var word = clickedNode.textContent;
			count = this.editor.tools.ximdocannotationtool._annotationWordsArray.length;
			var checker = null;
			for (var i = 0; i < count; i ++) {
				if (this.editor.tools.ximdocannotationtool._annotationWordsArray[i].nodeValue == word) {
					checker = this.editor.tools.ximdocannotationtool._annotationWordsArray[i];
					break;
				}
			}

			// TODO: Ofrecer info e interacción con las anotaciones de la palabra word. See this.createSpellcheckItems
			if(checker) {
				menu.push(new ContextMenuElement(_('Annotate word ') + ' \'' + word + '\'', this.editor.tools.ximdocannotationtool.annotateWord(word)));
			}
		}
    	};

	// Create 'Remove' context menu element.
    	this.createRemoveItems = function(menu, rngElement, rngParent) {
    		if (this.editor.ximElement.isRoot || !this.editor.ximElement.isRemovable()) return;
		menu.push(new ContextMenuElement(_('-----'), function(){}, this));
		menu.push(new ContextMenuElement(_('Remove') + ': ' + this.editor.ximElement.tagName, this.removeElementWrapper(this.editor.ximElement), this));
    	};

	// Create 'Dis-Apply' context menu element.
    	this.createDisapplyItems = function(menu, rngElement, rngParent) {
    		if (this.editor.ximElement.isRoot || !this.editor.ximElement.isApplyable()) return;
		menu.push(new ContextMenuElement(_('-----'), function(){}, this));
		menu.push(new ContextMenuElement(_('Do not apply:') + ' ' + this.editor.ximElement.tagName, this.disApplyElementWrapper(this.editor.ximElement), this));
    	};

	// Create 'Add Sibling' context menu elements.
    	this.createSiblingItems = function(menu, rngElement, rngParent) {
    		if (this.editor.ximElement.isRoot) return;
		var i = 0;
		var count = rngParent.childNodes.length;
		if(count == 0)
			return;
		menu.push(new ContextMenuElement(_('-----'), function(){}, this));
		while (i < count) {
			var childElement = rngParent.childNodes[i];
			var childName = childElement.tagName;
			var button = this.editor.tools[childName + '_rngbutton'];
			menu.push(new ContextMenuElement(_('Add sibling:') + ' ' + childName, button.commandfunc, button));
			i++;
		}
    	};

	// Create 'Add Child' context menu elements.
    	this.createChildItems = function(menu, rngElement, rngParent) {
		var i = 0;
		var count = rngElement.childNodes.length;
		if(count == 0)
			return;
		menu.push(new ContextMenuElement(_('-----'), function(){}, this));
		while (i < count) {
			childElement = rngElement.childNodes[i];
			childName = childElement.tagName;
			var button = this.editor.tools[childName + '_rngbutton'];
			menu.push(new ContextMenuElement(_('Add child:') + ' ' + childName, button.addchildfunc, button));
			i++;
		}
    	};

	// Create 'ximlets' context menu elements.
    	this.createXimletItems = function(menu, rngElement, rngParent, uid) {
		if(!rngElement.type.contains('ximlet'))
			return;
		var ximElement = this.editor.getXimDocument().getElement(uid);
		//var button = this.editor.tools['ximletdrawerbutton'];
		menu.push(new ContextMenuElement(_('-----'), function(){}, this));
		menu.push(new ContextMenuElement(_('Toggle') + ' ' + _('ximlet'), this.editor.tools['ximlettool'].toggleXimlet(uid)));
		//menu.push(new ContextMenuElement(_('Edit') + ' ' + _('ximlet') + ' ' + _('content'), this.editor.tools['ximlettool'].openEditWindow(ximElement)));
		menu.push(new ContextMenuElement(_('Edit ximlet content'), this.editor.tools['ximlettool'].openEditWindow(ximElement)));
		//if(!this.editor.ximElement.isSectionXimlet())
		//	menu.push(new ContextMenuElement(_('Edit') + ' ' + _('ximlet') + ' ' + _('id'), button.commandfunc, button));
	};
};

XimdocContextMenuTool.prototype = new XimdocTool();

