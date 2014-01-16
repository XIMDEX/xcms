<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
<xsl:template name="imagen" match="imagen">
	
	<xsl:if test="(ancestor-or-self::*[@tipo_documento][1]/@tipo_documento = 'rng-page.xml')">
		<img src="{ancestor-or-self::*[@url][1]/@url}" height="{ancestor-or-self::*[@alto_imagen][1]/@alto_imagen}" width="{ancestor-or-self::*[@ancho_imagen][1]/@ancho_imagen}" alt="{ancestor-or-self::*[@texto][1]/@texto}" title="{ancestor-or-self::*[@texto][1]/@texto}" class="{ancestor-or-self::*[@clase][1]/@clase}" align="{ancestor-or-self::*[@align][1]/@align}" usemap="#{ancestor-or-self::*[@usemap][1]/@usemap}" uid="{@uid}"/>
	</xsl:if>

	<xsl:if test="(ancestor-or-self::*[@tipo_documento][1]/@tipo_documento = 'rng-post.xml')">
		<img src="{ancestor-or-self::*[@url][1]/@url}" height="249" width="213" alt="{ancestor-or-self::*[@texto][1]/@texto}" title="{ancestor-or-self::*[@texto][1]/@texto}" class="{ancestor-or-self::*[@clase][1]/@clase}" align="{ancestor-or-self::*[@align][1]/@align}" usemap="#{ancestor-or-self::*[@usemap][1]/@usemap}" uid="{@uid}"/>
	</xsl:if>
	
</xsl:template>
</xsl:stylesheet>
