## Script (Python) "kupuGetResourceTypes"
##title=Provide a list of portal types for a resource
##bind container=container
##bind context=context
##bind namespace=
##bind script=script
##bind subpath=traverse_subpath
##parameters=resource_type, includeCollections=False
from Products.CMFCore.utils import getToolByName
kupu_tool = getToolByName(context, 'kupu_library_tool')
types_tool = getToolByName(context, 'portal_types')

portal_types = kupu_tool.queryPortalTypesForResourceType(resource_type, ())

if includeCollections:
    coll_types = kupu_tool.queryPortalTypesForResourceType('collection', ())
    portal_types += coll_types

return { 'portal_type': portal_types }

