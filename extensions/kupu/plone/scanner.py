import os, re

IDPATTERN = re.compile(r'\$\Id[^$]*\$')
JSPATTERN = re.compile(r'(?:string:\$\{portal_url\}/([^."]+.js)")|(?:\<link href="([^."]*.css)")')
KWS = 'kupu_wysiwyg_support'
ROOT = os.path.dirname(os.path.dirname(__file__))

def matchFiles(extension, root, paths):
    for dirname in paths:
        dirname = os.path.join(root, dirname)
        files = os.listdir(dirname)
        for f in files:
            if f.endswith(extension):
                yield f, os.path.join(dirname, f)

def getFileData(path):
    fh = open(path, 'rU')
    try:
        return fh.read()
    finally:
        fh.close()

def getKWS(root):
    return os.path.join(root, 'plone', 'kupu_plone_layer', KWS+'.html')

def scanFile(path):
    '''Scan a single file returning all the Id strings it contains'''
    ids = IDPATTERN.findall(getFileData(path))
    return ids

    
def scanKWS(root=ROOT):
    try:
        wysiwyg = scanFile(getKWS(root))
    except:
        return KWS, "cannot open template: run make"

    wysiwyg = dict.fromkeys(wysiwyg)

    for fname, path in matchFiles('.kupu', root, ('default', 'plone')):
        for id in scanFile(path):
            if id in wysiwyg:
                del wysiwyg[id]
    if wysiwyg:
        return KWS, "template appears to be out of date: run make"
    return KWS, ''

def scanIds(root=ROOT):
    status = {}
    wanted = {}
    for groups in JSPATTERN.findall(getFileData(getKWS(root))):
        for group in groups:
            if group:
                wanted[group] = None

    wanted = dict([ (name, None)
        for groups in JSPATTERN.findall(getFileData(getKWS(root)))
            for name in groups if name])

    for fname, path in matchFiles('.js', root, ('common', os.path.join('plone', 'kupu_plone_layer'))):
        if fname in wanted:
            for id in scanFile(path):
                status[fname] = id
    res = status.items()
    res.sort()
    return res

if __name__=='__main__':
    print scanKWS('..')
    print scanIds('..')
