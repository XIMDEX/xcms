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
 * Function which detects Ximdex Ximlets and calls ximletdrawer for creating/editing them
 */

function ximletTool() {
    /* Adding and updating ximlet references */

	//executed when the tool is initialized
	this.initialize = function(editor) {
        	this.editor = editor;
        	this.editor.logMessage(_('Ximlet tool initialized'));
    	};

	//updating the ximlet IdNode, and then the editor
    	this.updateXimlet = function (ximletElement, ximletNodeId) {
        	ximletElement.ximElement.setMacroId(ximletNodeId);
        	ximletElement.ximElement.refresh(this._setXimletStyle.bind(this));
        	this.editor.reloadXml();
    	};

    	this.beforeSave = function () {
    		this._removeXimletsContent();
    	}

    	this.updateState = function (options) {
//		if (options.event && options.event.type != 'dblclick') return;
    	}

	//executed before update the iframe content
    	this.beforeUpdateContent = function(options) {
		//finding ximlets in the document
		$('[uid]', options.xslResult).each(
			function(index, elem) {
				var uid = elem.getAttribute('uid');
				try {
					var ximElement = this.editor.getXimDocument().getElement(uid);
					var rngElement = this.editor.getXimDocument().getElement(uid).schemaNode;
					if (rngElement.type.contains('ximlet')) {
		    				this._generateXimletElement(elem, options.xslResult);
					}
				} catch(e) {
					console.error(_("ERROR while finding Ximlets. "),rngElement);
				}
			}.bind(this)
		);
		return;
    	};

	//executed after the iframe content is updated
    	this.afterUpdateContent = function(options) {
		this.initialize(options.editor);
		var frameBody = this.editor.getBody();

		//finding ximlets in the document
		$('div.kupu-ximlet-box', frameBody).each(
			function(index, elem) {
				if (elem.rngElement.type.contains('ximlet')) {
			    		this._initializeXimletElement(elem);
				}
			}.bind(this) //change the context to the tool context
		);
		return;
    	};

	//called from beforeUpdateContent. Creates the html ximlet structure on the editor
    	this._generateXimletElement = function(ximlet, domdoc) {

		var uid = ximlet.getAttribute('uid');
		var ximElement = this.editor.getXimDocument().getElement(uid);

		var newXimlet = domdoc.createElement('div');
		newXimlet.setAttribute('class', '');

		var span = domdoc.createElement('span');
		span.setAttribute('editable', 'no');
		span.setAttribute('class', 'kupu-text');
		newXimlet.appendChild(span);

		var imgEdit = domdoc.createElement('span');
		imgEdit.setAttribute('editable', 'no');
		imgEdit.setAttribute('class', 'kupu-ximlet-edit');
		newXimlet.appendChild(imgEdit);

		var imgToggle = domdoc.createElement('span');
		imgToggle.setAttribute('editable', 'no');
		imgToggle.setAttribute('class', 'kupu-ximlet-toggle-opened');
		newXimlet.appendChild(imgToggle);

		var it = new DOMAttrIterator(ximlet);
		while (it.hasNext()) {
			var attr = it.next();
			newXimlet.setAttribute(attr.nodeName, attr.nodeValue);
			if(attr.nodeName == 'editable' && attr.nodeValue == 'no')
				return;
		}

		ximlet.parentNode.insertBefore(newXimlet, ximlet);

		newXimlet.setAttribute('class', 'kupu-ximlet-box');
		newXimlet.setAttribute('ximlet_macro', $(ximlet).text());

		if(!ximElement.status) ximElement.status='visible';

		if(ximElement.status == 'hidden') {
			$(ximlet).remove(); //delete from the DOM
			if($(imgToggle).hasClass('kupu-ximlet-toggle-opened')){
				$(imgToggle).attr('class','kupu-ximlet-toggle-closed');
			}
    		} else {
			if($(imgToggle).hasClass('kupu-ximlet-toggle-closed')){
				$(imgToggle).removeClass('kupu-ximlet-toggle-closed').addClass('kupu-ximlet-toggle-opened');
			}
    		}
    	};

    	this._initializeXimletElement = function(ximlet) {
		//$(ximlet).unbind('dblclick');

		$(ximlet).css('border', '1px dotted black');
		$(ximlet).css('padding', '5px');
		$(ximlet).css('background-color', '#999');
		$(ximlet).css('color', '#FFF');
		$(ximlet).css('position', 'relative');

		$(ximlet).fadeTo('fast', 0.6);

		$(ximlet).next().contents().filter(function() {return this.nodeType == Node.TEXT_NODE;}).remove();
		$(ximlet).next().filter('[uid="' + $(ximlet).attr('uid') + '"]').children().fadeTo('fast', 0.5);
		//$(ximlet).next().filter('[uid="' + $(ximlet).attr('uid') + '"]').children().css('background-color', 'orange');
		try {
			$(ximlet).next().filter('[uid="' + $(ximlet).attr('uid') + '"]').children().css('border', '1px dotted black');
			//$(ximlet).css('top', $($(ximlet).next().filter('[uid="' + $(ximlet).attr('uid') + ']'").children()[0]).position().top);
			$(ximlet).css('left', $($(ximlet).next().filter('[uid="' + $(ximlet).attr('uid') + '"]').children()[0]).position().left);
		} catch(e) {
			//console.warn(e); //Something is going wrong when getting in for second time
		}

		var macro = ximlet.getAttribute('ximlet_macro');
		if (macro) {
			ximlet.attributes.removeNamedItem('ximlet_macro');
		} else {
			macro = ximlet.ximElement.macro;
		}

		$.extend(ximlet.ximElement, {
			macro: macro,
			htmlElement: ximlet,

			refresh: function(callback) {
				this.setMacroId();
				this.setEditable(callback);
			},

			getMacroId: function() {
				var macroId = (this.attributes['ximlet_id']) ? this.attributes['ximlet_id'] : null;
				if (macroId === null || !macroId) {
					macroId = '00000';
				}
		    	return macroId;
			},

			setMacroId: function(id) {
				// TODO: Validate ximletId (integer, positive)
				id = id || this.getMacroId();
		        this.attributes['ximlet_id'] = id;
		        this.macro = '@@@GMximdex.ximlet(' + id + ')@@@';
		        this.value = [this.macro];
		        $(this.htmlElement).children('span.kupu-text').text('Ximlet ID:' + id);
		        this.isEditable = undefined;
			},

			setEditable: function(callback) {

				if (this.isEditable != undefined) {
					callback(this.htmlElement);
				} else {
					if(this.editor) {
						$.getJSON(
				    		window.url_root + '/xmd/loadaction.php',
				    		{actionid: this.editor.actionId, nodeid: this.getMacroId(), ajax: 'json', method: 'canEditNode'},
				    		function(data, textStatus) {
				    			if (textStatus == 'success') {
				    				this.isEditable = data.editable;
						    		callback(this.htmlElement);
						    	}
				    		}.bind(this)
				    	);
			    	}
				}

			}
		});
		ximlet.ximElement.refresh(this._setXimletStyle.bind(this));

    	};

    	this.ximletDblClick = function(event) {
		// Fix for IE. currentTarget is NULL and target is the span element
		var target = event.currentTarget || event.target;
		if (!target['ximElement']) {
			target = target.parentNode;
		}

		//confirm dialog functions: Yes pressed, go on.
		var cbYes = function() {
			var macroId = target.ximElement.getMacroId();
			var url = window.url_root + '/xmd/loadaction.php?actionid='+this.editor.actionId+'&nodes[]='+macroId;
			var win = window.open(url);

			// Register unload event so we can refresh the ximlet content
			// See startKupu()
			var ximletid = 'unload_ximlet_'+macroId;
			window[ximletid] = function(event) {
				// do things, refresh, stuff, whatever...
				delete window[ximletid];
			};
		}.bind(this);

		//confirm dialog functions: No pressed, cancel action.
		var cbNo = function() {
		}

		if(target.ximElement.isSectionXimlet()){
			this.editor.confirm(_('WARNING: You are going to edit a ximlet which is linked to a section.\n\n This action will affect to all the section documents. Are you sure you want to continue?'), {"yes": cbYes, "no": cbNo});
		}
		else{
			this.editor.confirm(_('Do you want to edit the Ximlet in a new tab?'), {"yes": cbYes, "no": cbNo});

		}

    	};

	//called from ximdextools.js. It's executed to edit a ximlet in another browser tab
    	this.openEditWindow = function(ximElement) {
    		return function () {
    			this.ximletDblClick({ //event launch by the user, not the browser
    				target: {
    					ximElement: ximElement
    				}
    			});
    		}.bind(this);
    	}

    	this._setXimletStyle = function(divXimlet) {
		var editable = divXimlet.ximElement.isEditable;
		var showable= divXimlet.ximElement.status;
		var isSectionXimlet = divXimlet.ximElement.isSectionXimlet();

		if(editable) {	$(divXimlet).addClass('kupu-ximlet-editable');}
		else{$(divXimlet).addClass('kupu-ximlet-noeditable');}

		if(showable=="visible"){ $(divXimlet).addClass('kupu-ximlet-visible');}
		else{ $(divXimlet).addClass('kupu-ximlet-hidden');}

		if(isSectionXimlet) {$(divXimlet).addClass('kupu-ximlet-section');}

		//$('.kupu-ximlet-edit').click(this.ximletDblClick.bind(this).bind(this));
		$('.kupu-ximlet-edit').click(this.ximletDblClick.bind(this));
		//$('.kupu-ximlet-toggle-opened', divXimlet).click(this.toggleXimlet(divXimlet.ximElement.uid).bind(this));
//		$('.kupu-ximlet-toggle-opened').bind('dblclick', function(){window.alert("Pincho!!");});
//		$('.kupu-ximlet-toggle-closed', divXimlet).click(this.toggleXimlet(divXimlet.ximElement.uid).bind(this));

/*
$('.clickme').bind('click', function() {
  // Bound handler called.
});

*/

    	};

	//called from ximdextools.js. Executes when the context menu toggle options is clicked.
    	this.toggleXimlet = function(uid) {
		return function (event) {
			var ximElement = this.editor.getXimDocument().getElement(uid);

			if(!ximElement.status) ximElement.status='visible';

			if(ximElement.status == 'hidden') {
				ximElement.status = 'visible';
			} else {
				ximElement.status = 'hidden';
			}
			this.editor.updateEditor({caller: this});
		}.bind(this);
    	}

	this._removeXimletsContent = function() {
    		var ximModel = this.editor.getXimDocument().getXimModel();
    		for(var ximElement in ximModel) {
    			if(ximModel[ximElement].attributes['ximlet_id']) {
    				var childs = ximModel[ximElement].childNodes;
    				var length = childs.length;
    				for(var i = 0; i ++; i < length) {
	    				this.editor.getXimDocument().removeChild(childs[i]);
	    			}
    			}
    		}
	}

	this._existsXimlet = function(ximletId) {
    		var ximModel = this.editor.getXimDocument().getXimModel();
    		for(var id in ximModel) {
			var ximElement = ximModel[id];
			var rngElement = ximElement.schemaNode;
			if (rngElement.type.contains('ximlet') && ximElement.attributes['ximlet_id'] == ximletId) {
				return true;
			}
    		}
    		return false;
	}

}

ximletTool.prototype = new XimdocTool();
