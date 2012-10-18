/*****************************************************************************
 *
 * Copyright (c) 2003-2005 Kupu Contributors. All rights reserved.
 *
 * This software is distributed under the terms of the Kupu
 * License. See LICENSE.txt for license text. For a list of Kupu
 * Contributors see CREDITS.txt.
 *
 *****************************************************************************/

// $Id: kupuinit.js 4015 2004-04-13 13:31:49Z guido $


function initKupu(iframe) {
    /* This is a copy of initKupu for the form support

        We only want to disable the save button, but unfortunately
        that currently still means this method should be overridden
        completely
    */

    // first we create a logger
    var l = new PlainLogger('kupu-toolbox-debuglog', 15);

    // now some config values
    var conf = loadDictFromXML(document, 'kupuconfig');

    // the we create the document, hand it over the id of the iframe
    var doc = new KupuDocument(iframe);

    // now we can create the controller
    var kupu = new KupuEditor(doc, conf, l);

    var contextmenu = new ContextMenu();
    kupu.setContextMenu(contextmenu);

    // now we can create a UI object which we can use from the UI
    // var ui = new KupuUI('kupu-tb-styles');

    // the ui must be registered to the editor like a tool so it can be notified
    // of state changes
    // kupu.registerTool('ui', ui); // XXX Should this be a different method?

    // function that returns a function to execute a button command
    var execCommand = function(cmd) {
        return function(button, editor) {
            editor.execCommand(cmd);
        };
    };

	node = getFromSelector('kupu-tb-buttons');
	var buttonGroup = document.createElement('span');
	buttonGroup.setAttribute('class','kupu-tb-buttongroup');
	node.appendChild(buttonGroup);

	// Fetch Rng Elements:
	var rngElements = null;
	if ((rngElements = kupu.config.rng_elements)) {

		var model = XmlMapper.getRngDocument().getModel();
		var default_icon_class = kupu.config.default_button_class;

		for(var rngElement in model) {

			var rngTagName = 'rng_element_' + rngElement;
			if(rngElement != "docxap" && rngElements[rngTagName]) {

				var buttonId = 'kupu-' + rngElement + '-button';
				var icon_class = rngElements[rngTagName].classname || default_icon_class;

				var button = document.createElement('button');
				button.setAttribute('type', 'button');
				button.setAttribute('id', buttonId);

				if (!XmlMapper.IS_IE) {
					button.setAttribute('class', icon_class);
				} else {
					// NOTE: MSIE doesn't likes 'class' attribute, attribute needs to be referenced by 'className'.
					button.setAttribute('className', icon_class);
					//button.className = icon_class;
				}

				button.setAttribute('title', rngElement);
				button.setAttribute('i18n:attributes', 'title');
				/*var text = document.createTextNode("");
				button.appendChild(text);*/
				buttonGroup.appendChild(button);

				var rngbutton = null;
				if (model[rngElement]['wizard'] == 'table') {
			    	rngbutton = new KupuRngWizardButton(buttonId, rngElement);
			    } else {
			    	rngbutton = new KupuRngElementButton(buttonId, rngElement);
			    }

				kupu.registerTool(rngElement + 'button', rngbutton);
				KupuButtonDisable(button);
			}
		}
	}


    var removeelementbutton = new KupuRemoveElementButton('kupu-remove-button');
	kupu.registerTool('removebutton', removeelementbutton);

    var undobutton = new KupuButton('kupu-undo-button', execCommand('undo'));
    kupu.registerTool('undobutton', undobutton);

    var redobutton = new KupuButton('kupu-redo-button', execCommand('redo'));
    kupu.registerTool('redobutton', redobutton);

    var attrtool = new AttributesTool();
    kupu.registerTool('attrtool', attrtool);
    var attrtoolbox = new AttributesToolBox('kupu-attribute-button');
    attrtool.registerToolBox('attrtoolbox', attrtoolbox);

    var proptool = new PropertyTool('kupu-properties-title', 'kupu-properties-description');
    kupu.registerTool('proptool', proptool);

    var showpathtool = new ShowPathTool();
    kupu.registerTool('showpathtool', showpathtool);


    var sourceedittool = new SourceEditTool('kupu-source-button',
                                            'kupu-editor-textarea');
    kupu.registerTool('sourceedittool', sourceedittool);


    var allowedchildrenstool = new AllowedChildrensTool();
    kupu.registerTool('allowedchildrenstool', allowedchildrenstool);

    // Drawers...

    // Function that returns function to open a drawer
    var opendrawer = function(drawerid) {
        return function(button, editor) {
            drawertool.openDrawer(drawerid);
        };
    };

    var previewbutton = new KupuButton('kupu-prevdoc-button',
    								function() {

										// It's necessary updating editor content before
										// previewing
										XmlMapper.updateEditor(kupu.getInnerDocument());

										// Calling PreviewHandler
										var xmlfile = "";
										var content = encodeURIComponent(XmlMapper._ximdoc.saveXML(true));
										var lang = "es";

										var encodedContent = "XML=" + encodeURIComponent(xmlfile) +
															 "&nodeid=" + XmlMapper._ximdoc._nodeId +
															 "&content=" + content +
															 "&lang=" + encodeURIComponent(lang);
										com.ximdex.ximdex.editors.PreviewHandler(encodedContent, {
											onComplete: function(req, json) {
												var previewContent = req.responseText;
										        var win = window.open();
												win.document.write(previewContent);
												win.document.close();
												delete win;
											},
											onError: function(req) {
												alert('Error obtaining preview.');
											}
										});
    								}
    							);
    kupu.registerTool('previewbutton', previewbutton);


    // create some drawers, drawers are some sort of popups that appear when a
    // toolbar button is clicked
    var drawertool = new DrawerTool();
    kupu.registerTool('drawertool', drawertool);

    //var tablewizarddrawer = new TableWizardDrawer('kupu-tabledrawer');
    //drawertool.registerDrawer('tablewizarddrawer', tablewizarddrawer);
    //drawertool.openDrawer('tablewizarddrawer');


    // make the prepareForm method get called on form submit
    // some bug in IE makes it crash on saving the form when a lib drawer
    // was added to the page at some point, remove it on form submit
    var savebutton = getFromSelector('kupu-save-button');
    function prepareForm() {
        var drawer = window.document.getElementById('kupu-librarydrawer');
        if (drawer) {
            drawer.parentNode.removeChild(drawer);
        }
        kupu.prepareForm(savebutton.form, 'kupu');
        //savebutton.form.submit();

        // First step is refresh the XML document content
        XmlMapper.updateEditor(kupu.getInnerDocument());

		var xmlfile = "";
		//var content = savebutton.form.kupu.value;

		var ximdoc = XmlMapper.getXimDocument();
		var content = ximdoc.saveXML(true);
		var lang = "es";

		var encodedContent = "XML=" + encodeURIComponent(xmlfile) +
							 "&content=" + encodeURIComponent(content) +
							 "&lang=" + encodeURIComponent(lang);

		// NOTE: XML validation on the server
		ximdoc.validateXML(function(valid, msg) {

			if (valid) {
				com.ximdex.ximdex.editors.SaveHandler(encodedContent, {
					onComplete: function(req, json) {
						alert(_('Document has been saved'));
					},
					onError: function(req) {
						alert(_('Error saving the document!\n' + 'Status: ' + req.status + '\nError: ' + req.statusText));
					}
				});
			} else {
				alert(msg);
			}
		});

    };
    addEventHandler(savebutton, 'click', prepareForm, kupu);

    // register some cleanup filter
    // remove tags that aren't in the XHTML DTD
    var nonxhtmltagfilter = new NonXHTMLTagFilter();
    kupu.registerFilter(nonxhtmltagfilter);

    if (window.kuputoolcollapser) {
        var collapser = new window.kuputoolcollapser.Collapser('kupu-toolboxes');
        collapser.initialize();
    };

    return kupu;
};
