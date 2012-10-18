=======
i18n.js
=======

What is it?
-----------

This library can be used to internationalize (i18n is short for
'internationalization') JavaScript code and HTML markup from JavaScript. It
reads a piece of XML embedded in the HTML of a page (you can also load XML
from the server if you want to, example not included though) and can then
optionally walk through the HTML to process all attributes called
'i18n:translate' and 'i18n:replace' ('i18n:translate' is used to translate the
contents of a tag and 'i18n:attributes' to translate attributes) and also it
provides a special function called '_' to allow translation from JS.

Server-side vs. client-side translation
---------------------------------------

The most common (and probably most practical) way of using this library is by
translating the contents of all 'msgstr' XML elements (see below) on the
server. In that case you translate the contents of the HTML together with that
of the XML embedded in it using your preferred technique for XML parsing
(XSLT, TAL) and send the translated lot to the client. From JavaScript your
translated strings will be available immediately.

Another option is to figure out what language the client wants to read, load
an XML message catalog from the server for that language and translate both
the HTML code and JS code completely from JavaScript. This is not generally
useful since it takes a while before the HTML is translated (which makes that
a user can see the original language get turned into the preferred one), and
takes more work to implement. However, if you want to write a fully JavaScript
application, either because your scripts don't run off a webserver with CGI
capabilities or because you want to be completely server-independent, it may
be an interesting option.

How to install and initialize
-----------------------------

Installing is a matter of including a script tag in your HTML that points to
the 'i18n.js' file in this directory. From that moment on, the magical '_'
function can be used from JS code (see below). To actually load a message
catalog, call::

  window.i18n_message_catalog.initialize(document, 'i18n');

where 'i18n' is the id of the 'XML island' (the piece of XML that contains the
message catalog). If you get the message catalog XML from somewhere else, you
can also call the 'initialize' method with a single arg::

  window.i18n_message_catalog.initialize(my_xml_document);

From now on the '_' function will actually translate code (if given useful 
arguments).

Using _
-------

The magical function '_' is probably not new to you if you've worked on i18n
before, since it's the name that 'gettext' (a set of utilities that are used
often to internationalize software) uses for their translating function too.
Note that the signature of the '_' method is different: our '_' method groks 2
arguments, the first one is the messageid and the second one an interpolation
mapping (that can be used to replace substrings of the translated string).

An example::

  // this will just replace the word 'foo' (if it's in the catalog)
  var foo = _('foo');

  // assuming that the catalog contains a messageid 'bar' with a translation
  // of 'foo {bar} baz', this will yield 'foo qux baz'
  var foo = _('bar', {'bar': 'qux'});

Translating HTML
----------------

If you want to translate HTML from JavaScript, you can add 'i18n:translate'
attributes to each tag for complete content translation (if you have HTML
inside the content you will have to use a messageid as the value of the
'i18n:translate' attribute). To translate HTML attributes, you can use the
'i18n:attributes' attribute. As its value you should enter a semi-colon
seperated list of attribute names, the HTMLTranslator (see below) will then
treat the value of all attributes entered as messageids.

To actually translate the 'i18n:translate' and 'i18n:attributes' attributes in
the HTML, you have to create and use an instance of the 'HTMLTranslator'
class:: 

  var translator = new HTMLTranslator();
  translator.translate(document);

*Note: This is only really useful if you translate fully from JavaScript
rather then from the server: if you translate on the server, you can just as
well translate the HTML code directly. This will reduce loading time and looks
better.*

XML format
----------

This library doesn't use the standard '.po' files as its message catalog
format, but instead uses a simple XML format. This is nice if you want to do
translation on the server (since that means you can use the same technique for
translating HTML as for JS code) but not really if you want to do full
client-side translation. Hopefully later versions will include a .po parser.

The format of the XML that was used for the examples is as follows (I don't
use a schema notation for documenting the format since it's *very* simple)::

    <div class="somewhere_in_the_document">
      <!-- this tag *must* be called 'xml' in order for it to work in IE -->
      <xml id="catalog">
        <catalog>
          <message>
            <msgid>
              foo
            </msdig>
            <msgstr>
              bar
            </msgstr>
          </message>
          <message>
            <msgid>
              bar
            </msdig>
            <msgstr>
              bar {baz} qux
            </msgstr>
          </message>
        </catalog>
      </xml>
    </div>

*Note: This is only useful if you fill this XML with translated code on the
server. If you want to actually translate on the client, you will have to
write some code to figure out what language a client wants to use (probably by
asking: browsers don't seem to provide much interesting info) and load a
message catalog using this information. See the 'MessageCatalog.initialize()'
method for more information.*

License information
-------------------

This library can be used under terms of a BSD-style license. For more
information, see LICENSE.txt.

More information
----------------

To find out more about the author of the library, visit
'http://johnnydebris.net'. If you have questions, remarks, bugreports,
patches, or free beer, send an email to 'guido@debris.demon.nl'.
