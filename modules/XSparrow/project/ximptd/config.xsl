<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
                 	<xsl:template name="config" match="config">
                 		<xsl:choose>
                 			<xsl:when test="//docxap[@tipo_documento]/@tipo_documento='rng-configuracion.xml'">
                                        <xsl:if test="//docxap[@transformer]/@transformer='xEDIT'">
                                          <div style="border:1px solid green; height:20px; text-align:center; clear:both; background-color:white; color:black;" uid="{@uid}">
                                          CONFIG
                                          </div>
                                        </xsl:if>
                                          <xsl:apply-templates select="config-header|config-container|config-footer"/>
                                        
                 			</xsl:when>
                 			<xsl:otherwise>                                   
                 				  <xsl:apply-templates select="config-header"/>			<!-- TODO -->
                 			</xsl:otherwise>
                 		</xsl:choose>
                 	</xsl:template>
</xsl:stylesheet>
