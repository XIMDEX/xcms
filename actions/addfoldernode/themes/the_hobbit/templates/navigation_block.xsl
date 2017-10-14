<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
<xsl:template name="navigation_block" match="navigation_block"> 

      <div class="navigation" uid="{@uid}">
            <xsl:apply-templates/>
      </div>

</xsl:template>
</xsl:stylesheet>
