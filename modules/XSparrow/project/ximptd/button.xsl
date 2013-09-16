<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
   	<xsl:template name="button" match="button">
   		<button uid="{@uid}" type="{@type}" class="btn btn-{@class} btn-{@size}">
   			<xsl:choose>
   				<xsl:when test="@icon!='none'">
   					<i class="icon-{@icon}"/>									
   				</xsl:when>
   			</xsl:choose>
   			<xsl:choose>
   				<xsl:when test="@text='' or not(@text)">			
   					<xsl:text>[Button]</xsl:text>
   				</xsl:when>
   				<xsl:otherwise>
   					<xsl:value-of select="@text"/>
   				</xsl:otherwise>
   			</xsl:choose>
   		</button>		
   	</xsl:template>
</xsl:stylesheet>
