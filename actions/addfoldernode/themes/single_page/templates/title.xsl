<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
    <xsl:template name="title" match="title">

        <xsl:choose>
            <xsl:when test="@type = 2">
                <header>
                    <h2 uid="{@uid}">
                        <xsl:apply-templates/>
                    </h2>
                </header>
            </xsl:when>

            <xsl:when test="@type = 3">
                <header>
                    <h3 uid="{@uid}">
                        <xsl:apply-templates/>
                    </h3>
                </header>
            </xsl:when>

            <xsl:when test="@type = 4">
                <header>
                    <h4 uid="{@uid}">
                        <xsl:apply-templates/>
                    </h4>
                </header>
            </xsl:when>

            <xsl:when test="@type = 5">
                <header>
                    <h5 uid="{@uid}">
                        <xsl:apply-templates/>
                    </h5>
                </header>
            </xsl:when>

            <xsl:otherwise>
                <h1 uid="{@uid}">
                    <xsl:apply-templates/>
                </h1>
            </xsl:otherwise>
        </xsl:choose>

    </xsl:template>
</xsl:stylesheet>