<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
    	<xsl:template name="ximlet" match="ximlet">
    		<xsl:choose>
                	<xsl:when test="//docxap[@transformer]/@transformer!='xEDIT'">
                        	<xsl:apply-templates/>
                        </xsl:when>
    			<xsl:when test="not(config)">
    				<div uid="{@uid}">
    					<xsl:apply-templates/>
    				</div>
    			</xsl:when>
    			<xsl:otherwise>
                              <span uid="{@uid}">
    				<xsl:apply-templates select="config"/>
                             </span>
    			</xsl:otherwise>
    		</xsl:choose>
    	</xsl:template>
</xsl:stylesheet>
