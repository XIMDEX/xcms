#!/usr/bin/python

"""SpellChecker for Kupu, CGI wrapper"""

import os, sys
os.chdir(os.path.abspath(os.path.dirname(__file__)))
sys.path.append(os.path.abspath('../python'))

from spellcheck import SpellChecker, format_result

if __name__ == '__main__':
    import cgi, cgitb
    #cgitb.enable()
    #result = repr(sys.stdin.read())
    data = cgi.FieldStorage()
    data = data['text'].value
    c = SpellChecker()
    result = c.check(data)
    if result == None:
        result = ''
    else:
        result = format_result(result)
    print 'Content-Type: text/xml,charset=UTF-8'
    print 'Content-Length: %s' % len(result)
    print
    print result
