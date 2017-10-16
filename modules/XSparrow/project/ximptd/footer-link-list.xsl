<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
 <xsl:template name="footer-link-list" match="footer-link-list">
  <ul class="footer-links" uid="{@uid}">
    <xsl:apply-templates select="*"/>
  </ul>
 </xsl:template>
</xsl:stylesheet>
