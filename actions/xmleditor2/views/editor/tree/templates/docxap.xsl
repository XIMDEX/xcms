<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0" xmlns:xim="http://www.ximdex.com/" exclude-result-prefixes="xim" extension-element-prefixes="xim">
	<xsl:param name="xmlcontent"/>
	
	<xsl:template name="docxap" match="docxap">
		<html xmlns="http://www.w3.org/1999/xhtml">
		<head>
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
			<title> XML Editor (Tree view) </title>
			<link rel="stylesheet" type="text/css" href="views/editor/tree/css/rngeditor.css" />
		</head>
		<body uid="{@uid}">
			<div class="rngeditor_block">
				<!--<img src="../../xmd/images/tree/blank.png" width="19px" align="absmiddle" />
				<img src="../../xmd/images/icons/root.png" align="absmiddle" />-->
				<!--<img src="../../xmd/images/tree/blank.png" width="10px" align="absmiddle" />-->
				<span class="rngeditor_title">Docxap</span>
				<xsl:apply-templates/>
			</div>
		</body>
		</html>
	</xsl:template>
	<xsl:template name="container" match="*">
		<div class="rngeditor_block">
			<img src="../../xmd/images/tree/Lminus.png" class="ctrl minus folding"/>
			<!--<img src="../../xmd/images/tree/openfolder.png" align="absmiddle" class="folder folding"/>
			<img src="../../xmd/images/tree/blank.png" width="10px" align="absmiddle"/>-->
			<span uid="{@uid}" editable="no" class="rngeditor_title folding"> <xsl:value-of select="local-name(.)"/> </span>
			<div uid="{@uid}" id="tg_{@uid}">
				<xsl:apply-templates/>
			</div>
		</div>
	</xsl:template>
</xsl:stylesheet>
