<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
                  <xsl:template name="content" match="content"> 
                    	<xsl:apply-templates select="main"/>
                   	<xsl:apply-templates select="menu"/>
                  </xsl:template>
</xsl:stylesheet>
