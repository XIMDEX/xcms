<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
 	<xsl:template name="link" match="link">
 		<a class="normal-link" uid="{@uid}" href="{@href}"> <xsl:apply-templates/>	</a>
 	</xsl:template>
</xsl:stylesheet>
