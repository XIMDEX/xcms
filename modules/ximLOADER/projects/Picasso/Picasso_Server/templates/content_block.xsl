<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
<xsl:key name="tagkey" match="//tag" use="./text()"/>
<xsl:template name="content_block" match="content_block">
<div class="content" uid="{@uid}">
<xsl:apply-templates/>
</div>


<div class="tags">
<ul>
<xsl:for-each select="//tag[generate-id(.) = generate-id(key('tagkey', .))]">
<xsl:sort select="."/>
<li>
<xsl:choose>
<xsl:when test="@url!=''">
       <a href="{@url}" rel="{@type}"> <xsl:value-of select="."/> </a>
</xsl:when>
<xsl:otherwise>
      <span rel="{@type}"> <xsl:value-of select="."/> </span>
</xsl:otherwise>
</xsl:choose>
 </li>
</xsl:for-each>
</ul>
</div>

</xsl:template>
</xsl:stylesheet>
