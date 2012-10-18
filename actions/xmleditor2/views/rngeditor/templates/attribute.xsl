<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
	<xsl:template name="attribute" match="attribute"> 
		<span name="{@name}" />
		<div uid="{@uid}">
			<span editable="no" class="rngeditor_block">
				<img src="../../xmd/images/tree/L.png" align="absmiddle"/>
				<img src="../../xmd/images/tree/file.png" align="absmiddle"/>
				<img src="../../xmd/images/tree/blank.png" width="10px" align="absmiddle"/>
			</span>
			<span editable="no">
				<span class="rngeditor_title">attribute:</span> Name: <xsl:value-of select="@name"/>
			</span>
		</div>
	</xsl:template>
</xsl:stylesheet>
