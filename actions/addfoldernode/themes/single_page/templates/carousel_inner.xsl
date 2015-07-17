<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
    <xsl:template name="carousel_inner" match="carousel_inner">

        <div class="carousel-inner" role="listbox">
            <img class="cargando" src="@@@RMximdex.dotdot(images/cargando-2.gif)@@@" alt="Cargando"/>

            <xsl:apply-templates>
                <xsl:with-param name="type" select="@type" />
            </xsl:apply-templates>
        </div>

    </xsl:template>
</xsl:stylesheet>