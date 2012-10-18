<?xml version="1.0" encoding="utf-8" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<!--
	dexT to XSLT translation: from dext variables to top-level xslt variables
-->

<xsl:template name="dext_variables">

	<xsl:call-template name="default_variables"/>
<!--	<xsl:call-template name="global_variables">
		<xsl:with-param name="dext_vars" select="$vars_list"/>
	</xsl:call-template>-->

</xsl:template>

<!--
	default variables translation
-->

<xsl:template name="default_variables">
	<xsl:for-each select="//@*">
			<xsl:if test="contains(local-name(.),'-default')">
				<xsl:call-template name="top_level_var">
					<xsl:with-param name="variable_name" select="substring-before(local-name(.),'-default')"/>
					<xsl:with-param name="variable_value" select="."/>
				</xsl:call-template>
			</xsl:if>
	</xsl:for-each>
</xsl:template>

<!--
	global variables translation
-->

<xsl:template name="global_variables">
	<xsl:param name="dext_vars"/>

	<xsl:variable name="left_var">
		<xsl:choose>
			<xsl:when test="string(substring-before($dext_vars,','))">
				<xsl:value-of select="substring-before($dext_vars,',')"/>
			</xsl:when>
			<xsl:otherwise>
				<xsl:value-of select="$dext_vars"/>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:variable>

	<xsl:variable name="right_var" select="substring-after($dext_vars,',')"/>

	<xsl:if test="string($left_var)">
		<xsl:call-template name="top_level_var">
			<xsl:with-param name="variable_name" select="$left_var"/>
			<xsl:with-param name="variable_value">
				<xsl:value-of select="concat('ancestor::node()//attribute::',$left_var)"/>
			</xsl:with-param>
		</xsl:call-template>
	</xsl:if>
	
	<xsl:if test="string($right_var)">
		<xsl:call-template name="global_variables">
			<xsl:with-param name="dext_vars" select="$right_var"/>
		</xsl:call-template>
	</xsl:if>
</xsl:template>

<!--
	the top-level xslt variable
-->

<xsl:template name="top_level_var">
	<xsl:param name="variable_name"/>
	<xsl:param name="variable_value"/>

	<xsl:if test="string($variable_name)">
		<xsl:element name="xsl:variable">
			<xsl:attribute name="name">
				<xsl:value-of select="$variable_name"/>
			</xsl:attribute>

			<xsl:element name="xsl:value-of">
				<xsl:attribute name="select">
					<xsl:value-of select="concat('@',$variable_name)"/>
				</xsl:attribute>
			</xsl:element>

			<xsl:element name="xsl:if">
				<xsl:attribute name="test">
					<xsl:value-of select="concat('not(string(@',$variable_name,'))')"/>
				</xsl:attribute>
				
				<xsl:element name="xsl:value-of">
					<xsl:attribute name="select">
					
						<xsl:variable name="variable_value2">
							<xsl:call-template name="string_replace">
								<xsl:with-param name="string" select="$variable_value"/>
								<xsl:with-param name="search" select="'{'"/>
								<xsl:with-param name="replacement" select="''"/>
							</xsl:call-template>
						</xsl:variable>

						<xsl:variable name="variable_value3">
							<xsl:call-template name="string_replace">
								<xsl:with-param name="string" select="$variable_value2"/>
								<xsl:with-param name="search" select="'}'"/>
								<xsl:with-param name="replacement" select="''"/>
							</xsl:call-template>
						</xsl:variable>

						<xsl:variable name="variable_value4">
							<xsl:choose>
								<xsl:when test="not(contains($variable_value3,'@'))">
									<xsl:value-of select='concat("&#39;", $variable_value3 ,"&#39;")'/>
								</xsl:when>
								<xsl:otherwise>
									<xsl:value-of select="$variable_value3"/>
								</xsl:otherwise>
							</xsl:choose>
						</xsl:variable>

						<xsl:value-of select="$variable_value4"/>
					</xsl:attribute>
				</xsl:element>
			</xsl:element>
		</xsl:element>
	</xsl:if>
</xsl:template>

</xsl:stylesheet>
