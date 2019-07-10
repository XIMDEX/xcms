##############################################################################
#
# Copyright (c) 2003-2005 Kupu Contributors. All rights reserved.
#
# This software is distributed under the terms of the Kupu
# License. See LICENSE.txt for license text. For a list of Kupu
# Contributors see CREDITS.txt.
#
##############################################################################
"""kupu package initialization

This module does some twirks to let us use kupu with Zope2, CMF/Plone
and Zope3

$Id: __init__.py 22394 2006-01-18 15:45:19Z guido $
"""

# we need this for the CMF install script
kupu_globals = globals()

# test for Zope2
try:
    import Zope2 as Zope
    have_zope2 = 1
except ImportError:
    have_zope2 = 0
    
if not have_zope2:
    try:
        import Zope
        have_zope2 = 1
    except ImportError:
        have_zope2 = 0

# test for CMF
try:
    import Products.CMFCore
    have_cmf = 1
except ImportError:
    have_cmf = 0

# test for Plone, removed because Plone isn't supported yet
try:
    import Products.CMFPlone
    have_plone = 1
except ImportError:
    have_plone = 0

# test for FileSystemSite
try:
    import Products.FileSystemSite
    have_fss = 1
except ImportError:
    have_fss = 0

# do the minimal stuff for skin registering
# note that CMF/Plone users will still have to run the
# Extensions/Install.py script
if have_cmf:
    # Need to do this in case Archetypes isn't present.
    from Products.CMFCore.FSFile import FSFile
    from Products.CMFCore.DirectoryView import registerFileExtension, registerDirectory
    registerFileExtension('xsl', FSFile)
    registerDirectory('common', globals())

    if have_plone:
        from Products.kupu.plone import initialize

elif have_zope2 and have_fss:
    import zope2
    initialize = zope2.initialize

if have_zope2:
    # in Zope 2, make all modules in the 'python' lib available
    def __allow_access_to_unprotected_subobjects__(name, value=None):
        return name in ('python')

    from AccessControl import allow_module
    allow_module('Products.kupu.python.nationalizer')
    allow_module('Products.kupu.python.spellcheck')

# do nothing for zope3 (all is done in zcml)
