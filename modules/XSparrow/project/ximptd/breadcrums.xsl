<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
	<xsl:template name="breadcrumb" match="breadcrumb">
		<ul class="breadcrumb">
			<xsl:apply-templates/>		
		</ul>
	</xsl:template>
</xsl:stylesheet>
