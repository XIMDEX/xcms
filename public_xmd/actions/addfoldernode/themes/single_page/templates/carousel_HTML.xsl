<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
    <xsl:template name="carousel_HTML" match="carousel_HTML">

        <xsl:variable name="direction">
            <xsl:choose>
                <xsl:when test="not(@direction) or (@direction = 'horizontal')">
                    <xsl:value-of select="''"/>
                </xsl:when>

                <xsl:otherwise>
                    <xsl:value-of select="'carousel-vertical'"/>
                </xsl:otherwise>
            </xsl:choose>
        </xsl:variable>

        <div id="carousel-{@uid}" class="carousel slide carousel-fade {$direction}" data-ride="carousel" data-interval="{@interval}">
            <xsl:call-template name="carousel_indicators"/>
            <xsl:call-template name="carousel_inner"/>
        </div>

    </xsl:template>
</xsl:stylesheet>