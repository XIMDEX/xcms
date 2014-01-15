<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
<xsl:template name="item_metadata" match="item_metadata"> 

      <div class="metadata" uid="{@uid}">
            <xsl:apply-templates/>
      </div>

</xsl:template>
</xsl:stylesheet>
