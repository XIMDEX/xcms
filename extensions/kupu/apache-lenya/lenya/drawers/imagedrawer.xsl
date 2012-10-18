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

XSL transformation from Kupu Library XML to HTML for the image library
drawer.

$Id: imagedrawer.xsl 9879 2005-03-18 12:04:00Z yuppie $
-->
<xsl:stylesheet
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns:i18n="http://apache.org/cocoon/i18n/2.1" 
  xmlns:jx="http://apache.org/cocoon/templates/jx/1.0"
  version="1.0">

  <xsl:import href="${parameters.getParameter('import-stylesheet-url')}"/>
    
  <xsl:template match="resource|collection" mode="properties">
    <table class="resource-properties">
      <tr>
        <td>
          <div>
            <strong>Title</strong><br />
            <xsl:value-of select="title" />
          </div>
          <div>
            <strong>Size</strong><br />
            <xsl:value-of select="size" />
          </div>
          <div>
            <strong>Description</strong><br />
            <xsl:value-of select="description" />
          </div>
          <div>
            <strong>ALT-text</strong><br />
            <form onsubmit="return false;">
              <input type="text" id="image_alt" size="10" />
            </form>
          </div>
        </td>
        <xsl:if test="preview">
        <td>
          <div><strong>Preview</strong></div>
          <div id="epd-imgpreview">
            <img src="{preview}" title="{title}" alt="{title}"/>
          </div>
        </td>
        </xsl:if>
      </tr>
    </table>
  </xsl:template>
  
  <xsl:template name="drawer-title">Image</xsl:template>  
</xsl:stylesheet>
