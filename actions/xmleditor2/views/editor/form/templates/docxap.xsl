<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
    <xsl:template name="docxap" match="docxap">
        <html xmlns="http://www.w3.org/1999/xhtml" xmlns:foaf="http://xmlns.com/foaf/0.1/">
            <head>
                <!--<link href="http://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,900,400italic,700italic" rel="stylesheet" type="text/css"/>
                <link href="http://fonts.googleapis.com/css?family=Coustard:400,900" rel="stylesheet" type="text/css"/>-->
                <link rel="stylesheet" type="text/css" href="@@URL_PATH@@/extensions/bootstrap/dist/css/bootstrap.min.css" />
                <link rel="stylesheet" type="text/css" media="screen" href="@@@RMximdex.dotdot(css/form.css)@@@"/>
                <link rel="stylesheet" type="text/css" href="views/editor/form/css/formview.css" />

                <title>Form View</title>
            </head>

            <body>
                <div uid="{@uid}" class="container form-view">
                    <xsl:if test="'##WARNING_ELEMENTS##' !=''">
                        <div class="alert alert-warning">
                            ##WARNINGS##
                        </div>
                    </xsl:if>
                    <xsl:apply-templates select="*[1]">
                        <xsl:with-param name="level" select="'1'"/>
                    </xsl:apply-templates>
                </div>
            </body>
        </html>
    </xsl:template>

    <xsl:template match="##CONTAINER_ELEMENTS##">
        <xsl:param name="level"/>
        <xsl:variable name="elementUid" select="substring-after(@uid,'.')"/>
        <xsl:variable name="newLevel" select="number($level) + 1"/>

        <div class="panel">
            <div uid="{@uid}" class="panel-heading">
                <p class="panel-title">
                    <a href="#collapse{$elementUid}" data-parent="#{$level}" data-toggle="collapse">
                        <xsl:value-of select="name()"/>
                    </a>
                </p>
            </div>

            <div id="collapse{$elementUid}" class="panel-body">
                <xsl:apply-templates select="*[1]">
                    <xsl:with-param name="level" select="$level"/>
                    <xsl:with-param name="parentType" select="'container'"/>
                </xsl:apply-templates>
            </div>
        </div>

        <xsl:apply-templates select="following-sibling::*[1]">
            <xsl:with-param name="level" select="$level"/>
            <xsl:with-param name="parentType" select="'container'"/>
        </xsl:apply-templates>
    </xsl:template>

    <xsl:template match="##BOLD_ELEMENTS##">
        <span uid="{@uid}" class="bold">
            <xsl:apply-templates />
        </span>
    </xsl:template>

    <xsl:template match="##ITALIC_ELEMENTS##">
        <span uid="{@uid}" class="italic">
            <xsl:apply-templates />
        </span>
    </xsl:template>

    <xsl:template match="##LINK_ELEMENTS##">
        <xsl:param name="parentType"/>

        <a href="" uid="{@uid}">
            <xsl:apply-templates />
        </a>

        <xsl:if test="$parentType='container'">
            <br/>
        </xsl:if>

        <xsl:apply-templates select="following-sibling::*[1]">
            <xsl:with-param name="parentType" select="'container'"/>
        </xsl:apply-templates>
    </xsl:template>

    <xsl:template match="##ITEM_ELEMENTS##">
        <li uid="{@uid}">
            <xsl:value-of select="."/>
        </li>
    </xsl:template>

    <xsl:template match="##INPUT_TEXT_ELEMENTS##">
        <xsl:variable name="elementUid" select="substring-after(@uid,'.')"/>

        <div class="form-group">
            <xsl:if test="count(preceding-sibling::*) = 0 or name(preceding-sibling::*[1]) != name(.)">
                <p><xsl:value-of select="name()"/></p>
            </xsl:if>

            <div class="input-group">
                <div uid="{@uid}" class="form-control" contentEditable="true">
                    <xsl:apply-templates select="text()"/>
                </div>

                <xsl:if test="count(following-sibling::*[1]) = 0 or name(following-sibling::*[1]) != name(.)">
                    <span class="input-group-btn">
                        <button class="btn js-add-more" type="button" data-uid="{$elementUid}">+</button>
                    </span>
                </xsl:if>
            </div>
        </div>

        <xsl:apply-templates select="following-sibling::*[1]"/>
    </xsl:template>

    <xsl:template match="##BLOCK_EDITION_ELEMENTS##">
        <xsl:variable name="elementUid" select="substring-after(@uid,'.')"/>
        <xsl:variable name="currentTagName" select="name(.)"/>

        <xsl:choose>
            <xsl:when test="contains('##BLOCK_EDITION_ELEMENTS##', name(preceding-sibling::*[1])) and count(preceding-sibling::*[1]) != 0">
                <xsl:choose>
                    <xsl:when test="contains('##TEXTAREA_ELEMENTS##',name())">
                        <p contentEditable="true" uid="{@uid}" class="form-control">
                            <xsl:apply-templates/>
                        </p>
                    </xsl:when>

                    <xsl:when test="contains('##IMAGE_ELEMENTS##',name())">
                        <xsl:variable name="src">
                            <xsl:choose>
                                <xsl:when test="@src and @src != ''">
                                    <xsl:choose>
                                        <xsl:when test="number(@src)=@src">
                                            <xsl:value-of select="concat('@@@RMximdex.pathto(',@src,')@@@')"/>
                                        </xsl:when>

                                        <xsl:otherwise>
                                            <xsl:value-of select="@src"/>
                                        </xsl:otherwise>
                                    </xsl:choose>
                                </xsl:when>

                                <xsl:when test="@url and @url != ''">
                                    <xsl:choose>
                                        <xsl:when test="number(@url)=@url">
                                            <xsl:value-of select="concat('@@@RMximdex.pathto(',@url,')@@@')"/>
                                        </xsl:when>

                                        <xsl:otherwise>
                                            <xsl:value-of select="@url"/>
                                        </xsl:otherwise>
                                    </xsl:choose>
                                </xsl:when>
                            </xsl:choose>
                        </xsl:variable>

                        <img uid="{@uid}" src="{$src}"/>
                    </xsl:when>

                    <xsl:when test="contains('##LIST_ELEMENTS##',name())">
                        <ul uid="{@uid}">
                            <xsl:apply-templates select="*"/>
                        </ul>
                    </xsl:when>

                    <xsl:otherwise>
                    </xsl:otherwise>
                </xsl:choose>
            </xsl:when>

            <xsl:otherwise>
                <div class="form-group">
                    <p><xsl:value-of select="name()"/></p>

                    <div class="btn-toolbar btn-toolbar-{$elementUid}" role="toolbar" aria-label="Toolbar" data-uid="{@uid}">
                        <div class="btn-group js-applies" role="group" aria-label="Texto">
                            <xsl:if test="not(contains('##BOLD_ELEMENTS##','@'))">
                                <button type="button" data-element="##BOLD_ELEMENTS##" class="btn btn-sm">
                                    <span class="glyphicon glyphicon-bold"></span>
                                </button>
                            </xsl:if>

                            <xsl:if test="not(contains('##ITALIC_ELEMENTS##','@'))">
                                <button type="button" data-element="##ITALIC_ELEMENTS##" class="btn btn-sm">
                                    <span class="glyphicon glyphicon-italic"></span>
                                </button>
                            </xsl:if>

                            <xsl:if test="not(contains('##LINK_ELEMENTS##','@'))">
                                <button type="button" data-element="##LINK_ELEMENTS##" class="btn btn-sm">
                                    <span class="glyphicon glyphicon-link"></span>
                                </button>
                            </xsl:if>
                        </div>

                        <div class="btn-group js-siblings" role="group" aria-label="Texto">
                            <xsl:if test="not(contains('##LIST_ELEMENTS##','@'))">
                                <button type="button" data-element="##LIST_ELEMENTS##" class="btn btn-sm">
                                    <span class="glyphicon glyphicon-list"></span>
                                </button>
                            </xsl:if>                                

                            <xsl:if test="not(contains('##IMAGE_ELEMENTS##','@'))">
                                <button type="button" data-element="##IMAGE_ELEMENTS##" class="btn btn-sm">
                                    <span class="glyphicon glyphicon-picture"></span>
                                </button>
                            </xsl:if>                            

                            <xsl:if test="not(contains('##LINK_ELEMENTS##','@'))">
                                <button type="button" class="btn btn-sm" data-element="##TEXTAREA_ELEMENTS##">
                                    <span class="glyphicon glyphicon-edit"></span>
                                </button>
                            </xsl:if>                            
                        </div>

                        <div class="btn-group js-extra" role="group" aria-label="Texto">
                            <button type="button" class="btn btn-sm">
                                <span class="glyphicon glyphicon-import"></span>
                            </button>
                        </div>
                    </div>

                    <div class="form-control editable_elements_block">
                        <xsl:choose>
                            <xsl:when test="contains('##TEXTAREA_ELEMENTS##',name())">
                                <p contentEditable="true" uid="{@uid}" class="form-control">
                                    <xsl:apply-templates/>
                                </p>
                            </xsl:when>

                            <xsl:when test="contains('##IMAGE_ELEMENTS##',name())">
                                <xsl:variable name="src">
                                    <xsl:choose>
                                        <xsl:when test="@src and @src != ''">
                                            <xsl:choose>
                                                <xsl:when test="number(@src)=@src">
                                                    <xsl:value-of select="concat('@@@RMximdex.pathto(',@src,')@@@')"/>
                                                </xsl:when>

                                                <xsl:otherwise>
                                                    <xsl:value-of select="@src"/>
                                                </xsl:otherwise>
                                            </xsl:choose>
                                        </xsl:when>

                                        <xsl:when test="@url and @url != ''">
                                            <xsl:choose>
                                                <xsl:when test="number(@url)=@url">
                                                    <xsl:value-of select="concat('@@@RMximdex.pathto(',@url,')@@@')"/>
                                                </xsl:when>

                                                <xsl:otherwise>
                                                    <xsl:value-of select="@url"/>
                                                </xsl:otherwise>
                                            </xsl:choose>
                                        </xsl:when>
                                    </xsl:choose>
                                </xsl:variable>

                                <img uid="{@uid}" src="{$src}"/>
                            </xsl:when>
                        </xsl:choose>

                        <xsl:variable name="currentPos" select="count(preceding-sibling::*)"/>

                        <xsl:variable name="posFirstDistinctTag">
                            <xsl:choose>
                                <xsl:when test="count(following-sibling::*[not(contains('##BLOCK_EDITION_ELEMENTS##',name()))]) != 0">
                                    <xsl:for-each select="following-sibling::*[not( contains('##BLOCK_EDITION_ELEMENTS##',name()))][1]">
                                        <xsl:value-of select="count(preceding-sibling::*)"/>
                                    </xsl:for-each>
                                </xsl:when>

                                <xsl:otherwise>
                                    <xsl:value-of select="0"/>
                                </xsl:otherwise>
                            </xsl:choose>
                        </xsl:variable>

                        <xsl:for-each select="following-sibling::*">
                            <xsl:choose>
                                <xsl:when test="$posFirstDistinctTag = 0">
                                    <xsl:apply-templates select="."/>
                                </xsl:when>

                                <xsl:otherwise>
                                    <xsl:variable name="nextSiblingPos" select="count(preceding-sibling::*)"/>
                                    <xsl:variable name="posDifference" select="$nextSiblingPos - $posFirstDistinctTag"/>
                                    <xsl:if test="starts-with($posDifference,'-')">
                                        <xsl:apply-templates select="."/>
                                    </xsl:if>
                                </xsl:otherwise>
                            </xsl:choose>
                        </xsl:for-each>
                    </div>
                </div>

                <xsl:apply-templates select="following-sibling::*[not(contains('##BLOCK_EDITION_ELEMENTS##',name()))][1]"/>
            </xsl:otherwise>
        </xsl:choose>

    </xsl:template>
</xsl:stylesheet>