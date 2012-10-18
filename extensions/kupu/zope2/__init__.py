##############################################################################
#
# Copyright (c) 2003-2005 Kupu Contributors. All rights reserved.
#
# This software is distributed under the terms of the Kupu
# License. See LICENSE.txt for license text. For a list of Kupu
# Contributors see CREDITS.txt.
#
##############################################################################
"""Zope2 integration module

This is a solution for plain Zope2 integration using the
FileSystemSite product. FileSystemSite can be found at
http://www.zope.org/Members/k_vertigo/Products/FileSystemSite
Note that FileSystemSite 1.3 is required.

$Id: __init__.py 9879 2005-03-18 12:04:00Z yuppie $
"""

import Globals
from Products.PageTemplates.PageTemplateFile import PageTemplateFile

from Products.FileSystemSite.DirectoryView import DirectoryView
from Products.FileSystemSite.DirectoryView import DirectoryViewSurrogate
from Products.FileSystemSite.DirectoryView import DirectoryRegistry
from Products.FileSystemSite.DirectoryView import registerFileExtension
from Products.FileSystemSite.DirectoryView import manage_listAvailableDirectories
from Products.FileSystemSite.FSFile import FSFile

def initialize(context):
    context.registerClass(
        KupuEditorSurrogate,
        constructors=(('manage_addKupuEditorForm', manage_addKupuEditorForm),
                      manage_addKupuEditor
                      ),
        icon='kupu_icon.gif'
    )

#_dirreg = DirectoryRegistry()
from Products.FileSystemSite.DirectoryView import _dirreg
_dirreg.registerDirectory('../common', globals())

# for library drawers
registerFileExtension('xsl', FSFile)
registerFileExtension('xml', FSFile)

class KupuEditor(DirectoryView):
    meta_type = 'kupu editor'

    def __of__(self, parent):
        info = _dirreg.getDirectoryInfo(self._dirpath)
        if info is not None:
            info = info.getContents(_dirreg)
        if info is None:
            data = {}
            objects = ()
        else:
            data, objects = info
        s = KupuEditorSurrogate(self, data, objects)
        res = s.__of__(parent)
        return res

Globals.InitializeClass(KupuEditor)

class KupuEditorSurrogate(DirectoryViewSurrogate):
    meta_type = "kupu editor"

Globals.InitializeClass(KupuEditorSurrogate)

manage_addKupuEditorForm = PageTemplateFile('addKupuEditor.pt', globals())

def createKupuEditor(parent, filepath, id=None):
    """Adds either a DirectoryView or a derivative object.
    """
    info = _dirreg.getDirectoryInfo(filepath)
    if info is None:
        raise ValueError('Not a registered directory: %s' % filepath)
    if not id:
        id = path.split(filepath)[-1]
    else:
        id = str(id)
    ob = KupuEditor(id, filepath)
    parent._setObject(id, ob)

def manage_addKupuEditor(self, filepath="Products/kupu/common",
                         id=None, REQUEST=None):
    """Adds either an kupu editor object
    """
    createKupuEditor(self, filepath, id)
    if REQUEST is not None:
        return self.manage_main(self, REQUEST)
