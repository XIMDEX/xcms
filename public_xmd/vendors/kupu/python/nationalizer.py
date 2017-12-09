#!/usr/bin/python2.3

"""Return the Kupu .html file with i18n applied"""

from xml.dom.minidom import parseString
import os

ID = 0
STR = 1

I18NNS = 'http://xml.zope.org/namespaces/i18n'

def ustr(i):
    if type(i) == unicode:
        return i
    else:
        return unicode(str(i), 'UTF-8')

def get_locale():
    if os.environ.has_key('HTTP_ACCEPT_LANGUAGE'):
        charsets = [l.strip() for l in 
                os.environ['HTTP_ACCEPT_LANGUAGE'].split(';')[0].split(',')]
        return charsets

class Nationalizer:
    """Translates string in an HTML or XML file using i18n: directives"""

    not_single = ['a', 'abbr', 'acronym', 'address', 'applet', 
                    'b', 'bdo', 'big', 'blink', 'blockquote', 
                    'button', 'caption', 'center', 'cite', 
                    'comment', 'del', 'dfn', 'dir', 'div',
                    'dl', 'dt', 'em', 'embed', 'fieldset',
                    'font', 'form', 'frameset', 'h1', 'h2',
                    'h3', 'h4', 'h5', 'h6', 'i', 'iframe',
                    'ins', 'kbd', 'label', 'legend', 'li',
                    'listing', 'map', 'marquee', 'menu',
                    'multicol', 'nobr', 'noembed', 'noframes',
                    'noscript', 'object', 'ol', 'optgroup',
                    'option', 'p', 'pre', 'q', 's', 'script',
                    'select', 'small', 'span', 'strike', 
                    'strong', 'style', 'sub', 'sup', 'table',
                    'tbody', 'td', 'textarea', 'tfoot',
                    'th', 'thead', 'title', 'tr', 'tt', 'u',
                    'ul', 'xmp']

    def __init__(self, htmlfile, locale):
        self.htmlfile = htmlfile
        self.locale = locale

    def translate(self):
        """load and translate everything"""
        popath = self.get_po_file_path(self.locale)
        if popath is not None:
            pofp = open(popath)
            try:
                msgcat = self.parse_po_file(pofp)
            finally:
                pofp.close()
        else:
            # if no pofile, parse anyway to get rid of those nasty i18n:
            # attributes (obviously not very fast, perhaps we need to either
            # cache a parsed version and send that back or just remove the
            # attributes here)
            msgcat = {}
        xmlfp = open(self.htmlfile)
        try:
            xml = xmlfp.read()
        finally:
            xmlfp.close()
        dom = parseString(xml)
        self.apply_i18n(dom, msgcat)
        return self.serialize(dom.documentElement)

    def parse_po_file(self, pofp):
        """parse the .po file, create a mapping msgid->msgstr"""
        cat = {}
        state = None
        msgid = None
        msgstr = None
        for line in pofp.readlines():
            line = line.strip()
            if line.startswith('#') or not line:
                continue
            if line.startswith('msgid'):
                if msgid and msgstr:
                    cat[msgid] = msgstr
                msgid = line[7:-1]
                state = ID
            elif line.startswith('msgstr'):
                msgstr = line[8:-1]
            else:
                # ignore for now, might be a multiline msgstr, if we
                # want to support those we should add some code here...
                pass
        if msgid and msgstr:
            cat[msgid] = msgstr
        return cat

    def apply_i18n(self, dom, msgcat):
        """apply nationalization of the full dom"""
        nodes = dom.documentElement.getElementsByTagName('*')
        for node in nodes:
            if node.hasAttributeNS(I18NNS, 'translate'):
                self.apply_translate(node, msgcat)
            if node.hasAttributeNS(I18NNS, 'attributes'):
                self.apply_attributes(node, msgcat)

    def apply_translate(self, node, msgcat):
        """handle Zope-style i18n:translate"""
        buf = []
        msgid = msgstr = node.getAttributeNS(I18NNS, 'translate').strip()
        if not msgid:
            # no msgid in the attribute, use the node value
            for child in node.childNodes:
                if child.nodeType == 3:
                    buf.append(child.nodeValue)
                else:
                    raise TypeError, \
                        ('illegal element %s in i18n:translate element' % 
                            child.nodeName)
            msgid = msgstr = self.reduce_whitespace(u''.join(buf).strip())
        if msgcat.has_key(msgid):
            msgstr = msgcat[msgid]
        # now replace the contents of the node with the new contents
        while node.hasChildNodes():
            node.removeChild(node.firstChild)
        node.removeAttributeNS(I18NNS, 'translate')
        node.appendChild(node.ownerDocument.createTextNode(msgstr))

    def apply_attributes(self, node, msgcat):
        """handle Zope-style i18n:attributes"""
        attrnames = node.getAttributeNS(I18NNS, 'attributes').split(' ')
        for attr in attrnames:
            value = node.getAttribute(attr)
            if value and msgcat.has_key(value):
                node.setAttribute(attr, unicode(msgcat[value], 'UTF-8'))
        node.removeAttributeNS(I18NNS, 'attributes')

    def reduce_whitespace(self, string):
        for char in ['\n', '\t', '\r']:
            string  = string.replace(char, ' ')
        while string.find('  ') > -1:
            string = string.replace('  ', ' ')
        return string

    def get_po_file_path(self, locale):
        for language in locale:
            startdir = '../i18n'
            language = language.split('-')
            pathstart = '%s/kupu-%s' % (startdir, language[0])
            paths = []
            if len(language) == 2:
                paths.append('%s-%s.po' % (pathstart, language[1]))
            paths += [
                '%s-default.po' % pathstart,
                '%s.po' % pathstart,
                ]
            for path in paths:
                if os.path.isfile(path):
                    return path

    def serialize(self, el):
        buf = []
        if el.nodeType == 1:
            buf.append('<%s' % el.nodeName)
            if len(el.attributes):
                for attr, value in el.attributes.items():
                    if value is not None:
                        buf.append(' %s="%s"' % (attr, self.entitize(value)))
            if el.hasChildNodes() or el.nodeName in self.not_single:
                buf.append('>')
                for child in el.childNodes:
                    buf += self.serialize(child)
                buf.append('</%s>' % el.nodeName)
            else:
                buf.append(' />')
        elif el.nodeType == 3:
            buf.append(el.nodeValue)
        else:
            print 'ignoring node of type', node.nodeType
        return ''.join([ustr(b) for b in buf])

    def entitize(self, string):
        string = string.replace('&', '&amp;')
        string = string.replace('<', '&lt;')
        string = string.replace('>', '&gt;')
        string = string.replace('"', '&quot;')
        return string
        
if __name__ == '__main__':
    # test code
    os.chdir(os.path.abspath(os.path.dirname(__file__)))
    i = Nationalizer('../common/kupu.html', ['nl'])
    print i.translate().encode('UTF-8')
