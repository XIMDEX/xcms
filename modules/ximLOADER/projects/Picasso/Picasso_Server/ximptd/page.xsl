<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">

<xsl:template name="page" match="page">

	<xsl:if test="(ancestor-or-self::*[@tipo_documento][1]/@tipo_documento = 'rng-page.xml')">

		<div class="post-ppal">
			<xsl:apply-templates select="page/post[1]"/>
		</div>
		
		<div class="post-sec">
			<xsl:apply-templates select="page/post[position()&gt;1]"/>
		</div>
		
	</xsl:if>

	<xsl:if test="(ancestor-or-self::*[@tipo_documento][1]/@tipo_documento = 'rng-post.xml')">
		<div id="post">
			<xsl:call-template name="post"/>
		</div>
	</xsl:if>
	
</xsl:template>
</xsl:stylesheet>
