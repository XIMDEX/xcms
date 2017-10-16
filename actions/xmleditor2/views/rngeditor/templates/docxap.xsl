<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0" xmlns:xim="http://www.ximdex.com/" exclude-result-prefixes="xim" extension-element-prefixes="xim">
	<xsl:param name="xmlcontent"/>
	<xsl:include href="./templates_include.xsl"/>
	<xsl:template name="docxap" match="docxap">
		<html>
		<head>
			<title> RNG Editor </title>
			<link rel="stylesheet" type="text/css" href="../../actions/xmleditor2/views/rngeditor/css/rngeditor.css" />
		</head>
		<span id="{@uid}" />
		<body uid="{@uid}">
			<h2>
				<img src="../../xmd/images/logo_ximdex.gif" align="absmiddle" />
				Rng Editor
			</h2>
			<div class="rngeditor_block">
				<!--<img src="../../xmd/images/tree/blank.png" width="19px" align="absmiddle" />-->
				<img src="../../xmd/images/icons/root.png" align="absmiddle" />
				<!--<img src="../../xmd/images/tree/blank.png" width="10px" align="absmiddle" />-->
				<span class="rngeditor_title">RNG Schema</span>
				<xsl:apply-templates/>
			</div>
		</body>
		</html>
	</xsl:template>
</xsl:stylesheet>
