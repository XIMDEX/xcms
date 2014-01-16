<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
<xsl:template name="abstract" match="abstract"> 


	<xsl:if test="(ancestor-or-self::*[@tipo_documento][1]/@tipo_documento = 'rng-page.xml')">
		<p uid="{@uid}"><xsl:value-of select="."/></p>
	</xsl:if>

	<xsl:if test="(ancestor-or-self::*[@tipo_documento][1]/@tipo_documento = 'rng-post.xml')">
		<p uid="{@uid}" class="bold_abstract"><xsl:value-of select="."/></p>
	</xsl:if>
	
</xsl:template>
</xsl:stylesheet>
