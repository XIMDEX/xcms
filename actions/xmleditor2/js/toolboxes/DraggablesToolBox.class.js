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
 *  @version $Revision: 8541 $
 */


/**
 * This class allows elements to be dragged/droped
 */
function DraggablesToolBox() {

	this._body = null;
	this._dragHandler = null;
	this._dragHandlerParent = null;
	this._dragging = false;
	this.lastDrag = { uid: null, time: null};
	this.lastDragParent =  { uid: null, time: null};

	this.initialize = function(tool, editor) {
    		this.tool = tool;
        	this.editor = editor;
        	this._body = editor.getBody();
		this.afterUpdateContent(null);
        	this.editor.logMessage(_('DraggablesToolBox tool initialized'));
	};

	this._elementIsallowed = function(element) {
		if (!element['rngElement']) return false;
		var rngElement = element.rngElement;
		var allowed = (!rngElement.type.contains('apply')) && (rngElement.tagName != 'docxap');
		//allowed = allowed && element.getAttribute('editable') != 'no';
		allowed = allowed && element.ximElement.isDroppable(this.editor.nodeId);
		return allowed;
	};

	/**
	 * Doing the highlight on the selected element
	 */
	this.updateState = function(options) {
		if (!options.selNode || (options.event && !['click', 'keyup','mouseenter'].contains(options.event.type)))
			return;
		this.onMouseOver(options);
		this.onMouseOver({selNode: this.editor.getParentWithUID(options.selNode)}, true);
	};

	this.onMouseOver = function(options, is_parent) {
		if (this._dragging) return;

		var handler = (is_parent) ? this._dragHandlerParent : this._dragHandler;

		var description = options.selNode.ximElement.schemaNode.description;
		var uid = options.selNode.getAttribute('uid');
		var pos = $(options.selNode).offset();
		var top = pos.top - $(handler).height();
		$(handler).text(description);
		var left = pos.left - $(handler).width();
		top = (top >= 0) ? top : 0;
		left = (left >= 0) ? left : 0;

		if(uid != this.lastDrag.uid && uid != this.lastDragParent.uid) {
			if(is_parent) {
				clearInterval(this.lastDragParent.time);
			}else {
				clearInterval(this.lastDrag.time);
			}

			handler.css({
				top: top + 'px',
				left: left + 'px'
			}).show();
		}else {
			return ;
		}

		handler[0].setAttribute('id', options.selNode.getAttribute('uid'));
		handler[0].draggableElement = options.selNode;


		if(is_parent) {
			this.lastDragParent.uid = uid;
			this.lastDragParent.time = setTimeout(function() {
				this.hide();
			}.bind(handler), 3*1000 /* 3 seconds */ );

			var tolerance = 20;
			var dist = Math.abs($(this._dragHandler).position().top - $(this._dragHandlerParent).position().top);
			var direction = (($(this._dragHandler).position().top - $(this._dragHandlerParent).position().top) <= 0) ? -1 : 1;
			var correction = (tolerance - dist) * direction;
			if(dist < tolerance)
				$(this._dragHandler).css({top: $(this._dragHandler).position().top + correction});
			return;
		}else {
			this.lastDrag.uid = uid;
			this.lastDrag.time = setTimeout(function() {
				this.hide();
			}.bind(handler), 3*1000 /* 3 seconds */ );
		}


		// deleting droppable and draggable elements
		this.beforeUpdateContent();
		// defining droppable elements
		this.editor.elements.each(function(index, elem) {
			if (/*this._elementIsallowed(elem) && */options.selNode.ximElement.schemaNode.isAllowedNearBy(elem.ximElement.schemaNode)) {
				$(elem).droppable({
					accept: '.kupu-draggable-handler',
					tolerance: 'touch',
					greedy: true,	// TODO: Test this option with other browsers than Firefox 3
					drop: function(event, ui) {
						this.onDrop(event, ui);
					}.bind(this)
				});
			}
		}.bind(this));
	};

	this.onMouseOut = function(options) {

		return;

		if (!this._elementIsallowed(options.selNode)) return;

		if (this._dragging) return;
		var uid = options.selNode.getAttribute('uid');

		var hide = true;
		try {
			var related = options.event.relatedTarget;
			hide = !$(related).hasClass('ui-draggable');
		} catch (e) {
			//console.error(e);
		}
		if (hide) {
			this._dragHandler[0].setAttribute('id', '');
			this._dragHandler[0].draggableElement = null;
			this._dragHandler.hide();
		}
	};

	this.beforeUpdateContent = function(options) {
		// Destroying draggable and droppable elements
//		var selector = '.ui-draggable:not(.ui-dialog), .ui-droppable:not(.ui-dialog)';
		var selector = '.xedit-rngelement';
		$(selector, this._body).each(function(index, elem) {
			if ($(elem).data("draggable"))
				$(elem).draggable('destroy');
			if ($(elem).data("droppable"))
				$(elem).droppable('destroy');
		});
	};

	this.afterUpdateContent = function(options) {

		// Important!
		this._body = this.editor.getBody();

		// Creating the draggable handler
		var draggable = $('<div id="" class="kupu-draggable-handler"></div>', this._body);
		var draggable_parent = $('<div id="" class="kupu-draggable-parent-handler kupu-draggable-handler"></div>', this._body);

		draggable[0].draggableElement = null;
		draggable_parent[0].draggableElement = null;
		$(this._body).append(draggable);
		$(this._body).append(draggable_parent);

       	$(draggable).draggable({
       		//iframeFix: true,
       		addClasses: false,
       		zIndex: 1000,
       		cursor: 'pointer',
       		opacity: 0.7,
       		scroll: true, // Not working inside an iframe
       		helper: 'clone',
       		document: this.editor.getInnerDocument(),
       		start: function(event, ui) {
       			this.onStart(event, ui);
       		}.bind(this),
       		/*drag: function(event, ui) {
       			this.onDrag(event, ui);
       		}.bind(this),*/
       		stop: function(event, ui) {
       			this.onStop(event, ui);
       		}.bind(this)
       	});

       	$(draggable_parent).draggable({
       		//iframeFix: true,
       		addClasses: false,
       		zIndex: 1000,
       		cursor: 'pointer',
       		opacity: 0.7,
       		scroll: true, // Not working inside an iframe
       		helper: 'clone',
       		document: this.editor.getInnerDocument(),
       		start: function(event, ui) {
       			this.onStart(event, ui);
       		}.bind(this),
       		/*drag: function(event, ui) {
       			this.onDrag(event, ui);
       		}.bind(this),*/
       		stop: function(event, ui) {
       			this.onStop(event, ui);
       		}.bind(this)
       	});

       	this._dragHandler = draggable;
       	this._dragHandlerParent = draggable_parent;

	};

	this.onStart = function(event, ui) {
		//console.log(event, ui, this);
		this._dragging = true;
		var uid = ui.helper[0].id;
		$('.xedit-rngelement[uid!='+uid+']', this._body).each(function(index, elem) {
			elem._beforeDragBorder = $(elem).css('border') || '';

			if (this.editor.getXimDocument().getElement(uid).schemaNode.isAllowedNearBy(elem.ximElement.schemaNode)) {
				$(elem).css({
					border: '1px dashed #006600'
				});
			}
		}.bind(this));
	};

	this.onDrag = function(event, ui) {
		//console.log(event, ui, this);
		this._dragging = true;
	};

	this.onStop = function(event, ui) {
		this._dragging = false;
		$('.xedit-rngelement', this._body).each(
			function(index, elem) {
				$(elem).css({
					border: (elem._beforeDragBorder || '')
				});
			}
		);
	};

	this.onDrop = function(event, ui) {

		this._dragging = false;

		// TODO: Fix tables here, this method is fired three times, once per table tag.

		var droppable = event.target;
		var draggable = ui.helper[0];

		var droppableUID = droppable.getAttribute('uid');
		var draggableUID = draggable.getAttribute('id');

		// Don't drop under the same element
		if (droppableUID == draggableUID) return;

		if ($('[uid="%s"] [uid="%s"]'.printf(draggableUID, droppableUID), this._body).length > 0) {
			// Parent dropped on a child, we don't want evil circular references
			return;
		}

		var ximElementDrag = this.editor.getXimDocument().getElement(draggableUID);
		var ximElementDrop = this.editor.getXimDocument().getElement(droppableUID);

		// You can't drag the root element
		if (Object.isEmpty(ximElementDrag.parentNode)) return;

		var elements = {
			droppable: droppable,
			draggable: draggable,
			ximElementDrag: ximElementDrag,
			ximElementOldParent: ximElementDrag.parentNode,
			ximElementOldPreviousSibling: ximElementDrag.previousSibling,
			ximElementOldNextSibling: ximElementDrag.nextSibling,
			ximElementDrop: ximElementDrop,
			ximElementNewParent: ximElementDrop.parentNode
		};

		var isCopy = event.shiftKey;
		if (!isCopy) {
			this._moveElement(elements);
		} else {
			this._copyElement(elements);
		}

		this.editor.getXimDocument().validateXML(function(valid, msg) {
	    	var blinkColor = '#43A1A2';//'#339900';
	    	var numOfBlinks = null;
	    	var blinkDuration = null;
	    	if (!valid) {
				this.editor.alert(msg);
				if (!isCopy) {
					this._undoMove(elements);
				} else {
					this._undoCopy(elements);
				}
				blinkColor = '#AA0000';
		    	numOfBlinks = 3;
		    	blinkDuration = 1000;
	    	}
	    	this.setActionDescription(_('Drag and drop'));
			this.editor.selNode = elements.clonedXimElement;
			this.editor.updateEditor({caller: this, callback: function () {this._blinkElement(elements.clonedXimElement.uid, blinkColor, numOfBlinks, blinkDuration, function () {});}.bind(this)});

	    }.bind(this));
	};

	this._blinkElement = function(elementUid, blinkColor, numOfBlinks, blinkDuration, callback) {

		if(!elementUid) return;
		if(!numOfBlinks) numOfBlinks = 1;
		if(!blinkDuration) blinkDuration = 1000;
		var blinkCount = numOfBlinks * 2;
		var duration = blinkCount * blinkDuration;

		$('[uid="' + elementUid + '"]', this._body).each(
			function(index, elem) {
				var oldBG = $(elem).css('background-color');
				console.log(oldBG);
				$(elem).animate({backgroundColor: blinkColor},blinkDuration, 
				function(){
					$(elem).animate({backgroundColor: oldBG},blinkDuration,
						function(){					 					
							$(elem).css({'background-color':oldBG});
						});
					}
				);
				/*var counter = blinkCount;
				var oldBG = $(elem).css('background-color');
				var oldBorder = $(elem).css('border');

				$(elem).css({
					'background-color': blinkColor,
					'border': '1px solid black'
				});
				do {
					$(elem)['fade' + (counter % 2 == 0 ? 'Out' : 'In')](blinkDuration);
					counter --;
				} while (counter > 0);
				setTimeout(function() {
						$(elem).css({
							'background-color': oldBG,
							'border': oldBorder
						});
						callback();
					},
					duration
				);*/
			}
		);
	}

	this._restoreChanges = function(ximElementDrag, ximElementOldParent, ximElementOldPreviousSibling, ximElementOldNextSibling) {
	};

	this._moveElement = function(e) {

		var ximdoc = this.editor.getXimDocument();

		// Modifying the ximdoc model
		ximdoc.removeChild(e.ximElementDrag);
		ximdoc.insertAfter(e.ximElementDrag, e.ximElementNewParent, e.ximElementDrop);
		//ximdoc.appendChild(e.ximElementDrag, e.ximElementNewParent);
		e.clonedXimElement = e.ximElementDrag;

		// Using copy/paste tools reduce the performance
		//this.tool.cutElement(e.ximElementDrag);
		//e.clonedXimElement = this.tool.pasteElement(e.ximElementNewParent, e.ximElementDrop);
	};

	this._undoMove = function(e) {
		//console.log(ximElementDrag, ximElementRoot, ximElementDrop);
		this.editor.getXimDocument().removeChild(e.clonedXimElement);
		if (e.ximElementOldPreviousSibling) {
			this.editor.getXimDocument().insertAfter(e.clonedXimElement, e.ximElementOldParent, e.ximElementOldPreviousSibling);
		} else if (e.ximElementOldNextSibling) {
			this.editor.getXimDocument().insertBefore(e.clonedXimElement, e.ximElementOldParent, e.ximElementOldNextSibling);
		} else {
			this.editor.getXimDocument().appendChild(e.clonedXimElement, e.ximElementOldParent);
		}
	};

	this._copyElement = function(e) {

		var ximdoc = this.editor.getXimDocument();
   		var ximElement = ximdoc.cloneElement(e.ximElementDrag, e.ximElementDrag.parentNode);

		// Modifying the ximdoc model
		ximdoc.removeChild(ximElement);
		ximdoc.insertAfter(ximElement, e.ximElementNewParent, e.ximElementDrop);
		e.clonedXimElement = ximElement;

		// Using copy/paste tools reduce the performance
		//this.tool.copyElement(e.ximElementDrag);
		//e.clonedXimElement = this.tool.pasteElement(e.ximElementNewParent, e.ximElementDrop);
	};

	this._undoCopy = function(e) {
		//console.warn(e.clonedXimElement);
		this.editor.getXimDocument().removeChild(e.clonedXimElement);
	};

};

