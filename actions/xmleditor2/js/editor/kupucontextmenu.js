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
 *  @version $Revision: 8528 $
 */


/*****************************************************************************
 *
 * Copyright (c) 2003-2005 Kupu Contributors. All rights reserved.
 *
 * This software is distributed under the terms of the Kupu
 * License. See LICENSE.txt for license text. For a list of Kupu
 * Contributors see CREDITS.txt.
 *
 *****************************************************************************/

// $Id: kupucontextmenu.js 8528 2013-03-06 09:56:03Z aperez $


//----------------------------------------------------------------------------
// ContextMenu
//----------------------------------------------------------------------------

contextmenu_selNode = null;
contextmenu_selection = null;
function ContextMenu() {
    /* the contextmenu */
    this.contextmenu = null;
    this.seperator = 1;

    this.initialize = function(editor) {
        /* setting the event handlers and such */
        this.editor = editor;
        // needs some work since it won't work for more than one editor
        addEventHandler(editor.getInnerDocument(), "contextmenu", this.createContextMenu, this);
        addEventHandler(editor.getInnerDocument(), "focus", this.hideContextMenu, this);
        addEventHandler(document, "focus", this.hideContextMenu, this);
        addEventHandler(editor.getInnerDocument(), "mousedown", this.hideContextMenu, this);
        addEventHandler(document, "mousedown", this.hideContextMenu, this);
    };

    this.createContextMenu = function(event) {
        /* Create and show the context menu

            The method will ask all tools for any (optional) elements they
            want to add the menu and when done render it
        */
        if (this.editor.getBrowserName() == 'IE') {
        	event.cancelBubble = true;
            this.editor._saveSelection();
        } else {
	        event.returnValue = false;
	        if (event.preventDefault)
	        	event.preventDefault();
			event.stopPropagation();
        }
        event.returnValue = false;
        // somehow Mozilla on Windows seems to generate the oncontextmenu event
        // several times on each rightclick, here's a workaround
        if (this.editor.getBrowserName() == 'Mozilla' && this.contextmenu) {
            return false;
        };
        var selNode = this.editor.getBrowserName() == 'IE'
        	? event.srcElement
        	: this.editor.getSelectedNode();

			contextmenu_selNode = selNode;
			contextmenu_selection =  this.getSelection();

			this.hideContextMenu();

        var elements = new Array();
        for (var id in this.editor.tools) {
            var tool = this.editor.tools[id];
            // alas, some people seem to want backward compatibility ;)
            if (tool.createContextMenuElements) {
                var els = tool.createContextMenuElements(selNode, event);
                elements = elements.concat(els);
            };
        };
        // remove the last seperator
        this._createNewContextMenu(elements, event);
        this.last_event = event;

        return false;
    };

    this.hideContextMenu = function(event) {

		 /* remove the context menu from view */
        if (this.contextmenu) {

            try {
					var iframe = this.editor.getDocument().getEditable();
					var left = event.clientX;
					var top = event.clientY;

					var currnode = iframe;
					if (this.editor.getBrowserName() == 'IE') {
						while (currnode) {
							left += currnode.offsetLeft + currnode.clientLeft;
							top += currnode.offsetTop + currnode.clientTop;
							currnode = currnode.offsetParent;
						};
					} else {
						while (currnode) {
							left += currnode.offsetLeft;
							top += currnode.offsetTop;
							currnode = currnode.offsetParent;
						};
					};


					var menu_left = $(this.contextmenu).position().left;
					var menu_top = $(this.contextmenu).position().top;
					var menu_width = $(this.contextmenu).width();
					var menu_height = $(this.contextmenu).height();
					var max_posX = menu_left +  menu_width + 15 /* scrollbar */;
					var max_posY = menu_top +  menu_height;

					if(  left > max_posX || left < menu_left || top < menu_top || top >  max_posY ||  event.type != "mousedown" ) //it is not scroll
                window.document.getElementsByTagName('body')[0].removeChild(this.contextmenu);
					else
						return null;
            } catch (e) {
                // after some commands, the contextmenu will be removed by
                // the browser, ignore those cases
            };
            this.contextmenu = null;
        };
    };

    this._createNewContextMenu = function(elements, event) {

        /* adding the elements to the contextmenu and showing it */
        var doc = window.document;
        var menu = doc.createElement('div');
        menu.contentEditable = false;
        menu.designMode = 'Off';
        this._setMenuStyle(menu);
        for (var i=0; i < elements.length; i++) {
            var element = elements[i];
            if (element !== this.seperator && element.label.indexOf('-----') == -1) {
                var div = doc.createElement('div');
                div.style.width = '100%';
                var label = doc.createTextNode('\u00a0' + element.label);
                div.appendChild(label);
                menu.appendChild(div);
                // setting a reference to the div on the element
                element.element = div;
                addEventHandler(div, "mousedown", element.action, element.context);
                // On Firefox there is a problem with this event, it hides the menu on the same click that initialy showed it
                addEventHandler(div, "mouseup", this.hideContextMenu, this);
            } else {
                var hr = doc.createElement('hr');
                menu.appendChild(hr);
            };
        };

        // now moving the menu to the right position
        var iframe = this.editor.getDocument().getEditable();

        var left = event.clientX;
        var top = event.clientY;

        var currnode = iframe;
        if (this.editor.getBrowserName() == 'IE') {
            while (currnode) {
                left += currnode.offsetLeft + currnode.clientLeft;
                top += currnode.offsetTop + currnode.clientTop;
                currnode = currnode.offsetParent;
            };
        } else {
            while (currnode) {
                left += currnode.offsetLeft;
                top += currnode.offsetTop;
                currnode = currnode.offsetParent;
            };
        };

		  var position_click = top;


        menu.style.left = left + 'px';
        menu.style.top = top + 'px';
        menu.style.visibility = 'visible';


        addEventHandler(menu, 'focus', function() {this.blur}, menu)
        doc.getElementsByTagName('body')[0].appendChild(menu);

        var wdim = this.getWindowSize();
        var mh = $(menu).height();

		  //If click in middle botton
		  var middle_win = wdim.h/2;
		  if(position_click > middle_win) {
			  var space_for_menu =  position_click - 50; /* others: toolbar */
			  if(mh > space_for_menu ) { //we need scroll
				  mh = space_for_menu;
				  $(menu).height(space_for_menu);
				  menu.style.top =  ( position_click - mh ) +  'px';
				  menu.style.overflowY = "scroll";
			  }else {
				  menu.style.top =  ( position_click - mh ) +  'px';
			  }
		  }else { //open botton menu
			  var space_for_menu =  wdim.h - position_click - 30; /* others: not paste final windows */
			  menu.style.top =   position_click  +  'px';
			  if(mh > space_for_menu ) { //we need scroll
					mh = space_for_menu;
					$(menu).height(space_for_menu);
					menu.style.overflowY = "scroll";
			  }
		  }


        this.contextmenu = menu;
    };

    this._setMenuStyle = function(menu) {
        /* setting the styles for the menu

            to change the menu style, override this method
        */
        $(menu).addClass('context-menu');
    };

    this._showOriginalMenu = function(event) {
        window.document.dispatchEvent(this._last_event);
    };

    this.getWindowSize = function() {
    	var dim = {
    		w: document.compatMode=='CSS1Compat' && !window.opera ? document.documentElement.clientWidth : document.body.clientWidth,
    		h: document.compatMode=='CSS1Compat' && !window.opera ? document.documentElement.clientHeight : document.body.clientHeight
    	}
    	return dim;
    }


    this.getSelection = function() {

		 var selNode = this.editor.selNode;
		 var selection = this.editor.getSelection();

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

		 var it = new DOMNodeIterator(selNode, 3);
		 var child = null;
		 var childPos = -1;
		 while (it.hasNext() && child !== startNode) {
			 child = it.next();
			 childPos++;
		 }

		 /*if (selNode.childNodes[0] && selNode.childNodes[0].nodeType != 3) {
		  / / C*orrects bug with apply elements when they are at the start of the string
		  childPos++;
		 }*/

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
};

function ContextMenuElement(label, action, context) {
    /* context menu element struct

        should be returned (optionally in a list) by the tools'
        createContextMenuElements methods
    */
    this.label = label; // the text shown in the menu
    this.action = action; // a reference to the method that should be called
    this.context = context; // a reference to the object on which the method
                            //  is defined
    this.element = null; // the contextmenu machinery will add a reference
                            // to the element here

    this.changeOverStyle = function(event) {
        /* set the background of the element to 'mouseover' style

            override only for the prototype, not for individual elements
            so every element looks the same
        */
        this.element.style.backgroundColor = 'blue';
        this.element.style.color = 'white';
    };

    this.changeNormalStyle = function(event) {
        /* set the background of the element back to 'normal' style

            override only for the prototype, not for individual elements
            so every element looks the same
        */
        this.element.style.backgroundColor = 'white';
        this.element.style.color = 'black';
    };
};

