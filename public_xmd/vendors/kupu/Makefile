##############################################################################
#
# Copyright (c) 2003-2005 Kupu Contributors. All rights reserved.
#
# This software is distributed under the terms of the Kupu
# License. See LICENSE.txt for license text. For a list of Kupu
# Contributors see CREDITS.txt.
#
##############################################################################

# $Id: Makefile 16388 2005-08-24 14:10:09Z mihxil $

XSLTPROC = /usr/bin/env xsltproc
XSL_DEBUG = --param debug true\(\)
XSLTPROC_PARAMS = --nonet --novalid --xinclude
XSL_FILE = make.xsl

JAVA_DIR=java.build
JAVA_RESOURCE=org.oscom.kupu.Messages
MSGFMT = /usr/bin/env msgfmt --verbose
MSGEN  = /usr/bin/env msgen

all: clean kupu.html kupuform.html kupumulti.html zope2macros plonemacros silvamacros lenyamacros kupucnf.html

kupu.html:
	$(XSLTPROC) $(XSLTPROC_PARAMS) -o common/kupu.html $(XSL_FILE) dist.kupu

zope2macros:
	$(XSLTPROC) $(XSLTPROC_PARAMS) -o common/kupumacros.html $(XSL_FILE) dist-zope2.kupu

kupuform.html:
	$(XSLTPROC) $(XSLTPROC_PARAMS) -o common/kupuform.html $(XSL_FILE) dist-form.kupu

kupumulti.html:
	$(XSLTPROC) $(XSLTPROC_PARAMS) -o common/kupumulti.html $(XSL_FILE) dist-multi.kupu

kupucnf.html:
	$(XSLTPROC) $(XSLTPROC_PARAMS) -o common/kupucnf.html $(XSL_FILE) dist-cnf.kupu

plonemacros:
	$(XSLTPROC) $(XSLTPROC_PARAMS) -o plone/kupu_plone_layer/kupu_wysiwyg_support.html $(XSL_FILE) dist-plone.kupu

silvamacros:
	$(XSLTPROC) $(XSLTPROC_PARAMS) -o silva/kupumacros.html $(XSL_FILE) dist-silva.kupu

lenyamacros:
	$(XSLTPROC) $(XSLTPROC_PARAMS) -o apache-lenya/kupu/kupumacros.html $(XSL_FILE) dist-apache-lenya.kupu


#used by kupu-18n.jar
$(JAVA_DIR)/kupu-defaults.po: i18n/kupu.pot
	mkdir -p $(JAVA_DIR)
	$(MSGEN) $< -o $@ 

#kupu-18n.jar can e.g. be used by fmt-tags of jstl 1.1, see e.g. common/kupu.pox.jspx
kupu-i18n.jar: i18n/*.po $(JAVA_DIR)/kupu-defaults.po
	mkdir -p $(JAVA_DIR)
	 $(MSGFMT) --java2  -d $(JAVA_DIR)  -r $(JAVA_RESOURCE) $(JAVA_DIR)/kupu-defaults.po
	for PO in `ls i18n/kupu-*.po` ; do \
		LOCALE=`echo $${PO} | sed "s|i18n/kupu-||;s|\.po||;s|-|_|g"`; \
		$(MSGFMT) --java2  -D . -d $(JAVA_DIR) -r $(JAVA_RESOURCE) -l $${LOCALE} $${PO}; \
	done
	jar cf $@ -C $(JAVA_DIR) org


clean:
	rm -f common/kupu.html
	rm -f common/kupumacros.html
	rm -f common/kupuform.html
	rm -f common/kupumulti.html
	rm -f common/kupucnf.html
	rm -f plone/kupu_plone_layer/kupu_wysiwyg_support.html
	rm -f silva/kupumacros.html
	rm -f apache-lenya/kupu/kupumacros.html
	rm -rf $(JAVA_DIR)
	rm -f kupu-i18n.jar

debug:
	$(XSLTPROC) $(XSL_DEBUG) $(XSLTPROC_PARAMS) -o common/kupu.html $(XSL_FILE) dist.kupu
