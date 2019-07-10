## Script (Python) "kupu-customisation-policy"
##bind container=container
##bind context=context
##bind namespace=
##bind script=script
##bind subpath=traverse_subpath
##parameters=
##title=Kupu Customisation Policy
##

# Make a copy of this script called 'kupu-customisation-policy'
# in any skin folder on your site and edit it to set up your own
# preferred kupu configuration.
from Products.CMFCore.utils import getToolByName

RESOURCES = dict(
    linkable = ('Document', 'Image', 'File', 'News Item', 'Event', 'Folder', 'Large Plone Folder'),
    mediaobject = ('Image',),
    collection = ('Plone Site', 'Folder', 'Large Plone Folder'),
    )

EXCLUDED_HTML = [
  {'tags': ('center','span','tt','big','small','s','strike','basefont','font',),
   'attributes':(),
   'keep': 1 },
  
  {'tags':(),
  'attributes': ('dir','lang','valign','halign','border','frame',
      'rules','cellspacing','cellpadding','bgcolor'),
   'keep': 1},

  {'tags': ('table','th','td'),
   'attributes': ('width','height'),
   'keep': 1},

   {'tags': '', 'attributes': '' } # Must be dummy entry at end.
]

STYLE_WHITELIST = ['text-align', 'list-style-type', 'float']
CLASS_BLACKLIST = ['MsoNormal', 'MsoTitle', 'MsoHeader', 'MsoFootnoteText',
        'Bullet1', 'Bullet2']

TABLE_CLASSNAMES = ('plain', 'listing', 'grid', 'data')

PARAGRAPH_STYLES = (
    "Heading|h2|Heading",
    "Subheading|h3|Subheading",
    "Formatted|pre",
    "Odd row|tr|odd",
    "Even row|tr|even",
    "Heading cell|th|",
#    'Fancy|div|fancyClass',
#    'Plain|div|plainClass',
)
    
LIBRARIES = (
    dict(id="root",
         title="string:Home",
         uri="string:${portal_url}",
         src="string:${portal_url}/kupucollection.xml",
         icon="string:${portal_url}/misc_/CMFPlone/plone_icon"),
    dict(id="current",
         title="string:Current folder",
         uri="string:${folder_url}",
         src="string:${folder_url}/kupucollection.xml",
         icon="string:${portal_url}/folder_icon.gif"),
    dict(id="myitems",
         title="string:My recent items",
         uri="string:${portal_url}/kupumyitems.xml",
         src="string:${portal_url}/kupumyitems.xml",
         icon="string:${portal_url}/kupuimages/kupusearch_icon.gif"),
    dict(id="recentitems",
         title="string:Recent items",
         uri="string:${portal_url}/kupurecentitems.xml",
         src="string:${portal_url}/kupurecentitems.xml",
         icon="string:${portal_url}/kupuimages/kupusearch_icon.gif")
    )
DEFAULT_LIBRARY = 'myitems'

INSTALL_BEFOREUNLOAD = True
LINKBYUID = False

tool = getToolByName(context, 'kupu_library_tool')
typetool = getToolByName(context, 'portal_types')

def typefilter(types):
    all_meta_types = dict([ (t.id, 1) for t in typetool.listTypeInfo()])
    return [ t for t in types if t in all_meta_types ]

print "remove old resources"
types = tool.zmi_get_type_mapping()
tool.deleteResourceTypes([ t for (t,p) in types])

print "add resources"
for k,v in RESOURCES.items():
    tool.addResourceType(k, typefilter(v))

mappings = tool.zmi_get_type_mapping()
for rname, t in mappings:
    print rname, ", ".join(t)

print "remove old libraries"
libs = tool.zmi_get_libraries()
tool.deleteLibraries(range(len(libs)))

print "add libraries"
for lib in LIBRARIES:
    tool.addLibrary(**lib)

for lib in tool.zmi_get_libraries():
    keys = lib.keys()
    keys.remove('id')
    keys.sort()
    print lib['id']
    for k in (keys):
        print '   ',k, lib[k]

tool.zmi_set_default_library(DEFAULT_LIBRARY)

print "configure kupu"
tool.configure_kupu(
    table_classnames = TABLE_CLASSNAMES,
    parastyles=PARAGRAPH_STYLES,
    html_exclusions = EXCLUDED_HTML,
    style_whitelist = STYLE_WHITELIST,
    class_blacklist = CLASS_BLACKLIST,
    installBeforeUnload=INSTALL_BEFOREUNLOAD,
    linkbyuid=LINKBYUID,
    )

return printed
