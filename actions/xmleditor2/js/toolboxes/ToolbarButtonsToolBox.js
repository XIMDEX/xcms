

/**
 * Controls the buttons that will be added to the toolbar and the states
 */
function ToolbarButtonsToolBox(buttonGroupClass) {

    	this.selectedNode = null;
    	this.buttonGroupClass = buttonGroupClass;

    	this.initialize = function(tool, editor) {
    		this.tool = tool;
        	this.editor = editor;
        	this._initializeToolbar();
        	this.editor.logMessage(_('ToolbarButtonsToolBox tool initialized'));
    	};

    	this._initializeToolbar = function() {

		var commonGroup = document.createElement('span');
		commonGroup.setAttribute('class', this.buttonGroupClass);

		var buttonGroup = document.createElement('span');
		buttonGroup.setAttribute('class', this.buttonGroupClass);

		$([commonGroup, buttonGroup]).css({
			'border-left': '2px solid #bbbbbb',
			'padding-left': '8px'
		});

		var tbar = getFromSelector('kupu-tb-rngbuttons');
		tbar.appendChild(commonGroup);
		tbar.appendChild(buttonGroup);

		var rngElements = this.editor.config.rng_elements;
		if (rngElements) {
			var model = this.editor.getRngDocument().getModel();
			var default_icon_class = this.editor.config.default_button_class;

			for (var rngElementName in model) {

				rngElementName = rngElementName.replace(":", "_");
				var rngTagName = 'rng_element_' + rngElementName;

				if (rngElementName != "docxap") {

					var buttonId = 'kupu-' + rngElementName + '-button';
					var commonElement = rngElements[rngTagName];
					if (commonElement) {
						var icon_class = rngElements[rngTagName].classname;
					} else {
						var icon_class = default_icon_class;
					}

					var button = document.createElement('button');
					$(button).addClass(icon_class);
					button.setAttribute('type', 'button');
					button.setAttribute('id', buttonId);
					button.setAttribute('title', rngElementName);
					button.setAttribute('i18n:attributes', 'title');

					if (commonElement) {
						commonGroup.appendChild(button);
					} else {
						buttonGroup.appendChild(button);
					}

					var rngbutton = new XimdocRngElementButton(buttonId, model[rngElementName]);
					this.editor.registerTool(rngElementName + '_button', rngbutton);
					KupuButtonDisable(button);
				}
			}
		}
    	};

    	this.updateState = function(options) {

		if (!options.selNode || !options.event || options.event.type != 'mouseup') return;

		if (this.editor.ximElement && this.editor.ximParent) {
			var rngElement = this.editor.ximElement.schemaNode;
			var rngParent = this.editor.ximParent.schemaNode;

			this.tool.disableAllButtons();

			// Disable 'remove' button if element is not removable
			var button = getFromSelector('kupu-remove-button');
			if(this.editor.ximElement.isRemovable()) {
				KupuButtonEnable(button);
			} else {
				KupuButtonDisable(button);
			}

			if (!rngElement.type.contains('ximlet') && this.editor.selectedTextLength > 0) {
				// Enables 'Apply' type buttons.
				for (var i in rngElement.childNodes) {
					var childElement = rngElement.childNodes[i];
					if(!childElement.tagName)
						continue;
					if(childElement.type.contains('apply')) {
						var button = getFromSelector('kupu-' + childElement.tagName.replace(":", "_") + '-button');
						KupuButtonEnable(button);
					}
				}
			} else {
				// Enables allowed 'Add Sibling' buttons.
				for (var i in rngParent.childNodes) {
					var childElement = rngParent.childNodes[i];
					if(!childElement.tagName)
						continue;
					var button = getFromSelector('kupu-' + childElement.tagName.replace(":", "_") + '-button');
					KupuButtonEnable(button);
				}

				// Enables 'Edit Ximlet' button, if proceed.
				var button = getFromSelector('kupu-ximletdrawer-button');
				KupuButtonDisable(button);
				if(rngElement.type.contains('ximlet') && !this.editor.ximElement.isSectionXimlet()) {
					KupuButtonEnable(button);
				}
			}
		}
    	}
};

ToolbarButtonsToolBox.prototype = new XimdocToolBox();
