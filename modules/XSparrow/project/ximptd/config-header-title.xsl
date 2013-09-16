<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
      	<xsl:template name="config-header-title" match="config-header-title">
      				<h1 uid="{@uid}">
          						<xsl:value-of select="."/>
                              </h1>		
      		
      	</xsl:template>
</xsl:stylesheet>
