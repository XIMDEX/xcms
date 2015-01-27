<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
    <xsl:template name="carousel_indicators" match="carousel_indicators">
        <xsl:variable name="uid" select="@uid" />

        <ol class="carousel-indicators">
            <xsl:for-each select="item">
                <xsl:variable name="index" select="position()"/>

                <xsl:variable name="active">
                    <xsl:choose>
                        <xsl:when test="position() = 1">
                            <xsl:value-of select="'active'"/>
                        </xsl:when>
                    </xsl:choose>
                </xsl:variable>

                <li data-target="#carousel-{$uid}" data-slide-to="{$index}" class="{$active}"></li>
            </xsl:for-each>
        </ol>

    </xsl:template>
</xsl:stylesheet>