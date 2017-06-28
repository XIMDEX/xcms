<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
    <xsl:template name="background" match="background">

      <section id="{@uid}" class="background-section section-page" style="background: url({@background_image}) no-repeat center center scroll;">
        <xsl:choose>
          <xsl:when test="not(@container_type) or (@container_type = 'sin-container')">

            <xsl:choose>
              <xsl:when test="@title != ''">
                <header>
                  <h2><xsl:value-of select="@title"/></h2>
                </header>
              </xsl:when>
            </xsl:choose>

            <xsl:apply-templates/>
          </xsl:when>

          <xsl:otherwise>
            <div class="{@container_type}">

              <xsl:choose>
                <xsl:when test="@title != ''">
                  <header>
                    <h2><xsl:value-of select="@title"/></h2>
                  </header>
                </xsl:when>
              </xsl:choose>

              <xsl:apply-templates />
            </div>
          </xsl:otherwise>
        </xsl:choose>
      </section>

    </xsl:template>
</xsl:stylesheet>