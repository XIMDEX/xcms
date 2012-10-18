<?xml version="1.0" encoding="utf-8" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<!--
	dexT to XSLT translation: dext:import
-->

<xsl:template name="import">
<xsl:apply-templates />
<!--
	<xsl:variable name="pattern"><xsl:value-of select="substring-before(@replace,'{@')"/></xsl:variable>
	<xsl:variable name="attribute_pattern"><xsl:value-of select="substring-before(@replace,'=')"/></xsl:variable>

	<xsl:element name="xsl:variable">
		<xsl:attribute name="name">replacement</xsl:attribute>
		<xsl:element name="xsl:value-of">
			<xsl:attribute name="select">
				<xsl:value-of select="concat('/docxap/@',substring-before(substring-after(@replace,'{@'),'}'))"/>
			</xsl:attribute>
		</xsl:element>
	</xsl:element>

	<xsl:element name="xsl:variable">
		<xsl:attribute name="name">file_import</xsl:attribute>
		<xsl:element name="xsl:value-of">
			<xsl:attribute name="select">
				<xsl:value-of select="@file"/>
			</xsl:attribute>
		</xsl:element>
	</xsl:element>
-->

<!--	<xsl:text disable-output-escaping="yes">&lt;xsl:copy-of xmlns:php="http://php.net/xsl" select="php:function('dext_import', '</xsl:text><xsl:value-of select="$attribute_pattern"/><xsl:text disable-output-escaping="yes">', $replacement, $file_import)"/&gt;</xsl:text>-->

</xsl:template>

</xsl:stylesheet>
