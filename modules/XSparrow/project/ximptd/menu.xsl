<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
 	<xsl:template name="menu" match="menu">
 		<div class="navbar navbar-fixed-top" uid="{@uid}">
       <div class="navbar-inner">
         <div class="container">          
           <div class="nav-collapse collapse">
             <ul class="nav" uid="{@uid}">
               <xsl:apply-templates/>
             </ul>
           </div>
         </div>
       </div>
     </div>
 	</xsl:template>
</xsl:stylesheet>
