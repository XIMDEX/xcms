<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
                  <xsl:template name="footer" match="footer"> 
                    <div class="content pure-u-1" uid="{@uid}">Footer
           			<xsl:apply-templates/>
                   	</div>
                  </xsl:template>
</xsl:stylesheet>
