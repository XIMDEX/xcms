<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
  	<xsl:template name="config-link-element" match="config-link-element">
  		<xsl:choose>
  			<xsl:when test="//docxap[@tipo_documento]/@tipo_documento='rng-configuracion.xml'">
 		 		<a class="normal-link" href="http://www.ximdex.com" uid="{@uid}" title="sample link">
         		   <xsl:apply-templates/>	
 		        </a>
 		    </xsl:when>
         </xsl:choose>
  	</xsl:template>
</xsl:stylesheet>
