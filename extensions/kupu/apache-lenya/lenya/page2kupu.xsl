<?xml version="1.0" encoding="UTF-8" ?>

<!--
  Transforms page to be edited by Kupu wysiwyg xhtml editor.
  Here the link to css etc. is inserted and marked(see lenyacontent attribute) 
  to be remved when saved.
-->

<xsl:stylesheet version="1.0"
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns:lenya="http://apache.org/cocoon/lenya/page-envelope/1.0"
  xmlns:xhtml="http://www.w3.org/1999/xhtml"
  xmlns="http://www.w3.org/1999/xhtml"
 >

<xsl:param name="css"/>


<xsl:template match="lenya:meta"/>


<xsl:template match="xhtml:head">
	<head>
		<xsl:copy-of select="*"/> 
		<link rel="stylesheet" href="{$css}" mime-type="text/css"/>
	</head>
</xsl:template>



<xsl:template match="@*|node()">
	<xsl:copy>
		<xsl:apply-templates select="@*|node()"/>
	</xsl:copy>
</xsl:template>  


</xsl:stylesheet>
