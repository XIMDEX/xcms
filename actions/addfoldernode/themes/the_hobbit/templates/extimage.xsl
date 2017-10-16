<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	version="1.0">
	<xsl:template name="extimage" match="extimage">

		<img uid="{@uid}" class="{@class}" src="{@url}" width="{@width}"
			height="{@height}" alt="{@alt}" />

	</xsl:template>
</xsl:stylesheet>
