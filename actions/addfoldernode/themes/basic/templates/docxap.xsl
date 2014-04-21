<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
                   	<xsl:output method="html"/>
                   	<xsl:param name="xmlcontent"/>
               	<xsl:include href="{URL_PATH}/data/nodes/{PROJECT_NAME}/templates/templates_include.xsl"/>
                   	<xsl:template name="docxap" match="docxap"> 
                    <!--<xsl:text disable-output-escaping="yes">
                    <![CDATA[ 
                    	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
                     ]]>
                   </xsl:text>
                   -->
                   <html xmlns="http://www.w3.org/1999/xhtml">
                    <head>
                     	<meta http-equiv="content-type" content="application/xhtml+xml; charset=utf-8"/>
                     	<link rel="stylesheet" type="text/css" href="@@@RMximdex.dotdot(css/main.css)@@@"/>
                      	<link rel="stylesheet" type="text/css" href="@@@RMximdex.dotdot(css/pure-min.css)@@@"/>
                     	<title>Blog</title>
                    </head>
                    <body uid="{@uid}">
                     	<div id="container">
            <div id="layout" role="main" class="pure-g-r">
                <xsl:apply-templates select="header"/>
		<xsl:apply-templates select="content"/>
		<xsl:apply-templates select="footer"/>
        </div>
    </div>	
               </body>
</html>
</xsl:template>
  </xsl:stylesheet>
