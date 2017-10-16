<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
	<xsl:template name="text" match="text">
        <span id="{@uid}" />
        <div uid="{@uid}" class="rngeditor_block">
			<img src="../../xmd/images/tree/L.png" align="absmiddle"/>
			<img src="../../xmd/images/tree/file.png" align="absmiddle"/>
			<img src="../../xmd/images/tree/blank.png" width="10px" align="absmiddle"/>
			<span class="rngeditor_title">text</span>
        </div>
	</xsl:template>
</xsl:stylesheet>
