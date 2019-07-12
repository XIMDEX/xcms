##############################################################################
#
# Copyright (c) 2003-2005 Kupu Contributors. All rights reserved.
#
# This software is distributed under the terms of the Kupu
# License. See LICENSE.txt for license text. For a list of Kupu
# Contributors see CREDITS.txt.
#
##############################################################################
"""Test browserSupportsKupu

$Id: test_browserSupportsKupu.py 14818 2005-07-20 14:37:17Z duncan $
"""

import os, sys
import time
if __name__ == '__main__':
    execfile(os.path.join(sys.path[0], 'framework.py'))

from Testing import ZopeTestCase
from Products.CMFPlone.tests import PloneTestCase
from Products.CMFPlone.tests.PloneTestCase import portal_name, portal_owner
from AccessControl.SecurityManagement import newSecurityManager, noSecurityManager

def installKupu(quiet=0):
    _start = time.time()
    if not quiet: ZopeTestCase._print('Adding Kupu ... ')

    ZopeTestCase.installProduct('kupu')

    # Install kupu into the test site. Done here because otherwise
    # it slows the tests down a lot on Plone 2.1
    app = ZopeTestCase.app()
    user = app.acl_users.getUserById(portal_owner).__of__(app.acl_users)
    newSecurityManager(None, user)

    portal = app[portal_name]
    quickinstaller = portal.portal_quickinstaller
    quickinstaller.installProduct('kupu')

    # Log out
    noSecurityManager()
    get_transaction().commit()
    if not quiet: ZopeTestCase._print('done (%.3fs)\n' \
                                      % (time.time()-_start,))
    ZopeTestCase.close(app)

installKupu()

class TestBrowserSupportsKupu(PloneTestCase.PloneTestCase):

    def afterSetUp(self):
        md = self.portal.portal_memberdata
        md._updateProperty('wysiwyg_editor', 'Kupu')
        #self.qi = self.portal.portal_quickinstaller
        #self.qi.installProduct('kupu')
        #self.script = self.portal.portal_skins.kupu_plone.browserSupportsKupu
        self.script = self.portal.kupu_library_tool.isKupuEnabled

# List of tuples of id, signature, os, version, browser
# browsers are:
# 1, MOZILLA            -- supported 1.4 and above
# 2, INTERNET_EXPORER   -- supported 5.5 and above
# 3, OPERA              -- not supported
# 4, KONQUEROR          -- not supported
# 5, NETSCAPE           -- not supported
# 6, OTHER              -- not supported
# 7, GOOGLE             -- not supported
# 8, YAHOO              -- not supported
# 9, GALEON             -- not supported

(MOZILLA, INTERNET_EXPLORER, OPERA, KONQUEROR, NETSCAPE, OTHER,
 GOOGLE, YAHOO, GALEON) = range(1,10)

BROWSERNAMES = ['NOTUSED', 'Mozilla', 'Internet Explorer', 'Opera',
                'Konqueror', 'Netscape', 'Other', 'Google',
                'Yahoo', 'Galeon' ]
 
SUPPORTED = {
    MOZILLA: (1,3,1),
    INTERNET_EXPLORER: (5,5),
}

