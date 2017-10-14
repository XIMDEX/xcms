<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
 	<xsl:template name="code-block" match="code-block">
 		<pre uid="{@uid}" class="pre-scrollable">
 			<xsl:apply-templates/>
 		</pre>	
 	</xsl:template>
</xsl:stylesheet>
