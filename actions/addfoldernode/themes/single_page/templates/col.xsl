<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
    <xsl:template name="col" match="col">

    	<!-- xs -->
      <xsl:variable name="xs">
      	<xsl:choose>
        	<xsl:when test="not(@xs) or (@xs = '')">
        		<xsl:value-of select="'12'"/>
        	</xsl:when>

        	<xsl:otherwise>
        			<xsl:value-of select="@xs"/>
        	</xsl:otherwise>
      	</xsl:choose>
     	</xsl:variable>

     	<!-- sm -->
      <xsl:variable name="sm">
      	<xsl:choose>
        	<xsl:when test="not(@sm) or (@sm = '')">
        		<xsl:value-of select="'12'"/>
        	</xsl:when>

        	<xsl:otherwise>
          		<xsl:value-of select="@sm"/>
        	</xsl:otherwise>
      	</xsl:choose>
     	</xsl:variable>

     	<!-- md -->
      <xsl:variable name="md">
      	<xsl:choose>
        	<xsl:when test="not(@md) or (@md = '')">
        		<xsl:value-of select="'12'"/>
        	</xsl:when>

        	<xsl:otherwise>
          		<xsl:value-of select="@md"/>
        	</xsl:otherwise>
      	</xsl:choose>
     	</xsl:variable>

     	<!-- lg -->
      <xsl:variable name="lg">
      	<xsl:choose>
        	<xsl:when test="not(@lg) or (@lg = '')">
        		<xsl:value-of select="'12'"/>
        	</xsl:when>

        	<xsl:otherwise>
          		<xsl:value-of select="@lg"/>
        	</xsl:otherwise>
      	</xsl:choose>
     	</xsl:variable>

      <div class="col-xs-{$xs} col-sm-{$sm} col-md-{$md} col-lg-{$lg}">
        <xsl:apply-templates/>
      </div>
    </xsl:template>
</xsl:stylesheet>