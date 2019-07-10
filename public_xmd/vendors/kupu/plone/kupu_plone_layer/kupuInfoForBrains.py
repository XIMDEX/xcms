## Script (Python) "kupuInfoForBrains"
##title=Provide dictionaries with information about a list of catalog brains
##bind container=container
##bind context=context
##bind namespace=
##bind script=script
##bind subpath=traverse_subpath
##parameters=values, linkhere=False, linkparent=False, showimagesize=False
from Products.CMFCore.utils import getToolByName
import AccessControl
from AccessControl import Unauthorized

request = context.REQUEST
response = request.RESPONSE
response.setHeader('Cache-Control', 'no-cache')

types_tool = getToolByName(context, 'portal_types')
kupu_tool = getToolByName(context, 'kupu_library_tool')
url_tool = getToolByName(context, 'portal_url')
uid_catalog = getToolByName(context, 'uid_catalog', None)

linkbyuid = kupu_tool.getLinkbyuid()
coll_types = kupu_tool.queryPortalTypesForResourceType('collection', ())
preview_action = 'kupupreview'
portal_base = url_tool.getPortalPath()
prefix_length = len(portal_base)+1

# The redirecting url must be absolute otherwise it won't work for
# preview when the page is using portal_factory
# The absolute to relative conversion when the document is saved
# should strip the url right back down to resolveuid/whatever.
base = context.absolute_url()
security = AccessControl.getSecurityManager()

def info_object(obj, allowCollection=True):
    '''Get information from a content object'''

    # Parent folder might not be accessible if we came here from a
    # search.
    if not security.checkPermission('View', obj):
        return None

    try:
        id = obj.getId()
        portal_type = getattr(obj, 'portal_type','')
        collection = allowCollection and portal_type in coll_types

        # Plone issue #4769: this should use
        # IReferenceable.implements(), only that isn't exposed to
        # scripts.
        if linkbyuid and not collection and hasattr(obj.aq_explicit, 'UID'):
            url = base+'/resolveuid/%s' % obj.UID()
        else:
            url = obj.absolute_url()


        icon = "%s/%s" % (context.portal_url(), obj.getIcon(1))
        width = height = size = None
        preview = obj.getTypeInfo().getActionById(preview_action, None)

        try:
                size = context.getObjSize(obj)
        except:
            size = None

        if showimagesize:
            width = getattr(obj, 'width', None)
            height = getattr(obj, 'height', None)
            if callable(width): width = width()
            if callable(height): height = height()

        title = obj.Title() or obj.getId()
        description = obj.Description()

        return {'id': id, 'url': url, 'portal_type': portal_type,
              'collection':  collection, 'icon': icon, 'size': size,
              'width': width, 'height': height,
              'preview': preview, 'title': title, 'description': description,
              }
    except Unauthorized:
        return None

def info(brain, allowCollection=True):
    '''Get information from a brain'''
    id = brain.getId

    url = brain.getURL()
    portal_type = brain.portal_type
    collection = portal_type in coll_types

    # Path for the uid catalog doesn't have the leading '/'
    path = brain.getPath()
    UID = None
    if path and uid_catalog:
        try:
            metadata = uid_catalog.getMetadataForUID(path[prefix_length:])
        except KeyError:
            metadata = None
        if metadata:
            UID = metadata.get('UID', None)

    if linkbyuid and not collection and UID:
        url = base+'/resolveuid/%s' % UID
    else:
        url = brain.getURL()

    icon = "%s/%s" % (context.portal_url(), brain.getIcon)
    width = height = size = None
    preview = types_tool.getTypeInfo(brain.portal_type).getActionById(preview_action, None)

    # It would be nice to do everything from the brain, but
    # unfortunately we need to get the object for the preview size.
    # XXX Figure out some way to get the image size client side
    # instead of inserting it here.
    if showimagesize:
        obj = brain.getObject()
        if hasattr(obj, 'get_size'):
            size = context.getObjSize(obj)
        width = getattr(obj, 'width', None)
        height = getattr(obj, 'height', None)
        if callable(width): width = width()
        if callable(height): height = height()
        
    title = brain.Title or brain.getId
    description = brain.Description

    return {'id': id, 'url': url, 'portal_type': portal_type,
          'collection':  collection, 'icon': icon, 'size': size,
          'width': width, 'height': height,
          'preview': preview, 'title': title, 'description': description,
          }
          
# For Plone 2.0.5 compatability, if getId is callable we assume
# we have an object rather than a brains.
if values and callable(values[0].getId):
    info = info_object

# return [info(brain) for brain in values]
res = []

portal = url_tool.getPortalObject()
if linkhere and portal is not context:
    data = info_object(context, False)
    if data:
        data['label'] = '. (%s)' % context.title_or_id()
        res.append(data)

if linkparent:
    if portal is not context:
        data = info_object(context.aq_parent, True)
        if data:
            data['label'] = '.. (Parent folder)'
            res.append(data)
            
for obj in values:
    data = info(obj, True)
    if data:
        res.append(data)
return res
