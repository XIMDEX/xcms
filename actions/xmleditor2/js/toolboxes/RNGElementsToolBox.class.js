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




var RNGElementsToolBox = Object.xo_create(FloatingToolBox, {

	_init: function(options) {
		RNGElementsToolBox._construct(this, options);
	},
	initialize: function(tool, editor) {
		RNGElementsToolBox._super(this, 'initialize', tool, editor);
		this.setTitle(_('Available elements'));
        this._initializeToolbar();
        this.editor.logMessage(_('ToolbarButtonsToolBox tool initialized'));
	},
	_initializeToolbar: function() {

		var commonGroup = $('<div/>');
		var buttonGroup = $('<span/>');

		$([commonGroup, buttonGroup]).css({
			'border-left': '2px solid #bbbbbb',
			'padding-left': '8px'
		});

		$(this.element)
			.attr('id', 'kupu-tb-rngbuttons')
			.addClass('kupu-tb-buttons')
			.addClass('kupu-tb-buttons-tool')
			.append(commonGroup)
			.append(buttonGroup);

		var rngElements = this.editor.config.rng_elements;
		if (rngElements) {
			var model = this.editor.getRngDocument().getModel();
			var default_icon_class = this.editor.config.default_button_class;

			for (var rngElementName in model) {

				rngElementName = rngElementName.replace(":", "_");
				var rngTagName = 'rng_element_%s'.printf(rngElementName);

				if (rngElementName != "docxap") {

					var buttonId = 'kupu-%s-button'.printf(rngElementName);
					var commonElement = rngElements[rngTagName];
					if (commonElement) {
						var icon_class = rngElements[rngTagName].classname;
					} else {
						var icon_class = default_icon_class;
					}

					var button = $('<div/>')
						.addClass(icon_class)
						.addClass('kupu-button')
						.attr('id', buttonId)
						.attr('title', _('Add ') + model[rngElementName].description + ' ' + _('under the currently selected element'))
						.attr('i18n:attributes', 'title');
						var description = $('<div>' + _('Add ') + model[rngElementName].description + '</div>');

					$(button).append(description);
					$(commonGroup).append(button);

					var rngbutton = new XimdocRngElementButton(buttonId, model[rngElementName], this.tool);
					this.editor.registerTool(rngElementName + '_rngbutton', rngbutton);
					rngbutton.disable();
				}
			}
		}
	},
	updateState: function(options) {

		if (!options.selNode || !options.event ||
			(options.event.type != 'click' && options.event.type != 'click' &&
			!(options.event && options.event.type == 'keyup' && options.event.keyCode >= 37 && options.event.keyCode <= 40)))
			return;

		if (this.editor.ximElement && this.editor.ximParent) {
			var rngElement = this.editor.ximElement.schemaNode;
			var rngParent = this.editor.ximParent.schemaNode;

			this.tool.disableAllButtons();

			// This code could be implemented on RNGButton::updateState() method ...

			if (!rngElement.type.contains('ximlet') && this.editor.selectedTextLength > 0) {
				// Enables 'Apply' type buttons.
				for (var i in rngElement.childNodes) {
					var childElement = rngElement.childNodes[i];
					if(!childElement.tagName)
						continue;
					if(childElement.type.contains('apply')) {
						var button = this.editor.getTool('%s_rngbutton'.printf(childElement.tagName));
						button.enable();
					}
				}
			} else {
				// Enables allowed 'Add Sibling' buttons.
				for (var i in rngParent.childNodes) {
					var childElement = rngParent.childNodes[i];
					if(!childElement.tagName)
						continue;
					var button = this.editor.getTool('%s_rngbutton'.printf(childElement.tagName));
					button.enable();
				}

				// Enables 'Edit Ximlet' button, if proceed.
				var button = this.editor.getTool('ximletdrawerbutton');
				button.disable();
				if(rngElement.type.contains('ximlet') && !this.editor.ximElement.isSectionXimlet()) {
					button.enable();
				}
			}
		}
	},
	updateButtonHandler: function(event) {

		return;
		//if (this.tool.selectedNode == null) return;
		var selNode = this.editor.getSelectedNode();

		$('.kupu-attribute-value', this.element).each(function(index, elem) {
			var attrName = $(elem).data('attribute-name');
			var attrValue = $(elem).val();
//			console.log('%s = %s'.printf(attrName, attrValue));
			if (attrName != 'uid') {
				selNode.ximElement.attributes[attrName] = attrValue;
			}
		});

		this.editor.logMessage(_('Attributes updated!'));

		// NOTE:
		// Updates Editor Content, because actually we don't edit html attributes but xml.
		this.setActionDescription(_('Update attributes'));

		// When update button is clicked, selected element losts his focus,
		// so we clean the attributes panel to prevent errors.
		// UpdateEditor will populate panel again.
		this._clean();

		this.editor.updateEditor({caller: this});

	}
});

