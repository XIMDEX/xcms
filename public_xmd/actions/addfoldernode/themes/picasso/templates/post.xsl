<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">

<xsl:template name="post_ppal" match="page/post[1]"> 

	<div class="entry" uid="{@uid}">
		<xsl:apply-templates select="post_title/."/>

		<div class="postinfo">
			<xsl:apply-templates select="fecha/."/>
		</div>

		<p class="clear">
			<xsl:apply-templates select="imagen/."/>
		</p>
		
		<xsl:apply-templates select="abstract/."/>

		<p class="more">
			<a target="_self" href="@@@RMximdex.pathto({ancestor-or-self::*[@nodeid][1]/@nodeid})@@@">Read more</a>
		</p>
	</div>

</xsl:template>

<xsl:template name="post_sec" match="page/post[position()&gt;1]"> 

	<div uid="{@uid}" class="entry">
		<xsl:apply-templates select="post_title/."/>

		<div class="postinfo">
			<xsl:apply-templates select="fecha/."/>
		</div>

		<p class="clear">
			<xsl:apply-templates select="imagen/."/>
		</p>
		
		<xsl:apply-templates select="abstract/."/>

		<p class="more">
			<a target="_self" href="@@@RMximdex.pathto({ancestor-or-self::*[@nodeid][1]/@nodeid})@@@">Read more</a>
		</p>
	</div>

</xsl:template>

<xsl:template name="post"> 

	<div uid="{@uid}" class="entry">
		<xsl:apply-templates select="post/post_title/."/>
		
		<div class="postinfo">
			<xsl:apply-templates select="post/fecha/."/>
		</div>

		<p class="clear">
			<xsl:apply-templates select="post/imagen/."/>
		</p>

		<xsl:apply-templates select="post/abstract/."/>
		<xsl:apply-templates select="post/parrafo/."/>
		<xsl:apply-templates select="post/related_info/."/>
	</div>

</xsl:template>
</xsl:stylesheet>
