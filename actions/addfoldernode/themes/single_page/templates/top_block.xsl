<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
    <xsl:template name="top_block" match="top_block">
        <header id="top" class="header" uid="{@uid}">
            <nav id="navbar" class="navbar" role="navigation">
                <div class="container">
                    <div id="navbar-header" class="navbar-header">
                        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#menu-fixed">
                            <span class="icon-bar"/>
                            <span class="icon-bar"/>
                            <span class="icon-bar"/>
                        </button>

                        <a class="navbar-brand scroll" uid="{@uid}" href="#top">Top</a>
                    </div>

                    <div id="menu-fixed" class="collapse navbar-collapse navbar-right">
                        <ul class="nav navbar-nav">
                            <li class="hidden"><a href="#top"/></li>
                            <xsl:for-each select="../body_block/section">
                                <li><a class="scroll" href="#section-{position()}"><xsl:value-of select="@title"/></a></li>
                            </xsl:for-each>
                        </ul>
                    </div>
                </div>
            </nav>
            <xsl:choose>
                <xsl:when test="@background != ''">
                    <div class="section-page section-window-height" style="
                        background: url(@@@RMximdex.pathto({@background})@@@) no-repeat center center scroll;
                        -webkit-background-size: cover;
                        -moz-background-size: cover;
                        background-size: cover;
                        -o-background-size: cover;">
                        <div class="container-center">
                            <div class="container">
                                <xsl:apply-templates/>
                            </div>
                        </div>
                    </div>
                </xsl:when>
                <xsl:otherwise>
                    <div class="section-page section-window-height" style="
                        background: url(@@@RMximdex.dotdot(images/intro.jpg)@@@) no-repeat center center scroll;
                        -webkit-background-size: cover;
                        -moz-background-size: cover;
                        background-size: cover;
                        -o-background-size: cover;">
                        <div class="container-center">
                            <div class="container">
                                <xsl:apply-templates/>
                            </div>
                        </div>
                    </div>
                </xsl:otherwise>
            </xsl:choose>

        </header>
    </xsl:template>
</xsl:stylesheet>