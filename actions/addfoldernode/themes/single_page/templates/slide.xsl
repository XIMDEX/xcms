<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
    <xsl:template name="slide" match="slide">
        <xsl:choose>
            <xsl:when test="@background != ''">
                <article class="item {@class} responsive-center"
                         style="background: url('@@@RMximdex.pathto({@background})@@@') 50% 50% no-repeat;">
                    <div class="carousel-caption">
                        <xsl:apply-templates/>
                    </div>
                </article>
            </xsl:when>
            <xsl:otherwise>
                <article class="item {@class} responsive-center"
                         style="background: url('@@@RMximdex.dotdot(images/carousel/{position() div 2}.jpg)@@@') 50% 50% no-repeat;">
                    <div class="carousel-caption">
                        <xsl:apply-templates/>
                    </div>
                </article>
            </xsl:otherwise>
        </xsl:choose>

    </xsl:template>
</xsl:stylesheet>