<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
    	<xsl:output method="html"/>
    	<xsl:param name="xmlcontent"/>
	<xsl:include href="{URL_PROJECT}/templates/templates_include.xsl"/>
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
      	<link rel="stylesheet" type="text/css" href="@@@RMximdex.dotdot(css/addressbook.css)@@@"/> 
           <link href="http://fonts.googleapis.com/css?family=Norican|Amatic+SC|Handlee|Devonshire|Cookie|Rochester|Patrick+Hand|Just+Another+Hand" rel="stylesheet" type="text/css"/>
      	<title>AddressBook</title>
     </head>
     <body uid="{@uid}">
      	<div class="main">
      		<div class="header">
      			<div class="title">
      <h1>Address Book</h1>
     </div>
     		</div>
     		<div class="container">
     			<xsl:apply-templates/>
     		</div>
     	</div>
    </body>
</html>
</xsl:template>
</xsl:stylesheet>
