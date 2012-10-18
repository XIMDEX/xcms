/*****************************************************************************
 *
 * Copyright (c) 2003-2005 Kupu Contributors. All rights reserved.
 *
 * This software is distributed under the terms of the Kupu
 * License. See LICENSE.txt for license text. For a list of Kupu
 * Contributors see CREDITS.txt.
 *
 *****************************************************************************/

// $Id: kupuploneinit.js 21720 2006-01-05 15:59:25Z paul $

function initPloneKupu(editorId) {
    var topnode = getFromSelector(editorId);
    var prefix = '#'+editorId+' ';

    var iframe = getFromSelector(prefix+'iframe.kupu-editor-iframe');
    var textarea = getFromSelector(prefix+'textarea.kupu-editor-textarea');
    var l = new DummyLogger();

    // XXX this should be fixed in stylesheets, but I don't know how to do 
    // that without applying this change to the outter document. Damn iframes.
    var ibody = iframe.contentWindow.document.body;
    var form = textarea.form;
    ibody.innerHTML = textarea.value || '<p class=""><br></p>';

    // now some config values
    var conf = loadDictFromXML(document, prefix+'xml.kupuconfig');

    // the we create the document, hand it over the id of the iframe
    var doc = new KupuDocument(iframe);

    // now we can create the controller
    var kupu = new KupuEditor(doc, conf, l);

    // add the contextmenu
    var cm = new ContextMenu();
    kupu.setContextMenu(cm);

    // now we can create a UI object which we can use from the UI
    var ui = new KupuUI(prefix+'select.kupu-tb-styles');
    kupu.registerTool('ui', ui);

    // function that returns a function to execute a button command
    var execCommand = function(cmd) {
        return function(button, editor) {
            editor.execCommand(cmd);
        };
    };

    var boldchecker = ParentWithStyleChecker(new Array('b', 'strong'),
					     'font-weight', 'bold');
    var boldbutton = new KupuStateButton(prefix+'button.kupu-bold', 
                                         execCommand('bold'),
                                         boldchecker,
                                         'kupu-bold',
                                         'kupu-bold-pressed');
    kupu.registerTool('boldbutton', boldbutton);

    var italicschecker = ParentWithStyleChecker(new Array('i', 'em'),
						'font-style', 'italic');
    var italicsbutton = new KupuStateButton(prefix+'button.kupu-italic', 
                                           execCommand('italic'),
                                           italicschecker, 
                                           'kupu-italic', 
                                           'kupu-italic-pressed');
    kupu.registerTool('italicsbutton', italicsbutton);

    var underlinechecker = ParentWithStyleChecker(new Array('u'));
    var underlinebutton = new KupuStateButton(prefix+'button.kupu-underline', 
                                              execCommand('underline'),
                                              underlinechecker,
                                              'kupu-underline', 
                                              'kupu-underline-pressed');
    kupu.registerTool('underlinebutton', underlinebutton);

    var subscriptchecker = ParentWithStyleChecker(new Array('sub'));
    var subscriptbutton = new KupuStateButton(prefix+'button.kupu-subscript',
                                              execCommand('subscript'),
                                              subscriptchecker,
                                              'kupu-subscript',
                                              'kupu-subscript-pressed');
    kupu.registerTool('subscriptbutton', subscriptbutton);

    var superscriptchecker = ParentWithStyleChecker(new Array('super', 'sup'));
    var superscriptbutton = new KupuStateButton(prefix+'button.kupu-superscript', 
                                                execCommand('superscript'),
                                                superscriptchecker,
                                                'kupu-superscript', 
                                                'kupu-superscript-pressed');
    kupu.registerTool('superscriptbutton', superscriptbutton);

    var justifyleftbutton = new KupuButton(prefix+'button.kupu-justifyleft',
                                           execCommand('justifyleft'));
    kupu.registerTool('justifyleftbutton', justifyleftbutton);

    var justifycenterbutton = new KupuButton(prefix+'button.kupu-justifycenter',
                                             execCommand('justifycenter'));
    kupu.registerTool('justifycenterbutton', justifycenterbutton);

    var justifyrightbutton = new KupuButton(prefix+'button.kupu-justifyright',
                                            execCommand('justifyright'));
    kupu.registerTool('justifyrightbutton', justifyrightbutton);

    var outdentbutton = new KupuButton(prefix+'button.kupu-outdent', execCommand('outdent'));
    kupu.registerTool('outdentbutton', outdentbutton);

    var indentbutton = new KupuButton(prefix+'button.kupu-indent', execCommand('indent'));
    kupu.registerTool('indentbutton', indentbutton);

    var undobutton = new KupuButton(prefix+'button.kupu-undo', execCommand('undo'));
    kupu.registerTool('undobutton', undobutton);

    var redobutton = new KupuButton(prefix+'button.kupu-redo', execCommand('redo'));
    kupu.registerTool('redobutton', redobutton);

    var removeimagebutton = new KupuRemoveElementButton(prefix+'button.kupu-removeimage',
							'img',
							'kupu-removeimage');
    kupu.registerTool('removeimagebutton', removeimagebutton);
    var removelinkbutton = new KupuRemoveElementButton(prefix+'button.kupu-removelink',
						       'a',
						       'kupu-removelink');
    kupu.registerTool('removelinkbutton', removelinkbutton);

    // add some tools

    var listtool = new ListTool(prefix+'button.kupu-insertunorderedlist',
                                prefix+'button.kupu-insertorderedlist',
                                prefix+'select.kupu-ulstyles',
                                prefix+'select.kupu-olstyles');
    kupu.registerTool('listtool', listtool);

    var definitionlisttool = new DefinitionListTool(prefix+'button.kupu-insertdefinitionlist');
    kupu.registerTool('definitionlisttool', definitionlisttool);
    
    var tabletool = new TableTool();
    kupu.registerTool('tabletool', tabletool);

    var showpathtool = new ShowPathTool('kupu-showpath-field');
    kupu.registerTool('showpathtool', showpathtool);

    var sourceedittool = new SourceEditTool(prefix+'button.kupu-source',
                                            prefix+'textarea.kupu-editor-textarea');
    kupu.registerTool('sourceedittool', sourceedittool);

    var imagetool = NoContextMenu(new ImageTool());
    kupu.registerTool('imagetool', imagetool);

    var linktool = NoContextMenu(new LinkTool());
    kupu.registerTool('linktool', linktool);

    var zoom = new KupuZoomTool(prefix+'button.kupu-zoom',
        prefix+'select.kupu-tb-styles',
        prefix+'button.kupu-logo');
    kupu.registerTool('zoomtool', zoom);

    // XXX  - Needs prefix here for multi area support, but also 
    // added to the template
    var spellchecker = new KupuSpellChecker('kupu-spellchecker-button',
                                            'kupu_library_tool/spellcheck');
    kupu.registerTool('spellchecker', spellchecker);

    // Use the generic beforeUnload handler if we have it:
    var beforeunloadTool = window.onbeforeunload && window.onbeforeunload.tool;
    if (beforeunloadTool) {
        var initialBody = ibody.innerHTML;
        beforeunloadTool.addHandler(function() {
            return ibody.innerHTML != initialBody;
        });
        beforeunloadTool.chkId[textarea.id] = function() { return false; }
        beforeunloadTool.addForm(form);
    }
    // Patch for bad AT format pulldown.
    var fmtname = textarea.name+'_text_format';
    var pulldown = form[fmtname];
    if (pulldown && pulldown.type=='select-one') {
        for (var i=0 ; i < pulldown.length; i++) {
            var opt = pulldown.options[i];
            opt.selected = opt.defaultSelected = (opt.value=='text/html');
        }
        pulldown.disabled = true;
        var hidden = document.createElement('input');
        hidden.type = 'hidden';
        hidden.name = fmtname;
        hidden.value = 'text/html';
        pulldown.parentNode.appendChild(hidden);
    };

    // Drawers...

    // Function that returns function to open a drawer
    var opendrawer = function(drawerid) {
        return function(button, editor) {
            drawertool.openDrawer(prefix+drawerid);
        };
    };

    var imagelibdrawerbutton = new KupuButton(prefix+'button.kupu-image',
                                              opendrawer('imagelibdrawer'));
    kupu.registerTool('imagelibdrawerbutton', imagelibdrawerbutton);

    var linklibdrawerbutton = new KupuButton(prefix+'button.kupu-inthyperlink',
                                             opendrawer('linklibdrawer'));
    kupu.registerTool('linklibdrawerbutton', linklibdrawerbutton);

    var linkdrawerbutton = new KupuButton(prefix+'button.kupu-exthyperlink',
                                          opendrawer('linkdrawer'));
    kupu.registerTool('linkdrawerbutton', linkdrawerbutton);

    var tabledrawerbutton = new KupuButton(prefix+'button.kupu-table',
                                           opendrawer('tabledrawer'));
    kupu.registerTool('tabledrawerbutton', tabledrawerbutton);

    // create some drawers, drawers are some sort of popups that appear when a 
    // toolbar button is clicked
    var drawertool = window.drawertool || new DrawerTool();
    kupu.registerTool('drawertool', drawertool);

    var drawerparent = prefix+'div.kupu-librarydrawer-parent';
    var linklibdrawer = new LinkLibraryDrawer(linktool, conf['link_xsl_uri'],
                                              conf['link_libraries_uri'],
                                              conf['search_links_uri'], drawerparent);
    drawertool.registerDrawer(prefix+'linklibdrawer', linklibdrawer, kupu);

    var imagelibdrawer = new ImageLibraryDrawer(imagetool, conf['image_xsl_uri'],
                                                conf['image_libraries_uri'],
                                                conf['search_images_uri'], drawerparent);
    drawertool.registerDrawer(prefix+'imagelibdrawer', imagelibdrawer, kupu);

    var linkdrawer = new LinkDrawer(prefix+'div.kupu-linkdrawer', linktool);
    drawertool.registerDrawer(prefix+'linkdrawer', linkdrawer, kupu);

    var tabledrawer = new TableDrawer(prefix+'div.kupu-tabledrawer', tabletool);
    drawertool.registerDrawer(prefix+'tabledrawer', tabledrawer, kupu);

    // register form submit handler, remove the drawer's contents before submitting 
    // the form since it seems to crash IE if we leave them alone
    function prepareForm(event) {
        kupu.saveDataToField(this.form, this);
        var drawer = window.document.getElementById('kupu-librarydrawer');
        if (drawer) {
            drawer.parentNode.removeChild(drawer);
        }
    };
    addEventHandler(textarea.form, 'submit', prepareForm, textarea);

    return kupu;
};

// modify LinkDrawer so all links have a target
// defaults to _self, override here if reqd.
//LinkDrawer.prototype.target = '_blank';

