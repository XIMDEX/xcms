<?xml version="1.0" encoding="UTF-8" ?>
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

XSL transformation from Kupu Library XML to HTML for the library
drawer.

$Id: librarydrawer.xsl 9879 2005-03-18 12:04:00Z yuppie $
-->
<xsl:stylesheet
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  version="1.0">

  <xsl:variable name="titlelength" select="14"/>

  <xsl:template match="/">
    <html lang="en" xml:lang="en" xmlns="http://www.w3.org/1999/xhtml">
      <head>
        <title>Imagedrawer</title>
      </head>
      <body>
        <div id="kupu-librarydrawer">
          <div id="kupu-drawerheader">
            <xsl:apply-templates
              select="/libraries/*[@selected]"
              mode="header"
              />
          <form onsubmit="return false;">
            <input id="kupu-searchbox" name="searchbox" value=""
                   onkeyup="if (event.keyCode == 13 ) drawertool.current_drawer.search();" />
          </form>
          </div>
          <div id="kupu-panels">
            <table width="95%" border="1">
              <tr class="kupu-panelsrow">
                <td id="kupu-librariespanel" width="10%">
                  <div id="kupu-librariesitems" class="overflow">
                    <xsl:apply-templates select="/libraries/library"/>
                  </div>
                </td>
                <td id="kupu-resourcespanel" width="10%">
                  <div id="kupu-resourceitems" class="overflow">
                    <xsl:apply-templates
                      select="/libraries/*[@selected]"
                      mode="currentpanel"
                      />
                  </div>
                </td>
                <td id="kupu-propertiespanel">
                  <div id="kupu-properties" class="overflow">
                    <xsl:apply-templates
                      select="//resource[@selected]"
                      mode="properties"
                      />
                  </div>
                </td>
              </tr>
            </table>
          </div>
          <div id="kupu-dialogbuttons">
            <button type="button" class="button"
              onclick="drawertool.current_drawer.reloadCurrent();">Reload current</button>
            <button type="button" class="button"
              onclick="drawertool.closeDrawer();">Cancel</button>
            <button type="button" class="button"
              onclick="drawertool.current_drawer.save();">Ok</button>
          </div>
        </div>
      </body>
    </html>
  </xsl:template>

  <xsl:template match="library">
    <div onclick="drawertool.current_drawer.selectLibrary('{@id}');"
         class="kupu-libsource">
      <xsl:attribute name="id">
        <xsl:value-of select="@id" />
      </xsl:attribute>
      <xsl:if test="icon">
        <img src="{icon}" title="{title}" alt="{title}" />&#xa0;
      </xsl:if>
      <xsl:apply-templates select="title"/>
    </div>
  </xsl:template>

  <xsl:template match="library|collection" mode="currentpanel">
    <xsl:apply-templates select="items/collection|items/resource" />
  </xsl:template>

  <xsl:template match="library|collection|resource" mode="header">
    <xsl:text>Current location: </xsl:text>
    <xsl:value-of select="uri/text()" />
  </xsl:template>

  <xsl:template match="resource|collection">
    <div id="{@id}" class="kupu-libsource">
      <xsl:attribute name="onclick">
        <xsl:choose>
          <xsl:when test="local-name()='collection'">drawertool.current_drawer.selectCollection('<xsl:value-of select="@id" />');</xsl:when>
          <xsl:otherwise>drawertool.current_drawer.selectItem('<xsl:value-of select="@id" />')</xsl:otherwise>
        </xsl:choose>
      </xsl:attribute>
      <xsl:if test="@selected">
        <xsl:attribute name="style">background-color: #C0C0C0</xsl:attribute>
      </xsl:if>
      <xsl:if test="icon">
        <img src="{icon}" title="{title}" alt="{title}" />&#xa0;
      </xsl:if>
      <xsl:apply-templates select="title"/>
    </div>
  </xsl:template>
  
  <xsl:template match="title">
    <xsl:choose>
      <xsl:when test="string-length() &gt; $titlelength">
        <xsl:value-of select="substring(., 0, $titlelength)"/>...
      </xsl:when>
      <xsl:otherwise>
        <xsl:value-of select="."/>
      </xsl:otherwise>
    </xsl:choose>
  </xsl:template>

  <xsl:template match="resource|collection" mode="properties">
    <!-- Override this template for your custom library drawer -->
  </xsl:template>
</xsl:stylesheet>
