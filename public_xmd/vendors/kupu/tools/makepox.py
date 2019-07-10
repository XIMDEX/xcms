"""Simple script to generate .pox files

    parses XML for i18n attrs and JS files for _() calls and generates an
    XML .pox template document (.poxt file)

    (c) Guido Wesdorp 2005

"""

from xml.dom.minidom import parseString, getDOMImplementation
import sys, re, os

stderr = sys.stderr

warn_on_broken_xml = True

class POX:
    """container for the results"""
    def __init__(self):
        impl = getDOMImplementation()
        self.doc = impl.createDocument(None, 'catalog', None)
        self.root = self.doc.documentElement
        self.processed = {} # mapping from mid to ([filenames], node)

    def add(self, msgid, filename):
        # strip and reduce whitespace
        msgid = msgid.strip().replace('\n', ' ').replace('\t', ' ')
        while msgid.find('  ') > -1:
            msgid.replace('  ', ' ')
        if self.processed.has_key(msgid):
            filenames, node = self.processed[msgid]
            if not filename in filenames:
                filenames.append(filename)
                node.setAttribute('filenames', 
                    '%s %s' % (node.getAttribute('filenames'), filename))
            return
        doc = self.doc
        root = self.root
        # add the nodes
        msgnode = doc.createElement('message')
        msgnode.setAttribute('filenames', filename)
        root.appendChild(msgnode)
        msgidnode = doc.createElement('msgid')
        msgidnode.appendChild(doc.createTextNode(msgid))
        msgnode.appendChild(msgidnode)
        msgstrnode = doc.createElement('msgstr')
        msgstrnode.appendChild(doc.createTextNode(msgid))
        msgnode.appendChild(msgstrnode)
        msgstrnode.setAttribute('i18n:translate', '')
        root.appendChild(msgnode)
        self.processed[msgid] = ([filename], msgnode)

    def get_result(self):
        return self.doc.toprettyxml()

class XMLParser:
    """scans XML files (or well-formed HTML files, obviously) for i18 attrs"""
    def __init__(self, files, pox):
        self._current = None
        for file in files:
            self.parse_file(file, pox)

    def parse_file(self, filename, pox):
        fp = open(filename)
        try:
            dom = parseString(fp.read())
        except:
            exc, e, tb = sys.exc_info()
            del tb
            if warn_on_broken_xml:
                print >>stderr, 'Error parsing %s: %s - %s' % (filename, exc, e)
            return
        # walk through all the nodes and scan for i18n: stuff
        while 1:
            node = self.next_node(dom)
            if not node:
                break
            if node.nodeType == 1:
                attrs = node.attributes
                translate = attrs.getNamedItem('i18n:translate')
                if translate:
                    msgid = translate.value
                    if not msgid.strip():
                        msgid = self.extract_text(node)
                    pox.add(msgid, filename)
                attributes = attrs.getNamedItem('i18n:attributes')
                if attributes:
                    attributes = [a.strip() for a in 
                                        attributes.value.split(';')]
                    for attr in attributes:
                        attritem = attrs.getNamedItem(attr)
                        if not attritem:
                            raise AttributeError, \
                                'No %s on %s in %s' % (
                                    attr, node.nodeName, filename)
                        msgid = attritem.value;
                        pox.add(msgid, filename)

    def extract_text(self, node):
        xml = ''
        for child in node.childNodes:
            xml += child.toxml().strip().replace('\n', ' ').replace('\t', ' ')
        while xml.find('  ') > -1:
            xml = xml.replace('  ', ' ')
        return xml

    def next_node(self, dom):
        if not self._current or self._current.ownerDocument != dom:
            self._current = dom.documentElement
        else:
            cur = self._current
            if cur.hasChildNodes():
                self._current = cur.childNodes[0]
            elif cur != cur.parentNode.lastChild:
                self._current = cur.nextSibling
            else:
                self._current = cur.parentNode.nextSibling
        return self._current

class JSParser:
    """scans JS files for _() calls"""
    def __init__(self, files, pox):
        for file in files:
            self.parse_file(file, pox)

    _startfuncreg = re.compile('.*?[^a-zA-Z0-9_]_\(')
    _startfuncreg_2 = re.compile('^_\(')
    def parse_file(self, filename, pox):
        lines = open(filename).readlines()
        lineno = 0
        more = False
        chunks = []
        for line in lines:
            lineno += 1
            if more is True or self._startfuncreg.search(line):
                chunk, more = self._get_func_content(line, filename, 
                                                        lineno, more)
                chunks.append(chunk)
            if chunks and more is False:
                literal = ''.join(chunks).strip()
                if not literal:
                    raise ValueError, ('Unrecognized function content -- ' 
                                        'file %s, line %s' % (
                                            filename, lineno))
                literal = literal.replace('\t', ' ').replace('\n', ' ')
                while literal.find('  ') > -1:
                    literal = literal.replace('  ', ' ')
                more = False
                chunks = []
                pox.add(literal, filename)
                
    def _get_func_content(self, line, filename, lineno, more=False):
        """return the content of the _() call in line

            if more is True, this will assume the function is already opened
            and continue adding to the result from the start of the line 
            without searching for '[^a-zA-Z_]_(' first

            returns a tuple (content, more) where more is True if the end of
            the function body is not reached, in that case this method should
            be called again with the 'more' argument set to True
        """
        line = line.strip()
        if not more:
            match = self._startfuncreg.search(line) or \
                        self._startfuncreg_2.search(line)
            line = line.replace(match.group(0), '')
        line = line.strip()
        quote = line[0]
        line = line[1:]
        if not quote in ['"', "'"]:
            raise ValueError, ('beginning of function body not a recognized '
                                'quote character: %s -- (file %s, line %s)' % (
                                    quote, filename, lineno))
        ret = []
        previous_char = None
        while 1:
            new_char = line[0]
            line = line[1:]
            if new_char == quote:
                if previous_char != '\\':
                    break
            ret.append(new_char)
            previous_char = new_char
        
        # find out if we should continue after this (do we have a '+' 
        # or a ');'?)
        more = False
        line = line.strip()
        if line and line[0] == '+':
            line = line[1:].strip()
            if line:
                raise ValueError, ('string concatenation only allowed for '
                                    'multiline strings, not for variable '
                                    'interpolation (use ${} instead) -- '
                                    '(file %s, line %s)' % (
                                        filename, lineno))
            more = True
        return ''.join(ret), more

if __name__ == '__main__':
    print >>stderr, 'POX extract v0.1'
    print >>stderr, '(c) Guido Wesdorp 2004'
    files = sys.argv[1:]
    print >>stderr, 'Going to parse files', ', '.join(files)
    pox = POX()
    xml = [f for f in files if not f.endswith('.js')]
    js = [f for f in files if f.endswith('.js')]
    XMLParser(xml, pox)
    JSParser(js, pox)
    pres = pox.get_result()
    pres = pres.replace('<catalog>',
        ('<catalog xmlns:i18n="http://xml.zope.org/namespaces/i18n" '
        'i18n:domain="kupu">'))
    print pres
    print >>stderr, 'Done'
