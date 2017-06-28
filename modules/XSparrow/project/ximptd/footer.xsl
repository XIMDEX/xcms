<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
 <xsl:template name="footer" match="footer">
<footer class="footer">
      <div class="container" uid="{@uid}">
        <xsl:apply-templates select="*"/>        
      </div>
    </footer>
 </xsl:template>
</xsl:stylesheet>
