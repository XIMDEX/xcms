<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
<xsl:template name="enlace" match="enlace"> 

	<a class="enlace" href="{ancestor-or-self::*[@a_enlaceid_url][1]/@a_enlaceid_url}" target="{ancestor-or-self::*[@ventana][1]/@ventana}" uid="{@uid}">
		<xsl:value-of select="ancestor-or-self::*[@a_enlaceid_url][1]/@a_enlaceid_url"/>
	</a>

</xsl:template>
</xsl:stylesheet>
