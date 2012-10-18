<?xml version="1.0" encoding="UTF-8" ?>

<!--
  Transform lenya info assets about a pages resources
  to Kupu's library format.
  
  @version $Id: pageassets2kupulibrary.xsl 9552 2005-03-01 16:51:51Z gregor $
-->

<xsl:stylesheet version="1.0"
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns:dc="http://purl.org/dc/elements/1.1/"
  xmlns:li="http://apache.org/cocoon/lenya/info/1.0"
 >

<xsl:param name="iconUrl"/>
<xsl:param name="nodeid"/>

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
  <xsl:if test="(contains(dc:source, '.jpg') or contains(dc:source, '.gif') or contains(dc:source, '.png') or contains(dc:source, '.swf'))">
    <xsl:variable name="resource-url">
      <xsl:value-of select="concat(concat($nodeid, '/'), dc:source)"/>
    </xsl:variable> 
    
    <resource id="{$resource-url}">
      <title><xsl:value-of select="dc:source"/></title>
      <uri><xsl:value-of select="$resource-url"/></uri>
      <icon><xsl:value-of select="$iconUrl"/></icon>
      <description><xsl:value-of select="dc:title"/></description>
      <preview><xsl:value-of select="$resource-url"/></preview>
      <size><xsl:value-of select="dc:extent"/>&#160;kb</size>
    </resource>
  </xsl:if>
</xsl:template>

<xsl:template match="@*|node()">
  <xsl:copy>
    <xsl:apply-templates select="@*|node()"/>
  </xsl:copy>
</xsl:template> 

</xsl:stylesheet>
