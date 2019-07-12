#!/usr/bin/env python

import sys

def convert(data, encoding='ISO-8859-1'):
    data = unicode(data, encoding)
    data = data.encode('UTF-8')
    return data

if __name__ == '__main__':
    if len(sys.argv) < 3:
        print 'Usage: %s <inputfile> <outputfile> [<encoding>]'
    infilename = sys.argv[1]
    outfilename = sys.argv[2]
    encoding = 'ISO-8859-1'
    if len(sys.argv) > 3:
        encoding = sys.argv[3]
    fpi = open(infilename)
    try:
        data = fpi.read()
    finally:
        fpi.close()
    utfdata = convert(data, encoding)
    fpo = open(outfilename, 'wb')
    try:
        fpo.write(utfdata)
    finally:
        fpo.close()
