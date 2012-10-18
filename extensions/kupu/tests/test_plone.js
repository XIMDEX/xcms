/*****************************************************************************
 *
 * Copyright (c) 2003-2005 Kupu Contributors. All rights reserved.
 *
 * This software is distributed under the terms of the Kupu
 * License. See LICENSE.txt for license text. For a list of Kupu
 * Contributors see CREDITS.txt.
 *
 *****************************************************************************/

// $Id: test_plone.js 15966 2005-08-11 15:16:18Z duncan $

// Various tests for html -> xhtml processing.

function KupuPloneTestCase() {
    SelectionTestCase.apply(this);
    this.base_setUp = this.setUp;
    this.name = 'KupuPloneTestCase';

    this.incontext = function(s) {
        return '<html><head><title>test</title></head><body>'+s+'</body></html>';
    }
    this.verifyResult = function(actual, expected) {
        //var expected = this.incontext(exp);

        if (actual == expected)
            return;

        var context = /<html><head><title>test<\/title><\/head><body>(.*)<\/body><\/html>/;
        if (context.test(actual) && context.test(expected)) {
            var a = context.exec(actual)[1];
            var e = context.exec(expected)[1];
            throw('Assertion failed: ' + a + ' != ' + e);
        }
        throw('Assertion failed: ' + actual + ' != ' + expected);
    }

    this.setUp = function() {
        this.base_setUp();
        this.editor = new KupuEditor(this.kupudoc, {}, null);
        this.ui = new PloneKupuUI('kupu-tb-styles');
        this.ui.editor = this.editor;
    };

    this.testRelativeLinks1 = function() {
        var data =  '<a href="http://localhost/cms/folder/emptypage#_ftnref1">[1]</a>';
        var expected = '<a href="#_ftnref1">[1]</a>';
        var base = 'http://localhost/cms/folder/';

        var actual = this.editor.makeLinksRelative(data, base);
        this.verifyResult(actual, expected);
    }

    this.testRelativeLinks2 = function() {
        var data =  '<a href="http://localhost/cms/folder/otherdoc#key">[1]</a>';
        var expected = '<a href="otherdoc#key">[1]</a>';
        var base = 'http://localhost/cms/folder/';

        var actual = this.editor.makeLinksRelative(data, base);
        this.verifyResult(actual, expected);
    }

    this.testRelativeLinks3 = function() {
        var data =  '<a href="http://localhost/cms/otherfolder/otherdoc">[1]</a>';
        var expected = '<a href="../otherfolder/otherdoc">[1]</a>';
        var base = 'http://localhost/cms/folder/';

        var actual = this.editor.makeLinksRelative(data, base);
        this.verifyResult(actual, expected);
    }

    this.testRelativeLinks4 = function() {
        var data =  '<a href="http://localhost:9080/plone/Members/admin/art1">[1]</a>';
        var expected = '<a href="art1">[1]</a>';
        var base = 'http://localhost:9080/plone/Members/admin/art1';

        var actual = this.editor.makeLinksRelative(data, base);
        this.verifyResult(actual, expected);
    }

    this.testRelativeLinks5 = function() {
        var data =  '<a href="http://localhost:9080/plone/Members/admin/art1/subitem">[1]</a>';
        var expected = '<a href="art1/subitem">[1]</a>';
        var base = 'http://localhost:9080/plone/Members/admin/art1';

        var actual = this.editor.makeLinksRelative(data, base);
        this.verifyResult(actual, expected);
    }

    this.testRelativeLinks6 = function() {
        var data =  '<a href="http://localhost:9080/plone/Members/admin">[1]</a>';
        var expected = '<a href=".">[1]</a>';
        var base = 'http://localhost:9080/plone/Members/admin/art1';

        var actual = this.editor.makeLinksRelative(data, base);
        this.verifyResult(actual, expected);
    }

    this.testSetTextStyle = function() {
        var data = '<p>line 1</p><div class="Caption">line 2</div><div class="Caption">line 3</div>';
        // select  .....................................|e 2</div><div class="Caption">line|...
        var expected = '<p>line 1</p><h2>line 2</h2><h2>line 3</h2>';
        this.body.innerHTML = data;
        this._setSelection(10, null, 18, null, 'e 2line');
        this.ui.setTextStyle('h2');
        this.assertEquals(this._cleanHtml(this.body.innerHTML), expected);
    }

    this.testSetTextStyleTable = function() {
        var data = '<table><tbody><tr><td>test</td></tr></tbody></table>';
        // select   ................es...................
        var expected = '<table><tbody><tr><td><div class="te st">test</div></td></tr></tbody></table>';
        var withheader = '<table><tbody><tr><th>test</th></tr></tbody></table>';
        this.body.innerHTML = data;
        var idx = _SARISSA_IS_IE ? 2 : 1;
        this._setSelection(idx, null, idx+2, null, 'es');
        this.ui.setTextStyle('div|te st'); // Space in class forces IE to put it in quotes!
        this.assertEquals(this._cleanHtml(this.body.innerHTML), expected);
        this._setSelection(idx, null, idx+2, null, 'es');
        this.ui.setTextStyle('td');
        this.assertEquals(this._cleanHtml(this.body.innerHTML), data);
        this.ui.setTextStyle('th');
        this.assertEquals(this._cleanHtml(this.body.innerHTML), withheader);
    }
}

KupuPloneTestCase.prototype = new SelectionTestCase;
