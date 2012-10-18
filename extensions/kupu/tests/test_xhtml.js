/*****************************************************************************
 *
 * Copyright (c) 2003-2005 Kupu Contributors. All rights reserved.
 *
 * This software is distributed under the terms of the Kupu
 * License. See LICENSE.txt for license text. For a list of Kupu
 * Contributors see CREDITS.txt.
 *
 *****************************************************************************/

// $Id: test_xhtml.js 15802 2005-08-09 09:06:11Z duncan $

// Various tests for html -> xhtml processing.

function KupuXhtmlTestCase() {
    this.name = 'KupuXhtmlTestCase';

    this.incontext = function(s) {
        return '<html><head><title>test</title></head><body>'+s+'</body></html>';
    }
    this.verifyResult = function(newdoc, exp) {
        var expected = this.incontext(exp);
        var actual = Sarissa.serialize(newdoc);
        actual = actual.replace('\xa0', '&nbsp;');
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

    this.conversionTest = function(data, expected) {
        var doc = this.doc.documentElement;
        var editor = this.editor;
        this.body.innerHTML = data;
        var xhtmldoc = Sarissa.getDomDocument();
        var newdoc = editor._convertToSarissaNode(xhtmldoc, this.doc.documentElement);
        this.verifyResult(newdoc, expected);
    }

    this.setUp = function() {
        var iframe = document.getElementById('iframe');
        this.doc = iframe.contentWindow.document;
        this.body = this.doc.getElementsByTagName('body')[0];
        this.doc.getElementsByTagName('title')[0].text = 'test';
        this.editor = new KupuEditor(null, {}, null);
    };

    this.arrayContains = function(ary, test) {
        for (var i = 0; i < ary.length; i++) {
            if (ary[i]==test) {
                return true;
            }
        }
        return false;
    }
    this.testExclude = function() {
        // Check that the exclude functions work as expected.
        var validator = new XhtmlValidation(this.editor);
        var events = ['onclick', 'ondblclick', 'onmousedown',
        'onmouseup', 'onmouseover', 'onmousemove',
        'onmouseout', 'onkeypress', 'onkeydown',
        'onkeyup'];
        var expected = ['onclick', 'ondblclick', 'onmousedown', 'onmouseup', 'onmouseout', 'onkeypress', 'onkeyup'];

        var actual = validator._exclude(events, 'onmouseover|onmousemove|onkeydown');
        this.assertEquals(actual.toString(), expected.toString());

        // check is also works with arrays.
        actual = validator._exclude(events, ['onmouseover','onmousemove','onkeydown']);
        this.assertEquals(actual.toString(), expected.toString());

        // Check we have a bgcolor attribute
        this.assertTrue(this.arrayContains(validator.tagAttributes.thead, 'charoff'));
        this.assertTrue(validator.attrFilters['charoff'] != null);
        validator.excludeAttributes(['charoff']);
        this.assertTrue(validator.attrFilters['charoff']==null);
        this.assertTrue(!this.arrayContains(validator.tagAttributes.thead, 'charoff'));
        this.assertTrue(this.arrayContains(validator.tagAttributes.td, 'height'));
        this.assertTrue(this.arrayContains(validator.tagAttributes.th, 'height'));
        validator.excludeTagAttributes(['table','th'], ['width','height']);
        this.assertTrue(this.arrayContains(validator.tagAttributes.td, 'height'));
        this.assertFalse(this.arrayContains(validator.tagAttributes.th, 'height'));
    }

    this.testSet = function() {
        var validator = new XhtmlValidation(this.editor);

        var set1 = new validator.Set(['a','b','c']);
        this.assertTrue(set1.a && set1.b && set1.c);
        var set2 = new validator.Set(set1);
        this.assertTrue(set2.a && set2.b && set2.c);
    }
    this.testValidator = function() {
        var validator = new XhtmlValidation(this.editor);
        var table = validator.States['table'];
        var tags = [];
        for (var tag in table) {
            this.assertEquals(table[tag], 1);
            tags.push(tag);
        }
        this.assertEquals(tags.toString(),
                          ['caption','col','colgroup',
                          'thead','tfoot','tbody','tr'].toString());
    };

    this.testConvertToSarissa = function() {
        var data = '<p class="blue">This is a test</p>';
        this.conversionTest(data, data);
    }
    this.testXmlAttrs = function() {
        var data = '<pre xml:space="preserve" xml:lang="fr">This is a test</pre>';
        var expected1 = '<pre xml:lang="fr" xml:space="preserve">This is a test</pre>';
        this.conversionTest(data, expected1);
        var expected2 = '<pre>This is a test</pre>';
        this.editor.xhtmlvalid.excludeAttributes(['xml:lang','xml:space']);
        this.conversionTest(data, expected2);
    }
    this.testConvertToSarissa2 = function() {
        var data = '<div id="div1">This is a test</div>';
        this.conversionTest(data, data);
    }
    this.testbadTags = function() {
        var data =  '<div><center>centered</center><p>Test</p><o:p>zzz</o:p></div>';
        var expected = '<div>centered<p>Test</p>zzz</div>';
        this.conversionTest(data, expected);
    }
    this.testnbsp = function() {
        var data = '<p>Text with&nbsp;<b>non-break</b> space</p>';
        this.conversionTest(data, data);
    };
    this.teststyle = function() {
        var data = '<p style="text-align:right; mso-silly: green">Text aligned right</p>';
        var expected = '<p style="text-align: left;">Text aligned right</p>';
        var doc = this.doc.documentElement;
        var editor = this.editor;
        this.body.innerHTML = data;
        this.body.firstChild.style.textAlign = 'left';
        this.body.firstChild.style.display = 'block';
        //alert(this.body.firstChild.style.cssText);
        var xhtmldoc = Sarissa.getDomDocument();
        var newdoc = editor._convertToSarissaNode(xhtmldoc, this.doc.documentElement);
        this.verifyResult(newdoc, expected);
    };
    this.testclass = function() {
        var data = '<div class="MsoNormal fred">This is a test</div>';
        var expected = '<div class="fred">This is a test</div>';
        this.conversionTest(data, expected);
    }
    this.testclass2 = function() {
        var data = '<div class="MsoNormal">This is a test</div>';
        var expected = '<div>This is a test</div>';
        this.conversionTest(data, expected);
    }
    this.testTable = function() {
        // N.B. This table contains text and a <P> tag where they
        // aren't legal. Mozilla strips out the <P> tag but lets the
        // text through, IE lets both through.
        
        var data = '<TABLE class="listing">BADTEXT!<THEAD><TR><TH>Col 01</TH><TH class=align-center>Col 11</TH>'+
            '<TH class=align-right>Col 21</TH></TR></THEAD>'+
            '<TBODY><TR>'+
            '<TD>text</TD>'+
            '<TD class=align-center>a</TD>'+
            '<TD class=align-right>r</TD></TR>'+
            '<TR>'+
            '<TD>more text</TD>'+
            '<TD class=align-center>aaa</TD>'+
            '<TD class=align-right>rr</TD></TR>'+
            '<TR>'+
            '<TD>yet more text</TD>'+
            '<TD class=align-center>aaaaa</TD>'+
            '<TD class=align-right>rrr</TD></TR></TBODY><P></TABLE>';
        var expected = '<table class="listing"><thead><tr><th>Col 01</th><th class="align-center">Col 11</th>'+
            '<th class="align-right">Col 21</th></tr></thead>'+
            '<tbody><tr>'+
            '<td>text</td>'+
            '<td class="align-center">a</td>'+
            '<td class="align-right">r</td></tr>'+
            '<tr>'+
            '<td>more text</td>'+
            '<td class="align-center">aaa</td>'+
            '<td class="align-right">rr</td></tr>'+
            '<tr>'+
            '<td>yet more text</td>'+
            '<td class="align-center">aaaaa</td>'+
            '<td class="align-right">rrr</td></tr></tbody></table>';

        this.editor.xhtmlvalid.filterstructure = true;
        this.conversionTest(data, expected);
    }
    this.testCustomAttribute = function() {
        var validator = this.editor.xhtmlvalid;
        var data = '<div special="magic">This is a test</div>';
        this.assertTrue(validator.tagAttributes.td===validator.tagAttributes.th);
        this.editor.xhtmlvalid.includeTagAttributes(['div','td'],['special']);
        // Check that shared arrays are no longer shared...
        this.assertFalse(validator.tagAttributes.td===validator.tagAttributes.th);
        this.assertTrue(this.arrayContains(validator.tagAttributes.td, 'special'));
        this.assertFalse(this.arrayContains(validator.tagAttributes.th, 'special'));
        this.editor.xhtmlvalid.setAttrFilter(['special']);
        this.conversionTest(data, data);
    }

    this.tearDown = function() {
        this.body.innerHTML = '';
    };
}

KupuXhtmlTestCase.prototype = new TestCase;
