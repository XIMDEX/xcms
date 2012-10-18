<?xml version="1.0" encoding="utf-8" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<!--
	dexT to XSLT translation: from dext %%%_BODY_%%% to xsl:apply-templates or xsl:call-template
-->

<xsl:template name="var_body">
	<xsl:choose>	
		<xsl:when test="namespace-uri(../.)='http://www.w3.org/TR/html401'">
			<xsl:element name="xsl:apply-templates"/>
		</xsl:when>
		<xsl:otherwise>    
				<xsl:choose>
					<xsl:when test="local-name(/*[1])=local-name(../.)">
						<xsl:element name="xsl:apply-templates"/>
					</xsl:when>
					<xsl:when test="contains(local-name(../.),'___')">			
						<xsl:variable name="temp2">
						<xsl:value-of select="substring-before(local-name(../.),'___')"/></xsl:variable>
						<xsl:variable name="temp3">
						<xsl:value-of select="substring-after(local-name(../.),'___')"/></xsl:variable>
						<xsl:variable name="temp4"><xsl:value-of select="substring-before($temp2,'-')"/></xsl:variable>
						<xsl:variable name="temp5"><xsl:value-of select="substring-before($temp3,'___')"/></xsl:variable>

						<xsl:call-template name="call_template_dinamically">
							<xsl:with-param name="a0" select="$temp2"/>
							<xsl:with-param name="a1" select="$temp5"/>
							<xsl:with-param name="a2" select="$temp4"/>
						</xsl:call-template>

					</xsl:when>
					<xsl:when test="contains(local-name(../.),'ifcondition')">			
						<xsl:element name="xsl:apply-templates"/>
					</xsl:when>
					<xsl:otherwise>
						<xsl:element name="xsl:call-template">
							<xsl:attribute name="name"><xsl:value-of select="local-name(../.)"/></xsl:attribute>
						</xsl:element>
					</xsl:otherwise>
				</xsl:choose>

		</xsl:otherwise>    
	</xsl:choose>		
</xsl:template>

</xsl:stylesheet>
