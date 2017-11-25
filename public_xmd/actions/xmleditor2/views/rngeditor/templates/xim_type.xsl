<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0" xmlns:xim="http://www.ximdex.com/" exclude-result-prefixes="xim" extension-element-prefixes="xim">
	<xsl:template name="xim:type" match="xim:type">
        <span id="{@uid}" />
        <div uid="{@uid}">
	        <span uid="{@uid}" editable="no" class="rngeditor_block">
				<img src="../../xmd/images/tree/L.png" align="absmiddle"/>
				<img src="../../xmd/images/tree/file.png" align="absmiddle"/>
				<img src="../../xmd/images/tree/blank.png" width="10px" align="absmiddle"/>
			</span>
			<span uid="{@uid}" editable="no" class="rngeditor_title">xim:type:</span> <xsl:apply-templates/>
        </div>
	</xsl:template>
</xsl:stylesheet>
