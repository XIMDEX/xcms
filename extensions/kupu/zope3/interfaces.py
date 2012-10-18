##############################################################################
#
# Copyright (c) 2003-2005 Kupu Contributors. All rights reserved.
#
# This software is distributed under the terms of the Kupu
# License. See LICENSE.txt for license text. For a list of Kupu
# Contributors see CREDITS.txt.
#
##############################################################################
"""kupu interfaces for Zope3

kupu for Zope3 consist of a an IHTMLBody field and a widget (browser
view) to a zope.schema field that we call HTMLBody (since it contains
the contents of an HTML body). As a widget, it can make no assumptions
whatsoever on the object that its field is being part of. It can only
make assumptions on the field itself.

We therefore also define the IKupuAsynchronousCapable, a marker
interface that can be set on content objects (even on an object per
object basis) and tells the Zope3 view machiner that our special Kupu
views, the ones that make asynchronous editing possible, apply.

$Id: interfaces.py 9879 2005-03-18 12:04:00Z yuppie $
"""

from zope.interface import Interface
from zope.schema.interfaces import IBytes
from zope.schema import Bool

class IHTMLBody(IBytes):
    """A field that stores the body of an HTML document.
    """

    html2xhtml1 = Bool(
        title=u"Convert browser generated HTML to well-formed XHTML1",
        default=False
        )

class IKupuAsynchronousCapable(Interface):
    """Any content object that wants to support kupu asynchore editing
    will have to implement this marker interface
    """
