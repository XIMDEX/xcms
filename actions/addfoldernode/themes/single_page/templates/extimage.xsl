<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
   <xsl:template name="extimage" match="extimage">
     <xsl:variable name="width">
       <xsl:choose>
         <xsl:when test="@width and @width != '' and @width!=0">
           <xsl:value-of select="@width"/>
         </xsl:when>
         <xsl:otherwise>
           <xsl:value-of select="'auto'"/>
         </xsl:otherwise>
       </xsl:choose>        
     </xsl:variable>
     <xsl:variable name="height">
       <xsl:choose>
         <xsl:when test="@height and @height != '' and @height!=0">
           <xsl:value-of select="@height"/>
         </xsl:when>
         <xsl:otherwise>
           <xsl:value-of select="'auto'"/>
         </xsl:otherwise>
       </xsl:choose> 
     </xsl:variable>
     <p>
       <img uid="{@uid}" class="{@class}" width="{$width}" height="{$height}" src="{@url}" alt="{@alt}"/>
     </p>
   </xsl:template>
</xsl:stylesheet>