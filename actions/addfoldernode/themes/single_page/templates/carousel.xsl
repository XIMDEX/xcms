<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
    <xsl:template name="carousel" match="carousel">

        <div id="{@uid}" class="carousel-section section-page">
            <xsl:choose>
                <xsl:when test="not(@container_type) or (@container_type = 'sin-container')">
                    <xsl:choose>
                        <xsl:when test="/docxap/@transformer = 'xEDIT'">
                            <xsl:apply-templates />
                        </xsl:when>

                        <xsl:otherwise>
                            <xsl:call-template name="carousel_HTML"/>
                        </xsl:otherwise>
                    </xsl:choose>
                </xsl:when>

                <xsl:otherwise>
                    <div class="{@container_type}">
                        <xsl:choose>
                            <xsl:when test="/docxap/@transformer = 'xEDIT'">
                                <xsl:apply-templates />
                            </xsl:when>

                            <xsl:otherwise>
                                <xsl:call-template name="carousel_HTML"/>
                            </xsl:otherwise>
                        </xsl:choose>
                    </div>
                </xsl:otherwise>
            </xsl:choose>
        </div>

    </xsl:template>
</xsl:stylesheet>