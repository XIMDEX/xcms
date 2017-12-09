/* here's where the customized initialization will be done */

function RoundupLinkTool() {
    this.createLinkHandler = function(event) {
        /* create a link according to a url entered in a popup */
        var linkWindow = openPopup('_file/kupu/common/popups/link.html', 300, 200);
        linkWindow.linktool = this;
        linkWindow.focus();
    };
};

RoundupLinkTool.prototype = new LinkTool;

function RoundupImageTool() {
    this.createImageHandler = function(event) {
        /* create an image according to a url entered in a popup */
        var imageWindow = openPopup('_file/kupu/common/popups/image.html', 300, 200);
        imageWindow.imagetool = this;
        imageWindow.focus();
    };
};

RoundupImageTool.prototype = new ImageTool;

function initKupu(iframe) {
    /* Although this is meant to be a sample implementation, it can
        be used out-of-the box to run the sample pagetemplate or for simple
        implementations that just don't use some elements. When you want
        to do some customization, this should probably be overridden. For 
        larger customization actions you will have to subclass or roll your 
        own UI object.
    */

    // first we create a logger
    var l = new DummyLogger();
    
    // now some config values
    // XXX To mimic the 'old' behaviour, vars should be retrieved from the 
    // iframe (attributes)
    var src = iframe.getAttribute('src');
    var dst = iframe.getAttribute('dst');
    if (!dst) {
        dst = '..';
    }
    var use_css = (iframe.getAttribute('usecss') != "0");
    var reload_src = (iframe.getAttribute('reloadsrc') == "1");
    var strict_output = (iframe.getAttribute('strict_output') == "1");
    var content_type = 'application/xhtml+xml';
    if (iframe.getAttribute('content_type')) {
        content_type = iframe.getAttribute('content_type');
    };
    
    var conf = {'src': src,
                'dst': dst,
                'use_css': use_css,
                'reload_after_save': reload_src,
                'strict_output': strict_output,
                'content_type': content_type
                };
    
    // the we create the document, hand it over the id of the iframe
    var doc = new KupuDocument(iframe);
    
    // now we can create the controller
    var kupu = new KupuEditor(doc, conf, l);

    // add the contextmenu
    var cm = new ContextMenu();
    kupu.setContextMenu(cm);

    var listtool = new ListTool('kupu-list-ul-addbutton', 'kupu-list-ol-addbutton', 'kupu-ulstyles', 'kupu-olstyles');
    kupu.registerTool('listtool', listtool);
    
    var linktool = new RoundupLinkTool();
    kupu.registerTool('linktool', linktool);

    var imagetool = new RoundupImageTool();
    kupu.registerTool('imagetool', imagetool);

    // now we can create a UI object which we can use from the UI
    var ui = new KupuUI('kupu-tb-styles');

    // the ui must be registered to the editor as well so it can be notified
    // of state changes
    kupu.registerTool('ui', ui); // XXX Should this be a different method?

    // register some cleanup filter
    // remove tags that aren't in the XHTML DTD
    var nonxhtmltagfilter = new NonXHTMLTagFilter();
    kupu.registerFilter(nonxhtmltagfilter);

    return kupu;
}
