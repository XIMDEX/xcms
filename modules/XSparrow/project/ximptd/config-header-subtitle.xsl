<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
   	<xsl:template name="config-header-subtitle" match="config-header-subtitle">
          <p uid="{@uid}">
            <xsl:value-of select="."/>
          </p>		   		
   	</xsl:template>
</xsl:stylesheet>
