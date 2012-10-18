/*****************************************************************************
 *
 * Copyright (c) 2004 Guido Wesdorp. All rights reserved.
 *
 * This software is distributed under the terms of the i18n.js
 * License. See LICENSE.txt for license text.
 *
 *****************************************************************************/

function MessageCatalog() {
    /* Simple i18n registry

        An XML island is used to produce a mapping from msgid to phrase,
        the phrase can optionally contain interpolation terms in the format
        ${name}. Each string in the application should be retrieved from
        this object using the 'translate' method with the msgid as argument,
        optionally called with a mapping (object) from name to value which
        will be used for the interpolation. Example:

        Say we have the following XML island (note that this also serves as
        an example for the format):

        <xml id="i18n">
          <i18n language="en">
            <foo>bar</foo>
            <someline>this is a ${type} line</someline>
          </i18n>
        </xml>

        we can create an MessageCatalog object like this:

        var mc = new MessageCatalog();
        mc.initialize(document, 'i18n');

        and can then make the following calls:

        mc.translate('foo'); # would result in 'bar'
        mc.translate('someline', {'type': 'short'}); # 'this is a short line'

    */
    this.mapping = {};
};

MessageCatalog.prototype.initialize = function(doc, elid) {
    /* load the mapping from XML

        if you don't call this function, no translation will be done,
        but the object is still usable
    */
    var mapping = this.getCatalogFromXML(doc, elid);
    this.mapping = mapping;
};

MessageCatalog.prototype.getCatalogFromXML = function(doc, elid) {
    /* Parse the message catalog XML

        If called with a single arg, that arg should be some XML document.
        If called with 2 args, the first one is the HTML 'document' and
        the second the id of the XML element containing the message catalog.
    */
    if (elid) {
        doc = doc.getElementById(elid);
    };
    var mapping = {};
    var items = doc.getElementsByTagName('message');
    for (var i=0; i < items.length; i++) {
        var msgid = this.getTextFromNode(
                        items[i].getElementsByTagName('msgid')[0]);
        var msgstr = this.getTextFromNode(
                        items[i].getElementsByTagName('msgstr')[0]);
        mapping[msgid] = msgstr;
    };

    return mapping;
};

MessageCatalog.prototype.getTextFromNode = function(node) {
    /* returns the text contents of a single, not-nested node */
    var text = '';
    for (var i=0; i < node.childNodes.length; i++) {
        var child = node.childNodes[i];
        if (child.nodeType != 3) {
            continue;
        };
        text += child.nodeValue.reduceWhitespace().strip();
    };
    return text;
};

MessageCatalog.prototype.acents = function( msg ) {

	msg = msg.replace("\\xBF", "&iquest;"); /* ¿ */
	msg = msg.replace("\\xe1", "&aacute;"); /* á */
	msg = msg.replace("\\xe9", "&eacute;"); /* é */
	msg = msg.replace("\\xed", "&iacute;"); /* í */
	msg = msg.replace("\\xf3", "&oacute;"); /* ó */
	msg = msg.replace("\\xfa", "&uacute;"); /* ú */
	msg = msg.replace("\\xf1", "&ntilde;"); /* ñ */

	return msg;
};

MessageCatalog.prototype.translate = function(msgid, interpolations) {
    var translated = this.mapping[msgid];
    if (!translated) {
        translated = msgid;
    };
    if (interpolations) {
        for (var id in interpolations) {
            var value = interpolations[id];
            var reg = new RegExp('\\\$\\\{' + id + '\\\}', 'g');
            translated = translated.replace(reg, value);
        };
    };
    return translated;
};

// instantiate a global MessageCatalog for all scripts to use
window.i18n_message_catalog = new MessageCatalog();

// make a gettext-style _ function globally available
window._ = new ContextFixer(window.i18n_message_catalog.translate,
            window.i18n_message_catalog).execute;

function HTMLTranslator() {
    /* finds Zope-style messageids in the HTML and translates them

        tries to be completely compatible to allow using Zope tools etc.
    */
};

HTMLTranslator.prototype.translate = function(doc, catalog) {
    this.doc = doc;
    if (!catalog) {
        catalog = window.i18n_message_catalog;
    };
    this.catalog = catalog;
    var docel = doc.documentElement;
    var iterator = new NodeIterator(docel);
    while (true) {
        var node = iterator.next();
        if (!node) {
            break;
        };
        this.handleNode(node);
    };
};


