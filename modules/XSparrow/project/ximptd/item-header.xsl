<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
  	<xsl:template name="item-header" match="item-header">
  		<xsl:choose>
  			<xsl:when test="../@type='description' or ../type='horizontal-description'">
  				<dt uid="{@uid}">
  					<xsl:apply-templates/>
  				</dt>
  			</xsl:when>
  			<xsl:otherwise>
  				<li uid="{@uid}" class="strong"> 					
                                   <xsl:apply-templates/>
 				</li>
 			</xsl:otherwise>
 		</xsl:choose>
 	</xsl:template>
</xsl:stylesheet>
