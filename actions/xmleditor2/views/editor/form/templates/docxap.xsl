<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">     
    <xsl:template name="docxap" match="docxap">
    <html xmlns="http://www.w3.org/1999/xhtml" xmlns:foaf="http://xmlns.com/foaf/0.1/">
        <head>
        <link href="@@@RMximdex.dotdot(css/form.css)@@@" rel="stylesheet" type="text/css" media="screen"/>
        <!--<link href="http://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,900,400italic,700italic" rel="stylesheet" type="text/css"/>
        <link href="http://fonts.googleapis.com/css?family=Coustard:400,900" rel="stylesheet" type="text/css"/>-->
        <link rel="stylesheet" type="text/css" href="views/editor/form/css/formview.css" />
        <link rel="stylesheet" type="text/css" href="@@URL_PATH@@/extensions/bootstrap/dist/css/bootstrap.min.css" />
        <script type="text/javascript" src="@@URL_PATH@@/extensions/jquery/js/jquery-1.8.3.min.js" ></script>
        <script type="text/javascript" src="@@URL_PATH@@/extensions/jquery/js/jquery-ui-1.9.1.custom.min.js" ></script>
        <title></title>
        </head>
        <body class="formview">
        <div id="level1" class="block indentable" uid="{@uid}">                        
            <xsl:apply-templates select="*[1]">
            <xsl:with-param name="level" select="'1'"/>
            </xsl:apply-templates>
        </div>
        </body>                
    </html>
    </xsl:template>

    <xsl:template match="@@CONTAINER_ELEMENTS@@">        
    <xsl:param name="level"/>
    <xsl:variable name="elementUid" select="substring-after(@uid,'.')"/>
    <xsl:variable name="newLevel" select="number($level) + 1"/>
    <div class="panel panel-default form-input-box">
        <div class="panel-heading" uid="{@uid}">
        <h4 class="panel-title">
            <a href="#collapse{$elementUid}" data-parent="#{$level}" data-toggle="collapse">
            <xsl:value-of select="name()"/>
            </a>
        </h4>
        </div>
        <div class="panel-collapse" id="collapse{$elementUid}">
        <div class="panel-body">
            <xsl:apply-templates select="*[1]">
            <xsl:with-param name="level" select="$level"/>
            <xsl:with-param name="parentType" select="'container'"/>
            </xsl:apply-templates>
        </div>
        </div>
    </div>
    <xsl:apply-templates select="following-sibling::*[1]">
        <xsl:with-param name="level" select="$level"/>
        <xsl:with-param name="parentType" select="'container'"/>
    </xsl:apply-templates>
    </xsl:template>
                      
    <xsl:template match="@@INPUT_TEXT_ELEMENTS@@">
        <xsl:variable name="elementUid" select="substring-after(@uid,'.')"/>
        <div class="form-group ">
            <xsl:if test="count(preceding-sibling::*) = 0 or name(preceding-sibling::*[1]) != name(.)">
            <label for="exampleInputEmail1">
                <xsl:value-of select="name()"/>
            </label>
            </xsl:if>
            <div>
                <span contentEditable="true" class="input form-control xedit-rngelement" uid="{@uid}">
                    <xsl:apply-templates select="text()"/>
                </span>
                <xsl:if test="count(following-sibling::*[1]) = 0 or name(following-sibling::*[1]) != name(.)">
                <button type="button" class=" btn btn-default btn-sm js-add-more" style="color:green" data-uid="{$elementUid}">
                    <span class="glyphicon glyphicon-plus"></span>
                </button>   
                </xsl:if>   
            </div>
        </div>                      
        <xsl:apply-templates select="following-sibling::*[1]"/>
    </xsl:template>
    
    <xsl:template match="bold">
    <b uid="{@uid}">
        <xsl:apply-templates />
    </b>        
    </xsl:template>
    
    <xsl:template match="italic">
    <i uid="{@uid}">
        <xsl:apply-templates />
    </i>
    </xsl:template>
    
    <xsl:template match="link">
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
    
    <xsl:template match="@@BLOCK_EDITION_ELEMENTS@@">
    <xsl:variable name="elementUid" select="substring-after(@uid,'.')"/>
    <xsl:variable name="currentTagName" select="name(.)"/>
    <xsl:choose>
        <xsl:when test="contains('@@BLOCK_EDITION_ELEMENTS@@', name(preceding-sibling::*[1])) and count(preceding-sibling::*[1]) != 0">
        <xsl:choose>
            <xsl:when test="contains('@@TEXTAREA_ELEMENTS@@',name())">
            <p contentEditable="true" uid="{@uid}" class="textarea form-control xedit-rngelement">
                <xsl:apply-templates/>  
            </p>
            </xsl:when>
            <xsl:when test="contains('@@IMAGE_ELEMENTS@@',name())">
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
            <img uid="{@uid}" src="{$src}" title="" class="form_view"/>
            </xsl:when>
            <xsl:when test="contains('@@LIST_ELEMENTS@@',name())">
            <ul uid="{@uid}">
                <xsl:apply-templates select="*"/>
            </ul>
            </xsl:when>
            <xsl:otherwise>
            
            </xsl:otherwise>            
        </xsl:choose>       
        </xsl:when>
        <xsl:otherwise>
        <div class="form-group js-edition-block">
            <label for="exampleInputEmail1">
            <xsl:value-of select="name()"/>
            </label>
            <div class="btn-toolbar btn-toolbar-{$elementUid}" role="toolbar" data-uid="{@uid}">
            <div class="btn-group js-applies">
                <button type="button" data-element="bold" class="btn btn-default btn-xs disabled">
                <span class="glyphicon glyphicon-bold "></span>
                </button>
                <button type="button" data-element="italic" class="btn btn-default btn-xs disabled">
                <span class="glyphicon glyphicon-italic"></span>
                </button>
                <button type="button" data-element="link" class="btn btn-default btn-xs disabled">
                <span class="glyphicon glyphicon-link"></span>
                </button>
                
                
                
            </div>
            <div class="btn-group js-siblings">

                <button type="button" data-element="@@LIST_ELEMENTS@@" class="btn btn-default btn-xs ">
                <span class="glyphicon glyphicon-list"></span>
                </button>
                <button type="button" data-element="@@IMAGE_ELEMENTS@@" class="btn btn-default btn-xs ">
                <span class="glyphicon glyphicon-picture"></span>
                </button>
                <button type="button" class="btn btn-default btn-xs" data-element="parrafo">
                <span class="glyphicon glyphicon-edit"></span>
                </button>
            </div>
            
            <div class="btn-group js-extra">

                <button type="button" class="btn btn-default btn-xs ">
                <span class="glyphicon glyphicon-import"></span>
                </button>
            </div>      
                  
            </div>
            
            <div class="form-control editable_elements_block">
            <xsl:choose>
                <xsl:when test="contains('@@TEXTAREA_ELEMENTS@@',name())">
                <p contentEditable="true" uid="{@uid}" class="textarea form-control xedit-rngelement">
                    <!--<xsl:apply-templates/>  -->
                    <xsl:apply-templates/> 
                </p>
                </xsl:when>
                <xsl:when test="contains('@@IMAGE_ELEMENTS@@',name())">
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
                <img uid="{@uid}" src="{$src}" class="form_view" title=""/>
                </xsl:when>
            </xsl:choose>
            
            <xsl:variable name="currentPos" select="count(preceding-sibling::*)"/>          
            <xsl:variable name="posFirstDistinctTag">
                <xsl:choose>
                    <xsl:when test="count(following-sibling::*[not(contains('@@BLOCK_EDITION_ELEMENTS@@',name()))]) != 0">
                        <xsl:for-each select="following-sibling::*[not( contains('@@BLOCK_EDITION_ELEMENTS@@',name()))][1]">
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
                
        <xsl:apply-templates select="following-sibling::*[not(contains('@@BLOCK_EDITION_ELEMENTS@@',name()))][1]"/>
        </xsl:otherwise>
    </xsl:choose>
        
    </xsl:template>
    
    <xsl:template match="item">
    <li uid="{@uid}">
        <xsl:value-of select="."/>
    </li>
    </xsl:template>
    
</xsl:stylesheet>