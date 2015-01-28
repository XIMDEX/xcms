<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
    <xsl:param name="xmlcontent"/>
    <xsl:include href="http://localhost/ximdex/data/nodes/singlepagetheme/templates/templates_include.xsl"/>
    <xsl:template name="docxap" match="docxap">
        <html lang="{@language}">
            <head>
                <title><xsl:value-of select="/docxap/top_block/header_block/title"/></title>

                <xsl:call-template name="INCLUDE_metas"/>
                <xsl:call-template name="INCLUDE_styles"/>
            </head>

            <body data-spy="scroll" data-target="#navbar" data-offset="{@offset}">
                <xsl:apply-templates />
                <xsl:call-template name="INCLUDE_js"/>
            </body>
        </html>
    </xsl:template>
</xsl:stylesheet>