if (document.all) {
    HTMLTranslator.prototype.handleNode = function(node) {
        /* find out if the node contains i18n attrs and if so handle them

            it seems that the only way to not make IE barf on getAttributes
            if an attribute doesn't exist (hasAttribute sometimes reports it's
            there when it's not) is use a try block, but it slows Moz down
            like crazy, hence the two different methods
        */
        if (node.nodeType != 1) {
            return;
        };
        try {
            var t = node.getAttribute('i18n:translate');
            this.handle_i18n_translate(node, t);
        } catch(e) {
            // IE seems to barf on certain node types
        };
        try {
            var a = node.getAttribute('i18n:attributes');
            this.handle_i18n_attributes(node, a);
        } catch(e) {
        };
    };
} else {
    HTMLTranslator.prototype.handleNode = function(node) {
        /* find out if the node contains i18n attrs and if so handle them */
        if (node.nodeType != 1) {
            return;
        };
        if (node.hasAttribute('i18n:translate')) {
            var t = node.getAttribute('i18n:translate');
            this.handle_i18n_translate(node, t);
        };
        if (node.hasAttribute('i18n:attributes')) {
            var a = node.getAttribute('i18n:attributes');
            this.handle_i18n_attributes(node, a);
        };
    };
};

HTMLTranslator.prototype.handle_i18n_translate = function(node, midstring) {
    var mid;
    if (midstring.strip() != '') {
        mid = midstring;
    } else {
        mid = node.innerHTML;
    };
    mid = mid.strip().reduceWhitespace();
    node.innerHTML = this.catalog.translate(mid);
};

HTMLTranslator.prototype.handle_i18n_attributes = function(node, attrstring) {
    var attrnames = attrstring.split(';');
    for (var i=0; i < attrnames.length; i++) {
        var attr = node.getAttribute(attrnames[i]).strip();
        node.setAttribute(attrnames[i], this.catalog.translate(attr));
    };
};

//-----------------------------------------------------------------------------
// from (kupu)helpers.js
//-----------------------------------------------------------------------------

function ContextFixer(func, context) {
    /* Make sure 'this' inside a method points to its class */
    this.func = func;
    this.context = context;
    this.args = arguments;
    var self = this;

    this.execute = function() {
        /* execute the method */
        var args = new Array();
        // the first arguments will be the extra ones of the class
        for (var i=0; i < self.args.length - 2; i++) {
            args.push(self.args[i + 2]);
        };
        // the last are the ones passed on to the execute method
        for (var i=0; i < arguments.length; i++) {
            args.push(arguments[i]);
        };

        return self.func.apply(self.context, args);
    };
};

function NodeIterator(node, continueatnextsibling) {
    /* simple node iterator

        can be used to recursively walk through all children of a node,
        the next() method will return the next node until either the next
        sibling of the startnode is reached (when continueatnextsibling is
        false, the default) or when there's no node left (when
        continueatnextsibling is true)

        returns false if no nodes are left
    */

    this.node = node;
    this.current = node;
    this.terminator = continueatnextsibling ? null : node;

    this.next = function() {
        /* return the next node */
        if (!this.current) {
            // restart
            this.current = this.node;
        };
        var current = this.current;
        if (current.firstChild) {
            this.current = current.firstChild;
        } else {
            // walk up parents until we finish or find one with a nextSibling
            while (current && current != this.terminator && !current.nextSibling) {
                current = current.parentNode;
            };
            if (!current || current == this.terminator) {
                this.current = false;
            } else {
                this.current = current.nextSibling;
            };
        };
        return this.current;
    };

    this.reset = function() {
        /* reset the iterator so it starts at the first node */
        this.current = this.node;
    };

    this.setCurrent = function(node) {
        /* change the current node

            can be really useful for specific hacks, the user must take
            care that the node is inside the iterator's scope or it will
            go wild
        */
        this.current = node;
    };
};

String.prototype.strip = function() {
    var stripspace = /^\s*([\s\S]*?)\s*$/;
    return stripspace.exec(this)[1];
};

String.prototype.reduceWhitespace = function() {
    /* returns a string in which all whitespace is reduced
        to a single, plain space */
    var spacereg = /(\s+)/g;
    var copy = this;
    while (true) {
        var match = spacereg.exec(copy);
        if (!match) {
            return copy;
        };
        copy = copy.replace(match[0], ' ');
    };
};

