<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
	<xsl:template name="config-footer" match="config-footer">
		<xsl:choose>
			<xsl:when test="//docxap[@tipo_documento]/@tipo_documento='rng-configuracion.xml'">
				<div uid="{@uid}" class="footer">
					<xsl:apply-templates/>
				</div>
			</xsl:when>
			<xsl:otherwise>
							<!-- TODO -->
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
</xsl:stylesheet>
