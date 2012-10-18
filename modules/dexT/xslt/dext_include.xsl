<?xml version="1.0" encoding="utf-8" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<!--
	dexT to XSLT translation: from dext INCLUDES to xsl:include
-->

<xsl:template name="include">	
	<xsl:param name="to"/>

	<xsl:variable name="file_included">
		<xsl:choose>	
			<xsl:when test="string-length($to) > 0">
				<xsl:value-of select="$to"/>
			</xsl:when>
			<xsl:otherwise>    
				<xsl:value-of select="concat(local-name(.),'.xsl')"/>
			</xsl:otherwise>    
		</xsl:choose>	
	</xsl:variable>

	<xsl:element name="xsl:include">
		<xsl:attribute name="href"><xsl:value-of select="concat($ximdex_root_path,$file_included)"/></xsl:attribute>
	</xsl:element>
</xsl:template>

</xsl:stylesheet>
