<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
<xsl:template name="list_item" match="list_item"> 

      <li uid="{@uid}">
            <xsl:apply-templates/>
      </li>

</xsl:template>
</xsl:stylesheet>
