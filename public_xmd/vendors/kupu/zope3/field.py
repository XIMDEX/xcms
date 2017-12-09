##############################################################################
#
# Copyright (c) 2003-2005 Kupu Contributors. All rights reserved.
#
# This software is distributed under the terms of the Kupu
# License. See LICENSE.txt for license text. For a list of Kupu
# Contributors see CREDITS.txt.
#
##############################################################################
"""HTMLBody field

$Id: field.py 9879 2005-03-18 12:04:00Z yuppie $
"""

from zope.interface import implements
from zope.schema import Bytes
from zope.schema.fieldproperty import FieldProperty

from interfaces import IHTMLBody

class HTMLBody(Bytes):
    implements(IHTMLBody)

    html2xhtml1 = FieldProperty(IHTMLBody['html2xhtml1'])
