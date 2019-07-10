<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
<xsl:template name="name" match="name">
	<li>
	<span class="label name">Name</span>
	<span class="card-field name" uid="{@uid}"><xsl:value-of select="."/></span>
	</li>
</xsl:template>
</xsl:stylesheet>
