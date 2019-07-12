##############################################################################
#
# Copyright (c) 2003-2005 Kupu Contributors. All rights reserved.
#
# This software is distributed under the terms of the Kupu
# License. See LICENSE.txt for license text. For a list of Kupu
# Contributors see CREDITS.txt.
#
##############################################################################

# $Id: Makefile 15880 2005-08-09 22:56:43Z guido $

XSLTPROC = /usr/bin/env xsltproc
XSL_DEBUG = --param debug true\(\)
XSLTPROC_PARAMS = --nonet --novalid --xinclude
XSL_FILE = ../make.xsl

all: clean dist

dist:
	$(XSLTPROC) $(XSLTPROC_PARAMS) -o ../common/kupu-widgeteer.html $(XSL_FILE) dist.kupu

clean:
	rm -f ../common/kupu-widgeteer.html

