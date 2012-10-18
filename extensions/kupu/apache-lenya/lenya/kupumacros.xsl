<?xml version="1.0"?>

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
  xmlns:xhtml="http://www.w3.org/1999/xhtml" 
  xmlns:i18n="http://apache.org/cocoon/i18n/2.1" 
  exclude-result-prefixes="xhtml i18n" >
  
  <xsl:param name="document-path"/>
  <xsl:param name="contentfile"/>
  <xsl:param name="save-destination"/>
  <xsl:param name="exit-destination"/>
  <xsl:param name="reload-after-save" select="'1'"/>
  <xsl:param name="use-css" select="'1'"/>
  <xsl:param name="context-prefix" select="/"/>
  <xsl:param name="kupu-common-dir" 
    select="concat($context-prefix,'/kupu/common/')"/>
  <xsl:param name="kupu-logo" 
    select="concat($kupu-common-dir, 'kupuimages/kupu_icon.gif')"/>
  <xsl:param name="lenya-logo" 
    select="concat($context-prefix, '/lenya/images/project-logo-small.png')"/>
  <xsl:param name="imagedrawer-xsl-uri"/>
  <xsl:param name="image-libraries-uri"/>
  <xsl:param name="linkdrawer-xsl-uri"/>
  <xsl:param name="link-libraries-uri"/>
      
  <!--
    Kupu config
  -->
  <xsl:template match="xhtml:kupuconfig/xhtml:dst">
    <dst>
      <xsl:value-of select="$save-destination"/>
    </dst>
  </xsl:template>
  
  <xsl:template match="xhtml:kupuconfig/xhtml:reload_after_save">
    <reload_after_save>
      <xsl:value-of select="$reload-after-save"/>
    </reload_after_save>
  </xsl:template>
  
  <xsl:template match="xhtml:kupuconfig/xhtml:use_css">
    <use_css>
      <xsl:value-of select="$use-css"/>
    </use_css>
  </xsl:template>
  
  <xsl:template match="xhtml:kupuconfig/xhtml:exit_destination">
    <exit_destination>
      <xsl:value-of select="$exit-destination"/>
    </exit_destination>
  </xsl:template>
  
  <xsl:template match="xhtml:kupuconfig/xhtml:image_xsl_uri">
    <image_xsl_uri>
      <xsl:value-of select="$imagedrawer-xsl-uri"/>
    </image_xsl_uri>
  </xsl:template>
  
  <xsl:template match="xhtml:kupuconfig/xhtml:link_xsl_uri">
    <link_xsl_uri>
      <xsl:value-of select="$linkdrawer-xsl-uri"/>
    </link_xsl_uri>
  </xsl:template>
 
  <xsl:template match="xhtml:kupuconfig/xhtml:image_libraries_uri">
    <image_libraries_uri>
      <xsl:value-of select="$image-libraries-uri"/>
    </image_libraries_uri>
  </xsl:template>
  
  <xsl:template match="xhtml:kupuconfig/xhtml:link_libraries_uri">
    <link_libraries_uri>
      <xsl:value-of select="$link-libraries-uri"/>
    </link_libraries_uri>
  </xsl:template>
  
  <!-- 
    Use default tables classes from xmlconfig.kupu.
    Override if appropriate.
  -->
  <xsl:template match="xhtml:kupuconfig/xhtml:table_classes">
    <xsl:copy-of select="."/>
  </xsl:template>
  
    <xsl:template match="//xhtml:*[@id='kupu-editor']/@src">
    <xsl:attribute name="src">
      <xsl:value-of select="$contentfile"/>
    </xsl:attribute>
  </xsl:template>
  
  <!--
    Link rewriting.
    TODO: Take care of Lenya's link rewriting machanism.
  -->
  <xsl:template match="xhtml:link/@href">
    <xsl:attribute name="href">
      <xsl:value-of select="concat($kupu-common-dir, .)"/>
    </xsl:attribute>
  </xsl:template>
  
  <xsl:template match="xhtml:script/@src">
    <xsl:attribute name="src">
      <xsl:value-of select="concat($kupu-common-dir, .)"/>
    </xsl:attribute>
  </xsl:template>
  
  <!--
    Content stuff.
  -->
  <xsl:template match="xhtml:title">
    <title>Apache Lenya | Edit <xsl:value-of select="$document-path"/> with Kupu </title>
  </xsl:template>
  
  <xsl:template match="xhtml:h1[1]">
    <div style="float:left; width: 50%; margin-left: 5px;">
      <h1 style="margin: 0; padding: 0;">Edit document</h1>
      <span 
        style="font-style: italic; font-size: 1.3em; letter-spacing: 1px; color: gray;">
        <xsl:value-of select="$document-path"/>
      </span>
    </div>
    <div style="display: inline; float: right;">
      <a href="http://kupu.oscom.org/" target="_blank">
        <img src="{$kupu-logo}" style="vertical-align: top; border: 0;" 
          alt="Kupu logo"/>
      </a>
      <a href="http://lenya.apache.org" target="_blank">
        <img src="{$lenya-logo}" 
          alt="Lenya project logo" style="border: 0;"/>
      </a>
    </div>
    <br clear="all"/>    
  </xsl:template>
  
  <xsl:template match="@*|node()">
    <xsl:copy>
      <xsl:apply-templates select="@*|node()"/>
    </xsl:copy>
  </xsl:template>
  
</xsl:stylesheet>
