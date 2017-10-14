<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
   	<xsl:template name="list" match="list">
   		<xsl:choose>
   			<xsl:when test="@type='unorderer'">
   				<ul uid="{@uid}">
                                 <xsl:choose>
                                   	<xsl:when test="*">
                                   		<xsl:apply-templates/>
                                   	</xsl:when>
                                   	<xsl:otherwise>
                                          <li>[EmptyList]</li>
                                   </xsl:otherwise>
                                  </xsl:choose>
   					
   				</ul>
   			</xsl:when>
   			<xsl:when test="@type='unstyled'">
   				<ul class="unstyled" uid="{@uid}">
                                          <xsl:choose>
                                   	<xsl:when test="*">
                                   		<xsl:apply-templates/>
                                   	</xsl:when>
                                   	<xsl:otherwise>
                                          <li>[EmptyList]</li>
                                   </xsl:otherwise>
                                  </xsl:choose>
   				</ul>
   			</xsl:when>
   			<xsl:when test="@type='orderer'">
   				<ol uid="{@uid}">
   					<xsl:choose>
                                   	<xsl:when test="*">
                                   		<xsl:apply-templates/>
                                   	</xsl:when>
                                   	<xsl:otherwise>
                                          <li>[EmptyList]</li>
                                   </xsl:otherwise>
                                  </xsl:choose>
   				</ol>
   			</xsl:when>
   			<xsl:when test="@type='description'">
   				<dl uid="{@uid}">
   					<xsl:choose>
                                   	<xsl:when test="*">
                                   		<xsl:apply-templates/>
                                   	</xsl:when>
                                   	<xsl:otherwise>
                                          <dd>[EmptyList]</dd>
                                   </xsl:otherwise>
                                  </xsl:choose>
   				</dl>
   			</xsl:when>
   			<xsl:when test="@type='horizontal-description'">
   				<dl class="dl-horizontal" uid="{@uid}">
   					<xsl:choose>
                                   	<xsl:when test="*">
                                   		<xsl:apply-templates/>
                                   	</xsl:when>
                                   	<xsl:otherwise>
                                          <dd>[EmptyList]</dd>
                                   </xsl:otherwise>
                                  </xsl:choose>								
   				</dl>
   			</xsl:when>
   			<xsl:otherwise>
   				<ul uid="{@uid}">
   					<xsl:choose>
                                   	<xsl:when test="*">
                                   		<xsl:apply-templates/>
                                   	</xsl:when>
                                   	<xsl:otherwise>
                                          <li>[EmptyList]</li>
                                   </xsl:otherwise>
                                  </xsl:choose>
   				</ul>				
   			</xsl:otherwise>	
   		</xsl:choose>
   	</xsl:template>
</xsl:stylesheet>