BROWSERS = (
    (1, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; NetCaptor 7.2.0)', 'Windows XP', '6.0', 2),
    (2, 'Mozilla/4.0 (compatible; MSIE 5.5; Windows 98; Win 9x 4.90)', 'Windows 95', '5.5', 2),
    (3, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)', 'Windows XP', '6.0', 2),
    (4, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; .NET CLR 1.1.4322)', 'Windows XP', '6.0', 2),
    (5, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0; {F69FABBA-7A20-4724-93CB-A717BBB0AB5A}; MyIE2; .NET CLR 1.0.3705)', 'Windows 2000', '6.0', 2),
    (6, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0; Crazy Browser 1.0.5)', 'Windows 2000', '6.0', 2),
    (7, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0; .NET CLR 1.1.4322)', 'Windows 2000', '6.0', 2),
    (8, 'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0; .NET CLR 1.0.3705)', 'Windows 2000', '5.01', 2),
    (9, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0; .NET CLR 1.0.3705; .NET CLR 1.1.4322)', 'Windows 2000', '6.0', 2),
    (10, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; .NET CLR 1.1.4322; Alexa Toolbar)', 'Windows XP', '6.0', 2),
    (11, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; Q312461)', 'Windows XP', '6.0', 2),
    (12, 'Mozilla/4.0 (compatible; MSIE 5.01; Windows 98)', 'Windows 95', '5.01', 2),
    (13, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows 98)', 'Windows 95', '6.0', 2),
    (14, 'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)', 'Windows 2000', '5.01', 2),
    (15, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; {514CEB04-E26C-4724-B559-3BBF7D079CF9}; .NET CLR 1.1.4322)', 'Windows XP', '6.0', 2),
    (16, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0)', 'Windows 2000', '6.0', 2),
    (17, 'Googlebot/2.1 (+http://www.googlebot.com/bot.html)', '', '2.1', 7),
    (18, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.6) Gecko/20040206 Firefox/0.8', 'Windows XP', '1.6', 1),
    (19, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; .NET CLR 1.0.3705)', 'Windows XP', '6.0', 2),
    (20, 'Java1.4.0', None, None, 6),
    (21, 'Mozilla/4.0 (compatible; MSIE 5.5; Windows NT 4.0)', 'Windows NT', '5.5', 2),
    (22, 'Mozilla/5.0 (Windows; U; Windows NT 5.0; en-US; rv:1.4) Gecko/20030624 Netscape/7.1 (ax)', 'Windows 2000', '1.4', 1),
    (23, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; FunWebProducts)', 'Windows XP', '6.0', 2),
    (24, 'Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.6) Gecko/20040113', 'GNU/Linux', '1.6', 1),
    (25, 'Opera/7.23 (Windows NT 5.1; U)  [en]', 'Windows XP', '7.23 (Windows NT 5.1; U)', 3),
    (26, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.6) Gecko/20040113', 'Windows XP', '1.6', 1),
    (27, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows 98) Opera 7.20  [en]', 'Windows 95', '7.20', 3),
    (28, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0; YComp 5.0.0.0; Avalon Ltd.)', 'Windows 2000', '6.0', 2),
    (29, 'Mozilla/5.0 (Macintosh; U; PPC Mac OS X; en) AppleWebKit/85.7 (KHTML, like Gecko) Safari/85.7', 'Mac PPC', '5.0 (Macintosh; U; PPC Mac OS X; en', 1),
    (30, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; Hotbar 4.4.2.0; .NET CLR 1.0.3705)', 'Windows XP', '6.0', 2),
    (31, 'Mozilla/4.0 (compatible; MSIE 5.5; Windows NT 5.0) Active Cache Request', 'Windows 2000', '5.5', 2),
    (32, 'Mozilla/4.0 (compatible; MSIE 5.0; Windows 98; DigExt)', 'Windows 95', '5.0', 2),
    (33, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0; {BEBB62E1-3900-4425-91F4-BC0C940212A1}; FunWebProducts; .NET CLR 1.1.4322; .NET CLR 1.0.3705)', 'Windows 2000', '6.0', 2),
    (34, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0; .NET CLR 1.0.3705)', 'Windows 2000', '6.0', 2),
    (35, 'Mozilla/5.0 (compatible; Yahoo! Slurp; http://help.yahoo.com/help/us/ysearch/slurp)', '', '', 8),
    (36, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows 98; Win 9x 4.90)', 'Windows 95', '6.0', 2),
    (37, 'Mozilla/4.0 (compatible; grub-client-1.0.5; Crawl your own stuff with http://grub.org)', 'uknown OS', '4.0', 5),
    (38, 'Mozilla/4.0 (compatible; MSIE 5.0; Windows NT)', 'Windows NT', '5.0', 2),
    (39, 'Mozilla/4.0 (compatible; grub-client-1.5.3; Crawl your own stuff with http://grub.org)', 'uknown OS', '4.0', 5),
    (40, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; YComp 5.0.0.0; .NET CLR 1.0.3705; .NET CLR 1.1.4322)', 'Windows XP', '6.0', 2),
    (41, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; AUTOSIGN W2000 WNT VER03; FunWebProducts-MyWay)', 'Windows XP', '6.0', 2),
    (42, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; MyIE2; .NET CLR 1.1.4322)', 'Windows XP', '6.0', 2),
    (43, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 4.0; iOpus-I-M)', 'Windows NT', '6.0', 2),
    (44, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0; DigExt)', 'Windows 2000', '6.0', 2),
    (45, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1) Opera 7.20  [en]', 'Windows XP', '7.20', 3),
    (46, 'MSProxy/2.0', None, None, 6),
    (47, 'Mozilla/5.0 (Windows; U; Windows NT 5.0; en-US; rv:1.6) Gecko/20040113', 'Windows 2000', '1.6', 1),
    (48, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0; Q312461)', 'Windows 2000', '6.0', 2),
    (49, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; FunWebProducts-MyWay)', 'Windows XP', '6.0', 2),
    (50, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1) Opera 7.21  [en]', 'Windows XP', '7.21', 3),
    (51, 'Mozilla/3.01 (compatible;)', None, None, 6),
    (52, 'Lynx/2.8.4dev.16 libwww-FM/2.14 SSL-MM/1.4.1 OpenSSL/0.9.6', None, None, 6),
    (53, 'Mozilla/4.0 (compatible; MSIE 5.5; Windows 98)', 'Windows 95', '5.5', 2),
    (54, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; TUCOWS; MyIE2)', 'Windows XP', '6.0', 2),
    (55, 'Mozilla/5.0 (Macintosh; U; PPC Mac OS X; en) AppleWebKit/124 (KHTML, like Gecko) Safari/125.1', 'Mac PPC', '5.0 (Macintosh; U; PPC Mac OS X; en', 1),
    (56, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0; H010818; .NET CLR 1.0.3705)', 'Windows 2000', '6.0', 2),
    (57, 'Mozilla/5.0 (Windows; U; Windows NT 5.0; en-US; rv:1.5) Gecko/20031007 Firebird/0.7', 'Windows 2000', '1.5', 1),
    (58, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; Hotbar 4.4.0.0)', 'Windows XP', '6.0', 2),
    (59, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0; {1D013B5D-D0E7-4EAB-9FCF-AE4016583348})', 'Windows 2000', '6.0', 2),
    (60, 'Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.7b;) Gecko/20020604 OLYMPIAKOS SFP', 'GNU/Linux', '1.7', 1),
    (61, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; Q312461; .NET CLR 1.1.4322)', 'Windows XP', '6.0', 2),
    (62, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; .NET CLR 1.0.3705; .NET CLR 1.1.4322)', 'Windows XP', '6.0', 2),
    (63, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0; YComp 5.0.2.6)', 'Windows 2000', '6.0', 2),
    (64, 'Mozilla/4.0 (compatible; MSIE 5.0; Windows 98)', 'Windows 95', '5.0', 2),
    (65, 'Avant Browser (http://www.avantbrowser.com)', None, None, 6),
    (66, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; {624D10FA-5EBC-4100-9316-C6769E251849}; .NET CLR 1.0.3705; .NET CLR 1.1.4322)', 'Windows XP', '6.0', 2),
    (67, 'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT)', 'Windows NT', '5.01', 2),
    (68, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.4) Gecko/20030624 Netscape/7.1 (ax)', 'Windows XP', '1.4', 1),
    (69, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0; FunWebProducts-MyWay)', 'Windows 2000', '6.0', 2),
    (70, 'Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.4) Gecko/20030624 Netscape/7.1', 'GNU/Linux', '1.4', 1),
    (71, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0; Q312461; .NET CLR 1.1.4322)', 'Windows 2000', '6.0', 2),
    (72, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0) Opera 7.22  [en]', 'Windows 2000', '7.22', 3),
    (73, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0; T312461)', 'Windows 2000', '6.0', 2),
    (74, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0; EurobankSec)', 'Windows 2000', '6.0', 2),
    (75, 'Mozilla/4.0 (compatible; MSIE 5.0; Windows NT; DigExt)', 'Windows NT', '5.0', 2),
    (76, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0; NetCaptor 7.5.0 Gold; .NET CLR 1.0.3705; .NET CLR 1.1.4322)', 'Windows 2000', '6.0', 2),
    (77, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 4.0)', 'Windows NT', '6.0', 2),
    (78, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; Alexa Toolbar)', 'Windows XP', '6.0', 2),
    (79, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.7b) Gecko/20040316', 'Windows XP', '1.7', 1),
    (80, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; i-NavFourF)', 'Windows XP', '6.0', 2),
    (81, 'Scooter/3.3_SF', None, None, 6),
    (82, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; {A2D2036B-F33C-4612-AB02-CDACAAA0DC39})', 'Windows XP', '6.0', 2),
    (83, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; NetCaptor 7.5.0 Gold)', 'Windows XP', '6.0', 2),
    (84, 'Mozilla/4.0 (compatible; MSIE 5.5; Windows NT 5.0)', 'Windows 2000', '5.5', 2),
    (85, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; ONEWAY NET; .NET CLR 1.1.4322)', 'Windows XP', '6.0', 2),
    (86, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; .NET CLR 1.1.4322; .NET CLR 1.0.3705)', 'Windows XP', '6.0', 2),
    (87, 'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0; .NET CLR 1.1.4322)', 'Windows 2000', '5.01', 2),
    (88, 'Opera/7.21 (Windows NT 5.1; U)  [en]', 'Windows XP', '7.21 (Windows NT 5.1; U)', 3),
    (89, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; MyIE2)', 'Windows XP', '6.0', 2),
    (90, 'Mozilla/5.0 (Windows; U; Windows NT 5.0; en-US; rv:1.4) Gecko/20030624', 'Windows 2000', '1.4', 1),
    (91, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.7a) Gecko/20040219', 'Windows XP', '1.7', 1),
    (92, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.2; .NET CLR 1.1.4322)', 'Windows NT', '6.0', 2),
    (93, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0) Opera 7.21  [en]', 'Windows 2000', '7.21', 3),
    (94, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; AUTOSIGN W98 WNT VER03)', 'Windows XP', '6.0', 2),
    (95, 'Mozilla/5.0 (Windows; U; Win 9x 4.90; en-US; rv:1.6) Gecko/20040206 Firefox/0.8', 'Windows ME', '1.6', 1),
    (96, 'Opera/7.23 (Windows NT 5.0; U)  [en]', 'Windows 2000', '7.23 (Windows NT 5.0; U)', 3),
    (97, 'Opera/7.23 (X11; FreeBSD i386; U)  [en]', 'uknown OS', '7.23 (X11; FreeBSD i386; U)', 3),
    (98, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.5) Gecko/20031007', 'Windows XP', '1.5', 1),
    (99, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.5) Gecko/20031007 Firebird/0.7', 'Windows XP', '1.5', 1),
    (100, 'Mozilla/5.0 (Windows; U; Windows NT 5.0; en-US; rv:1.6) Gecko/20040206 Firefox/0.8', 'Windows 2000', '1.6', 1),
    (101, 'Mozilla/4.0 (compatible; MSIE 5.16; Mac_PowerPC)', 'Mac PPC', '5.16', 2),
    (102, 'Mozilla/4.0 (compatible; MSIE 6.0; Mac_PowerPC) Opera 7.50  [en]', 'uknown OS', '7.50', 3),
    (103, 'Mediapartners-Google/2.1 (+http://www.googlebot.com/bot.html)', None, None, 6),
    (104, 'Mozilla/4.0 (compatible; MSIE 5.0; Windows 98; MyIE2)', 'Windows 95', '5.0', 2),
    (105, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows 98; .NET CLR 1.1.4322)', 'Windows 95', '6.0', 2),
    (106, 'Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.6) Gecko/20040207 Firefox/0.8', 'GNU/Linux', '1.6', 1),
    (107, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows 98; AUTOSIGN W98 WNT VER03)', 'Windows 95', '6.0', 2),
    (108, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; AUTOSIGN W2000 WNT VER03; Q312461)', 'Windows XP', '6.0', 2),
    (109, 'Mozilla/4.0 (compatible; MSIE 5.0; Mac_PowerPC)', 'Mac PPC', '5.0', 2),
    (110, 'Opera/7.20 (Windows NT 5.1; U)  [en]', 'Windows XP', '7.20 (Windows NT 5.1; U)', 3),
    (111, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0; Avant Browser [avantbrowser.com])', 'Windows 2000', '6.0', 2),
    (112, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; (R1 1.3); .NET CLR 1.1.4322)', 'Windows XP', '6.0', 2),
    (113, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows 98; Win 9x 4.90; YComp 5.0.0.0)', 'Windows 95', '6.0', 2),
    (114, 'Opera/7.23 (Windows 98; U)  [en]', 'Windows 95', '7.23 (Windows 98; U)', 3),
    (115, 'Mozilla/4.0 (compatible; MSIE 5.01; Windows 98; Feat Ext 18)', 'Windows 95', '5.01', 2),
    (116, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; AUTOSIGN W2000 WNT VER03)', 'Windows XP', '6.0', 2),
    (117, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; Q342532)', 'Windows XP', '6.0', 2),
    (118, 'Mozilla/5.0 Galeon/1.2.12 (X11; Linux i686; U;) Gecko/20031004', 'GNU/Linux', '5.0 Galeon/1.2.12 (X11; Linux i686; U;', 9),
    (119, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1) via Avirt Gateway Server v4.2', 'Windows XP', '6.0', 2),
    (120, 'Mozilla/5.0 (X11; U; SunOS sun4u; en-US; rv:0.9.4) Gecko/20011206 Netscape6/6.2.1', 'Sun OS', '0.9.4', 1),
    (121, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1) Opera 7.23  [en]', 'Windows XP', '7.23', 3),
    (122, 'Mozilla/5.0 (Windows; U; Win98; en-US; rv:1.0.2) Gecko/20030208 Netscape/7.02', 'Windows 95', '1.0.2', 1),
    (123, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; BCD2000)', 'Windows XP', '6.0', 2),
    (124, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; .NET CLR 1.0.3705; .NET CLR 1.1.4322; Alexa Toolbar)', 'Windows XP', '6.0', 2),
    (125, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1) Opera 7.10  [en]', 'Windows XP', '7.10', 3),
    (126, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; FunWebProducts; .NET CLR 1.0.3705)', 'Windows XP', '6.0', 2),
    (127, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0; .NET CLR 1.1.4322; .NET CLR 1.0.3705)', 'Windows 2000', '6.0', 2),
    (128, 'Mozilla/5.0 (Windows; U; Win 9x 4.90; en-US; rv:1.4) Gecko/20030624', 'Windows ME', '1.4', 1),
    (129, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1) Opera 7.11  [en]', 'Windows XP', '7.11', 3),
    (130, 'Mozilla/4.0 (compatible; grub-client-1.4.3; Crawl your own stuff with http://grub.org)', 'uknown OS', '4.0', 5),
    (131, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; el-GR; rv:1.5) Gecko/20031007', 'Windows XP', '1.5', 1),
    (132, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0) Opera 7.23  [en]', 'Windows 2000', '7.23', 3),
    (133, 'Mozilla/4.0 (compatible; MSIE 5.13; Mac_PowerPC)', 'Mac PPC', '5.13', 2),
    (134, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 4.0; .NET CLR 1.0.3705)', 'Windows NT', '6.0', 2),
    (135, 'Mozilla/4.0 (compatible;)', 'uknown OS', '4.0', 5),
    (136, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; Hotbar 4.4.2.0)', 'Windows XP', '6.0', 2),
    (137, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0; Hotbar 4.1.7.0)', 'Windows 2000', '6.0', 2),
    (138, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0; TUCOWS; .NET CLR 1.1.4322)', 'Windows 2000', '6.0', 2),
    (139, 'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0; ESB{9404F370-72A9-465A-94D5-C275AE965397})', 'Windows 2000', '5.01', 2),
    (140, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0; MyIE2; .NET CLR 1.0.3705)', 'Windows 2000', '6.0', 2),
    (141, 'Mozilla/5.0 (Windows; U; Windows NT 5.0; en-US; rv:1.5) Gecko/20031007', 'Windows 2000', '1.5', 1),
    (142, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows 98; Win 9x 4.90; .NET CLR 1.1.4322)', 'Windows 95', '6.0', 2),
    (143, 'Mozilla/4.0 (compatible; MSIE 6.0; MSIE 5.5; Windows NT 5.1) Opera 7.01  [en]', 'Windows XP', '7.01', 3),
    (144, 'Mozilla/4.0 (compatible; MSIE 4.01; Windows 98)', 'Windows 95', '4.01', 2),
    (145, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows 98; ESB{F65AACA0-5C7B-11D8-B676-00C02628848A})', 'Windows 95', '6.0', 2),
    (146, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0; IE@netCD)', 'Windows 2000', '6.0', 2),
    (147, 'Mozilla/4.0 (compatible; MSIE 5.5; Windows NT 5.0; Compulink Network)', 'Windows 2000', '5.5', 2),
    (148, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows 98; Alexa Toolbar)', 'Windows 95', '6.0', 2),
    (149, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 4.0; T312461; .NET CLR 1.0.3705)', 'Windows NT', '6.0', 2),
    (150, 'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT; Aztec)', 'Windows NT', '5.01', 2),
    (151, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows 98; CivilTech)', 'Windows 95', '6.0', 2),
    (152, 'Mozilla/5.0 (Windows; U; Windows NT 5.0; de-DE; rv:1.6) Gecko/20040206 Firefox/0.8', 'Windows 2000', '1.6', 1),
    (153, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; iOpus-I-M)', 'Windows XP', '6.0', 2),
    (154, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows 98; TUCOWS)', 'Windows 95', '6.0', 2),
    (155, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; Hewlett-Packard; .NET CLR 1.0.3705; .NET CLR 1.1.4322)', 'Windows XP', '6.0', 2),
    (156, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows 98; TUCOWS; FunWebProducts; .NET CLR 1.1.4322)', 'Windows 95', '6.0', 2),
    (157, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; MyIE2; .NET CLR 1.1.4322; .NET CLR 1.0.3705)', 'Windows XP', '6.0', 2),
    (158, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; FunWebProducts; .NET CLR 1.1.4322)', 'Windows XP', '6.0', 2),
    (159, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0; MyIE2)', 'Windows 2000', '6.0', 2),
    (160, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0; ONEWAY NET)', 'Windows 2000', '6.0', 2),
    (161, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; {FF087BE5-1083-4DE5-8F21-637B924CB76E})', 'Windows XP', '6.0', 2),
    (162, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0; AUTOSIGN W2000 WNT VER03)', 'Windows 2000', '6.0', 2),
    (163, 'Mozilla/5.0 (Windows; U; Windows NT 5.0; en-US; rv:1.4) Gecko/20030529', 'Windows 2000', '1.4', 1),
    (164, 'Mozilla/5.0 (Windows; U; Windows NT 5.0; en-US; rv:1.4a) Gecko/20030401', 'Windows 2000', '1.4', 1),
    (165, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0; T312461; (R1 1.3))', 'Windows 2000', '6.0', 2),
    (166, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0; brip1)', 'Windows 2000', '6.0', 2),
    (167, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0; SEARCHALOT)', 'Windows 2000', '6.0', 2),
    (168, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; Feat Ext 18)', 'Windows XP', '6.0', 2),
    (169, 'Mozilla/5.0 (Windows; U; Win98; en-US; rv:1.5) Gecko/20031007', 'Windows 95', '1.5', 1),
    (170, 'Mozilla/5.0 (Windows; U; Windows NT 5.0; en-US; rv:1.7b) Gecko/20040316', 'Windows 2000', '1.7', 1),
    (171, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0; FunWebProducts)', 'Windows 2000', '6.0', 2),
    (172, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; Alexa Toolbar; .NET CLR 1.1.4322)', 'Windows XP', '6.0', 2),
    (173, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; Hotbar 4.3.1.0; FunWebProducts)', 'Windows XP', '6.0', 2),
    (174, 'Mozilla/5.0 (X11; U; Linux i686; el-gr; rv:1.4) Gecko/20030630', 'GNU/Linux', '1.4', 1),
    (175, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; brip1)', 'Windows XP', '6.0', 2),
    (176, 'SurveyBot/2.3 (Whois Source)', None, None, 6),
    (177, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows 98) Opera 7.23  [en]', 'Windows 95', '7.23', 3),
    (178, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; IE 6.05; .NET CLR 1.1.4322)', 'Windows XP', '6.0', 2),
    (179, 'Mozilla/4.0 (compatible; MSIE 5.15; Mac_PowerPC)', 'Mac PPC', '5.15', 2),
    (180, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.0.1) Gecko/20020823 Netscape/7.0', 'Windows XP', '1.0.1', 1),
    (181, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.0.2) Gecko/20021120 Netscape/7.01', 'Windows XP', '1.0.2', 1),
    (182, 'Mozilla/4.0 (compatible; MSIE 5.5; Windows NT 4.0; Hotbar 4.4.0.0)', 'Windows NT', '5.5', 2),
    (183, 'Mozilla/4.0 (compatible; MSIE 5.0; Windows 98; DigExt; FunWebProducts)', 'Windows 95', '5.0', 2),
    (184, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; {95A9C2FB-E969-47DB-A5F8-4F7D70528FF7})', 'Windows XP', '6.0', 2),
    (185, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows 98; T312461; Q312461)', 'Windows 95', '6.0', 2),
    (186, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0; DigExt; AUTOSIGN W2000 WNT VER03)', 'Windows 2000', '6.0', 2),
    (187, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; DigExt)', 'Windows XP', '6.0', 2),
    (188, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.4b) Gecko/20030516 Mozilla Firebird/0.6', 'Windows XP', '1.4', 1),
    (189, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; DigExt; .NET CLR 1.1.4322)', 'Windows XP', '6.0', 2),
    (190, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; AIRF)', 'Windows XP', '6.0', 2),
    (191, 'Mozilla/5.0 (X11; U; Linux i686; en-US; rv:0.9.9) Gecko/20020408', 'GNU/Linux', '0.9.9', 1),
    (192, 'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0; Feat Ext 18)', 'Windows 2000', '5.01', 2),
    (193, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.6) Gecko/20040113 MultiZilla/1.6.3.0d', 'Windows XP', '1.6', 1),
    (194, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; Crazy Browser 1.0.5; .NET CLR 1.1.4322)', 'Windows XP', '6.0', 2),
    (195, 'Mozilla/4.0 (compatible; MSIE 5.0; Windows 98; DigExt; Hotbar 4.3.5.0; FunWebProducts)', 'Windows 95', '5.0', 2),
    (196, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.2; ACN; MyIE2; .NET CLR 1.1.4322)', 'Windows NT', '6.0', 2),
    (197, 'Mozilla/5.0 (compatible; Konqueror/3.2; Linux) (KHTML, like Gecko)', ' Linux', '3.2', 4),
    (198, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows ME) Opera 7.50  [en]', 'uknown OS', '7.50', 3),
    (199, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; ESB{C9D3416E-AF99-432D-BB0F-629589DD2A96})', 'Windows XP', '6.0', 2),
    (200, 'Opera/7.11 (Windows NT 5.1; U)  [en]', 'Windows XP', '7.11 (Windows NT 5.1; U)', 3),
    (201, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; (R1 1.5); .NET CLR 1.1.4322)', 'Windows XP', '6.0', 2),
    (202, 'Mozilla/5.0 (Windows; U; WinNT4.0; en-US; rv:1.6) Gecko/20040206 Firefox/0.8', 'Windows NT', '1.6', 1),
    (203, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; Feat Ext 13)', 'Windows XP', '6.0', 2),
    (204, 'CURIValidate', None, None, 6),
    (205, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.3) Gecko/20030312', 'Windows XP', '1.3', 1),
    (206, 'Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.6) Gecko/20040309', 'GNU/Linux', '1.6', 1),
    (207, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; winweb; .NET CLR 1.0.3705)', 'Windows XP', '6.0', 2),
    (208, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; AUTOSIGN W2000 WNT VER03; .NET CLR 1.0.3705; .NET CLR 1.1.4322)', 'Windows XP', '6.0', 2),
    (209, 'Mozilla/4.0 (compatible; MSIE 5.01; Windows 98; Feat Ext 15)', 'Windows 95', '5.01', 2),
    (210, 'Mozilla/5.0 (Macintosh; U; PPC Mac OS X; en) AppleWebKit/124 (KHTML, like Gecko) Safari/125', 'Mac PPC', '5.0 (Macintosh; U; PPC Mac OS X; en', 1),
    (211, 'Mozilla/4.0 compatible ZyBorg/1.0 (wn.zyborg@looksmart.net; http://www.WISEnutbot.com)', 'uknown OS', '4.0 compatible ZyBorg/1.0', 5),
    (212, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; YComp 5.0.2.6)', 'Windows XP', '6.0', 2),
    (213, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; AUTOSIGN W2000 WNT VER03; .NET CLR 1.1.4322; .NET CLR 1.0.3705)', 'Windows XP', '6.0', 2),
    (214, 'Opera/7.20 (Windows NT 5.0; U)  [en]', 'Windows 2000', '7.20 (Windows NT 5.0; U)', 3),
    (215, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0; Alexa Toolbar)', 'Windows 2000', '6.0', 2),
    (216, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1) Opera 7.23  [el]', 'Windows XP', '7.23', 3),
    (217, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows 98; Win 9x 4.90; Smart Explorer 6.1)', 'Windows 95', '6.0', 2),
    (218, 'Mozilla/4.0 (compatible; Netcraft Web Server Survey)', 'uknown OS', '4.0', 5),
    (219, 'Mozilla/5.0 (compatible; Konqueror/3.1-rc4; i686 Linux; 20020516)', ' i686 Linux; 20020516', '3.1', 4),
    (220, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0; UKC VERSION)', 'Windows 2000', '6.0', 2),
    (221, 'Mozilla/4.0 (compatible; MSIE 6.0; MSIE 5.5; Windows XP) Opera 7.0  [en]', 'uknown OS', '7.0', 3),
    (222, 'Mozilla/5.0 (Windows; U; Win98; en-US; rv:1.6) Gecko/20040206 Firefox/0.8', 'Windows 95', '1.6', 1),
    (223, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows 98; SEARCHALOT 11022003)', 'Windows 95', '6.0', 2),
    (224, 'Mozilla/4.0 (compatible; MSIE 5.5; Windows 98; Win 9x 4.90; FunWebProducts; iOpus-I-M; .NET CLR 1.0.3705)', 'Windows 95', '5.5', 2),
    (225, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0; .NET CLR 1.0.2914)', 'Windows 2000', '6.0', 2),
    (226, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; .NET CLR 1.1.4322; .NET CLR 1.0.2914)', 'Windows XP', '6.0', 2),
    (227, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; Mozilla/4.0 (compatible; MSIE 6.0; Windows NT))', 'Windows XP', '6.0', 2),
    (228, 'Googlebot-Image/1.0 (+http://www.googlebot.com/bot.html)', '', '1.0', 7),
    (229, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.2.1) Gecko/20021130', 'Windows XP', '1.2.1', 1),
    (230, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.7b) Gecko/20040314', 'Windows XP', '1.7', 1),
    (231, 'Mozilla/5.0 (Windows; U; Win98; en-US; rv:1.5) Gecko/20031007 Firebird/0.7', 'Windows 95', '1.5', 1),
    (232, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.4) Gecko/20030624', 'Windows XP', '1.4', 1),
    (233, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; iOpus-I-M; CLINK)', 'Windows XP', '6.0', 2),
    (234, 'Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.7b) Gecko/20040316', 'GNU/Linux', '1.7', 1),
    (235, 'Mozilla/4.0 (compatible; MSIE 5.5; Windows 98; Win 9x 4.90; Alexa Toolbar)', 'Windows 95', '5.5', 2),
    (236, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows 98; .NET CLR 1.0.3705; .NET CLR 1.1.4322)', 'Windows 95', '6.0', 2),
    (237, 'Mozilla/5.0 (Windows; U; WinNT4.0; en-US; rv:1.0.1) Gecko/20020823 Netscape/7.0', 'Windows NT', '1.0.1', 1),
    (238, 'Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.5) Gecko/20031007 Firebird/0.7', 'GNU/Linux', '1.5', 1),
    (239, 'Mozilla/4.0 (compatible; MSIE 5.5; Windows NT 5.0; .NET CLR 1.0.3705)', 'Windows 2000', '5.5', 2),
    (240, 'Mozilla/5.0 (Windows; U; WinNT; en; rv:1.0.2) Gecko/20030311 Beonex/0.8.2-stable', 'Windows NT', '1.0.2', 1),
    (241, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; {5D5A1B12-0F6C-4483-A2FF-B03498A4570F})', 'Windows XP', '6.0', 2),
    (242, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows 98; Win 9x 4.90; Q312461; AT&T CSM6.0; sbcydsl 3.12; YComp 5.0.0.0; .NET CLR 1.0.3705)', 'Windows 95', '6.0', 2),
    (243, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; AUTOSIGN W2000 WNT VER03; .NET CLR 1.1.4322)', 'Windows XP', '6.0', 2),
    (244, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; MyIE2; .NET CLR 1.0.3705; .NET CLR 1.1.4322)', 'Windows XP', '6.0', 2),
    (245, 'Mozilla/4.0 (compatible; MSIE 5.0; Windows XP) Opera 6.05  [el]', 'uknown OS', '6.05', 3),
    (246, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; ESB{C46FA41C-A455-4506-A8C0-4B25AFA4C704}; FunWebProducts)', 'Windows XP', '6.0', 2),
    (247, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; DVD Owner; .NET CLR 1.1.4322)', 'Windows XP', '6.0', 2),
    (248, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; Q312461; MyIE2)', 'Windows XP', '6.0', 2),
    (249, 'Mozilla/5.0 (compatible; Konqueror/3.1; Linux)', ' Linux', '3.1', 4),
    (250, 'Mozilla/5.0 (Windows; U; Win98; en-US; rv:1.4) Gecko/20030624 Netscape/7.1 (ax)', 'Windows 95', '1.4', 1),
    (251, 'Baiduspider+(+http://www.baidu.com/search/spider.htm)', None, None, 6),
    (252, 'Mozilla/4.0 (compatible; MSIE 5.0; Windows 95; DigExt)', 'Windows 95', '5.0', 2),
    (253, 'Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.2.1) Gecko/20030225', 'GNU/Linux', '1.2.1', 1),
    (254, 'Mozilla/4.0 (compatible; MSIE 6.0; X11; Linux i686) Opera 7.23  [en]', 'GNU/Linux', '7.23', 3),
    (255, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0; TUCOWS)', 'Windows 2000', '6.0', 2),
    (256, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.5a) Gecko/20030728 Mozilla Firebird/0.6.1', 'Windows XP', '1.5', 1),
    (257, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; LRF; .NET CLR 1.1.4322)', 'Windows XP', '6.0', 2),
    (258, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows 98; Win 9x 4.90; (R1 1.3); .NET CLR 1.1.4322)', 'Windows 95', '6.0', 2),
    (259, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; .NET CLR 1.0.3705; Alexa Toolbar; .NET CLR 1.1.4322)', 'Windows XP', '6.0', 2),
    (260, 'Mozilla/4.0 (compatible; MSIE 6.0b; Windows NT 5.0; NetCaptor 7.5.0 Gold; .NET CLR 1.1.4322)', 'Windows 2000', '6.0', 2),
    (261, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows 98; Tucows; YComp 5.0.0.0)', 'Windows 95', '6.0', 2),
    (262, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; Q312461; .NET CLR 1.0.3705; .NET CLR 1.1.4322)', 'Windows XP', '6.0', 2),
    (263, 'Mozilla/5.0 (Windows; U; Windows NT 5.0; el-GR; rv:1.6) Gecko/20040113', 'Windows 2000', '1.6', 1),
    (264, 'UptimeBot(www.uptimebot.com)', None, None, 6),
    (265, 'Xenu Link Sleuth 1.2e', None, None, 6),
    (266, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; ESB{1BAAA30F-BDC9-4E83-AF38-660B1E484271})', 'Windows XP', '6.0', 2),
    (267, 'Mozilla/4.0 (compatible; MSIE 4.01; Windows CE; PPC; 240x320)', 'Mac PPC', '4.01', 2),
    (268, 'Mozilla/4.0 (compatible; MSIE 5.5; Windows NT 5.0; T312461)', 'Windows 2000', '5.5', 2),
    (269, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1) Opera 7.50  [en]', 'Windows XP', '7.50', 3),
    (270, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0; H010818; UB1.4_IE6.0_SP1; .NET CLR 1.0.3705)', 'Windows 2000', '6.0', 2),
    (271, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SALT 1.0.4223.1 0111 Developer; .NET CLR 1.1.4322; .NET CLR 1.0.3705)', 'Windows XP', '6.0', 2),
    (272, 'Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.0.2) Gecko/20030716', 'GNU/Linux', '1.0.2', 1),
    (273, 'Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.4b) Gecko/20030507', 'GNU/Linux', '1.4', 1),
    (274, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows 98; FunWebProducts)', 'Windows 95', '6.0', 2),
    (275, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0; YComp 5.0.0.0)', 'Windows 2000', '6.0', 2),
    (276, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows 98; winweb)', 'Windows 95', '6.0', 2),
    (277, 'Mozilla/4.0 (compatible; MSIE 6.0; MSIE 5.5; Windows 98) Opera 7.02  [en]', 'Windows 95', '7.02', 3),
    (278, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; NetCaptor 7.2.0; .NET CLR 1.0.3705)', 'Windows XP', '6.0', 2),
    (279, 'Mozilla/4.0 (compatible; MSIE 5.5; Windows 98; Installed by Symantec Package)', 'Windows 95', '5.5', 2),
    (280, 'Mozilla/5.0 (Windows; U; Windows NT 5.2; en-US; rv:1.6) Gecko/20040206 Firefox/0.8', 'Windows NT', '1.6', 1),
    (281, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; NetCaptor 7.2.2)', 'Windows XP', '6.0', 2),
    (282, 'Mozilla/4.0 (compatible; MS FrontPage 6.0)', 'uknown OS', '4.0', 5),
    (283, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; StumbleUpon.com 1.760)', 'Windows XP', '6.0', 2),
    (284, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1) Opera 7.22  [en]', 'Windows XP', '7.22', 3),
    (285, 'Mozilla/4.0 (compatible; MSIE 5.0; Windows XP) Opera 6.05  [en]', 'uknown OS', '6.05', 3),
    (286, 'Mozilla/4.0 (compatible; MSIE 6.0; MSIE 5.5; Windows NT 5.1) Opera 7.0  [en]', 'Windows XP', '7.0', 3),
    (287, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows ME) Opera 7.23  [en]', 'uknown OS', '7.23', 3),
    (288, 'Java1.3.0', None, None, 6),
    (289, 'Mozilla/5.0 (Windows; U; Windows NT 5.2; fr-FR; rv:1.4) Gecko/20030624 Netscape/7.1 (ax)', 'Windows NT', '1.4', 1),
    (290, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; Argentina.com v12b8.1; .NET CLR 1.1.4322)', 'Windows XP', '6.0', 2),
    (291, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; AT&T CSM6.0)', 'Windows XP', '6.0', 2),
    (292, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0; FunWebProducts; .NET CLR 1.0.3705)', 'Windows 2000', '6.0', 2),
    (293, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; .NET CLR 1.1.4322; MSN 9.0; MSNbVZ02; MSNmen-us; MSNcOTH)', 'Windows XP', '6.0', 2),
    (294, 'Mozilla/5.0 (Windows; U; Win 9x 4.90; en-US; rv:1.4) Gecko/20030624 Netscape/7.1 (ax)', 'Windows ME', '1.4', 1),
    (295, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; FunWebProducts-MyWay; .NET CLR 1.1.4322)', 'Windows XP', '6.0', 2),
    (296, 'Mozilla/4.0 (compatible; MSIE 5.5; Windows 98; Creative)', 'Windows 95', '5.5', 2),
    (297, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows 98; Avant Browser [avantbrowser.com])', 'Windows 95', '6.0', 2),
    (298, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0; AskBar 3.00; Hotbar 4.4.2.0)', 'Windows 2000', '6.0', 2),
    (299, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0) Opera 7.11  [en]', 'Windows 2000', '7.11', 3),
    (300, 'Mozilla/5.0 (Windows NT 5.1; U) Opera 7.23  [en]', 'Windows XP', '7.23', 3),
    (301, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0; Mozilla/4.0 (Compatible; MSIE 6.0; Windows 2000; MCK); Mozilla/4.0 (Compatible; MSIE 6.0; Win; MCK))', 'Windows 2000', '6.0', 2),
    (302, 'Mozilla/5.0 (X11; U; Linux i586; en-US; rv:1.4.1) Gecko/20031114', 'GNU/Linux', '1.4.1', 1),
    (303, 'Mozilla/4.0 (compatible; MSIE 5.5; Windows NT 5.0; H010818; UB1800; .NET CLR 1.0.3705)', 'Windows 2000', '5.5', 2),
    (304, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; (R1 1.3))', 'Windows XP', '6.0', 2),
    (305, 'Mozilla/6.20  (BEOS; U ;Nav)', None, None, 6),
    (306, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows 98; TUCOWS.COM)', 'Windows 95', '6.0', 2),
    (307, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows 98; .NET CLR 1.0.3705)', 'Windows 95', '6.0', 2),
    (308, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0; T312461; YComp 5.0.0.0; .NET CLR 1.0.3705)', 'Windows 2000', '6.0', 2),
    (309, 'Mozilla/4.0 (compatible; MSIE 5.5; Windows 95; FunWebProducts)', 'Windows 95', '5.5', 2),
    (310, 'Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.6) Gecko/20040122 Debian/1.6-1', 'GNU/Linux', '1.6', 1),
    (311, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0; www.ASPSimply.com)', 'Windows 2000', '6.0', 2),
    (312, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0; ESB{59E9535D-AE57-4D68-A91A-F568540A69C8})', 'Windows 2000', '6.0', 2),
    (313, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.0.2) Gecko/20030208 Netscape/7.02', 'Windows XP', '1.0.2', 1),
    )

def createTest(sig, isSupported, index, os, browser, version):
    def test(self):
        actual = self.script(sig)
        self.assertEquals(isSupported, actual)
    testname = 'test_%d %s %s %s' % (index, os, BROWSERNAMES[browser],
                                     '.'.join([str(v) for v in version]))
    setattr(TestBrowserSupportsKupu, testname.strip(), test)

def createTests():
    for id, sig, os, version, browser in BROWSERS:
        if version:
            version = version.split()[0]
            version = tuple([int(v) for v in version.split('.')])
        else:
            version = ()
        minver = SUPPORTED.get(browser, None)
        supported = minver != None and version >= minver

        # Specifically exclude support for some browsers
        #XXX Hack
        if 'Safari' in sig:
            supported = False

        createTest(sig, supported, id, os, browser, version)

createTests()

from unittest import TestSuite, makeSuite
def test_suite():
    suite = TestSuite()
    suite.addTest(makeSuite(TestBrowserSupportsKupu))
    return suite

if __name__ == '__main__':
    framework()
