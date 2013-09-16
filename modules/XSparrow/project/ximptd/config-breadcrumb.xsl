<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
    	<xsl:template name="config-breadcrumb" match="config-breadcrumb">
 		<ul uid="{@uid}" class="breadcrumb" id="breadcrumb" style="margin-bottom:40px; margin-top:-40px;">
 			<li class="generic-breadcrumb">
 			<a href="#">Home</a> <span class="divider">/</span>
 			</li>
 			<li class="generic-breadcrumb">
 			<a href="#">Library</a> <span class="divider">/</span>
 			</li>
 			<li class="active">Data</li>
 		</ul>           	
    	</xsl:template>
</xsl:stylesheet>
