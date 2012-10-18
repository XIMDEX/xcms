<?xml version="1.0" encoding="UTF-8" ?>

<!--
  Transforms page to be edited by Kupu wysiwyg xhtml editor.
  Here the link to css etc. is inserted and marked(see lenyacontent attribute) 
  to be remved when saved.
-->

<xsl:stylesheet version="1.0"
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns:lenya="http://apache.org/cocoon/lenya/page-envelope/1.0"
  xmlns:xhtml="http://www.w3.org/1999/xhtml"
    xmlns:dc="http://purl.org/dc/elements/1.1/"
  xmlns="http://www.w3.org/1999/xhtml"
 >

<xsl:param name="css"/>
<xsl:param name="nodeid"/>

<xsl:template match="lenya:meta"/>

<xsl:template match="xhtml:head">
  <head>
    <xsl:apply-templates/> 
    <link rel="stylesheet" href="{$css}" mime-type="text/css" />
    <!-- 
      Fix for IE: Special characters e.g. german umlauts are displayed correct in the document being edited.
      NOTE: This should normally be done by the serialization, 
      but it doen't work in case of usecase=kupu step=content.
    -->
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
  </head>
</xsl:template>

   <!-- this template converts the object tag to img (for compatiblity with older browsers 
    for more, see http://www.xml.com/pub/a/2003/07/02/dive.html -->
   <xsl:template name="object2img">
      <img border="0">
        <xsl:attribute name="src">
          <xsl:choose>
            <xsl:when test="not(starts-with(@data, '/'))">
              <xsl:value-of select="$nodeid"/>/<xsl:value-of select="@data"/>
            </xsl:when>
            <xsl:otherwise>            
              <xsl:value-of select="@data"/>
            </xsl:otherwise>
          </xsl:choose>
        </xsl:attribute>
        <!-- use the rarely-used ismap to roundtrip the type attribute for the object element -->
        <xsl:attribute name="ismap">
          <xsl:value-of select="@type"/>
        </xsl:attribute>
        <xsl:attribute name="alt">
          <!-- the overwritten title (stored in @name) has precedence over dc:title -->
          <xsl:choose>
            <xsl:when test="@name != ''">
              <xsl:value-of select="@name"/>
            </xsl:when>
            <xsl:otherwise>
              <xsl:value-of select="dc:metadata/dc:title"/>                    
            </xsl:otherwise>
            </xsl:choose>
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
      </img>
   </xsl:template>
  
  <xsl:template match="xhtml:object" priority="3">
    <xsl:choose>
      <xsl:when test="@href != ''">
        <a href="{@href}">
          <xsl:call-template name="object2img"/>
        </a>
      </xsl:when>
      <xsl:when test="@type = 'application/x-shockwave-flash'">
        <object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000">
            <param name="movie" value="{$nodeid}/{@data}"/>
        </object>
      </xsl:when>
      <xsl:otherwise>
        <xsl:call-template name="object2img"/>
      </xsl:otherwise>
    </xsl:choose>
  </xsl:template>  

<xsl:template match="@*|node()">
  <xsl:copy>
    <xsl:apply-templates select="@*|node()"/>
  </xsl:copy>
</xsl:template> 

</xsl:stylesheet>
