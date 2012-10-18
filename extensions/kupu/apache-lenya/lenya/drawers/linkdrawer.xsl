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

XSL transformation from Kupu Library XML to HTML for the link library
drawer.

$Id: linkdrawer.xsl 9879 2005-03-18 12:04:00Z yuppie $
-->
<xsl:stylesheet
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns:i18n="http://apache.org/cocoon/i18n/2.1" 
  version="1.0">

  <xsl:import href="${parameters.getParameter('import-stylesheet-url')}"/>

  <xsl:template match="resource|collection" mode="properties">
   <form onsubmit="return false;">
     <table class="resource-properties">
       <tr class="kupu-linkdrawer-title-row">
         <td>
           <strong>Title</strong><br />
           <xsl:value-of select="title" />
         </td>
       </tr>
       <tr class="kupu-linkdrawer-description-row">
         <td>
           <strong>Description</strong><br />
           <xsl:value-of select="description" />
         </td>
       </tr>
       <!--
       <tr class="kupu-linkdrawer-language-row">
         <td>
           <strong>Language</strong><br />
           <xsl:value-of select="language" />
         </td>
       </tr>
       -->
       <tr class="kupu-linkdrawer-name-row">
         <td>
           <strong>Name</strong><br />
           <input type="text" id="link_name" size="14" value="{title}"/>
         </td>
       </tr>       
       <tr class="kupu-linkdrawer-target-row">
         <td>
           <strong>Target</strong><br />
           <input type="text" id="link_target" value="" size="10" title="Target window of link: _self, _blank, or custom"/>
         </td>
       </tr>       
       <xsl:if test="preview">
       <tr>
         <td>
           <strong>Preview</strong><br/>
           <div id="epd-imgpreview">
             <a href="{preview}" target="_blank" title="Preview page {uri} in new a window">
               Preview page in new window.
             </a>
           </div>
         </td>
       </tr>
       </xsl:if>
     </table>
    </form>
  </xsl:template>
  
  <xsl:template name="drawer-title">Link</xsl:template>  
</xsl:stylesheet>
