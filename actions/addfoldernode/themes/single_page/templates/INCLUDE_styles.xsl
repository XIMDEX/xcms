<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
    <xsl:template name="INCLUDE_styles" match="INCLUDE_styles">
        <link href="@@@RMximdex.dotdot(images/favicon.ico)@@@" rel="shortcut icon"/>
        <!-- bootstrap -->
        <link href="@@@RMximdex.dotdot(common/bootstrap/css/bootstrap.min.css)@@@" rel="stylesheet"/>
        <!-- main -->
        <link href="@@@RMximdex.dotdot(css/main.css)@@@" rel="stylesheet"/>
    </xsl:template>
</xsl:stylesheet>