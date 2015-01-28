<?xml version="1.0" encoding="utf-8" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output method="html" encoding="utf-8" indent="no"/>
<xsl:include href="@@@XIMDEX_ROOT_PATH@@@/lib/ximdex/_files/xslt/copy-of-extended.xsl"/>

<xsl:template match="cuerpo_noticia">
    <xsl:call-template name="add-cuerpo_noticia-attributes"/>
</xsl:template>

<xsl:template match="noticia">
    <xsl:call-template name="add-bulletin-attributes"/>
</xsl:template>

<xsl:template match="*">
<xsl:call-template name="copy-of-header"/>
<xsl:apply-templates/>
<xsl:call-template name="copy-of-footer"/>
</xsl:template>

<xsl:template name="add-bulletin-attributes">
    <xsl:call-template name="open-header"/>

    <!-- Adding the attribute 'boletin' if it does not exist --> 
    <xsl:choose>
	<xsl:when test="@boletin">
	</xsl:when>
	<xsl:otherwise>
	    <xsl:call-template name="add-attribute">
		<xsl:with-param name="atributo"><xsl:value-of select="'boletin'"/></xsl:with-param>
		<xsl:with-param name="valor"><xsl:value-of select="'yes'"/></xsl:with-param>
	    </xsl:call-template>
	</xsl:otherwise>
    </xsl:choose>

    <!-- Adding the attribute 'attribute' if it does not exist --> 
    <xsl:choose>
	<xsl:when test="@type">
	</xsl:when>
	<xsl:otherwise>
	    <xsl:call-template name="add-attribute">
		<xsl:with-param name="atributo"><xsl:value-of select="'type'"/></xsl:with-param>
		<xsl:with-param name="valor"><xsl:value-of select="'attribute'"/></xsl:with-param>
	    </xsl:call-template>
	</xsl:otherwise>
    </xsl:choose>

    <xsl:call-template name="close-header"/>
    <xsl:apply-templates/>

    <xsl:call-template name="copy-of-footer"/>
</xsl:template>

<xsl:template name="add-cuerpo_noticia-attributes">
    <xsl:call-template name="open-header"/>

    <!-- Adding the attribute 'boletin' if it does not exist --> 
    <xsl:choose>
        <xsl:when test="@boletin">
        </xsl:when>
        <xsl:otherwise>
            <xsl:call-template name="add-attribute">
                <xsl:with-param name="atributo"><xsl:value-of select="'boletin'"/></xsl:with-param>
                <xsl:with-param name="valor"><xsl:value-of select="'yes'"/></xsl:with-param>
            </xsl:call-template>
        </xsl:otherwise>
    </xsl:choose>

    <!-- Adding the attribute 'attribute' if it does not exist --> 
    <xsl:choose>
        <xsl:when test="@type">
        </xsl:when>
        <xsl:otherwise>
            <xsl:call-template name="add-attribute">
                <xsl:with-param name="atributo"><xsl:value-of select="'type'"/></xsl:with-param>
                <xsl:with-param name="valor"><xsl:value-of select="'attribute'"/></xsl:with-param>
            </xsl:call-template>
        </xsl:otherwise>
    </xsl:choose>

    <!-- Adding the attribute 'nodeid' if it does not exist --> 
    <xsl:choose>
        <xsl:when test="@nodeid">
        </xsl:when>
        <xsl:otherwise>
            <xsl:call-template name="add-attribute">
                <xsl:with-param name="atributo"><xsl:value-of select="'nodeid'"/></xsl:with-param>
                <xsl:with-param name="valor"><xsl:value-of select="'@@@NODEID@@@'"/></xsl:with-param>
            </xsl:call-template>
        </xsl:otherwise>
    </xsl:choose>

    <!-- Adding the attribute 'name' if it does not exist --> 
    <xsl:choose>
        <xsl:when test="@name">
        </xsl:when>
        <xsl:otherwise>
            <xsl:call-template name="add-attribute">
                <xsl:with-param name="atributo"><xsl:value-of select="'name'"/></xsl:with-param>
                <xsl:with-param name="valor"><xsl:value-of select="'@@@NAME@@@'"/></xsl:with-param>
            </xsl:call-template>
        </xsl:otherwise>
    </xsl:choose>

    <xsl:call-template name="close-header"/>
    <xsl:apply-templates/>

    <xsl:call-template name="copy-of-footer"/>
</xsl:template>


</xsl:stylesheet>