DraggablesToolBox.prototype = new XimdocToolBox();



// Extends jQuery "draggable ui" to allow drag&drop inside the iframe.
// See "this.options.document"...
(function($) {

	$.extend($.ui.draggable.prototype, {
		_mouseDown: function(e) {
			// we may have missed mouseup (out of window)
			(this._mouseStarted && this._mouseUp(e));

			this._mouseDownEvent = e;

			var self = this,
				btnIsLeft = (e.which == 1),
				elIsCancel = (typeof this.options.cancel == "string" ? $(e.target).parents().add(e.target).filter(this.options.cancel).length : false);
			if (!btnIsLeft || elIsCancel || !this._mouseCapture(e)) {
				return true;
			}

			this.mouseDelayMet = !this.options.delay;
			if (!this.mouseDelayMet) {
				this._mouseDelayTimer = setTimeout(function() {
					self.mouseDelayMet = true;
				}, this.options.delay);
			}

			if (this._mouseDistanceMet(e) && this._mouseDelayMet(e)) {
				this._mouseStarted = (this._mouseStart(e) !== false);
				if (!this._mouseStarted) {
					e.preventDefault();
					return true;
				}
			}

			// these delegates are required to keep context
			this._mouseMoveDelegate = function(e) {
				return self._mouseMove(e);
			};
			this._mouseUpDelegate = function(e) {
				return self._mouseUp(e);
			};
			$(this.options.document)
				.bind('mousemove.'+this.widgetName, this._mouseMoveDelegate)
				.bind('mouseup.'+this.widgetName, this._mouseUpDelegate);

			return false;
		},

		_mouseUp: function(e) {
			$(this.options.document)
				.unbind('mousemove.'+this.widgetName, this._mouseMoveDelegate)
				.unbind('mouseup.'+this.widgetName, this._mouseUpDelegate);

			if (this._mouseStarted) {
				this._mouseStarted = false;
				this._mouseStop(e);
			}

			return false;
		},

		createHelper: function() {
			var o = this.options;
			var helper = null;

			// Fix a crash in IE when this.element.clone() is called
			// TODO: Try jQuery 1.3 ...
			try {
				helper = $.isFunction(o.helper)
					? $(o.helper.apply(this.element[0], [e]))
					: (
						o.helper == 'clone'
						? this.element.clone()
						: this.element
					);
			} catch(e) {
				helper = this.element;
			}

			if (!helper.parents('body').length)
				helper.appendTo((o.appendTo == 'parent' ? this.element[0].parentNode : o.appendTo));
			if (helper[0] != this.element[0] && !(/(fixed|absolute)/).test(helper.css("position")))
				helper.css("position","absolute");

			return helper;
		}

	});

	$.extend($.ui.draggable.defaults, {
		document: document
	});

})(jQuery);
