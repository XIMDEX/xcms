<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
   	<xsl:template name="paragraph" match="paragraph">
   		<p uid="{@uid}">
   			<xsl:apply-templates/>
   		</p>
   	</xsl:template>
</xsl:stylesheet>
