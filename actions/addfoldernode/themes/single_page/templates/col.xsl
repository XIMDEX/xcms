<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
    <xsl:template name="col" match="col">
        <article class="col-xs-12 col-sm-6 col-md-3 col-lg-3">
            <xsl:apply-templates/>
        </article>
    </xsl:template>
</xsl:stylesheet>