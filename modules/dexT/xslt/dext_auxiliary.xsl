<?xml version="1.0" encoding="utf-8" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<!--
	dexT to XSLT translation: auxiliary templates
-->

<!--
	Looking for boolean operators in dext expressions
-->
<xsl:template name="boolean_expressions">
	<xsl:param name="exp"/>

	<xsl:choose>
		<xsl:when test="contains($exp,' AND ')">
			<xsl:call-template name="operators">
			<xsl:with-param name="expresion"><xsl:value-of select="substring-before($exp,' AND ')"/></xsl:with-param>
			</xsl:call-template>

			<xsl:text disable-output-escaping = "yes"> and </xsl:text>
			
			<xsl:call-template name="operators">
			<xsl:with-param name="expresion"><xsl:value-of select="substring-after($exp,' AND ')"/></xsl:with-param>
			</xsl:call-template>
		</xsl:when>

		<xsl:when test="contains($exp,' OR ')">
			<xsl:call-template name="operators">
			<xsl:with-param name="expresion"><xsl:value-of select="substring-before($exp,' OR ')"/></xsl:with-param>
			</xsl:call-template>

			<xsl:text disable-output-escaping = "yes"> or </xsl:text>

			<xsl:call-template name="operators">
			<xsl:with-param name="expresion"><xsl:value-of select="substring-after($exp,' OR ')"/></xsl:with-param>
			</xsl:call-template>
		</xsl:when>

		<xsl:otherwise>
			<xsl:call-template name="operators">
				<xsl:with-param name="expresion"><xsl:value-of select="$exp"/></xsl:with-param>
			</xsl:call-template>
		</xsl:otherwise>
	</xsl:choose>
</xsl:template>

<!--
	translate (in)equality comparisons
-->

<xsl:template name="operators">
	<xsl:param name="expresion"/>

    <xsl:choose>
        <xsl:when test="contains($expresion,' ne ')">
            <xsl:variable name="va" select="normalize-space(substring-before($expresion,'ne'))"/>
            <xsl:variable name="va2" select="normalize-space(substring-after($expresion,'ne'))"/>
			
			<!-- Removing quotes -->
            <xsl:variable name="var0" select="substring($va,2,string-length($va)-2)"/>
            <xsl:variable name="var2" select="substring($va2,2,string-length($va2)-2)"/>

			<!-- Removing { } -->
            <xsl:variable name="var" select="substring($var0,2,string-length($var0)-2)"/>

			<xsl:value-of select='concat($var," != &#39;",$var2,"&#39;")'/>
        </xsl:when>
        <xsl:when test="contains($expresion,' eq ')">
            <xsl:variable name="va" select="normalize-space(substring-before($expresion,'eq'))"/>
            <xsl:variable name="va2" select="normalize-space(substring-after($expresion,'eq'))"/>

			<!-- Removing quotes -->
            <xsl:variable name="var0" select="substring($va,2,string-length($va)-2)"/>
            <xsl:variable name="var2" select="substring($va2,2,string-length($va2)-2)"/>

			<!-- Removing { } -->
            <xsl:variable name="var" select="substring($var0,2,string-length($var0)-2)"/>

			<xsl:value-of select='concat($var," = &#39;",$var2,"&#39;")'/>
		</xsl:when>
        <xsl:when test="starts-with($expresion,'NOT')">
            <xsl:variable name="va" select="substring-after($expresion,'NOT ')"/>
			<xsl:value-of select="concat('not(@',$va,')')"/>
		</xsl:when>
        <xsl:otherwise>
			<xsl:value-of select="concat('@',$expresion)"/>
        </xsl:otherwise>
    </xsl:choose>
</xsl:template>

<!--
	return the operator wich separes a boolean expression
-->

<xsl:template name="get_separator">
	<xsl:param name="exp"/>

    <xsl:choose>
        <xsl:when test="contains($exp,' AND ')">
			<xsl:value-of select="'AND'"/>
        </xsl:when>
        <xsl:otherwise>
			<xsl:value-of select="'OR'"/>
        </xsl:otherwise>
    </xsl:choose>
</xsl:template>

<!--
	search dext variables in $string and close them by caracters $open and $close 
-->

<xsl:template name="vars_replacement">
	<xsl:param name="string"/>
	<xsl:param name="open"/>
	<xsl:param name="close"/>

	<xsl:variable name="search" select="'%%%'"/>

	<xsl:choose>
		<xsl:when test="contains($string,$search)">
			<xsl:variable name="left"><xsl:value-of select="substring-before($string,$search)"/></xsl:variable>
			<xsl:variable name="right"><xsl:value-of select="substring-after($string,$search)"/></xsl:variable>

			<xsl:variable name="left_right"><xsl:value-of select="substring-before($right,$search)"/></xsl:variable>
			<xsl:variable name="right_right"><xsl:value-of select="substring-after($right,$search)"/></xsl:variable>

			<xsl:variable name="bla">
				<xsl:value-of select="concat($left,$open,$left_right,$close,$right_right)"/>
			</xsl:variable>

			<xsl:call-template name="vars_replacement">
				<xsl:with-param name="string"><xsl:value-of select="$bla"/></xsl:with-param>
				<xsl:with-param name="open"><xsl:value-of select="$open"/></xsl:with-param>
				<xsl:with-param name="close"><xsl:value-of select="$close"/></xsl:with-param>
			</xsl:call-template>
		</xsl:when>
		<xsl:otherwise>
			<xsl:value-of select="$string"/>
		</xsl:otherwise>
	</xsl:choose>
</xsl:template>

<!--
	replace by $replacement all ocurrences of $search in $string
-->

<xsl:template name="string_replace">
	<xsl:param name="string"/>
	<xsl:param name="search"/>
	<xsl:param name="replacement"/>

	<xsl:choose>
		<xsl:when test="contains($string,$search)">

			<xsl:variable name="string_before">
				<xsl:value-of select="substring-before($string,$search)"/>
			</xsl:variable>

			<xsl:variable name="string_after">
				<xsl:value-of select="substring-after($string,$search)"/>
			</xsl:variable>
			
			<xsl:call-template name="string_replace">
				<xsl:with-param name="string">
					<xsl:choose>
						<xsl:when test="not(string($string_after))">
							<xsl:value-of select="$string_before"/>
						</xsl:when>
						<xsl:when test="not(string($string_before))">
							<xsl:value-of select="$string_after"/>
						</xsl:when>
						<xsl:otherwise>
							<xsl:value-of select="concat($string_before,$replacement,$string_after)"/>
						</xsl:otherwise>
					</xsl:choose>
				</xsl:with-param>
				<xsl:with-param name="search"><xsl:value-of select="$search"/></xsl:with-param>
				<xsl:with-param name="replacement"><xsl:value-of select="$replacement"/></xsl:with-param>
			</xsl:call-template>

		</xsl:when>
		<xsl:otherwise>
			<xsl:value-of select="$string"/>
		</xsl:otherwise>
	</xsl:choose>
</xsl:template>

</xsl:stylesheet>
