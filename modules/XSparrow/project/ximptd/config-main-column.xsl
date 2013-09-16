<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
                      	<xsl:template name="config-main-column" match="config-main-column">
                            	<xsl:variable name="leftColumnWidth">
                                  <xsl:choose>
                                    <xsl:when test="//config-container[@left_column]/@left_column='Yes'">
                                    	2
                                    </xsl:when>
                                    <xsl:otherwise>
                                      0
                                    </xsl:otherwise>
                                   </xsl:choose>
                            	</xsl:variable>
                            	<xsl:variable name="rightColumnWidth">
                            		<xsl:choose>
                                    <xsl:when test="//config-container[@right_column]/@right_column='Yes'">
                                    	2
                                     </xsl:when>
                                    <xsl:otherwise>
                                      0
                                    </xsl:otherwise>
                                   </xsl:choose>
                            	</xsl:variable>

                            	<xsl:variable name="mainColumnWidth">
                            		<xsl:value-of select="12 - $leftColumnWidth - $rightColumnWidth"/>
                            	</xsl:variable>

                            	<xsl:choose>
                      			<xsl:when test="/docxap/@tipo_documento='rng-configuracion.xml'">

                                               <div class="main span{$mainColumnWidth}">
                    		    <div class="page-header">
                                       <xsl:apply-templates select="config-title-element"/>
                    		    </div>
                    		      <xsl:apply-templates select="config-subtitle-element"/>
                    		    <div class="row-fluid">
                                      <p class="offset1 span12 pull-right">Este ximlet sirve para <xsl:apply-templates select="config-link-element"/> configurar la <strong>apariencia</strong> de los documentos del portal. Desde aquí se pueden modificar determinados elementos generales del portal. En concreto:</p>
                 			<dl class="">
                 				<dt>Configuración general</dt>
                 				<dd>Desde la capa CONFIG situada en la parte superior del ximlet se puede modificar el <strong>Color principal</strong> del portal (el del fondo), <strong>Color secundario</strong> y el color de la fuente</dd>
                 				<dt>Cabecera del portal</dt>
                 				<dd>Se puede elegir la imagen principal y alinearla a derecha o izquierda. Además es configurable el texto y color del título y subtítulo</dd>
                 				<dt>Estructura central</dt>
                 				<dd>Las 2 columnas a los lados del cuerpo central son opcionales. El contenido ya lo editarás en cada una de las páginas</dd>
                 			</dl>
                    			</div>
                    		    <ul class="thumbnails row">
                    			<li class="span6">
                    			    <a class="thumbnail" href="#">
                                                  <img alt="" src="http://placehold.it/360x268"/>
                    			    </a>
                    			</li>
                    			<li class="span3">
                    			    <a class="thumbnail" href="#">
                    				<img alt="" src="http://placehold.it/160x120"/>
                    			    </a>
                    			</li>
                    			<li class="span2">
                    			    <a class="thumbnail" href="#">
                    				<img alt="" src="http://placehold.it/160x120"/>
                    			    </a>
                    			</li>
                    			<li class="span2">
                    			    <a class="thumbnail" href="#">
                    				<img alt="" src="http://placehold.it/160x120"/>
                    			    </a>
                    			</li>
                    			<li class="span2">
                    			    <a class="thumbnail" href="#">
                    				<img alt="" src="http://placehold.it/160x120"/>
                    			    </a>
                    			</li>
                    			<li class="span2">
                    			    <a class="thumbnail" href="#">
                    				<img alt="" src="http://placehold.it/160x120"/>
                    			    </a>
                    			</li>
                    		    </ul>
                    		</div>
                      			</xsl:when>
                               		<xsl:when test="/docxap/@tipo_documento='rng-bootstrap-based.xml'">
                                                <xsl:apply-templates select="/docxap/content">
                                                  <xsl:with-param name="paramMainColumnWidth" select="$mainColumnWidth"/>
                                                 </xsl:apply-templates>
                               		</xsl:when>
                      			<xsl:otherwise>
                      							<!-- TODO -->
                      			</xsl:otherwise>
                      		</xsl:choose>
                      	</xsl:template>
</xsl:stylesheet>