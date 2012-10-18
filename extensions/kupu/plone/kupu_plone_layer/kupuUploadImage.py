## Script (Python) "kupuUploadImage"
##bind container=container
##bind context=context
##bind namespace=
##bind script=script
##bind subpath=traverse_subpath
##parameters=node_prop_caption, node_prop_image

from Products.CMFCore.utils import getToolByName
request = context.REQUEST
RESPONSE =  request.RESPONSE

TEMPLATE = """
<html>
<head></head>
<body onload="window.parent.drawertool.current_drawer.%s('%s');">
</body>
</html>
"""

def Error(fmt, *args):
    msg = fmt % args
    script = TEMPLATE % ('cancelUpload', msg.replace("'", "\\'"))
    return script

kupu_tool = getToolByName(context, 'kupu_library_tool')
ctr_tool = getToolByName(context, 'content_type_registry')

id = request['node_prop_image'].filename
linkbyuid = kupu_tool.getLinkbyuid();
base = context.absolute_url()

# MTR would also do content-based classification, alas, we don't want it as a dependency here
# content_type= getToolByName(context,'mimetypes_registry').classify(node_prop_image)

content_type = request['node_prop_image'].headers["Content-Type"]
typename = ctr_tool.findTypeName(id, content_type, "")

# Permission checks based on code by Danny Bloemendaal

# 1) check if we are allowed to create an Image in folder 
if not typename in [t.id for t in context.getAllowedTypes()]: 
   return Error("Creation of '%s' content is not allowed in %s", typename, context.title_or_id())

# 2) check if the current user has permissions to add stuff 
if not context.portal_membership.checkPermission('Add portal content',context): 
    return Error("You do not have permission to add content in %s", context.getId())

# IE submits whole path to file, moz just the filename
id = id.split("\\")[-1]

# check for a bad id
if context.check_id(id) is not None or getattr(context,id,None) is not None:
   id = context.generateUniqueId(typename)

# check for a duplicate
newid = context.invokeFactory(type_name=typename, id=id, title=node_prop_caption, file=node_prop_image)

if newid is None or newid == '':
   newid = id 

obj = getattr(context,newid, None)

if not obj:
   return Error("Could not create %s with %s as id and %s as title!", typename,newid, node_prop_caption)

obj.reindexObject() 
if linkbyuid and hasattr(obj, 'UID'):
    url = base+'/resolveuid/%s' % obj.UID()
else:
    url = obj.absolute_url()

return TEMPLATE % ('finishUpload', url)


