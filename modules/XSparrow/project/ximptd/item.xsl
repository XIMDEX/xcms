<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
 	<xsl:template name="item" match="item">
 		<xsl:choose>
 			<xsl:when test="../@type = 'description' or ../type = 'horizontal-description'">
 				<dd uid="{@uid}">
 					<xsl:apply-templates/>
 				</dd>
 			</xsl:when>
 			<xsl:otherwise>
 				<li uid="{@uid}">
 					<xsl:apply-templates/>
 				</li>
 			</xsl:otherwise>
 		</xsl:choose>
 	</xsl:template>
</xsl:stylesheet>
