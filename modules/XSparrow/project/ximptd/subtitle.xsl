<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
    	<xsl:template name="subtitle" match="subtitle">
    		<h3 uid="{@uid}">
    			<xsl:apply-templates/>
    		</h3>	
    	</xsl:template>
</xsl:stylesheet>
