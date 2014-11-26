<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
    <xsl:template name="map" match="map">
        <iframe src="{@link}"
                frameborder="0" style="border:0"></iframe>
        <xsl:apply-templates/>
    </xsl:template>
</xsl:stylesheet>