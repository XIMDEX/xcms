<?xml version="1.0" encoding="UTF-8" ?>

<!--
  Merges lenya:meta into xhtml send by kupu for save.
  We also remove some <link>s here i.e. for css and rel.
-->

<xsl:stylesheet version="1.0"
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns:lenya="http://apache.org/cocoon/lenya/page-envelope/1.0"
  xmlns:dc="http://purl.org/dc/elements/1.1/"
  xmlns:dcterms="http://purl.org/dc/terms/"
  xmlns="http://www.w3.org/1999/xhtml"
  xmlns:xhtml="http://www.w3.org/1999/xhtml"
 >

<xsl:template match="edit-envelope">
  <!-- 
       FIXME: The _keepmes_ are needed for our incredible from(s) editor :) 
       I hope we can remove this someday. 
  -->
  <html dc:dummy="FIXME:keepNamespace" dcterms:dummy="FIXME:keepNamespace" lenya:dummy="FIXME:keepNamespace" xhtml:dummy="FIXME:keepNamespace">
    <xsl:copy-of select="original/xhtml:html/lenya:meta"/>
    <head>
      <xsl:apply-templates select="edited/xhtml:html/xhtml:head/xhtml:title"/>
      <meta http-equiv="Content-Type" content="text/html;charset=utf-8"/>    
    </head>
    <xsl:apply-templates select="edited/xhtml:html/xhtml:body"/>
  </html>
</xsl:template>

<xsl:template match="xhtml:link"/>

   <!-- this template converts the img tag to object 
    for more, see http://www.xml.com/pub/a/2003/07/02/dive.html -->
   <xsl:template match="xhtml:img">
      <object>
        <xsl:attribute name="data">
          <!-- strip the nodeid out again (it is not saved in the object @data) -->
          <xsl:choose>
            <xsl:when test="starts-with(@src, '/')">
              <xsl:value-of select="@src"/>              
            </xsl:when>
            <xsl:otherwise>
              <xsl:value-of select="substring-after(@src, '/')"/>
            </xsl:otherwise>
          </xsl:choose>
        </xsl:attribute>
        <xsl:attribute name="title">
          <xsl:value-of select="@alt"/>
        </xsl:attribute>
        <!-- use the rarely-used ismap to roundtrip the type attribute for the object element -->
        <xsl:attribute name="type">
          <xsl:value-of select="@ismap"/>
        </xsl:attribute>
         <xsl:if test="string(@height)">
          <xsl:attribute name="height">
            <xsl:value-of select="@height"/>
          </xsl:attribute>
        </xsl:if> 
        <xsl:if test="string(@width)">
          <xsl:attribute name="width">
            <xsl:value-of select="@width"/>
          </xsl:attribute>
        </xsl:if>         
      </object>
   </xsl:template>

    <!-- convert to semantic markup -->
   <xsl:template match="xhtml:b">
    <strong>
      <xsl:apply-templates/>
    </strong>
   </xsl:template>

    <!-- convert to semantic markup -->
   <xsl:template match="xhtml:i">
    <em>
      <xsl:apply-templates/>
    </em>
   </xsl:template>

    <!-- ignore these -->
   <xsl:template match="@shape"/>
   <xsl:template match="@align"/>
   <xsl:template match="@name"/>
   <xsl:template match="@type"/>
   <xsl:template match="@style"/>
   <xsl:template match="@start"/>
   <xsl:template match="@clear"/>

    <!-- kupu seems to use those for hidden anchors, but does not get the nesting right. -->
   <xsl:template match="a[@href = '']"/>
  
  <xsl:template match="@*|node()">
  <xsl:copy>
    <xsl:apply-templates select="@*|node()"/>
  </xsl:copy>
</xsl:template> 

</xsl:stylesheet>
