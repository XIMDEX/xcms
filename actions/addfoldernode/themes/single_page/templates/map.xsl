<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
    <xsl:template name="map" match="map">

    	<div id="{uid}" class="map-section section-page">
    		<xsl:choose>
          		<xsl:when test="not(@container_type) or (@container_type = 'sin-container')">
          			<xsl:choose>
            			<xsl:when test="/docxap/@transformer = 'xEDIT'">
            				<div style="display:block;height: 100px; width: 100%;background-color:red"></div>
            			</xsl:when>

            			<xsl:otherwise>
            				<iframe src="{@link}" style="border:0"></iframe>
            			</xsl:otherwise>
            		</xsl:choose>
          		</xsl:when>

          		<xsl:otherwise>
            		<div class="{@container_type}">
            			<xsl:choose>
            				<xsl:when test="/docxap/@transformer = 'xEDIT'">
            					<div style="display:block;height: 100px; width: 100%;background-color:red"></div>
            				</xsl:when>

            				<xsl:otherwise>
            					<iframe src="{@link}" style="border:0"></iframe>
            				</xsl:otherwise>
            			</xsl:choose>
            		</div>
          		</xsl:otherwise>
        	</xsl:choose>
		</div>

    </xsl:template>
</xsl:stylesheet>