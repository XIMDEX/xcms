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
 *  @version $Revision: 7842 $
 */




/**
 * A button specialized in adding new ximelements in the current node context.
 */
var XimdocRngElementButton = Object.xo_create(XimdocButton, {

	_init: function(buttonid, rngElement, tool) {
		XimdocRngElementButton._construct(this, buttonid, null, tool);
		this.rngElement = rngElement;
    },

    initialize: function(editor) {

        XimdocRngElementButton._super(this, 'initialize', editor);

        var body = this.editor.getBody();
        $(this.button).draggable({
        	containment: $('.kupu-editorframe'),
			helper: 'clone',
        	appendTo: 'body',
        	iframeFix: false,
        	start: function(event, ui) {

        		var helper = $(ui.helper[0])
        			.clone()
					.addClass('ui-cloned-helper')
        			.css({
        				position: 'absolute',
        				width: '26px',
        				height: '26px',
        				backgroundColor: '#FFFFFF',
						border: '1px solid #ccc',
						'background-repeat': 'no-repeat'
        			})
        			.show()
        			.appendTo(this.editor.getBody());

    			try {
    				var dtb = this.editor.tools['hovertool'].toolboxes['draggabletoolbox'];
    				dtb.onStart(event, ui);
    			} catch(e) {
    				console.error(e.message);
    			}

        	}.bind(this),
        	drag: function(event, ui) {

				var view = this.editor.getBody().ownerDocument.defaultView;
				var p = {
//					top: ui.position.top - $('.kupu-editorframe').position().top + view.scrollY,
					top: ui.absolutePosition.top - $('.kupu-editorframe').position().top + view.scrollY,
//					left: ui.position.left
					left: ui.absolutePosition.left
				};
				$('.ui-cloned-helper', this.editor.getBody()).css({
					top: p.top,
					left: p.left
				});
        	}.bind(this),
        	stop: function(event, ui) {

        		// TODO: elementFromPoin will not work in all browsers
        		// See http://www.quirksmode.org/dom/w3c_cssom.html
        		// Parameters can be diferent in distinct browsers

        		$('.ui-cloned-helper', this.editor.getBody()).remove();
        		var p = {
//        			x: ui.position.top,
        			x: event.clientX,
//        			x: event.pageX,
//        			y: ui.position.left - $('.kupu-editorframe').position().top
        			y: event.clientY - $('.kupu-editorframe').position().top
//        			y: event.pageY - $('.kupu-editorframe').position().top
        		};

        		var element = this.editor.getBody().ownerDocument.elementFromPoint(p.x, p.y);
        		element = element && element.ximElement ? element.ximElement : null;
        		var parent = element ? element.parentNode : null;
        		this.commandfunc(this, this.editor, this.tool, parent, element);

    			try {
    				var dtb = this.editor.tools['hovertool'].toolboxes['draggabletoolbox'];
    				dtb.onStop(event, ui);
    			} catch(e) {
    				console.error(e);
    			}
        	}.bind(this)
        });

		//this.disableDraggable();
    },

    enable: function() {
    	XimdocRngElementButton._super(this, 'enable');
    	this.enableDraggable();
    },

    enableDraggable: function() {
    	$(this.button).draggable('enable');
    },

    disable: function() {
    	XimdocRngElementButton._super(this, 'disable');
    	this.disableDraggable();
    },

    disableDraggable: function() {
    	$(this.button).draggable('disable');
    },

    commandfunc: function(button, editor, tool, ximParent, ximElement, addChild) {

		ximParent = ximParent || this.editor.ximParent;
		ximElement = ximElement || this.editor.ximElement;

		if (ximElement /*&& ximParent*/) {

			if (ximParent && ximParent == ximElement) {
				this.createElement(this.rngElement.tagName, ximElement, null);
			} else if (!this.rngElement['wizard']) {

				if (this.rngElement.type.contains('apply') || this.editor.selectedTextLength > 0) {
					this.createElement(this.rngElement.tagName, ximElement, null);
				} else if(ximParent && !addChild) {
					this.createElement(this.rngElement.tagName, ximParent, ximElement);
				} else if (ximElement.isRoot || addChild === true) {
					this.createElement(this.rngElement.tagName, ximElement, null);
				}
			} else {

				// TODO: Make this section with a different structure...

				var wizard = this.rngElement.wizard.toLowerCase() + 'managertool';
				var tool = this.editor.getTool(wizard);
		    	var ximdocdrawertool = this.editor.getTool('ximdocdrawertool');

				if (!ximdocdrawertool.drawers[wizard]) {
					if (wizard == 'tablemanagertool') {
						var tablewizarddrawer = new TableWizardDrawer(tool, this.rngElement, ximElement);
					    ximdocdrawertool.registerDrawer(wizard, tablewizarddrawer);
					    //console.log(tablewizarddrawer);
					} else if(wizard == 'listmanagertool') {
						// nothing at this moment...
				    }
				}

			    ximdocdrawertool.openDrawer(wizard);
			}

		} else {
			this.editor.logMessage(_('Error adding new element when clicking ')+this.buttonid);
		}
    },

    addchildfunc: function() {
    	this.commandfunc(null, null, null, null, null, true);
    },

    updateState: function(options) {
    	// see RNGElementsToolBox::updateState()
    }
});

