<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
       	<xsl:template name="config-container" match="config-container">
       		      		
     		<div class="container-fluid" uid="{@uid}">
     	    		<div class="row-fluid">
                             <xsl:if test="@left_column='Yes'">
                               <xsl:apply-templates select="config-left-column"/>
                             </xsl:if>
                               <xsl:apply-templates select="config-main-column"/>
                             <xsl:if test="@right_column='Yes'">
                               <xsl:apply-templates select="config-right-column"/>
                             </xsl:if>
                       	</div>
           	</div>      			
       	</xsl:template>
</xsl:stylesheet>
