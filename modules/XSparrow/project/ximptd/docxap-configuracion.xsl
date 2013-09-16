<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
                     	<xsl:template name="docxap-configuracion" match="docxap-configuracion">
                            	<xsl:text disable-output-escaping="yes">
                              	<![CDATA[<!DOCTYPE html>]]>
               		</xsl:text>
                		<html lang="en">
                                  <head>
                                    <xsl:call-template name="INCLUDE-css"/>
                                    <xsl:call-template name="INCLUDE-style"/>
                                  </head>
                                  <body>
                                    <xsl:choose>
                                      <xsl:when test="//docxap[@tipo_documento]/@tipo_documento='rng-configuracion.xml'">                                   	
                                           <xsl:apply-templates select="//config"/>                                      
                                      </xsl:when>
                                      <xsl:when test="//docxap[@tipo_documento]/@tipo_documento='rng-ximlet-bootstrap-menu.xml'">                                   	
                                           <xsl:apply-templates select="//config"/>                                      
                                      </xsl:when>
                                      <xsl:when test="//docxap[@tipo_documento]/@tipo_documento='rng-bootstrap-new.xml'">
                                        		<xsl:apply-templates select="//menu/.."/>
                                              <xsl:apply-templates select="//config/.."/>
                                              <xsl:apply-templates select="//new"/>
                                      </xsl:when>
                                      <xsl:otherwise>
                                        	<xsl:apply-templates select="//menu/.."/>
                                              <xsl:apply-templates select="//config/.."/>
                                        <xsl:apply-templates select="//config/config-container"/>
                                        <xsl:apply-templates select="//footer/.."/>
                                      </xsl:otherwise>
                                    </xsl:choose>
                                  
                                  </body>
                          	</html>
                          	
                  	</xsl:template>
</xsl:stylesheet>
