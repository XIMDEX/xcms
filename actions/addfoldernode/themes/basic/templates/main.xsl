<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
                       	<xsl:template name="main" match="main">
                                         <section class="post pure-u-1" uid="{@uid}">
                           			<header class="post-header">
                             <img class="post-avatar" alt="Cat" src="http://placekitten.com/100/100" height="48" width="48"/>
                                  
                             <h2 class="post-title pure-u-1">
               <xsl:value-of select="title"/>
</h2>
              <p class="post-meta pure-u-1">
                <xsl:for-each select="tags/tag">
                  <xsl:variable name="tagname">
                    <xsl:value-of select="."/>
                  </xsl:variable>
                  <xsl:choose>
                    <xsl:when test="$tagname = 'design'">
                      <a class="post-category post-category-design" href="#">
<xsl:value-of select="."/>
</a>
                    </xsl:when>
                    <xsl:when test="$tagname = 'pure'">
                      <a class="post-category post-category-pure" href="#">
<xsl:value-of select="."/>
</a>
                    </xsl:when>
                    <xsl:when test="$tagname = 'yui'">
                      <a class="post-category post-category-yui" href="#">
<xsl:value-of select="."/>
</a>
                    </xsl:when>
                    <xsl:when test="$tagname = 'js'">
                      <a class="post-category post-category-js" href="#">
<xsl:value-of select="."/>
</a>
                    </xsl:when>
                    <xsl:otherwise>
                      <a class="post-category" href="#">
<xsl:value-of select="."/>
</a>
                    </xsl:otherwise>
                  </xsl:choose>
    		</xsl:for-each>
              </p>
            </header>
            <div class="post-description pure-u-1">
              <p>
                <xsl:value-of select="cuerpo"/>
              </p>
            </div>
        </section>
           </xsl:template>
</xsl:stylesheet>
