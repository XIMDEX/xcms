<?xml version="1.0" encoding="utf-8"?>
<!--
##############################################################################
#
# Copyright (c) 2003-2005 Kupu Contributors. All rights reserved.
#
# This software is distributed under the terms of the Kupu
# License. See LICENSE.txt for license text. For a list of Kupu
# Contributors see CREDITS.txt.
#
##############################################################################

Generate an JSPX template from Kupu distribution files

The main difference from make.xsl itself, is that this creates fmt:message tags for the i18n part.

This means that also the fmt-tags must be available, so your html.kupu must specificy something like:
  <kupu:part name="html">
    <html xmlns="http://www.w3.org/1999/xhtml"
          xmlns:jsp="http://java.sun.com/JSP/Page" 
          xmlns:fmt="http://java.sun.com/jsp/jstl/fmt">
      <jsp:output doctype-root-element="html"
                  doctype-public="-//W3C//DTD XHTML 1.0 Strict//EN"
                  doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"/>
      <fmt:bundle basename="org.oscom.kupu.Messages">
         <kupu:define-slot name="html" />
      </fmt:bundle>
    </html>
  </kupu:part>

See also: common/kupu.pox.jspx (which can be used by i18n.js)
-->
<xsl:stylesheet
   xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
   xmlns:kupu="http://kupu.oscom.org/namespaces/dist"
   xmlns:i18n="http://xml.zope.org/namespaces/i18n" 
   xmlns:fmt="http://java.sun.com/jsp/jstl/fmt"
   xmlns:html="http://www.w3.org/1999/xhtml"
   xmlns:jsp="http://java.sun.com/JSP/Page"
   exclude-result-prefixes="kupu"
   version="1.0"
   >
  <xsl:import href="make.xsl" />  


  <xsl:template match="html:*" mode="expand">
    <xsl:choose>
      <xsl:when test="@i18n:translate">
        <xsl:element name="{name()}">
          <xsl:copy-of select="@html:*" />
          <fmt:message>
            <xsl:attribute name="key">
              <xsl:choose>
                <xsl:when test="@i18n:translate = ''">
                  <xsl:apply-templates select="text()" mode="expand-i18n" />
                </xsl:when>
                <xsl:otherwise>
                  <xsl:value-of select="@i18n:translate" />
                </xsl:otherwise>
              </xsl:choose>
            </xsl:attribute>
          </fmt:message>
        </xsl:element>
      </xsl:when>
      <xsl:when test="@i18n:attributes">
        <xsl:variable name="attributes"><xsl:value-of select="@i18n:attributes" /></xsl:variable>
        <fmt:message var="_">
          <xsl:attribute name="key">
            <xsl:value-of select="@title" /><!-- should be @$attributes, but that doesn't work -->
          </xsl:attribute>
        </fmt:message>
        <xsl:element name="{name()}">
          <xsl:copy-of select="@html:*" />
          <xsl:attribute name="{$attributes}">${_}</xsl:attribute>
          <xsl:apply-templates  mode="expand" />
        </xsl:element>
      </xsl:when>
      <xsl:otherwise>
        <xsl:element name="{name()}">
          <xsl:copy-of select="@html:*" />
          <xsl:apply-templates  mode="expand" />
        </xsl:element>
      </xsl:otherwise>
    </xsl:choose>
  </xsl:template>

  <xsl:template match="html:html" mode="expand"> <!-- to preserve the namespaces-->
    <xsl:copy>
      <xsl:copy-of select="@*" />
      <xsl:apply-templates mode="expand" />
    </xsl:copy>
  </xsl:template>

  <xsl:template match="html:select" mode="expand">
    <!-- in some toolboxes an empty select appears, add jsp:text to avoid that it collapses away, which browsers cannot handle -->
    <xsl:copy>
      <xsl:copy-of select="@*" />
      <jsp:text> </jsp:text>
      <xsl:apply-templates mode="expand" />
    </xsl:copy>
  </xsl:template>
  
  <xsl:template match="text()" mode="expand-i18n">
    <!-- this should probably be trim() rather then normalize-space (but that functions does not natively exist) -->
    <xsl:value-of select="normalize-space(.)" />
  </xsl:template>

</xsl:stylesheet>
