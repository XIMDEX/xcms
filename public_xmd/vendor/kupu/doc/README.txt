====
Kupu
====

What is Kupu?
-------------

Kupu is a cross-browser WYWSIWYG editor. It allows the comfortable
editing of the body of an HTML document. It's client-side (browser)
requirements are one of:

  - Mozilla 1.3.1 or higher

  - Internet Explorer 5.5 or higher

  - Netscape Navigator 7.1 or higher

Server-side there are hardly any requirements, except for some way of
processing data (CGI or something more fancy like PHP, ASP or Python
scripts in Zope).

Kupu is particularly suited for content migration as well as editing.
Content copied from an existing web page is pasted with all formatting
intact. This includes structure such as headings and lists, plus links,
image references, text styling, and other aspects. Copying text from a
word processor with an HTML clipboard - such as MSWord - works exactly
the same.

Kupu will clean up the content before it is sent to the server, and can
send data to the server asynchronously using PUT (which allows the data 
to be saved without reloading the page) as well as in a form.

Kupu can be customized on many different levels, allowing a lot of changes
from CSS, but also providing a JavaScript extension API.


More documentation...
---------------------

General information

  o Authors: see CREDITS.txt

  o License: see LICENSE.txt

  o Frequently asked questions: see FAQ.txt

Installation

  o general: see INSTALL.txt

  o Zope 2.x: see ZOPE2.txt

  o Plone 2.x: see PLONE2.txt

Developing

  o Customizing kupu: see CUSTOMIZING.txt

  o Extending kupu: see EXTENDING.txt

  o Java Script API: see JSAPI.txt

  o Old browser support: see OLDBROWSERS.txt

  o Templating system: see TEMPLATE-SYSTEM.txt

  o Library Feature Specification: see LIBRARIES.txt


Homepage
--------

Kupu has a homepage at http://kupu.oscom.org


Reporting bugs
--------------

Please report bugs to the issue tracker available at:
http://codespeak.net/issues/kupu/ (mind the trailing slash).


Mailing list
------------

There is a mailing lists for Kupu development: kupu-dev@codespeak.net


License
-------

Unless otherwise stated, kupu is released under the Kupu License. See
LICENSE.txt for the license text.

The Sarissa ECMAScript library shipped in this distribution
(common/sarissa.js) is the work of Manos Batis and distributed under
the Kupu License with his kind permission. See the Sarissa homepage at
http://sarissa.sourceforge.net for more information.
