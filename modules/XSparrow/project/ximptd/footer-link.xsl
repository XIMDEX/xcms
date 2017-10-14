<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
  <xsl:template name="footer-link" match="footer-link">
    <xsl:variable name="href">
      <xsl:choose>
        <xsl:when test="@href and @href!=''">
          <xsl:value-of select="concat('@@@RMximdex.pathto(',@href,')@@@')"/>
        </xsl:when>
      </xsl:choose>
    </xsl:variable>
    <li >
      <a href="{$href}" uid="{@uid}">
 <xsl:apply-templates select="text()"/>
</a>
   </li>
   <li class="muted">·</li>
 </xsl:template>
</xsl:stylesheet>