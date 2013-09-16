<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
 	<xsl:template name="button-icon" match="button-icon">
 		<i class="icon-{@type}">
 <xsl:apply-templates/>
</i>
	</xsl:template>
</xsl:stylesheet>
