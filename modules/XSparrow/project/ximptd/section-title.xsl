<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
  	<xsl:template name="section-title" match="section-title">
  		<div class="page-header">
  				<h2 uid="{@uid}">
                                 <xsl:apply-templates/>
                               </h2>	
 		</div>
 	</xsl:template>
</xsl:stylesheet>
