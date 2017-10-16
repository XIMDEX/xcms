<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
   <xsl:template name="image" match="image">
	<xsl:variable name="src">
		<xsl:if test="@src and @src != ''">
			<xsl:value-of select="concat('@@@RMximdex.pathto(',@src,')@@@')"/>
		</xsl:if>
	</xsl:variable>
 	<xsl:choose>
 		<xsl:when test="../image-list">
			<li class="span{@cols}">
 			    <img src="{$src}" uid="{@uid}"/>
 			</li>
         </xsl:when>
         <xsl:otherwise>
	    <img class="span{@cols}" src="{$src}" uid="{@uid}"/>
         </xsl:otherwise>
     </xsl:choose>
   </xsl:template>
</xsl:stylesheet>
