# Portal transform for images with captions
#
# We want to be able to support captions in images.
# The easiest way to do this is to define a Portal Transform which is
# applied to the HTML body text on output.
#
# The transform finds all the embedded images, and replaces them with
# an appropriate chunk of HTML to include the caption.
#
from Products.CMFCore.utils import getToolByName
from Products.PortalTransforms.interfaces import itransform
from DocumentTemplate.DT_Util import html_quote
from DocumentTemplate.DT_Var import newline_to_br
import re

__revision__ = '$Id$'

# IMAGE_PATTERN matches an image tag on its own, or an image tag
# enclosed in a simple <p> or <div>. In the latter case we strip out
# the enclosing tag since we are going to insert our own.
PATIMG = '\\<img[^>]+class=[^=>]*captioned[^>]+\\>'
PATA = '(?:\\<a[^>]*\\>'+PATIMG+'\\</a\\>)' + '|' + PATIMG
PAT0 = '('+PATA+')'
PAT1 = '<(?:p|div)[^>]*>'+PAT0 + '</(?:p|div)>' + '|' + PAT0
IMAGE_PATTERN = re.compile(PAT1, re.IGNORECASE)

# Regex to match stupid IE attributes. In IE generated HTML an
# attribute may not be enclosed by quotes if it doesn't contain
# certain punctuation.
ATTR_VALUE = '=(?:"?)(?P<%s>(?<=")[^"]*|[^ \/>]*)'
ATTR_CLASS = ATTR_VALUE % 'class'
ATTR_WIDTH = ATTR_VALUE % 'width'

ATTR_PATTERN = re.compile('''
    (?P<tag>\<
     ( class%s
     | src="resolveuid/(?P<src>([^/"#? ]*))
     | width%s
     | .
     )*\>
    )''' % (ATTR_CLASS, ATTR_WIDTH), re.VERBOSE)

CLASS_PATTERN = re.compile('\s*class=("[^"]*captioned[^"]*"|[^" \/>]+)')
IMAGE_TEMPLATE = '''\
<div class="%(class)s" style="width:%(width)spx;">
 <div style="width:%(width)spx;">
  %(tag)s
 </div>
 <div class="image-caption">
  %(caption)s
 </div>
</div>
'''

UID_PATTERN = re.compile('(?P<tag><(?:a|img) [^>]*(?:src|href)=")(?P<url>[^"]*resolveuid/(?P<uid>[^"#? ]*))')

class HTMLToCaptioned:
    """Transform which adds captions to images embedded in HTML"""
    __implements__ = itransform
    __name__ = "html_to_captioned"
    inputs = ('text/html',)
    output = "text/x-html-captioned"
    
    def __init__(self, name=None):
        self.config_metadata = {
            'inputs' : ('list', 'Inputs', 'Input(s) MIME type. Change with care.'),
            }
        if name:
            self.__name__ = name

    def name(self):
        return self.__name__

    def __getattr__(self, attr):
        if attr == 'inputs':
            return self.config['inputs']
        if attr == 'output':
            return self.config['output']
        raise AttributeError(attr)

    def convert(self, data, idata, filename=None, **kwargs):
        """convert the data, store the result in idata and return that
        optional argument filename may give the original file name of received data
        additional arguments given to engine's convert, convertTo or __call__ are
        passed back to the transform
        
        The object on which the translation was invoked is available as context
        (default: None)
        """
        context = kwargs.get('context', None)
        if context:
            at_tool = context.archetype_tool

        if context and at_tool:        
            def replaceImage(match):
                tag = match.group(1) or match.group(2)
                attrs = ATTR_PATTERN.match(tag)
                src = attrs.group('src')
                klass = attrs.group('class')
                width = attrs.group('width')
                if src:
                    d = attrs.groupdict()
                    target = at_tool.reference_catalog.lookupObject(src)
                    if target:
                        d['caption'] = newline_to_br(target.Description())
                        d['tag'] = CLASS_PATTERN.sub('', d['tag'])
                        if not width:
                            d['width'] = target.getWidth()

                        return IMAGE_TEMPLATE % d
                return match.group(0) # No change

            html = IMAGE_PATTERN.sub(replaceImage, data)

            # Replace urls that use UIDs with human friendly urls.
            def replaceUids(match):
                tag = match.group('tag')
                uid = match.group('uid')
                target = at_tool.reference_catalog.lookupObject(uid)
                if target:
                    return tag + target.absolute_url()
                return match.group(0)

            html = UID_PATTERN.sub(replaceUids, html)
            
            idata.setData(html)
            return idata

        # No context to use for replacements, so don't bother trying.
        return data

def register():
    return HTMLToCaptioned()

def initialize():
    engine = getToolByName(portal, 'portal_transforms')
    engine.registerTransform(register())
