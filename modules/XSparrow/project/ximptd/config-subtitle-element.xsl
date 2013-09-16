<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
 	<xsl:template name="config-subtitle-element" match="config-subtitle-element">
 		<xsl:choose>
 			<xsl:when test="//docxap[@tipo_documento]/@tipo_documento='rng-configuracion.xml'">
		 		<h3 uid="{@uid}">
        		   <xsl:apply-templates/>	
		        </h3>
		    </xsl:when>
        </xsl:choose>
 	</xsl:template>
</xsl:stylesheet>
