<?xml version="1.0" encoding="utf-8" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<!--
	dexT to XSLT translation: dext inclusion with xsl:call-template or xsl:apply-templates
-->

<xsl:template name="calltemplate">

		<xsl:choose>
			<xsl:when test="@dinamic">
				<xsl:element name="xsl:call-template">
					<xsl:attribute name="name">dinamic_caller</xsl:attribute>
					<xsl:element name="xsl:with-param">
						<xsl:attribute name="name">template_name</xsl:attribute>
						<xsl:attribute name="select">'<xsl:value-of select="@dinamic" />'</xsl:attribute>
					</xsl:element>
				</xsl:element>
			</xsl:when>
			<xsl:otherwise>
				<xsl:variable name="calling">
					<xsl:call-template name="string_replace">
						<xsl:with-param name="string" select="@expr"/>
						<xsl:with-param name="search" select="':'"/>
						<xsl:with-param name="replacement" select="'/'"/>
					</xsl:call-template>
				</xsl:variable>

				<xsl:element name="xsl:call-template">
					<xsl:attribute name="name"><xsl:value-of select="$calling"/></xsl:attribute>
				</xsl:element>
			</xsl:otherwise>
		</xsl:choose>

	</xsl:template>

<xsl:template name="applytemplates">
	<xsl:variable name="calling">
		<xsl:call-template name="string_replace">
			<xsl:with-param name="string" select="@expr"/>
			<xsl:with-param name="search" select="':'"/>
			<xsl:with-param name="replacement" select="'/'"/>
		</xsl:call-template>
	</xsl:variable>

	<xsl:element name="xsl:apply-templates">
		<xsl:attribute name="select"><xsl:value-of select="$calling"/></xsl:attribute>
	</xsl:element>
</xsl:template>

<xsl:template name="call_template_dinamically">
<xsl:element name="xsl:apply-templates" />
</xsl:template>

</xsl:stylesheet>
