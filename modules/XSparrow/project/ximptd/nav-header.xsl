<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
	<xsl:template name="nav-header" match="nav-header">
		<li class="nav-header">
			<xsl:apply-templates/>		
		</li>
	</xsl:template>
</xsl:stylesheet>
