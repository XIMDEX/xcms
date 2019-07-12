from AccessControl import ClassSecurityInfo
from Products.CMFCore.utils import getToolByName
from Products.Archetypes.public import *
from Products.Archetypes.config import REFERENCE_CATALOG
from Products.Archetypes.Field import TextField
from Products.Archetypes.ReferenceEngine import Reference
from ZPublisher.HTTPRequest import FileUpload
import re

# UID_PATTERN matches a UID in an anchor or image tag.
UID_PATTERN = re.compile(r'''<(?:a\b[^>]+href|img\b[^>]+src)="resolveuid/(?P<uid>[^">/]+)''', re.I)

class ReftextField(TextField):
    __implements__ = TextField.__implements__

    _properties = TextField._properties.copy()
    _properties.update({
        'widget': RichWidget,
        'default_content_type' : 'text/html',
        'default_output_type'  : 'text/x-html-captioned',
        'allowable_content_types' : ('text/html',),
        'relationship' : None, # defaults to field name
        'referenceClass' : Reference,
        })

    security = ClassSecurityInfo()

    security.declarePrivate('set')
    def set(self, instance, value, **kwargs):
        """ Assign input value to object. If mimetype is not specified,
        pass to processing method without one and add mimetype
        returned to kwargs. Assign kwargs to instance.
        """
        if value is None:
            # nothing to do
            return

        TextField.set(self, instance, value, **kwargs)

        if not isinstance(value, basestring):
            value.seek(0);
            value = value.read()

        uids = UID_PATTERN.findall(value) # XXX: build list of uids from the value here
        uids = dict.fromkeys(uids).keys() # Remove duplicate uids.

        tool = getToolByName(instance, REFERENCE_CATALOG)

        relationship = self.relationship
        if relationship is None:
            relationship = self.__name__

        targetUIDs = [ref.targetUID for ref in
                      tool.getReferences(instance, relationship)]

        add = [v for v in uids if v and v not in targetUIDs]
        sub = [t for t in targetUIDs if t not in uids]

        # tweak keyword arguments for addReference
        addRef_kw = kwargs.copy()
        addRef_kw.setdefault('referenceClass', self.referenceClass)
        if addRef_kw.has_key('schema'): del addRef_kw['schema']

        for uid in add:
            __traceback_info__ = (instance, uid, value, targetUIDs)
            try:
                # throws ReferenceError if uid is invalid
                tool.addReference(instance, uid, relationship, **addRef_kw)
            except ReferenceError:
                pass
        for uid in sub:
            tool.deleteReference(instance, uid, relationship)

#         print "Result was:",[ref.targetUID for ref in
#                       tool.getReferences(instance, relationship)]
#         print "Objects:",[ref.getTargetObject() for ref in
#                       tool.getReferences(instance, relationship)]
