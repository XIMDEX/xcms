<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
         	<xsl:param name="xmlcontent"/>
         <xsl:include href="{URL_PATH}/data/nodes/{PROJECT_NAME}/ximptd/templates_include.xsl"/>
         	<xsl:template name="docxap" match="docxap">
     	   <xsl:choose>
      	      <xsl:when test="/docxap/@tipo_documento='rng-configuracion.xml'">
                     <xsl:call-template name="docxap-configuracion"/>
                  </xsl:when>
                <xsl:when test="/docxap/@tipo_documento='rng-bootstrap-new.xml'">
                     <xsl:call-template name="docxap-configuracion"/>
                  </xsl:when>
    	       <xsl:when test="/docxap/@tipo_documento='rng-bootstrap-based.xml'">
                     <xsl:call-template name="docxap-configuracion"/>
                  </xsl:when>
    	       <xsl:when test="/docxap/@tipo_documento='rng-bootstrap-footer.xml'">
                     <xsl:call-template name="docxap-footer"/>
                  </xsl:when>
                <xsl:when test="/docxap/@tipo_documento='rng-ximlet-bootstrap-menu.xml'">
                     <xsl:call-template name="docxap-menu"/>
                  </xsl:when>
               </xsl:choose>
     	</xsl:template>
</xsl:stylesheet>
