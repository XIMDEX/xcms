<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
	<xsl:template name="navbar" match="navbar">
		<xsl:choose>
			<xsl:when test="@fixed='no'">
				<div class="navbar">
					<div class="navbar-inner">
						<div class="container">
							<xsl:apply-templates/>				
						</div>
					</div>
				</div>
			</xsl:when>
			<xsl:otherwise>
				<div class="navbar navbar-fixed-{@fixed }">
					<div class="navbar-inner">
						<div class="container">
							<xsl:apply-templates/>				
						</div>
					</div>
				</div>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
</xsl:stylesheet>
