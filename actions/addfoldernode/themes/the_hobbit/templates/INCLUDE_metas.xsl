<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
<xsl:template name="INCLUDE_metas" match="INCLUDE_metas">
 
    <meta http-equiv="content-type" content="text/html; charset=iso-8859-1"/>
    <meta name="description" content="{@description}"/>
    <meta name="keywords" content="{@keywords}"/> 
    <meta name="author" content="{@author}"/> 

</xsl:template>
</xsl:stylesheet>
