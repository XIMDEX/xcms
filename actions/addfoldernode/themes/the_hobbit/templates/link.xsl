<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
<xsl:template name="link" match="link"> 

      <xsl:if test="(@a_enlaceid_url != '')">
            <a uid="{@uid}" href="{@a_enlaceid_url}" id="{@id}">
                 <xsl:apply-templates/>
           </a>
      </xsl:if>

      <xsl:if test="((@a_enlaceid_url = '') or (not(@a_enlaceid_url)))">
            <a uid="{@uid}" href="{@file}" id="{@id}">
                 <xsl:apply-templates/>
           </a>
      </xsl:if>

</xsl:template>
</xsl:stylesheet>
