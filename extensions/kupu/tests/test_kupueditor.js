/*****************************************************************************
 *
 * Copyright (c) 2003-2005 Kupu Contributors. All rights reserved.
 *
 * This software is distributed under the terms of the Kupu
 * License. See LICENSE.txt for license text. For a list of Kupu
 * Contributors see CREDITS.txt.
 *
 *****************************************************************************/

// $Id: test_kupueditor.js 15802 2005-08-09 09:06:11Z duncan $

function KupuEditorTestCase() {
    this.name = 'KupuEditorTestCase';

    this.setUp = function() {
        this.editor = new KupuEditor(null, {}, null);
    };

    this.testGetNearestParentOfType = function() {
        var parser = new DOMParser();
        var xmlstring = '<p><a id="outer"><a id="inner"><b><span>some link</span></b> Even more</a></a></p>'
        var doc = parser.parseFromString(xmlstring, 'text/xml');
        this.assertEquals(Sarissa.serialize(doc).strip(),xmlstring);

        var span = doc.documentElement.firstChild.firstChild.firstChild.firstChild;

        // first test with a non-existing parent; we should get null.
        var ret = this.editor.getNearestParentOfType(span, 'br');
        this.assertFalse(ret);

        // now test with a real parent; we expect the exact same node.
        ret = this.editor.getNearestParentOfType(span, 'a');
        var expected = doc.documentElement.firstChild.firstChild;
        this.assert(ret === expected);
        // assert again that we got the nearest...
        this.assertEquals(ret.getAttribute('id'), 'inner');
    };

    this.testRemoveNearestParentOfType = function() {
        var xmlstring = '<p><a id="outer"><a id="inner"><b><span>some link</span></b> Even more</a></a></p>'
        var doc = (new DOMParser()).parseFromString(xmlstring, 'text/xml');
        this.assertEquals(Sarissa.serialize(doc).strip(), xmlstring);

        var span = doc.documentElement.firstChild.firstChild.firstChild.firstChild;

        // first try to remove a parent we don't have; we expect the
        // xml not to change.
        this.editor.removeNearestParentOfType(span, 'br');
        this.assertEquals(Sarissa.serialize(doc).strip(), xmlstring);  

        // now remove a real parent; we expect it to be gone in the
        // resulting xml.
        this.editor.removeNearestParentOfType(span, 'a');
        var expected = '<p><a id="outer"><b><span>some link</span></b> Even more</a></p>';
        this.assertEquals(Sarissa.serialize(doc).strip(), expected);
    };

    this.test_serializeOutputToString = function() {
        var doc = Sarissa.getDomDocument();
        //var docel = doc.documentElement;
        var docel = doc.documentElement ? doc.documentElement : doc;
        var html = doc.createElement('html');
        docel.appendChild(html);
        var head = doc.createElement('head');
        html.appendChild(head);
        var title = doc.createElement('title');
        head.appendChild(title);
        var titletext = doc.createTextNode('foo');
        title.appendChild(titletext);
        var body = doc.createElement('body');
        html.appendChild(body);
        var sometext1 = doc.createTextNode('foo');
        body.appendChild(sometext1);
        var br = doc.createElement('br');
        body.appendChild(br);
        var sometext2 = doc.createTextNode('bar');
        body.appendChild(sometext2);
        var result_not_replaced = '<html><head><title>foo</title></head><body>foo<br/>bar</body></html>';
        this.assertEquals(this.editor._serializeOutputToString(docel),
                          result_not_replaced);
        var result_replaced = '<html><head><title>foo</title></head><body>foo<br />bar</body></html>';
        this.editor.config.compatible_singletons = true;
        this.assertEquals(this.editor._serializeOutputToString(docel),
                          result_replaced);
        var result_strict = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" ' + 
          '"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">\n' + 
          '<html xmlns="http://www.w3.org/1999/xhtml"><head><title>foo</title></head><body>foo<br />bar</body></html>';
        this.editor.config.strict_output = true;
        this.assertEquals(this.editor._serializeOutputToString(docel),
                          result_strict);
    };

    this.testEscapeEntities = function() {
        var test = 'r\xe9diger\r\nhello';
        var expected = 'r&#233;diger\r\nhello';
        this.assertEquals(this.editor.escapeEntities(test), expected);
    };
};

KupuEditorTestCase.prototype = new TestCase;
