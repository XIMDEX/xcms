<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
                   	<xsl:template name="config-header" match="config-header">
                   		<xsl:choose>
                   			<xsl:when test="@image and @image != '' ">
                                           <xsl:choose>
                                             <xsl:when test="@image-side='left'">
                                               <header uid="{@uid}" class="hero-unit" id="header">
                                                 
                 	    				<div class="span3">
                                                           <img src="{@image}" alt="main page"/>
                 	    				</div>
                                                 <div class="span9">
                                                   <xsl:apply-templates select="config-header-title"/>
                                                   <xsl:apply-templates select="config-header-subtitle"/>
                                                    </div>
                                                    <div style="clear:both"/>
                				</header>
                                            </xsl:when>    
                                            <xsl:otherwise>
                                              <div class="hero-unit" id="header">
                 	    				<div class="span9">
                                                       <xsl:apply-templates select="config-header-title"/>
                                                      <xsl:apply-templates select="config-header-subtitle"/>
                                                    </div>
                                                    <div class="span3 pull-right">
                                                           <img src="@@@RMximdex.pathto(@image)@@@" alt="main page"/>
                 	    				</div>
                                                     
                                                    <div style="clear:both"/>
                				</div>
                                              <xsl:apply-templates select="config-breadcrumb"/>
                                            </xsl:otherwise>
                                          </xsl:choose>  				
                  			</xsl:when>
              <!--FIN DE RNG-CONFIGURATION-->
                  			<xsl:otherwise>
 					         <header uid="{@uid}" class="hero-unit" id="header">
                         				 <div class="span12">
                                                   <xsl:apply-templates select="config-header-title"/>
                                                   <xsl:apply-templates select="config-header-subtitle"/>
                                          </div>
                                                    <div style="clear:both"/>
                				</header>
                  			</xsl:otherwise>
                  		</xsl:choose>
                  	</xsl:template>
</xsl:stylesheet>
