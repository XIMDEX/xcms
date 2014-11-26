<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
    <xsl:template name="carousel" match="carousel">
        <div id="carousel-section-2" class="carousel slide" data-ride="carousel" data-interval="false">
            <div class="carousel-inner" role="listbox">
                <xsl:apply-templates/>
            </div>
            <a class="left carousel-control" href="#carousel-section-2" role="button" data-slide="prev">
                <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
                <span class="sr-only"><xsl:attribute name="previous" /></span>
            </a>

            <a class="right carousel-control" href="#carousel-section-2" role="button" data-slide="next">
                <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
                <span class="sr-only"><xsl:attribute name="next" /></span>
            </a>
        </div>
    </xsl:template>
</xsl:stylesheet>