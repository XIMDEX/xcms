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
 *  @version $Revision: 8538 $
 */


var ChangesetToolBox = Object.xo_create(FloatingToolBox, {

	_init: function(options) {

		ChangesetToolBox._construct(this, options);

		this._maxlength = options.maxlength;
		this._nChangeset = 0;

		this._isRestoring = false;
		this.stack = null;
		this.p = null;

		this._content = null;
		this._struct = null;
		this._element = null;
	},

	initialize: function(tool, editor) {
	    	this.tool = tool;
	        this.editor = editor;

		ChangesetToolBox._super(this, 'initialize', tool, editor);
		this.setTitle(_('Change history'));
		this.setOption('buttons', null);

	        $('#kupu-toolbox-undo').unbind().remove();
	        $(this.element).attr('id', 'kupu-toolbox-undolog');

		var undobutton = new KupuButton('kupu-undo-button', function() {
			this.undo();
		}.bind(this));
		editor.registerTool('undobutton', undobutton);

		var redobutton = new KupuButton('kupu-redo-button', function() {
			this.redo();
		}.bind(this));
		editor.registerTool('redobutton', redobutton);

	        this.editor.logMessage(_('UndoToolBox tool initialized'));
	},

	updateState: function(options) {

		// Content changes
		if (this._isRestoring) return;

		var saveState = false;

		if (this._element != options.selNode) {
			if (this._content != $(this._element).text()) {
				var ximdoc = this.editor.getXimDocument();
				saveState = true;
				//this.editor.updateEditorContent();
				if (this._element && this._element.isEditable) {
					var ximElement = ximdoc.importHtmlElement(this._element);
					ximdoc.updateElement(ximElement.uid, ximElement);
				}
			}
			this._content = $(options.selNode).text();
			this._element = options.selNode;
		}

		if (options.caller) {
			options.caller.setActionDescription('');
		}

		description = _('Content updated');
		if (saveState) this.saveState(description);

	},

	afterUpdateContent: function(options) {

		if (this._element) {
			// NOTE: Be sure to keep updated this object reference always!
			// NOTE: Corrects a bug on the "Enter functionality"!
			// When user press enter key, the selected element breaks into two pieces and the
			// editor is refreshed to correct that "break".
			// In this case, this._element reference is outdated and the content will be lost.
			this._element = $('[uid="'+this._element.getAttribute('uid')+'"]', this.editor.getInnerDocument())[0];
		}

		// XML structure changes
		if (this._isRestoring) return;

		var saveState = false;
		var struct = this.editor.getXimDocument().saveXML({
			asString: true,
			hideXimlets: true
		});

		if (struct != this._struct) {
			this._struct = struct;
			saveState = true;
		}

		description = '';
		if (options.caller) {
			description = options.caller.getActionDescription() || "";
			if( (null == description || '' == description) && null != this.editor) {
				description = this.editor.getActionDescription();
			}
			options.caller.setActionDescription('');
		}else if( null != this.editor) {
			description = this.editor.getActionDescription();
		}

		if(''==description || '' ==description) {
				description = _("Create ")+_("element");
		}

		if (saveState) this.saveState(description);
	},

	saveState: function(label) {

		if (this._isRestoring) return;

		// Linear, multiuser undo
		// It doesn't manage branches, when a new state is saved
		// it removes all states after the "pointer" position.
		var xml = this.editor.getXimDocument().saveXML({
			asString: true,
			hideXimlets: false
		});
		if (!this.stack) {
			this.p = -1;
			this.stack = [];
		}

		var previousXML = this.stack[this.p];

		if (this.p == (this.stack.length - 1)) {
			if (xml != previousXML) this.stack.push(xml);
		} else {
			var ndelete = this.stack.length - this.p;
			this.stack.splice(this.p, ndelete, xml);
			// Updating the history panel, we have just removed some changesets...
			$('div.changeset-item', this.element).each(function(index, elem) {
				if (!this.stack[index] || index == this.p) {
					$(elem).remove();
				}
			}.bind(this));
		}

		// keeping always the specified number of changes
		while (this.stack.length > this._maxlength) {
			this.stack.shift();
		}

		this.p = this.stack.length - 1;
		if(this.p==0){
			$("#kupu-undo-button").addClass("disabled");
		}
		else{
			$("#kupu-undo-button").removeClass("disabled");
		}

		if(this.p == this.stack.length-1){
			$("#kupu-redo-button").addClass("disabled");
		}
		else{
			$("#kupu-redo-button").removeClass("disabled");
		}
		this.log(label);

		// Updating the pointer to the change stack
		$('div.changeset-item', this.element).each(function(index, elem) {
			elem._changesetId = index;
		});

	},

	setState: function(event) {

		var changesetId = (event.currentTarget || event.target)._changesetId;
		var state = this.stack[changesetId];
		if (!state) {
			this.editor.logMessage(_('Changeset not found!'));
			return;
		}
		this.p = changesetId;
		this.restoreChangeset(state);
	},

	restoreChangeset: function(state) {
		loadingImage.showLoadingImage();
		this._isRestoring = true;
		var doc = this.editor.createDomDocument(state);
		this.editor.getXimDocument().loadXML(doc, this.editor.getRngDocument());
		// Don't update the editor content or we could lost some changes!
		this.editor.updateEditor({updateContent: false});
		this.editor.logMessage(_('Restoring changeset') + ' ' + this.p);
		this._isRestoring = false;
		loadingImage.hideLoadingImage();
	},

    	undo: function() {
    		if (this.p <= 0) {
    			this.p = 0;
			$("#kupu-undo-button").addClass("disabled");
    			return;
    		}
		var state = this.stack[--this.p];
    		if (this.p <= 0) {
			$("#kupu-undo-button").addClass("disabled");
		}
		$("#kupu-redo-button").removeClass("disabled");
		this.restoreChangeset(state);
    	},

    	redo: function() {
    		if (this.p >= this.stack.length - 1) {
    			this.p = this.stack.length - 1;
			$("#kupu-redo-button").addClass("disabled");
    			return;
    		}
		var state = this.stack[++this.p];
    		if (this.p >= this.stack.length - 1) {
			$("#kupu-redo-button").addClass("disabled");
		}
    			this.p = this.stack.length - 1;
		$("#kupu-undo-button").removeClass("disabled");
		this.restoreChangeset(state);
    	},

	log: function(label) {
		/* log a message */
		if (this._maxlength) {
		    if (this.element.childNodes.length > this._maxlength) {
	        	//this.element.removeChild(this.element.childNodes[0]);
	        	var elem = $('div.changeset-item', this.element)[0];
	        	$(elem).remove();
		    }
		}
		var now = new Date();
		var time = this.formatTime(now);

		var div = document.createElement('div');
		$(div).addClass('changeset-item');
		// ... expert mode ...
		if (!this.editor.schemaValidatorIsActive()) $(div).addClass('invalid-changeset');
		++this._nChangeset;
		var span = document.createElement('span');
		var subtext = document.createTextNode(time);
		span.appendChild(subtext);
		var text = document.createTextNode(label);
		div.appendChild(text);
		div.appendChild(span);
		div._changesetId = this.p;
		$([div, text]).click(this.setState.bind(this));
		this.element.appendChild(div);
	},

	formatTime: function(time) {
		var hours = (time.getHours() < 10) ? '0' + time.getHours() : time.getHours();
		var minutes = (time.getMinutes() < 10) ? '0' + time.getMinutes() : time.getMinutes();
		var seconds = (time.getSeconds() < 10) ? '0' + time.getSeconds() : time.getSeconds();
		return hours + ':' + minutes + ':' + seconds;
	}

});

