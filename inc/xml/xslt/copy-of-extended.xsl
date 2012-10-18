<?xml version="1.0" encoding="iso-8859-1" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:template name="copy-of-header">
	<xsl:call-template name="open-header"/>
	<xsl:call-template name="close-header"/>
</xsl:template>

<xsl:template name="copy-of-footer">
	<xsl:text disable-output-escaping="yes">&lt;</xsl:text>/<xsl:value-of select="local-name()" /><xsl:text disable-output-escaping="yes">&gt;</xsl:text>
</xsl:template>

<!-- copia la etiqueta de apertura del tag y todos sus atributos -->
<xsl:template name="open-header">
    <xsl:text disable-output-escaping="yes">&lt;</xsl:text><xsl:value-of select="local-name(.)" />
    <xsl:for-each select="@*">
	<xsl:text disable-output-escaping="yes"> </xsl:text><xsl:value-of select="local-name(.)" />=<xsl:text disable-output-escaping="yes">&quot;</xsl:text><xsl:value-of select="." /><xsl:text disable-output-escaping="yes">&quot;</xsl:text><xsl:text disable-output-escaping="yes"> </xsl:text>
    </xsl:for-each>
</xsl:template>

<!-- copia la etiqueta de cierre del tag -->
<xsl:template name="close-header">
	<xsl:text disable-output-escaping="yes">&gt;</xsl:text>
</xsl:template>

<!-- agrega al tag un nuevo atributo con un determinado valor -->
<xsl:template name="add-attribute">
    <xsl:param name="atributo" />
    <xsl:param name="valor" />
    <xsl:text disable-output-escaping="yes"> </xsl:text><xsl:value-of select="$atributo" />=<xsl:text disable-output-escaping="yes">&quot;</xsl:text><xsl:value-of select="$valor" /><xsl:text disable-output-escaping="yes">&quot;</xsl:text><xsl:text disable-output-escaping="yes"> </xsl:text>
</xsl:template>



</xsl:stylesheet>
