<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	version="1.0">
	<xsl:param name="xmlcontent" />
	<xsl:include href="{URL_PATH}/data/nodes/{PROJECT_NAME}/templates/templates_include.xsl" />
	<xsl:template name="docxap" match="docxap">

		<html xmlns="http://www.w3.org/1999/xhtml"  xmlns:foaf="http://xmlns.com/foaf/0.1/">

			<head>
				<xsl:call-template name="INCLUDE_metas" />
				<xsl:call-template name="INCLUDE_styles" />
				<title><xsl:value-of select="/docxap/top_block/header_block/title"/></title>
			</head>

			<body contentEditable="false">
				<div class="container">
					<div class="gfx">
						<span />
					</div>
					<xsl:apply-templates />
				</div>
			</body>

		</html>

	</xsl:template>
</xsl:stylesheet>
