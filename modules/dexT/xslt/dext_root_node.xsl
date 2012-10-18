<?xml version="1.0" encoding="utf-8" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<!--
	dexT to XSLT translation: replace dext root node by xsl:stylesheet + its match template
-->

<xsl:template name="root_node">
	<xsl:element name="xsl:stylesheet">	 
		<xsl:attribute name="version">1.0</xsl:attribute>

		<xsl:if test="local-name(.)='docxap'">
			<xsl:element name="xsl:param">
				<xsl:attribute name="name">xmlcontent</xsl:attribute>
			</xsl:element>

			<xsl:element name="xsl:include">
				<xsl:attribute name="href">
					<xsl:value-of select="concat($ptds_path, 'templates_include.xsl')"/>
				</xsl:attribute>
			</xsl:element>
		</xsl:if>

		<xsl:element name="xsl:template">			
			<xsl:attribute name="name"><xsl:value-of select="local-name(.)"/></xsl:attribute>
			<xsl:attribute name="match"><xsl:value-of select="local-name(.)"/></xsl:attribute>

			<xsl:call-template name="dext_variables"/>
<!--
			<xsl:call-template name="default_variables"/>
			<xsl:call-template name="dext_variables">
				<xsl:with-param name="dext_vars" select="$vars_list"/>
			</xsl:call-template>
-->
			<xsl:apply-templates/>
		</xsl:element>
	</xsl:element>
</xsl:template>

</xsl:stylesheet>
