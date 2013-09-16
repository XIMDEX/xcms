<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
     	<xsl:template name="INCLUDE-css" match="INCLUDE-css">
            <link rel="stylesheet" href="@@@RMximdex.dotdot(css/bootstrap/bootstrap.min.css)@@@" type="text/css"/>
            <link rel="stylesheet" href="@@@RMximdex.dotdot(css/custom.css)@@@" type="text/css"/>
   </xsl:template>
</xsl:stylesheet>
