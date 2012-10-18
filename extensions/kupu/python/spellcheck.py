#!/usr/bin/python

"""Zope 2 spellchecker web service for Kupu"""

COMMAND = 'aspell -a'

import popen2, re

try:
    from Globals import ClassSecurityInfo
except ImportError:
    pass
else:
    # hmmm... Zope 2...
    __allow_access_to_unprotected_subobjects__ = 1

class SpellChecker:
    """Simple spell checker, uses ispell (or aspell) with pipes"""

    __allow_access_to_unprotected_subobjects__ = 1

    reg_unknown = re.compile('^& (.*?) \d* \d*: (.*)$', re.U)
    reg_unknown_no_replacement = re.compile('^\# (.*?) \d*.*$', re.U)

    def __init__(self):
        self.chout, self.chin = popen2.popen2(COMMAND)
        # throw away intro
        self.read_line()

    def __del__(self):
        self.chout.close()
        self.chin.close()

    def check(self, text):
        """checks a line of text
        
            returns None if spelling was okay, and an HTML string with words 
            that weren't recognized marked (with a span class="wrong_spelling")
        """
        result = {}
        for line in text.split('\n'):
            line = line.strip()
            if line:
                self.write_line(line)
            while 1:
                resline = self.read_line()
                if not resline.strip():
                    break
                if resline.strip() != '*':
                    match = self.reg_unknown.match(resline)
                    have_replacement = True
                    if not match:
                        match = self.reg_unknown_no_replacement.match(resline)
                        have_replacement = False
                    assert match, 'Unknown formatted line: %s' % resline
                    word = match.group(1)
                    if result.has_key(word):
                        continue
                    replacements = []
                    if have_replacement:
                        replacements = match.group(2).split(', ')
                    result[word] = replacements
        return result

    def read_line(self):
        buf = []
        while 1:
	    char = self.read_char()
            if not char:
                return ''	    
            if char == '\n':
                return ''.join(buf)
            buf.append(char)

    def write_line(self, line):
        try:
            self.chin.write('%s\n' % line)
            self.chin.flush()
            return
        except IOError:
            self.reconnect()
            self.chin.write('%s\n' % line)
            self.chin.flush()
            return
        raise

    def read_char(self):
        try:
            return self.chout.read(1)
        except IOError:
            self.reconnect()
            return self.chout.read(1)
        raise

    def reconnect(self):
        try:
            self.chout.close()
        except IOError:
            pass
        try:
            self.chin.close()
        except IOError:
            pass
        self.chout, self.chin = popen2.popen2(COMMAND)

def format_result(result):
    """convert the result dict to XML"""
    buf = ['<?xml version="1.0" encoding="UTF-8" ?>\n<spellcheck_result>']
    for key, value in result.items():
        buf.append('<incorrect><word>')
        buf.append(key)
        buf.append('</word><replacements>')
        buf.append(' '.join(value))
        buf.append('</replacements></incorrect>')
    buf.append('</spellcheck_result>')
    return ''.join(buf)

if __name__ == '__main__':
    c = SpellChecker()
    while 1:
        line = raw_input('Enter text to check: ')
        if line == 'q':
            break
        ret = c.check(line)
        if ret is None:
            print 'okay'
        else:
            print ret
