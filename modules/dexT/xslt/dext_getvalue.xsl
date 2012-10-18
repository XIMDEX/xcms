<?xml version="1.0" encoding="utf-8" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<!--
	dexT to XSLT translation: replacing dext variables path by xsl:value-of 
-->

<xsl:template name="getvalue">
	<xsl:element name="xsl:value-of" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
		<xsl:attribute name="select"><xsl:value-of select="@expr"/></xsl:attribute>
		<xsl:apply-templates/>
	</xsl:element>
</xsl:template>

</xsl:stylesheet>
