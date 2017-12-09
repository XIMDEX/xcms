##############################################################################
#
# Copyright (c) 2003-2005 Kupu Contributors. All rights reserved.
#
# This software is distributed under the terms of the Kupu
# License. See LICENSE.txt for license text. For a list of Kupu
# Contributors see CREDITS.txt.
#
##############################################################################
"""Kupu Plone interfaces

$Id: interfaces.py 9879 2005-03-18 12:04:00Z yuppie $
"""
from Interface import Interface

class ILibraryManager(Interface):
    """Provide an interface for managing and retrieving libraries for
    the Kupu editor.
    """

    def getLibraries(context):
        """Return an ordered sequence of libraries.

        Since libraries might be defined placefully, we look them up
        using a context. The return value is provided as a sequence of
        dictionaries with the following keys:

          id    - the computed id
          title - the computed title of the library
          uri   - the computed URI of the library
          src   - the computed source URI
          icon  - the computed icon URI
        """

    def addLibrary(id, title, uri, src, icon):
        """Add a library.
        """

    def deleteLibraries(indices):
        """Delete libraries
        """

    def updateLibraries(libraries):
        """Update libraries.

        Update libraries using the sequence of mapping objects
        provided in the 'libraries' parameter. Each mapping object
        needs to provide an 'index' key to indicate which library it
        is updating.
        """

    def moveUp(indices):
        """Reorder libraries by moving specified libraries up.
        """

    def moveDown(indices):
        """Reorder libraries by moving specified libraries down.
        """

class IResourceTypeMapper(Interface):
    """Map portal types to resource types"""

    def getPortalTypesForResourceType(resource_type):
        """Return a sequence of portal types for a specific resource type.

        Raises KeyError if resource_type is not found.
        """

    def queryPortalTypesForResourceType(resource_type, default=None):
        """Return a sequence of portal types for a specific resource type.

        Returns 'default' if resource_type is not found.
        """

    def addResourceType(resource_type, portal_types):
        """Add a resource type pointing to a sequence of portal_types."""

    def updateResourceTypes(type_mapping):
        """Update resource types using the type mapping passed as argument."""

    def deleteResourceTypes(resource_types):
        """Delete the type mapping for the specififed resource types

        Raises KeyError if one of the resource_types is not found.
        """

class IKupuLibraryTool(ILibraryManager, IResourceTypeMapper):
    """Interface for the Kupu library tool"""
