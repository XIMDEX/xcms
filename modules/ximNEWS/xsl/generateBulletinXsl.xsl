<?xml version="1.0" encoding="utf-8" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:output method="xml" encoding="utf-8" indent="no"/>

<xsl:template match="/*[1]">
	<xsl:text disable-output-escaping = "yes">
	&lt;xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"&gt;
	&lt;xsl:output method="xml" encoding="utf-8" indent="no"/&gt;
	</xsl:text>

	<xsl:for-each select="//*[@boletin='yes']">
		<xsl:call-template name="insert_params"/>
	</xsl:for-each>

	<xsl:text disable-output-escaping = "yes">
	&lt;xsl:template match="/"&gt;
	</xsl:text>

	<xsl:apply-templates select="*"/>

	<xsl:text disable-output-escaping = "yes">
	&lt;/xsl:template&gt;
	&lt;/xsl:stylesheet&gt;
	</xsl:text>
</xsl:template>

<xsl:template match="*">
	<xsl:element name="{local-name(.)}">
		<xsl:if test="@boletin='yes'">
			<xsl:apply-templates select="@*[local-name(.)!='name']"/>
		</xsl:if>
		<xsl:apply-templates select="@name"/>
		<xsl:apply-templates select="*"/>
		<xsl:apply-templates select="text()"/>
	</xsl:element>
</xsl:template>

<xsl:template match="@*[local-name(.)!='name']">

	<xsl:choose>
		<xsl:when test="local-name(.)!='id' and local-name(.)!='type' and local-name(.)!='boletin' and local-name(.)!='label'">
			<xsl:attribute name="{local-name(.)}">
				<xsl:text disable-output-escaping = "yes">{$</xsl:text>
					<xsl:value-of select="local-name(.)"/>
				<xsl:text disable-output-escaping = "yes">}</xsl:text>
			</xsl:attribute>
		</xsl:when>
		<xsl:when test="local-name(.)='name' and not(string-length(.)>0)">
			<xsl:text disable-output-escaping = "yes">&lt;xsl:value-of select="$</xsl:text>
				<xsl:value-of select="."/>
			<xsl:text disable-output-escaping = "yes">"/&gt;</xsl:text>
		</xsl:when>
		<xsl:otherwise>
			<xsl:attribute name="{local-name(.)}">
				<xsl:value-of select="."/>
			</xsl:attribute>
		</xsl:otherwise>
	</xsl:choose>
	
</xsl:template>

<xsl:template match="@name">

<xsl:choose>
	<xsl:when test="not(string(.))">
		<xsl:attribute name="{local-name(.)}">
			<xsl:text disable-output-escaping = "yes">{$</xsl:text>
				<xsl:value-of select="local-name(.)"/>
			<xsl:text disable-output-escaping = "yes">}</xsl:text>
		</xsl:attribute>
	</xsl:when>
	<xsl:otherwise>
		<xsl:attribute name="{local-name(.)}">
			<xsl:value-of select="."/>
		</xsl:attribute>

		<xsl:text disable-output-escaping = "yes">&lt;xsl:value-of select="$</xsl:text>
			<xsl:value-of select="."/>
		<xsl:text disable-output-escaping = "yes">"/&gt;</xsl:text>
	</xsl:otherwise>
	</xsl:choose>
	
</xsl:template>

<xsl:template match="text()">
	<xsl:if test="not(contains(text(),'['))">
		<xsl:value-of select="text()"/>
	</xsl:if>
</xsl:template>

<xsl:template name="insert_params">
    <xsl:choose>
		<xsl:when test="@type='text'">
			<xsl:text disable-output-escaping = "yes">&lt;xsl:param name="</xsl:text>
			<xsl:value-of select="@name"/>
			<xsl:text disable-output-escaping = "yes">"/&gt;</xsl:text>
		</xsl:when>
		<xsl:when test="@type='textarea'">
			<xsl:text disable-output-escaping = "yes">&lt;xsl:param name="</xsl:text>
			<xsl:value-of select="@name"/>
			<xsl:text disable-output-escaping = "yes">"/&gt;</xsl:text>
		</xsl:when>
		<xsl:when test="@type='attribute'">
			<xsl:for-each select="@*">
			<xsl:if test="local-name(.)!='type' and local-name(.)!='boletin'">
				<xsl:text disable-output-escaping = "yes">&lt;xsl:param name="</xsl:text>
				<xsl:value-of select="local-name(.)"/>
				<xsl:text disable-output-escaping = "yes">"/&gt;</xsl:text>
			</xsl:if>
			</xsl:for-each>
		</xsl:when>
		<xsl:otherwise>
		</xsl:otherwise>
    </xsl:choose>
</xsl:template>

</xsl:stylesheet>
