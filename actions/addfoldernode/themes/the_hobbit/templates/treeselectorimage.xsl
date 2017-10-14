<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
<xsl:template name="treeselectorimage" match="treeselectorimage"> 

    <img uid="{@uid}" class="{@class}" src="{@url}" width="{@width}" height="{@height}" alt="{@alt_text}"/>

</xsl:template>
</xsl:stylesheet>
