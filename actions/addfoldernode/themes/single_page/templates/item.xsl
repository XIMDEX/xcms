<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
    <xsl:template name="item" match="item">
        <xsl:param name="type"/>

        <xsl:variable name="active">
            <xsl:choose>
                <xsl:when test="position() = 1">
                    <xsl:value-of select="'active'"/>
                </xsl:when>
            </xsl:choose>
        </xsl:variable>

         <xsl:choose>
            <xsl:when test="/docxap/@transformer = 'xEDIT'">
                <img src="{@background}" alt="{@alt_text}"/>
                <xsl:apply-templates />
            </xsl:when>

            <xsl:otherwise>
                <xsl:choose>
                    <xsl:when test="not($type) or ($type = 'img')">
                        <div uid="{@uid}" class="item {$active}">
                            <img src="{@background}" class="img-responsive img-responsive-max" alt="{@alt_text}"/>
                            <xsl:apply-templates />
                        </div>
                    </xsl:when>

                    <xsl:otherwise>
                        <div uid="{@uid}" class="item {$active} responsive-center" style="background: url({@background}) 50% 50% no-repeat;">
                            <xsl:apply-templates />
                        </div>
                    </xsl:otherwise>
                </xsl:choose>
            </xsl:otherwise>
        </xsl:choose>

    </xsl:template>
</xsl:stylesheet>