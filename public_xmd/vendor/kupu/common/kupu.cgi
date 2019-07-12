#!/usr/bin/python2.3

import os, sys
os.chdir(os.path.abspath(os.path.dirname(__file__)))
sys.path.append(os.path.abspath('../python'))

from nationalizer import Nationalizer, get_locale

if __name__ == '__main__':
    print 'Content-Type: text/html;charset=UTF-8'
    print
    locale = get_locale()
    if locale is None:
        locale = ['nl']
    i = Nationalizer('kupu.html', locale)
    print i.translate().encode('UTF-8')
