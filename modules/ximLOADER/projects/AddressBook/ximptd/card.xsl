<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
      <xsl:template name="card" match="card"> 
       	<fieldset class="card" uid="{@uid}">
       		<legend>
       			<span>
       <xsl:value-of select="name"/>
      </span>
      		</legend>
                    
                   	<xsl:choose>
                             <xsl:when test="not(@id_image) or @id_image=''">
                               <img src="@@@RMximdex.dotdot(images/default.png)@@@" alt="Photograph" title="Photograph card"/>
                             </xsl:when>
                             <xsl:otherwise>
                                <img src="@@@RMximdex.pathto({@id_image})@@@" alt="Photograph" title="Photograph card"/>
                             </xsl:otherwise>                               
                          </xsl:choose>
                    
                    
      		<ul>
      				<xsl:apply-templates/>
      		</ul>
      	</fieldset>
</xsl:template>
</xsl:stylesheet>
