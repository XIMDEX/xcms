/*****************************************************************************
 *
 * Copyright (c) 2003-2005 Kupu Contributors. All rights reserved.
 *
 * This software is distributed under the terms of the Kupu
 * License. See LICENSE.txt for license text. For a list of Kupu
 * Contributors see CREDITS.txt.
 *
 *****************************************************************************/

// $Id: test_kupubasetools.js 9984 2005-03-21 14:29:21Z yuppie $

function KupuUITestCase() {
    this.name = 'KupuUITestCase';
    SelectionTestCase.apply(this);
    this.base_setUp = this.setUp;

    this.setUp = function() {
        this.base_setUp();
        this.editor = new KupuEditor(this.kupudoc, {}, null);
        this.ui = new KupuUI('kupu-tb-styles');
        this.ui.editor = this.editor;
    };

    this.test_updateState = function() {
        this.body.innerHTML = '<p>foo</p><pre>bar</pre><p>baz</p>';
        var node = this.body.getElementsByTagName('pre')[0];
        this.ui.tsselect.selectedIndex = 0;
        this.assertEquals(this.ui.tsselect.selectedIndex, 0);
        this.ui.updateState(node);
        this.assertEquals(this.ui.tsselect.selectedIndex, 3);
    };

    this.test_setTextStyle = function() {
        this.body.innerHTML = '<p>foo</p><p>bar</p><p>baz</p>';
        // select                          |bar|
        this._setSelection(4, null, 7, null, 'bar');
        this.ui.setTextStyle('h1');
        this.assertEquals(this._cleanHtml(this.body.innerHTML),
                          '<p>foo</p><h1>bar</h1><p>baz</p>');
    };

    this.XXXtest_setTextStyleReplacingDiv = function() {
        this.body.innerHTML = '<p>foo</p><div>bar</div><p>baz</p>';
        // select                          |bar|
        this._setSelection(4, null, 7, null, 'bar');
        this.ui.setTextStyle('h1');
        this.assertEquals(this._cleanHtml(this.body.innerHTML),
                          '<p>foo</p><h1>bar</h1><p>baz</p>');
    };
};

KupuUITestCase.prototype = new SelectionTestCase;

function ImageToolTestCase() {
    this.name = 'KupuUITestCase';
    SelectionTestCase.apply(this);
    this.base_setUp = this.setUp;

    this.setUp = function() {
        this.base_setUp();
        this.editor = new KupuEditor(this.kupudoc, {}, new DummyLogger());
        this.editor._initialized = true;
        this.imagetool = new ImageTool();
        this.imagetool.editor = this.editor;
    };

    this.test_createImage = function() {
        this.body.innerHTML = '<p>foo bar baz</p>';
        // select                    |bar|
        this._setSelection(4, null, 7, null, 'bar');
        this.imagetool.createImage('bar.png');
        this.assertEquals(this._cleanHtml(this.body.innerHTML),
                          '<p>foo <img src="bar.png"> baz</p>');
    };

    this.test_createImageFull = function() {
        this.body.innerHTML = '<p>foo bar baz</p>';
        // select                    |bar|
        this._setSelection(4, null, 7, null, 'bar');
        this.imagetool.createImage('bar.png', 'spam', 'image-inline');
        var nodes = this.body.getElementsByTagName('img');
        this.assertEquals(nodes.length, 1);
        this.assertEquals(nodes[0].className, 'image-inline');
        this.assertEquals(nodes[0].alt, 'spam');
    };
};

ImageToolTestCase.prototype = new SelectionTestCase;
