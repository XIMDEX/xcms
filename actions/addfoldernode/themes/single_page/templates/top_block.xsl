<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
    <xsl:template name="top_block" match="top_block">

        <header id="{@uid}" uid="{@uid}">
            <nav id="navbar" class="navbar" role="navigation">
                <div class="container">
                    <div id="navbar-header" class="navbar-header">
                        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#menu-fixed">
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>

                        <a class="navbar-brand scroll" href="#{@uid}">Ir al top</a>
                    </div>

                    <div id="menu-fixed" class="collapse navbar-collapse">
                        <ul class="nav navbar-nav navbar-center">
                            <li class="hidden"><a href="#{@uid}"></a></li>

                            <xsl:for-each select="following-sibling::*[not( contains('footer_block',name()))][1]">
                                <li>
                                    <a class="scroll" href="#section-{position()}">
                                        <xsl:value-of select="@title"/>
                                    </a>
                                </li>
                            </xsl:for-each>
                        </ul>
                    </div>
                </div>
            </nav>

            <xsl:apply-templates />
        </header>

    </xsl:template>
</xsl:stylesheet>