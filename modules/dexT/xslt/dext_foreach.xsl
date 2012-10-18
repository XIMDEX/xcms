<?xml version="1.0" encoding="utf-8" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<!--
	dexT to XSLT translation: from  dext:foreach to xsl:foreach
-->

<xsl:template name="foreach">	
	<xsl:variable name="loop">
		<xsl:call-template name="string_replace">
			<xsl:with-param name="string" select="@expr"/>
			<xsl:with-param name="search" select="':'"/>
			<xsl:with-param name="replacement" select="'/'"/>
		</xsl:call-template>
	</xsl:variable>

	<xsl:element name="xsl:for-each" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
		<xsl:attribute name="select"><xsl:value-of select="$loop"/></xsl:attribute>
		<xsl:apply-templates/>
	</xsl:element>
</xsl:template>

</xsl:stylesheet>
