<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
 	<xsl:template name="config-title-element" match="config-title-element">
 		<xsl:choose>
 			<xsl:when test="//docxap[@tipo_documento]/@tipo_documento='rng-configuracion.xml'">
		 		<h2 uid="{@uid}">
        		   <xsl:apply-templates/>	
		        </h2>
		    </xsl:when>
        </xsl:choose>
 	</xsl:template>
</xsl:stylesheet>
