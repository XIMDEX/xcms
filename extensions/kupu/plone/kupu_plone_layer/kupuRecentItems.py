## Script (Python) "kupuRecentItems"
##title=Retrieve a list of recently published items
##bind container=container
##bind context=context
##bind namespace=
##bind script=script
##bind subpath=traverse_subpath
##parameters=
from Products.CMFCore.utils import getToolByName

request = context.REQUEST
response = request.RESPONSE
response.setHeader('Cache-Control', 'no-cache')

catalog = getToolByName(context, 'portal_catalog')
kupu_tool = getToolByName(context, 'kupu_library_tool')

# We *could* do it like the Plone "Recent Items" portlet and only
# return a list of objects that have been published since the last
# login, but I don't think it serves the purpose here. philiKON.
#member_tool = getToolByName(context, 'portal_membership')
#member = member_tool.getAuthenticatedMember()
#last_login_time = member.getProperty('last_login_time', DateTime());

# the default resource type is mediaobject
resource_type = request.get('resource_type', 'mediaobject')
portal_types = kupu_tool.queryPortalTypesForResourceType(resource_type, ())

max = 20
review_state = ('visible','published')

results = catalog.searchResults(
    #modified={'query':last_login_time, 'range':'min'},
    portal_type=portal_types,
    sort_on='modified',
    sort_order='reverse',
    review_state=review_state)[:max]

return context.kupuInfoForBrains(results)
