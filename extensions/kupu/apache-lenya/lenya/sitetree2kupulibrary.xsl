<?xml version="1.0" encoding="UTF-8" ?>

<!--
  Transform lenya Sitetree to Kupu's library format
  for usage in the link drawer.
  
  @version $Id: sitetree2kupulibrary.xsl 8771 2005-01-31 19:05:00Z gregor $
-->

<xsl:stylesheet version="1.0"
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns:dc="http://purl.org/dc/elements/1.1/"
  xmlns:li="http://apache.org/cocoon/lenya/info/1.0"
 >

<xsl:param name="resource-icon-url"/>
<xsl:param name="resource-path-url"/>

<xsl:template match="/">
  <collection>
    <uri>FIXME URI</uri>
    <icon></icon>
    <title>Page Image Library</title>
    <description>Images related to page or document.</description>
    <items>
      <xsl:apply-templates select="//li:asset"/>
    </items>
  </collection>
</xsl:template>

<xsl:template match="li:asset">
  <xsl:variable name="resource-url">
    <xsl:value-of select="concat($resource-path-url, dc:source)"/>
  </xsl:variable> 
  
  <resource id="{$resource-url}">
    <title><xsl:value-of select="dc:source"/></title>
    <uri><xsl:value-of select="$resource-url"/></uri>
    <icon><xsl:value-of select="$resource-icon-url"/></icon>
    <description><xsl:value-of select="dc:title"/></description>
    <preview><xsl:value-of select="$resource-url"/></preview>
    <size><xsl:value-of select="dc:extent"/>&#160;kb</size>
  </resource>
</xsl:template>

<xsl:template match="@*|node()">
  <xsl:copy>
    <xsl:apply-templates select="@*|node()"/>
  </xsl:copy>
</xsl:template> 

</xsl:stylesheet>
