<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0"  xmlns:xim="http://www.ximdex.com/" exclude-result-prefixes="xim" extension-element-prefixes="xim">
	<xsl:template name="xim:default_content" match="xim:default_content">
        <span id="{@uid}" />
        <div uid="{@uid}">
			<span uid="{@uid}" class="rngeditor_block" editable="no">
				<img src="../../public_xmd/assets/images/tree/L.png" align="absmiddle"/>
				<img src="../../public_xmd/assets/images/tree/file.png" align="absmiddle"/>
				<img src="../../public_xmd/assets/images/tree/blank.png" width="10px" align="absmiddle"/>
			</span>
			<span uid="{@uid}" editable="no" class="rngeditor_title">xim:default_content:</span> <xsl:apply-templates/>
		</div>
	</xsl:template>
</xsl:stylesheet>
