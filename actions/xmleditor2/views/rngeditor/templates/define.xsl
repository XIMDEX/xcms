<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
	<xsl:template name="define" match="define">
		<span id="{@uid}" name="{@name}" />
		<div uid="{@uid}" class="rngeditor_block">
			<img src="../../xmd/images/tree/Lminus.png" align="absmiddle" class="ctrl minus folding"/>
			<img src="../../xmd/images/tree/folder.png" align="absmiddle" class="folder folding"/>
			<img src="../../xmd/images/tree/blank.png" width="10px" align="absmiddle"/>
			<span class="rngeditor_title">define: <xsl:value-of select="@name"/></span>
			<div id="tg_{@uid}">
				<xsl:apply-templates/>
			</div>
		</div>
	</xsl:template>
</xsl:stylesheet>
