<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
    <xsl:template name="INCLUDE_js" match="INCLUDE_js">
        <!-- jquery -->
        <script src="@@@RMximdex.dotdot(common/jquery/jquery.js)@@@"/>
        <!-- jquery.easing.min -->
        <script src="@@@RMximdex.dotdot(common/jquery/extra/jquery.easing.min.js)@@@"/>
        <!-- bootstrap -->
        <script src="@@@RMximdex.dotdot(common/bootstrap/js/bootstrap.min.js)@@@"/>

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->

        <!-- main -->
        <script src="@@@RMximdex.dotdot(common/js/main.js)@@@"/>
    </xsl:template>
</xsl:stylesheet>
