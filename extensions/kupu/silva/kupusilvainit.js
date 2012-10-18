/*****************************************************************************
 *
 * Copyright (c) 2003-2005 Kupu Contributors. All rights reserved.
 *
 * This software is distributed under the terms of the Kupu
 * License. See LICENSE.txt for license text. For a list of Kupu
 * Contributors see CREDITS.txt.
 *
 *****************************************************************************/

// $Id: kupusilvainit.js 22576 2006-01-24 10:23:04Z guido $

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

function initSilvaKupu(iframe) {
    // first we create a logger
    var l = new DummyLogger();

    // now some config values
    var conf = loadDictFromXML(document, 'kupuconfig');
    
    // the we create the document, hand it over the id of the iframe
    var doc = new KupuDocument(iframe);

    // now we can create the controller
    var kupu = new KupuEditor(doc, conf, l);
    
    kupu.registerContentChanger(document.getElementById('kupu-editor-textarea'));

    if (kupu.getBrowserName() == 'IE') {
        // IE supports onbeforeunload, so let's use that
        addEventHandler(window, 'beforeunload', saveOnPart);
    } else {
        // some versions of Mozilla support onbeforeunload (starting with 1.7)
        // so let's try to register and if it fails fall back on onunload
        var re = /rv:([0-9\.]+)/
        var match = re.exec(navigator.userAgent)
        if (match[1] && parseFloat(match[1]) > 1.6) {
            addEventHandler(window, 'beforeunload', saveOnPart);
        } else {
            addEventHandler(window, 'unload', saveOnPart);
        };
    };

    var cm = new ContextMenu();
    kupu.setContextMenu(cm);

    // now we can create a UI object which we can use from the UI
    var ui = new SilvaKupuUI('kupu-tb-styles');
    kupu.registerTool('ui', ui);

    var savebuttonfunc = function(button, editor) {editor.saveDocument()};
    var savebutton = new KupuButton('kupu-save-button', savebuttonfunc);
    kupu.registerTool('savebutton', savebutton);

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

    var listtool = new ListTool('kupu-list-ul-addbutton', 'kupu-list-ol-addbutton',
                                'kupu-ulstyles', 'kupu-olstyles');
    kupu.registerTool('listtool', listtool);

    var dltool = new DefinitionListTool('kupu-list-dl-addbutton');
    kupu.registerTool('dltool', dltool);

    var toctool = new SilvaTocTool(
        'kupu-toolbox-toc-depth', 'kupu-toc-add-button', 'kupu-toc-del-button',
        'kupu-toolbox-toc', 'kupu-toolbox', 'kupu-toolbox-active');
    kupu.registerTool('toctool', toctool);
    
    var linktool = new SilvaLinkTool();
    kupu.registerTool('linktool', linktool);
    var linktoolbox = new SilvaLinkToolBox(
        "kupu-link-input", 'kupu-linktarget-select', 'kupu-linktarget-input',
        "kupu-link-addbutton", 'kupu-link-updatebutton',
        'kupu-link-delbutton', 'kupu-toolbox-links', 'kupu-toolbox',
        'kupu-toolbox-active');
    linktool.registerToolBox("linktoolbox", linktoolbox);
  
    var indextool = new SilvaIndexTool(
        "kupu-index-input", 'kupu-index-addbutton', 'kupu-index-updatebutton',
        'kupu-index-deletebutton', 'kupu-toolbox-indexes', 'kupu-toolbox',
        'kupu-toolbox-active');
    kupu.registerTool('indextool', indextool);

    var extsourcetool = new SilvaExternalSourceTool(
        'kupu-toolbox-extsource-id', 'kupu-extsource-formcontainer', 
        'kupu-extsource-addbutton', 'kupu-extsource-cancelbutton',
        'kupu-extsource-updatebutton', 'kupu-extsource-delbutton',
        'kupu-toolbox-extsource', 'kupu-toolbox', 'kupu-toolbox-active');
    kupu.registerTool('extsourcetool', extsourcetool);

    var citationtool = new SilvaCitationTool(
        'kupu-citation-authorinput', 'kupu-citation-sourceinput',
        'kupu-citation-addbutton', 'kupu-citation-updatebutton',
        'kupu-citation-deletebutton');
    kupu.registerTool('citationtool', citationtool);
  
    var abbrtool = new SilvaAbbrTool('kupu-abbr-type-abbr', 'kupu-abbr-type-acronym', 
                                        'kupu-abbr-radiorow', 'kupu-abbr-title',
                                        'kupu-abbr-addbutton', 'kupu-abbr-updatebutton',
                                        'kupu-abbr-deletebutton', 'kupu-toolbox-abbr',
                                        'kupu-toolbox', 'kupu-toolbox-active');
    kupu.registerTool('abbrtool', abbrtool);
  
    var imagetool = new SilvaImageTool(
        'kupu-toolbox-image-edit', 'kupu-toolbox-image-src',
        'kupu-toolbox-image-target', 'kupu-toolbox-image-target-input',
        'kupu-toolbox-image-link-checkbox-hires',
        'kupu-toolbox-image-link',
        'kupu-toolbox-image-align', 'kupu-toolbox-image-alt', 
        'kupu-toolbox-images', 'kupu-toolbox',
        'kupu-toolbox-active');
    kupu.registerTool('imagetool', imagetool);

    var tabletool = new SilvaTableTool(); 
    kupu.registerTool('tabletool', tabletool);
    var tabletoolbox = new SilvaTableToolBox(
        'kupu-toolbox-addtable', 'kupu-toolbox-edittable', 'kupu-table-newrows',
        'kupu-table-newcols','kupu-table-makeheader', 'kupu-table-classchooser',
        'kupu-table-alignchooser', 'kupu-table-columnwidth',
        'kupu-table-addtable-button', 'kupu-table-addrow-button',
        'kupu-table-delrow-button', 'kupu-table-addcolumn-button',
        'kupu-table-delcolumn-button', 'kupu-table-fix-button',
        'kupu-table-delete-button', 'kupu-toolbox-tables', 
        'kupu-toolbox', 'kupu-toolbox-active'
        );
    tabletool.registerToolBox('tabletoolbox', tabletoolbox);

    var propertytool = new SilvaPropertyTool('propsrow', 
                                                'kupu-properties-form');
    kupu.registerTool('properties', propertytool);

    var showpathtool = new ShowPathTool();
    kupu.registerTool('showpathtool', showpathtool);

    var sourceedittool = new SourceEditTool('kupu-source-button',
                                            'kupu-editor-textarea');
    kupu.registerTool('sourceedittool', sourceedittool);

/*
    var spellchecker = new KupuSpellChecker('kupu-spellchecker-button',
                                            'kupu_spellcheck');
    kupu.registerTool('spellchecker', spellchecker);
*/

    var cleanupexpressions = new CleanupExpressionsTool(
            'kupucleanupexpressionselect', 'kupucleanupexpressionbutton');
    kupu.registerTool('cleanupexpressions', cleanupexpressions);

    var viewsourcetool = new ViewSourceTool();
    kupu.registerTool('viewsourcetool', viewsourcetool);
    
    // Function that returns function to open a drawer
    var opendrawer = function(drawerid) {
        return function(button, editor) {
            drawertool.openDrawer(drawerid);
        };
    };

    /*
    var imagelibdrawerbutton = new KupuButton('kupu-imagelibdrawer-button',
                                              opendrawer('imagelibdrawer'));
    kupu.registerTool('imagelibdrawerbutton', imagelibdrawerbutton);

    var linklibdrawerbutton = new KupuButton('kupu-linklibdrawer-button',
                                             opendrawer('linklibdrawer'));
    kupu.registerTool('linklibdrawerbutton', linklibdrawerbutton);
    */

    // create some drawers, drawers are some sort of popups that appear when a 
    // toolbar button is clicked
    var drawertool = new DrawerTool();
    kupu.registerTool('drawertool', drawertool);

    /*
    var linklibdrawer = new LinkLibraryDrawer(linktool, conf['link_xsl_uri'],
                                              conf['link_libraries_uri'],
                                              conf['link_images_uri']);
    drawertool.registerDrawer('linklibdrawer', linklibdrawer);

    var imagelibdrawer = new ImageLibraryDrawer(imagetool, conf['image_xsl_uri'],
                                                conf['image_libraries_uri'],
                                                conf['search_images_uri']);
    drawertool.registerDrawer('imagelibdrawer', imagelibdrawer);
    */
    
//    var nonxhtmltagfilter = new NonXHTMLTagFilter();
//    kupu.registerFilter(nonxhtmltagfilter);

    kupu.xhtmlvalid.setAttrFilter(['is_toc', 'toc_depth', 'is_citation', 
                                    'source', 'author', 'source_id', 
                                    'silva_type', 'alignment', 
                                    'link_to_hires', 'link', 'silva_src',
                                    'silva_href', 'silva_column_info']);
    // allow all attributes on div, since ExternalSources require that
    kupu.xhtmlvalid.includeTagAttributes(['div'], ['*']);
    kupu.xhtmlvalid.includeTagAttributes(['p'], ['silva_type']);
    kupu.xhtmlvalid.includeTagAttributes(['h6'], ['silva_type']);
    kupu.xhtmlvalid.includeTagAttributes(['img'], ['alignment', 
                                            'link_to_hires', 
                                            'target', 'link',
                                            'silva_src']);
    kupu.xhtmlvalid.includeTagAttributes(['a'], ['silva_href']);
    kupu.xhtmlvalid.includeTagAttributes(['table'], ['silva_column_info']);

    if (window.kuputoolcollapser) {
        var collapser = new window.kuputoolcollapser.Collapser(
                                                        'kupu-toolboxes');
        collapser.initialize();
    };
    
    // have to set a blacklist of tags for div, since IE will otherwise
    // save every possible HTML attr for the div
    kupu.xhtmlvalid.excludeTagAttributes(['div'], ['onrowexit', 'onfocusout',
                'onrowsinserted', 'disabled', 'oncopy', 'onresizestart',
                'onerrorupdate', 'tabIndex', 'ondeactivate', 
                'ondataavailable', 'ondragover', 'title', 'accessKey', 
                'onkeypress', 'dataFld', 'onmousemove', 'onactivate',
                'onafterupdate', 'ondrag', 'contentEditable', 'hideFocus',
                'onblur', 'onmouseout', 'oncellchange', 'onmouseleave',
                'onkeydown', 'dataSrc', 'onmousewheel', 'onpaste', 'ondrop',
                'onrowsdelete', 'onrowenter', 'ondragend', 'align', 
                'onlayoutcomplete', 'onbeforedeactivate', 'nofocusrect',
                'ondblclick', 'onselectstart', 'onreadystatechange',
                'dataFormatAs', 'onmousedown', 'onscroll', 'style',
                'implementation', 'onbeforecut', 'oncontrolselect',
                'ondatasetcomplete', 'onmouseup', 'noWrap', 'onfocusin',
                'onresizeend', 'oncontextmenu', 'ondragstart', 'onmoveend',
                'onbeforeeditfocus', 'onpropertychange', 'lang', 
                'onmovestart', 'onkeyup', 'dir', 'onfilterchange',
                'onmouseenter', 'onresize', 'onclick', 'onbeforecopy',
                'onfocus', 'ondatasetchanged', 'id', 'onmove', 'onpage',
                'ondragenter', 'ondragleave', 'oncut', 'onbeforedeactivate',
                'onhelp', 'onlosecapture', 'onbeforeupdate', 'onmouseover',
                'onbeforeactivate']);

    return kupu;
};
