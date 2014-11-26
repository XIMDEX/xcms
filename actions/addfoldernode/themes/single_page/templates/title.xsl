<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
    <xsl:template name="title" match="title">
        <xsl:choose>
            <xsl:when test="@type = 1">
                <h1 uid="{@uid}" >
                    <xsl:apply-templates/>
                </h1>
            </xsl:when>
            <xsl:when test="@type = 2">
                <header>
                    <h2 uid="{@uid}" ><xsl:apply-templates/></h2>
                </header>
            </xsl:when>
            <xsl:otherwise>
                <header>
                    <h3 uid="{@uid}" ><xsl:apply-templates/></h3>
                </header>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>
</xsl:stylesheet>
