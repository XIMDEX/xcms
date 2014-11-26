<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
    <xsl:template name="section" match="section">
        <section uid="{@uid}" id="section-{position() div 2}" class="section-page {@class}">
            <xsl:apply-templates/>
        </section>
    </xsl:template>
</xsl:stylesheet>