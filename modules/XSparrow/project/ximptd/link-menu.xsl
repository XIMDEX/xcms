<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
    	<xsl:template name="link-menu" match="link-menu">
    		<li class="">
            	<a href="{@href}" uid="{@uid}" class="navbar-link">
    				<xsl:if test="@icon and @icon!='none' and @icon != ''">
    					<i class="icon-{@icon} icon-{@icon-colour}"/>
    				</xsl:if>
   			<xsl:choose>
     				<xsl:when test="@text='' or not(@text)">			
     					<xsl:text>[Link]</xsl:text>
     				</xsl:when>
     				<xsl:otherwise>
     					<xsl:value-of select="concat(' ',@text)"/>
     				</xsl:otherwise>
     			</xsl:choose>
</a>
        </li>
	</xsl:template>
</xsl:stylesheet>
