##############################################################################
#
# Copyright (c) 2003-2005 Kupu Contributors. All rights reserved.
#
# This software is distributed under the terms of the Kupu
# License. See LICENSE.txt for license text. For a list of Kupu
# Contributors see CREDITS.txt.
#
##############################################################################
"""Tests for the library tool

$Id: test_librarymanager.py 9879 2005-03-18 12:04:00Z yuppie $
"""

import os, sys
if __name__ == '__main__':
    execfile(os.path.join(sys.path[0], 'framework.py'))

import Acquisition
from Testing.ZopeTestCase import ZopeTestCase

from Products.kupu.plone.plonelibrarytool import PloneKupuLibraryTool

class FakeMembershipTool:
    isAnonymousUser = lambda self: True

class FakePortal(Acquisition.Implicit):
    absolute_url = lambda(self): None
    portal_membership = FakeMembershipTool()

class FakeContextObject(Acquisition.Implicit):

    __allow_access_to_unprotected_subobjects__ = True
    isPrincipiaFolderish = True
    REQUEST = 42
    absolute_url = lambda(self): "The answer is 42"

class TestILibraryManager(ZopeTestCase):
    """Test the implementation of ILibraryManger in KupuLibraryTool"""

    def afterSetUp(self):
        self.libs = self.makeLibraries()

    def makeLibraries(self):
        # need to use Plone specific tool for Acquisition. Sucks.
        libs = PloneKupuLibraryTool()
        self.portal = FakePortal()
        context = FakeContextObject()
        libs = libs.__of__(self.portal)
        self.context = context.__of__(self.portal)
        libs.addLibrary('foo_id', 'Foobar', 'foobar', 'foosrc', 'fooicon')
        libs.addLibrary('bar_id', 'Barfoo', 'barfoo', 'barsrc', 'baricon')
        libs.addLibrary('baz_id', 'Baz', 'foobarbaz', 'bazsrc', 'bazicon')
        return libs

    def test_get_library(self):
        libs = self.libs
        expected = (
            dict(id='foo_id', title='Foobar', uri='foobar',
                 src='foosrc', icon='fooicon'),
            dict(id='bar_id', title='Barfoo', uri='barfoo',
                 src='barsrc', icon='baricon'),
            dict(id='baz_id', title='Baz', uri='foobarbaz',
                 src='bazsrc', icon='bazicon'),
            )
        self.assertEqual(libs.getLibraries(self.context), expected)

    def test_expressions(self):
        libs = self.libs
        context = self.context
        new_libs = (
            dict(id='foo_id', title='Foobar', uri='python:request',
                 src='foosrc', icon='fooicon'),
            dict(id='bar_id', title='Barfoo', uri='python:object',
                 src='barsrc', icon='baricon'),
            dict(id='baz_id', title='Baz', uri='string:${object/absolute_url}',
                 src='bazsrc', icon='bazicon'),
            )
        libs.updateLibraries(new_libs)

        expected = (
            dict(id='foo_id', title='Foobar', uri=42,
                 src='foosrc', icon='fooicon'),
            dict(id='bar_id', title='Barfoo', uri=context,
                 src='barsrc', icon='baricon'),
            dict(id='baz_id', title='Baz', uri="The answer is 42",
                 src='bazsrc', icon='bazicon')
            )
        self.assertEqual(libs.getLibraries(context), expected)

    def test_delete(self):
        libs = self.libs
        libs.deleteLibraries([1])
        expected = (
            dict(id='foo_id', title='Foobar', uri='foobar',
                 src='foosrc', icon='fooicon'),
            dict(id='baz_id', title='Baz', uri='foobarbaz',
                 src='bazsrc', icon='bazicon'),
            )
        self.assertEqual(libs.getLibraries(self.context), expected)

        libs = self.makeLibraries()
        libs.deleteLibraries([0, 1])
        expected = (
            dict(id='baz_id', title='Baz', uri='foobarbaz',
                 src='bazsrc', icon='bazicon'),
            )
        self.assertEqual(libs.getLibraries(self.context), expected)

    def test_update(self):
        libs = self.libs
        context = self.context
        new_libs = (
            dict(id='foo_new_id', title='Newfoo', uri="python:object",
                 src='foonewsrc', icon="foonewicon"),
            dict(id='just_a_new_id'),
            dict(src="python:'you stink'.upper()"),
            )
        libs.updateLibraries(new_libs)
        expected = (
            dict(id='foo_new_id', title='Newfoo', uri=context,
                 src='foonewsrc', icon='foonewicon'),
            dict(id='just_a_new_id', title='Barfoo', uri='barfoo',
                 src='barsrc', icon='baricon'),
            dict(id='baz_id', title='Baz', uri='foobarbaz',
                 src="YOU STINK", icon='bazicon'),
            )
        self.assertEqual(libs.getLibraries(context), expected)

    def test_move(self):
        libs = self.libs
        libs.moveUp([1])
        expected = (
            dict(id='bar_id', title='Barfoo', uri='barfoo',
                 src='barsrc', icon='baricon'),
            dict(id='foo_id', title='Foobar', uri='foobar',
                 src='foosrc', icon='fooicon'),
            dict(id='baz_id', title='Baz', uri='foobarbaz',
                 src='bazsrc', icon='bazicon'),
            )
        self.assertEqual(libs.getLibraries(self.context), expected)

        libs.moveDown([1])
        expected = (
            dict(id='bar_id', title='Barfoo', uri='barfoo',
                 src='barsrc', icon='baricon'),
            dict(id='baz_id', title='Baz', uri='foobarbaz',
                 src='bazsrc', icon='bazicon'),
            dict(id='foo_id', title='Foobar', uri='foobar',
                 src='foosrc', icon='fooicon'),
            )
        self.assertEqual(libs.getLibraries(self.context), expected)

        libs.moveUp([1, 2])
        expected = (
            dict(id='baz_id', title='Baz', uri='foobarbaz',
                 src='bazsrc', icon='bazicon'),
            dict(id='foo_id', title='Foobar', uri='foobar',
                 src='foosrc', icon='fooicon'),
            dict(id='bar_id', title='Barfoo', uri='barfoo',
                 src='barsrc', icon='baricon'),
            )
        self.assertEqual(libs.getLibraries(self.context), expected)

        libs.moveDown([2])
        expected = (
            dict(id='bar_id', title='Barfoo', uri='barfoo',
                 src='barsrc', icon='baricon'),
            dict(id='foo_id', title='Foobar', uri='foobar',
                 src='foosrc', icon='fooicon'),
            dict(id='baz_id', title='Baz', uri='foobarbaz',
                 src='bazsrc', icon='bazicon'),
            )
        self.assertEqual(libs.getLibraries(self.context), expected)

if __name__ == '__main__':
    framework()
else:
    # While framework.py provides its own test_suite()
    # method the testrunner utility does not.
    from unittest import TestSuite, makeSuite
    def test_suite():
        suite = TestSuite()
        suite.addTest(makeSuite(TestILibraryManager))
        return suite
