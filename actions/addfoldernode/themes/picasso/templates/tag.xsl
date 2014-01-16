<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
<xsl:template name="tag" match="tag">

<xsl:choose>
<xsl:when test='@type="people"'>
<span uid="{@uid}" about="foaf:Person" property="foaf:name" class="enriched" title="Enriched Content [tag:Person]"><xsl:apply-templates/></span>
</xsl:when>
<xsl:when test='@type="organizations"'>
<span uid="{@uid}" about="foaf:Organization" property="foaf:name" class="enriched" title="Enriched Content [tag:Organization]"><xsl:apply-templates/></span>
</xsl:when>
<xsl:when test='@type="places"'>
<span uid="{@uid}" about="foaf:Place" property="foaf:name" class="enriched" title="Enriched Content [tag:Place]"><xsl:apply-templates/></span>
</xsl:when>
<xsl:otherwise>
<span uid="{@uid}">
<xsl:apply-templates/>
</span>
</xsl:otherwise>
</xsl:choose>
</xsl:template>
</xsl:stylesheet>
