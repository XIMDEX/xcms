<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
<xsl:template name="INCLUDE_metas" match="INCLUDE_metas">
    <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
    <meta name="description" content="{@description}"/>
    <meta name="keywords" content="{@keywords}"/>
    <meta name="author" content="{@author}"/>
    <meta name="Revisit" content="7 days"/>
    <meta name="Revisit-After" content="7"/>
    <meta name="Classification" content="Business"/>
    <meta name="Rating" content="General"/>
    <meta name="Distribution" content="Global"/>
    <meta name="Robots" content="all"/>
    <meta name="Robot" content="follow"/>
    <meta name="Viewport" content="width=device-width, initial-scale=1.0"/>
</xsl:template>
</xsl:stylesheet>