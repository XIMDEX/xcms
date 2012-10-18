/*****************************************************************************
 *
 * Copyright (c) 2003-2005 Kupu Contributors. All rights reserved.
 *
 * This software is distributed under the terms of the Kupu
 * License. See LICENSE.txt for license text. For a list of Kupu
 * Contributors see CREDITS.txt.
 *
 *****************************************************************************/

// $Id: kupusilvainit.js 12187 2005-05-11 14:29:58Z guido $

// XXX Port this to the default dist?
KupuEditor.prototype.afterInit = function() {
    // select the line after the first heading, if the document is correctly
    // formatted
    this.getDocument().getWindow().focus();
    var doc = this.getInnerDocument();
    var body = doc.getElementsByTagName('body')[0];
    var h = null;
    var iterator = new NodeIterator(body);
    while (h = iterator.next()) {
        if (h.nodeType == 1 && h.nodeName.toLowerCase() == 'h2') {
            var selection = this.getSelection();
            // okay, the first element node is a h2, select
            // next node, if it doesn't exist create and select
            var next = h.nextSibling;
            if (!next) {
                next = doc.createElement('p');
                next.appendChild(doc.createTextNode('\xa0'));
                body.appendChild(next);
            } else {
                var nodeName = next.nodeName.toLowerCase();
                if (nodeName == 'table') {
                    next = next.getElementsByTagName('td')[0];
                } else if (nodeName == 'ul' || nodeName == 'ol') {
                    next = next.getElementsByTagName('li')[0];
                };
            };
            selection.selectNodeContents(next);
            selection.collapse();
            break;
        } else if (h.nodeType == 1) {
            break;
        };
    };
    // if we don't first focus the outer window, Mozilla won't show a cursor
    window.focus();
    this.getDocument().getWindow().focus();
};

function WidgeteerDrawerTool() {
    this.drawers = {};
    this.current_drawer = null;
};

//WidgeteerDrawerTool.prototype = new DrawerTool;

WidgeteerDrawerTool.prototype.openDrawer = function(id) {
    /* open a drawer 
    
        overridden so we can place the drawer in the parent document
    */
    if (this.current_drawer) {
        this.closeDrawer();
    };
    var drawer = this.drawers[id];
    if (this.isIE) {
        drawer.editor._saveSelection();
    };
    
    // make sure the right drawertool is available in parent
    parent.drawertool = window.drawertool;
    var parentdoc = parent.document;
    var placeholder = parentdoc.getElementById('drawerplaceholder')

    drawer.createContent();
    this.current_drawer = drawer;
    if (!parentdoc.importNode) {
        // f$@%ng IE
        var importNode = function(doc, oNode, bImportChildren){
            var oNew;

            if(oNode.nodeType == 1){
                oNew = doc.createElement(oNode.nodeName);
                for(var i = 0; i < oNode.attributes.length; i++){
                    oNew.setAttribute(oNode.attributes[i].name, 
                                        oNode.attributes[i].value);
                }
                oNew.style.cssText = oNode.style.cssText;
            } else if(oNode.nodeType == 3){
                oNew = doc.createTextNode(oNode.nodeValue);
            }
            
            if(bImportChildren && oNode.hasChildNodes()){
                for(var oChild = oNode.firstChild; oChild; 
                                oChild = oChild.nextSibling){
                    oNew.appendChild(importNode(doc, oChild, true));
                }
            }
            
            return oNew;
        }
        var imported = importNode(parentdoc, drawer.element, 1);
        placeholder.appendChild(imported);
        drawer.element.display = 'none';
    } else {
        parentdoc.importNode(drawer.element, 1);
        placeholder.appendChild(drawer.element);
    };
    drawer.editor.suspendEditing();
    placeholder.style.display = 'block';
};

WidgeteerDrawerTool.prototype.closeDrawer = function(button) {
    if (!this.current_drawer) {
        return;
    };
    this.current_drawer.hide();
    this.current_drawer.editor.resumeEditing();
    this.current_drawer = null;
    var parentdoc = parent.document;
    var placeholder = parentdoc.getElementById('drawerplaceholder')
    placeholder.style.display = 'none';
};

var win = window;
parent.HandleDrawerEnter = function(event, clickid) {
    var key;
    event = event || win.event;
    key = event.which || event.keyCode;

    if (key==13) {
        if (clickid) {
            var button = win.document.getElementById(clickid);
            if (button) {
                button.click();
            }
        }
        event.cancelBubble = true;
        if (event.stopPropogation) event.stopPropogation();

        return false;
    }
    return true;
};

