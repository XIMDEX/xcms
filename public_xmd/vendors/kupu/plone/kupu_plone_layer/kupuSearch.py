## Script (Python) "kupuSearch"
##title=Search the portal catalog
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

request = context.REQUEST
# the default resource type is mediaobject
resource_type = request.get('resource_type', 'mediaobject')
portal_types = kupu_tool.queryPortalTypesForResourceType(resource_type, ())

search_params = {}
search_params.update(request.form)
search_params['portal_type'] = portal_types
# Plone issue 4801: searches shouldn't just find visible/published.
#search_params['review_state'] = 'visible', 'published'

# Get the maximum number of results with 500 being the default and
# absolute maximum.
abs_max = 500
max = request.get('max_results', abs_max)
if max > abs_max:
    max = abs_max

results = context.queryCatalog(search_params)[:max]
return context.kupuInfoForBrains(results)
