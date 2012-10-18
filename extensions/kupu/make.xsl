<?xml version="1.0" encoding="utf-8"?>
<!--
##############################################################################
#
# Copyright (c) 2003-2005 Kupu Contributors. All rights reserved.
#
# This software is distributed under the terms of the Kupu
# License. See LICENSE.txt for license text. For a list of Kupu
# Contributors see CREDITS.txt.
#
##############################################################################

Generate an HTML template from Kupu distribution files

This XSLT is fed a Kupu distribution file (generally dist.kupu) which
contains:

  a) slot definitions,

  b) feature and part definitions,

  c) wiring that matches parts to slots,

  d) an order in which implementations are to be cascaded.

If the XSLT processor supports XInclude, the above stated items may of
course be located in different files and included later.

$Id: make.xsl 14422 2005-07-08 13:02:09Z duncan $
-->
<xsl:stylesheet
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:kupu="http://kupu.oscom.org/namespaces/dist"
    version="1.0"
    >

  <xsl:output
    method="xml"
    indent="yes"
    encoding="ascii"
    omit-xml-declaration="yes"
    />

  <xsl:strip-space
    elements="kupu:*"
    />

  <xsl:preserve-space
    elements="kupu:part"
    />

  <!-- ### Global parameters ### -->

  <!-- debug :: enables some debugging messages; by default false -->
  <xsl:param
    name="debug"
    select="false()"
    />


  <!-- ### Templates ### -->

  <!-- document element -->
  <xsl:template match="/kupu:dist">
    <xsl:apply-templates />
  </xsl:template>

  <!-- Ignore kupu stuff outside expand -->
  <xsl:template match="//kupu:*" />


  <!-- ## Expand mode templates ## -->

  <!-- Anything in kupu:expand runs in expand mode. This is where the
       whole things starts. -->
  <xsl:template match="//kupu:expand">
    <xsl:apply-templates mode="expand" />
  </xsl:template>

  <xsl:template match="//kupu:define-slot" mode="expand">
    <xsl:variable
      name="slot"
      select="@name"
      />

    <!-- Debug -->
    <xsl:if test="$debug">
      <xsl:comment>
        Slot named '<xsl:value-of select="$slot" />' defined.
      </xsl:comment>
    </xsl:if>

    <!-- We'll try to find a wiring that tells us what should go into
         our slot -->

    <xsl:call-template name="fill-slot">
      <xsl:with-param
        name="slot"
        select="$slot"
        />
    </xsl:call-template>

  </xsl:template>

  <!-- Named template that looks for an appropriate fill-slot element
       in a wiring. It recursively browses through implementations; that
       way wirings of different implementations cascade -->
  <xsl:template name="fill-slot">
    <xsl:param name="implno" select="1" />
    <xsl:param name="slot" />

    <xsl:variable
      name="impl"
      select="//kupu:implementation-order/kupu:implementation[$implno]/@name"
      />

    <xsl:variable
      name="fillnode"
      select="//kupu:wire[@implementation=$impl]/kupu:fill-slot[@name=$slot]"
      />

    <xsl:choose>
      <!-- if we've found a valid implementation, go for it -->
      <xsl:when test="$fillnode">
        <!-- Debug -->
        <xsl:if test="$debug">
          <xsl:comment>
            Found wiring for slot '<xsl:value-of select="$slot" />',
            at implementation no. <xsl:value-of select="$implno" />,
            '<xsl:value-of select="$impl" />'.
          </xsl:comment>
        </xsl:if>
        <xsl:apply-templates select="$fillnode" mode="expand" />
      </xsl:when>
      <xsl:otherwise>
        <!-- Cascade onto the next implementation under two circumstances:
             a) A specific implementation wasn't request: not(@implementation)
             b) We're already in the last implementation -->
        <xsl:choose>          
        <xsl:when test="$implno &lt;= count(//kupu:implementation-order/kupu:implementation)">
         <xsl:call-template name="fill-slot">
            <xsl:with-param name="implno" select="$implno+1" />
            <xsl:with-param name="slot" select="$slot" />
          </xsl:call-template>
        </xsl:when>
        <xsl:otherwise>
          <xsl:comment>
            Cannot find wiring for slot '<xsl:value-of select="$slot" />'.
          </xsl:comment>
        </xsl:otherwise>
        </xsl:choose>
      </xsl:otherwise>
    </xsl:choose>

  </xsl:template>

  <xsl:template match="//kupu:insert-ids" mode="expand">
     <xsl:apply-templates select="//kupu:id" mode="expand" />
  </xsl:template>
  <xsl:template match="//kupu:id" mode="expand">
    <xsl:comment><xsl:value-of select="." /></xsl:comment>
    <xsl:text>
    </xsl:text>

  </xsl:template>

  <!-- Handle part insertion; we delegate the work to the named
       template below -->
  <xsl:template match="//kupu:insert-part" mode="expand">
    <xsl:variable
      name="feature"
      select="@feature"
      />
    <xsl:variable
      name="part"
      select="@part"
      />
    <xsl:choose>
      <xsl:when test="//kupu:disable-feature[@name=$feature]">
        <xsl:if test="$debug">
          <xsl:comment>
            Feature '<xsl:value-of select="$feature" />' was disabled.
          </xsl:comment>
        </xsl:if>
      </xsl:when>
      <xsl:when test="//kupu:disable-part[@feature=$feature and @part=$part]">
        <xsl:if test="$debug">
          <xsl:comment>
            Part '<xsl:value-of select="$part" />' in feature
            '<xsl:value-of select="$feature" />' was disabled.
          </xsl:comment>
        </xsl:if>
      </xsl:when>
      <xsl:otherwise>
        <xsl:call-template name="insert-part">
          <xsl:with-param
            name="feature"
            select="$feature"
            />
          <xsl:with-param
            name="part"
            select="$part"
            />
        </xsl:call-template>
      </xsl:otherwise>
    </xsl:choose>
  </xsl:template>

  <!-- This template recursively looks for feature/part
       implementations and inserts the first it finds -->
  <xsl:template name="insert-part">
    <xsl:param name="implno" select="1" />
    <xsl:param name="feature" />
    <xsl:param name="part" />

    <!-- The caller can provide us with a specific implementation
         name; if not provided, fall back to the implementation given
         in 'implno'. -->
    <xsl:param name="implementation" />

    <xsl:variable name="impl">
      <xsl:choose>
        <xsl:when test="$implementation">
          <xsl:value-of select="$implementation" />
        </xsl:when>
        <xsl:otherwise>
          <xsl:value-of
            select="//kupu:implementation-order/kupu:implementation[$implno]/@name"
            />
        </xsl:otherwise>
      </xsl:choose>
    </xsl:variable>

    <xsl:variable
      name="partnode"
      select="//kupu:feature[@name=$feature and @implementation=$impl]/kupu:part[@name=$part]"
      />

    <xsl:choose>
      <!-- if we've found a valid implementation, go for it -->
      <xsl:when test="$partnode">
        <!-- Debug -->
        <xsl:if test="$debug">
          <xsl:comment>
            Found feature '<xsl:value-of select="$feature" />',
            part '<xsl:value-of select="$part" />' at implementation no.
            <xsl:value-of select="$implno" />, '<xsl:value-of select="$impl" />'.
          </xsl:comment>
        </xsl:if>
        <xsl:apply-templates select="$partnode" mode="expand" />
      </xsl:when>
      <xsl:otherwise>
        <!-- Cascade onto the next implementation under two circumstances:
             a) A specific implementation wasn't request: not(@implementation)
             b) We're already in the last implementation -->
        <xsl:choose>          
        <xsl:when test="not($implementation) and $implno &lt;= count(//kupu:implementation-order/kupu:implementation)">
         <xsl:call-template name="insert-part">
            <xsl:with-param name="implno" select="$implno+1" />
            <xsl:with-param name="feature" select="$feature" />
            <xsl:with-param name="part" select="$part" />
          </xsl:call-template>
        </xsl:when>
        <xsl:otherwise>
          <xsl:comment>
            Cannot find feature '<xsl:value-of select="$feature" />',
            part '<xsl:value-of select="$part" />'.
          </xsl:comment>
        </xsl:otherwise>
        </xsl:choose>
      </xsl:otherwise>
    </xsl:choose>
  </xsl:template>

  <!-- Make sure that we stay in expand mode once we are in it -->
  <xsl:template match="//kupu:*" mode="expand">
    <xsl:apply-templates mode="expand"/>
  </xsl:template>

  <!-- Display other tags (XHTML) verbatim in expand mode -->
  <xsl:template match="*" mode="expand">
    <xsl:copy>
      <xsl:copy-of select="@*" />
      <xsl:apply-templates mode="expand" />
    </xsl:copy>
  </xsl:template>

  <!-- Copy nodes through verbatim, but omit the id attribute
       from most of them -->
<!--  <xsl:template match="*" mode="expand">
    <xsl:choose>
     <xsl:when test="local-name()='xml' or local-name='iframe'">
        <xsl:copy>
          <xsl:copy-of select="@*" />
          <xsl:apply-templates mode="expand" />
        </xsl:copy>
     </xsl:when>
     <xsl:otherwise>
        <xsl:copy>
          <xsl:copy-of select="@*[local-name() != 'id']" />
          <xsl:apply-templates mode="expand" />
        </xsl:copy>
     </xsl:otherwise>
    </xsl:choose>
  </xsl:template> -->

</xsl:stylesheet>