function initKupu(iframe) {
    // we have to perform some tricks to find out what 'our' iframe is in
    // the parent document
    var parentiframes = parent.document.getElementsByTagName('iframe');
    var parentiframe = null;
    for (var i=0; i < parentiframes.length; i++) {
        var pif = parentiframe = parentiframes[i]
        if (pif.contentWindow == window) {
            // load the contents of the textarea into the body element
            iframe.contentWindow.document.getElementsByTagName('body')[0]
                    .innerHTML = pif.textarea.value;
            break
        };
    };

    // first we create a logger
    var l = new DummyLogger();

    // now some config values
    var conf = loadDictFromXML(document, 'kupuconfig');
    
    // the we create the document, hand it over the id of the iframe
    var doc = new KupuDocument(iframe);

    // now we can create the controller
    var kupu = new KupuEditor(doc, conf, l);
    
    var cm = new ContextMenu();
    kupu.setContextMenu(cm);

    // now we can create a UI object which we can use from the UI
    var ui = new KupuUI('kupu-tb-styles');
    kupu.registerTool('ui', ui);

    // function that returns a function to execute a button command
    var execCommand = function(cmd) {
        return function(button, editor) {
            editor.execCommand(cmd);
        };
    };

    var boldchecker = ParentWithStyleChecker(new Array('b', 'strong'),
                                             'font-weight', 'bold');
    var boldbutton = new KupuStateButton('kupu-bold-button', 
                                         execCommand('bold'),
                                         boldchecker,
                                         'kupu-bold',
                                         'kupu-bold-pressed');
    kupu.registerTool('boldbutton', boldbutton);

    var italicschecker = ParentWithStyleChecker(new Array('i', 'em'),
                                                'font-style', 'italic');
    var italicsbutton = new KupuStateButton('kupu-italic-button', 
                                           execCommand('italic'),
                                           italicschecker, 
                                           'kupu-italic', 
                                           'kupu-italic-pressed');
    kupu.registerTool('italicsbutton', italicsbutton);

    var underlinechecker = ParentWithStyleChecker(new Array('u'));
    var underlinebutton = new KupuStateButton('kupu-underline-button', 
                                              execCommand('underline'),
                                              underlinechecker,
                                              'kupu-underline', 
                                              'kupu-underline-pressed');
    kupu.registerTool('underlinebutton', underlinebutton);

    var subscriptchecker = ParentWithStyleChecker(new Array('sub'));
    var subscriptbutton = new KupuStateButton('kupu-subscript-button',
                                              execCommand('subscript'),
                                              subscriptchecker,
                                              'kupu-subscript',
                                              'kupu-subscript-pressed');
    kupu.registerTool('subscriptbutton', subscriptbutton);

    var superscriptchecker = ParentWithStyleChecker(new Array('super', 'sup'));
    var superscriptbutton = new KupuStateButton('kupu-superscript-button', 
                                                execCommand('superscript'),
                                                superscriptchecker,
                                                'kupu-superscript', 
                                                'kupu-superscript-pressed');
    kupu.registerTool('superscriptbutton', superscriptbutton);

    var undobutton = new KupuButton('kupu-undo-button', execCommand('undo'))
    kupu.registerTool('undobutton', undobutton);

    var redobutton = new KupuButton('kupu-redo-button', execCommand('redo'))
    kupu.registerTool('redobutton', redobutton);

    var colorchoosertool = new ColorchooserTool('kupu-forecolor-button',
                                                'kupu-hilitecolor-button',
                                                'kupu-colorchooser');
    kupu.registerTool('colorchooser', colorchoosertool);
    var listtool = new ListTool('kupu-list-ul-addbutton', 'kupu-list-ol-addbutton',
                                'kupu-ulstyles', 'kupu-olstyles');
    kupu.registerTool('listtool', listtool);

    var dltool = new DefinitionListTool('kupu-list-dl-addbutton');
    kupu.registerTool('dltool', dltool);

    var imagetool = new ImageTool();
    kupu.registerTool('imagetool', imagetool);
    var handler = function() {
        var create_handler = new ContextFixer(imagetool.createImage, 
                                                imagetool).execute;
        parent.browser_manager.startBrowser(create_handler,
                parentiframe.textarea.getAttribute('widget:imagepath'),
                                                ['image/jpeg', 'image/png',
                                                    'image/gif']);
    };
    addEventHandler(document.getElementById('kupu-image-button'), 'click',
                    handler);

    var linktool = new LinkTool();
    kupu.registerTool('linktool', linktool);
    var handler = function() {
        var create_handler = new ContextFixer(linktool.createLink, 
                                                    linktool).execute;
        parent.browser_manager.startBrowser(create_handler,
                parentiframe.textarea.getAttribute('widget:linkpath'));
    };
    addEventHandler(document.getElementById('kupu-intlink-button'), 'click',
                    handler);

    var tabletool = new TableTool(); 
    kupu.registerTool('tabletool', tabletool);

    var showpathtool = new ShowPathTool();
    kupu.registerTool('showpathtool', showpathtool);

    var sourceedittool = new SourceEditTool('kupu-source-button',
                                            'kupu-editor-textarea');
    kupu.registerTool('sourceedittool', sourceedittool);

    var spellchecker = new KupuSpellChecker('kupu-spellchecker-button',
                                            'kupu_spellcheck');
    kupu.registerTool('spellchecker', spellchecker);

    var viewsourcetool = new ViewSourceTool();
    kupu.registerTool('viewsourcetool', viewsourcetool);
    
    /*
    // Function that returns function to open a drawer
    var opendrawer = function(drawerid) {
        return function(button, editor) {
            drawertool.openDrawer(drawerid);
        };
    };

    var imagelibdrawerbutton = new KupuButton('kupu-imagelibdrawer-button',
                                              opendrawer('imagelibdrawer'));
    kupu.registerTool('imagelibdrawerbutton', imagelibdrawerbutton);

    var linklibdrawerbutton = new KupuButton('kupu-linklibdrawer-button',
                                             opendrawer('linklibdrawer'));
    kupu.registerTool('linklibdrawerbutton', linklibdrawerbutton);

    var linkdrawerbutton = new KupuButton('kupu-linkdrawer-button',
                                          opendrawer('linkdrawer'));
    kupu.registerTool('linkdrawerbutton', linkdrawerbutton);

    var tabledrawerbutton = new KupuButton('kupu-tabledrawer-button',
                                           opendrawer('tabledrawer'));
    kupu.registerTool('tabledrawerbutton', tabledrawerbutton);

    // create some drawers, drawers are some sort of popups that appear when a 
    // toolbar button is clicked
    var drawertool = new WidgeteerDrawerTool();
    kupu.registerTool('drawertool', drawertool);

    var linklibdrawer = new LinkLibraryDrawer(linktool, conf['link_xsl_uri'],
                                              conf['link_libraries_uri'],
                                              conf['link_images_uri']);
    drawertool.registerDrawer('linklibdrawer', linklibdrawer);

    var imagelibdrawer = new ImageLibraryDrawer(imagetool, conf['image_xsl_uri'],
                                                conf['image_libraries_uri'],
                                                conf['search_images_uri']);
    drawertool.registerDrawer('imagelibdrawer', imagelibdrawer);
    
    var linkdrawer = new LinkDrawer('kupu-linkdrawer', linktool);
    drawertool.registerDrawer('linkdrawer', linkdrawer);

    var tabledrawer = new TableDrawer('kupu-tabledrawer', tabletool);
    drawertool.registerDrawer('tabledrawer', tabledrawer);
    */

//    var nonxhtmltagfilter = new NonXHTMLTagFilter();
//    kupu.registerFilter(nonxhtmltagfilter);

    kupu.xhtmlvalid.setAttrFilter(['is_toc', 'toc_depth', 'is_citation', 
                                    'source', 'author', 'source_id', 
                                    'silva_type', 'alignment', 
                                    'link_to_hires', 'link']);
    // allow all attributes on div, since ExternalSources require that
    kupu.xhtmlvalid.includeTagAttributes(['div'], ['*']);
    kupu.xhtmlvalid.includeTagAttributes(['p'], ['silva_type']);
    kupu.xhtmlvalid.includeTagAttributes(['h6'], ['silva_type']);
    kupu.xhtmlvalid.includeTagAttributes(['img'], ['alignment', 
                                            'link_to_hires', 
                                            'target', 'link']);

    return kupu;
};
