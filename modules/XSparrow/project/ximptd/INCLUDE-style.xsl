<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
                            	<xsl:template name="INCLUDE-style" match="INCLUDE-style">
                            	
                            	<!--Config-->
                         		<xsl:variable name="color1">
                         			<xsl:choose>
                         				<xsl:when test="not (//config[@background-color]/@background-color) or //config[@background-color]/@background-color = ''">
                         					transparent
                         				</xsl:when>
                         				<xsl:otherwise>
                         					<xsl:value-of select="//config[@background-color]/@background-color"/>
                         				</xsl:otherwise>
                         			</xsl:choose>
                         		</xsl:variable>
                                         
                                         
                                   
                                   <xsl:variable name="backgroundImage">
                         			<xsl:choose>
                         				<xsl:when test="not (//config[@background-image]/@background-image) or //config[@background-image]/@background-image = ''">
                         					<xsl:text>none</xsl:text>
                         			</xsl:when>
                         			<xsl:otherwise>
                         				url(<xsl:value-of select="//config[@background-image]/@background-image"/>)
                         			</xsl:otherwise>
                         		</xsl:choose>
                         	</xsl:variable>
                                    
                                  <xsl:variable name="backgroundPosition">
                         			<xsl:choose>
                         				<xsl:when test="not (//config[@background-position]/@background-position) or //config[@background-position]/@background-position = ''">
                         					<xsl:text>none</xsl:text>
                         			</xsl:when>
                         			<xsl:otherwise>                         					
                         <xsl:value-of select="//config[@background-position]/@background-position"/>
                         			</xsl:otherwise>
                         		</xsl:choose>
                         	</xsl:variable>
                               <xsl:variable name="backgroundRepeat">
                         		<xsl:choose>
                         			<xsl:when test="not (//config[@background-repeat]/@background-position) or //config[@background-repeat]/@background-repeat = ''">
                         				<xsl:text>no-repeat</xsl:text>
                         			</xsl:when>
                         			<xsl:otherwise>
                         <xsl:value-of select="//config[@background-repeat]/@background-repeat"/>
                         			</xsl:otherwise>
                         		</xsl:choose>
                         	</xsl:variable>
                               
                                 
                               
                         	<xsl:variable name="color2">
                         		<xsl:choose>
                         			<xsl:when test="not (//config[@secundary-color]/@secundary-color)         or //config[@secundary-color]/@secundary-color = ''">
                         				transparent
                         			</xsl:when>
                         			<xsl:otherwise>
                         				<xsl:value-of select="//config[@secundary-color]/@secundary-color"/>
                         			</xsl:otherwise>
                         		</xsl:choose>
                         	</xsl:variable>
                         	<xsl:variable name="fontcolor">
                         		<xsl:choose>
                         			<xsl:when test="not (//config[@font-color]/@font-color) or //config[@font-color]/@font-color = ''">
                         				#000000
                         			</xsl:when>
                         			<xsl:otherwise>
                         				<xsl:value-of select="//config[@font-color]/@font-color"/>
                         			</xsl:otherwise>
                         		</xsl:choose>
                         	</xsl:variable>
                           
                           	<!--Enf of Config -->
                           	                       	
                           	
                           	<!-- Container -->
                           	
                           	<xsl:variable name="ContainerBackgroundColor">
                         			<xsl:choose>
                         				<xsl:when test="not (//config-container[@background-color]/@background-color) or //config-container[@background-color]/@background-color = ''">
                         					transparent
                         				</xsl:when>
                         				<xsl:otherwise>
                         					<xsl:value-of select="//config-container[@background-color]/@background-color"/>
                         				</xsl:otherwise>
                         			</xsl:choose>
                         		</xsl:variable>
                                         
                                         
                                   
                                   <xsl:variable name="ContainerBackgroundImage">
                         			<xsl:choose>
                         				<xsl:when test="not (//config-container[@background-image]/@background-image) or //config-container[@background-image]/@background-image = ''">
                         					<xsl:text>none</xsl:text>
                         			</xsl:when>
                         			<xsl:otherwise>
                         				url(<xsl:value-of select="//config-container[@background-image]/@background-image"/>)
                         			</xsl:otherwise>
                         		</xsl:choose>
                         	</xsl:variable>
                                    
                                  <xsl:variable name="ContainerBackgroundPosition">
                         			<xsl:choose>
                         				<xsl:when test="not (//config-container[@background-position]/@background-position) or //config-container[@background-position]/@background-position = ''">
                         					<xsl:text>none</xsl:text>
                         			</xsl:when>
                         			<xsl:otherwise>                         					
                         <xsl:value-of select="//config-container[@background-position]/@background-position"/>
                         			</xsl:otherwise>
                         		</xsl:choose>
                         	</xsl:variable>
                               <xsl:variable name="ContainerBackgroundRepeat">
                         		<xsl:choose>
                         			<xsl:when test="not (//config-container[@background-repeat]/@background-position) or //config-container[@background-repeat]/@background-repeat = ''">
                         				<xsl:text>no-repeat</xsl:text>
                         			</xsl:when>
                         			<xsl:otherwise>
                         <xsl:value-of select="//config-container[@background-repeat]/@background-repeat"/>
                         			</xsl:otherwise>
                         		</xsl:choose>
                         	</xsl:variable>
                           	
                           	
                           	<!-- End of container -->
                           
                         	<xsl:variable name="headerBorder">
                         	 <xsl:choose>
                         		 <xsl:when test="not (//config-header[@border]/@border) or                                //config-header[@border]/@border='' or                                //config-header[@border]/@border='None'">
                                                     <xsl:text>border-width:0px</xsl:text>		     
                         		 </xsl:when>
                                  <xsl:when test="//config-header[@border]/@border='All'">
                                         <xsl:text>border-width:1px</xsl:text>
                                  </xsl:when>
                                  <xsl:when test="contains(//config-header[@border]/@border,'Top')">
                                         <xsl:text>border-width:1px 0px 1px 0px</xsl:text>
                                  </xsl:when>
                                   <xsl:when test="contains(//config-header[@border]/@border,'Left')">
                                         <xsl:text>border-width:0px 1px 0px 1px</xsl:text>
                                  </xsl:when>
                                  
                         		 <xsl:otherwise>
                                         <xsl:text>border-width:1px</xsl:text>
                         		 </xsl:otherwise>
                         	 </xsl:choose>
                         	</xsl:variable>
                            
                           <xsl:variable name="headerBorderColor">
                         	 <xsl:choose>
                         		 <xsl:when test="not (//config-header[@border-color]/@border-color) or //config-header[@border-color]/@border-color=''">
                         		 </xsl:when>
                         		 <xsl:otherwise>
                         		    <xsl:text>border: solid</xsl:text> <xsl:value-of select="//config-header[@border-color]/@border-color"/>
                                        <xsl:text>;</xsl:text>
                         		 </xsl:otherwise>
                         	 </xsl:choose>
                         	</xsl:variable> 
                          
                                
                           
                           <xsl:variable name="titleAlign">
                         	 <xsl:choose>
                         		 <xsl:when test="not (//config-title[@align]/@align) or   //config-title[@align]/@align='' or    //config-title[@align]/@align='Left'">
                                                     <xsl:text>text-align:left</xsl:text>		     
                         		 </xsl:when>
                                  <xsl:when test="//config-title[@align]/@align='Center'">
                                         <xsl:text>text-align:center</xsl:text>
                                  </xsl:when>
                                  <xsl:when test="config-title[@align]/@align='Right'">
                                         <xsl:text>text-align:right</xsl:text>
                                  </xsl:when>
                                 
                         		 <xsl:otherwise>
                                         <xsl:text>text-align:left</xsl:text>
                         		 </xsl:otherwise>
                         	 </xsl:choose>
                         	</xsl:variable>
                                
                                
                           <xsl:variable name="headerTitleFontColor">
                         		<xsl:choose>
                         			<xsl:when test="not (//config-header-title[@font-color]/@font-color) or //config-header-title[@font-color]/@font-color = ''">
                         				#000000
                         			</xsl:when>
                         			<xsl:otherwise>
                         				<xsl:value-of select="//config-header-title[@font-color]/@font-color"/>
                         			</xsl:otherwise>
                         		</xsl:choose>
                         	</xsl:variable>
                               <xsl:variable name="headerTitleFontSize">
                         		<xsl:choose>
                         			<xsl:when test="not (//config-header-title[@font-size]/@font-size) or //config-header-title[@font-size]/@font-size = ''">
                                                 <xsl:text>42px</xsl:text>
                         			</xsl:when>
                         			<xsl:otherwise>
                         <xsl:value-of select="//config-header-title[@font-size]/@font-size"/>
                         			</xsl:otherwise>
                         		</xsl:choose>
                         	</xsl:variable>
                                      
                                      
                           <xsl:variable name="headerTitleAlign">
                         		<xsl:choose>
                         			<xsl:when test="not (//config-header-title[@align]/@align) or //config-header-title[@align]/@align = ''">
                                              <xsl:text>left</xsl:text>
                         			</xsl:when>
                         			<xsl:otherwise>
                         				<xsl:value-of select="//config-header-title[@align]/@align"/>
                         			</xsl:otherwise>
                         		</xsl:choose>
                         	</xsl:variable>
                                      
                                      
                                     <xsl:variable name="headerSubtitleFontColor">
                         		<xsl:choose>
                         			<xsl:when test="not (//config-header-subtitle[@font-color]/@font-color) or //config-header-subtitle[@font-color]/@font-color = ''">
                         				#000000
                         			</xsl:when>
                         			<xsl:otherwise>
                         				<xsl:value-of select="//config-header-subtitle[@font-color]/@font-color"/>
                         			</xsl:otherwise>
                         		</xsl:choose>
                         	</xsl:variable>
                               <xsl:variable name="headerSubtitleFontSize">
                         		<xsl:choose>
                         			<xsl:when test="not (//config-header-subtitle[@font-size]/@font-size) or //config-header-subtitle[@font-size]/@font-size = ''">
                                                 <xsl:text>42px</xsl:text>
                         			</xsl:when>
                         			<xsl:otherwise>
                         <xsl:value-of select="//config-header-subtitle[@font-size]/@font-size"/>
                         			</xsl:otherwise>
                         		</xsl:choose>
                         	</xsl:variable>
                                      
                            
                           <xsl:variable name="headerSubtitleAlign">
                         		<xsl:choose>
                         			<xsl:when test="not (//config-header-subtitle[@align]/@align) or //config-header-subtitle[@align]/@align = ''">
                                              <xsl:text>left</xsl:text>
                         			</xsl:when>
                         			<xsl:otherwise>
                         				<xsl:value-of select="//config-header-subtitle[@align]/@align"/>
                         			</xsl:otherwise>
                         		</xsl:choose>
                         	</xsl:variable>                  
                                      
                                      
                         	<xsl:variable name="breadcrumb-background">
                         		<xsl:choose>
                         			<xsl:when test="not (//config-breadcrumb[@background-color]/@background-color)       or //config-breadcrumb[@background-color]/@background-color = ''">
                         				#ffffff
                         			</xsl:when>
                         			<xsl:otherwise>
                         				<xsl:value-of select="//config-breadcrumb[@background-color]/@background-color"/>
                         			</xsl:otherwise>
                         		</xsl:choose>
                         	</xsl:variable>
                         	<xsl:variable name="breadcrumb-fontcolor">
                         		<xsl:choose>
                         			<xsl:when test="not (//config-breadcrumb[@font-color]/@font-color)       or //config-breadcrumb[@font-color]/@font-color = ''">
                         				#000000
                         			</xsl:when>
                         			<xsl:otherwise>
                         				<xsl:value-of select="//config-breadcrumb[@font-color]/@font-color"/>
                         			</xsl:otherwise>
                         		</xsl:choose>
                         	</xsl:variable>
                         	<xsl:variable name="breadcrumb-current-fontcolor">
                         		<xsl:choose>
                         			<xsl:when test="not (//config-breadcrumb[@current-font-color]/@current-font-color)              or //config-breadcrumb[@current-font-color]/@current-font-color = ''">
                         				#ffffff
                         			</xsl:when>
                         			<xsl:otherwise>
                         				<xsl:value-of select="//config-breadcrumb[@current-font-color]/@current-font-color"/>
                         			</xsl:otherwise>
                         		</xsl:choose>
                         	</xsl:variable>
                         	<xsl:variable name="bodyMargin">
                         		<xsl:choose>
                                               <xsl:when test="/docxap/ximlet/menu">
                                                 <xsl:text>40</xsl:text>
                         			</xsl:when>
                         			<xsl:otherwise>
                                                     <xsl:text>0</xsl:text>
                         			</xsl:otherwise>
                         		</xsl:choose>
                         	</xsl:variable>         
                 			<xsl:variable name="MenuBackgroundColor">
                         			<xsl:choose>
                         				<xsl:when test="not (//menu[@background-color]/@background-color) or //menu[@background-color]/@background-color = ''">
                         					transparent
                         				</xsl:when>
                         				<xsl:otherwise>
                         					<xsl:value-of select="//menu[@background-color]/@background-color"/>
                         				</xsl:otherwise>
                         			</xsl:choose>
                         		</xsl:variable>
                 			<xsl:variable name="MenuFontColor">
                         			<xsl:choose>
                         				<xsl:when test="not (//menu[@font-color]/@font-color) or //menu[@font-color]/@font-color = ''">
                         					transparent
                         				</xsl:when>
                         				<xsl:otherwise>
                         					<xsl:value-of select="//menu[@font-color]/@font-color"/>
                         				</xsl:otherwise>
                         			</xsl:choose>
                         		</xsl:variable>
                               <xsl:variable name="MenuHoverFontColor">
                         			<xsl:choose>
                         				<xsl:when test="not (//menu[@font-color-hover]/@font-color-hover) or //menu[@font-color-hover]/@font-color-hover = ''">
                         					transparent
                         				</xsl:when>
                         				<xsl:otherwise>
                         					<xsl:value-of select="//menu[@font-color-hover]/@font-color-hover"/>
                         				</xsl:otherwise>
                         			</xsl:choose>
                         		</xsl:variable>
                         		
                         		
                         		
                         		
                         		<!-- H2 Elements -->
                         		 <xsl:variable name="H2FontColor">
                         			<xsl:choose>
                         				<xsl:when test="not (//config-title-element[@font-color]/@font-color) or                                           //config-title-element[@font-color]/@font-color = ''">
                         					<xsl:value-of select="'auto'"/>
                         				</xsl:when>
                         				<xsl:otherwise>
                         					<xsl:value-of select="//config-title-element[@font-color]/@font-color"/>
                         				</xsl:otherwise>
                         			</xsl:choose>
                         		</xsl:variable>
                         		
                         		 <xsl:variable name="H2FontSize">
                         			<xsl:choose>
                         				<xsl:when test="not (//config-title-element[@font-size]/@font-size) or                                           //config-title-element[@font-size]/@font-size = ''">
                         					<xsl:value-of select="'auto'"/>
                         				</xsl:when>
                         				<xsl:otherwise>
                         					<xsl:value-of select="//config-title-element[@font-size]/@font-size"/>
                         				</xsl:otherwise>
                         			</xsl:choose>
                         		</xsl:variable>
         						
         						
         						<xsl:variable name="H2BorderBottomSize">
                         			<xsl:value-of select="//config-title-element[@border-size]/@border-size"/>
                         		</xsl:variable>   
         						
         						
         						<xsl:variable name="H2BorderBottom">                    			
         									<xsl:text>border-bottom:</xsl:text> <xsl:value-of select="//config-title-element[@border-size]/@border-size"/>
      <xsl:value-of select="' '"/>  <xsl:value-of select="//config-title-element[@border-bottom]/@border-bottom"/> 
         									<xsl:text>;text-decoration:none;</xsl:text>                    			
                         		</xsl:variable>                		
                         		<!-- END OF H2 Elements -->
                         		
                         		
                         		<!-- H3 Elements -->
                         		 <xsl:variable name="H3FontColor">
                         			<xsl:choose>
                         				<xsl:when test="not (//config-subtitle-element[@font-color]/@font-color) or                                           //config-subtitle-element[@font-color]/@font-color = ''">
                         					<xsl:value-of select="'auto'"/>
                         				</xsl:when>
                         				<xsl:otherwise>
                         					<xsl:value-of select="//config-subtitle-element[@font-color]/@font-color"/>
                         				</xsl:otherwise>
                         			</xsl:choose>
                         		</xsl:variable>
                         		
                         		 <xsl:variable name="H3FontSize">
                         			<xsl:choose>
                         				<xsl:when test="not (//config-subtitle-element[@font-size]/@font-size) or                                           //config-subtitle-element[@font-size]/@font-size = ''">
                         					<xsl:value-of select="'auto'"/>
                         				</xsl:when>
                         				<xsl:otherwise>
                         					<xsl:value-of select="//config-subtitle-element[@font-size]/@font-size"/>
                         				</xsl:otherwise>
                         			</xsl:choose>
                         		</xsl:variable>
         						
         						
         						<xsl:variable name="H3BorderBottomSize">
                         			<xsl:value-of select="//config-subtitle-element[@border-size]/@border-size"/>
                         		</xsl:variable>   
         						
         						
                                     <xsl:variable name="H3BorderBottom">
                                         <xsl:text>border-bottom:</xsl:text> <xsl:value-of select="//config-subtitle-element[@border-size]/@border-size"/> <xsl:value-of select="' '"/> <xsl:value-of select="//config-subtitle-element[@border-bottom]/@border-bottom"/> 
                                         <xsl:text>;text-decoration:none;</xsl:text>
                         		</xsl:variable>                		
                         		<!-- END OF H3 Elements -->
                         		
                         		
                         		<!-- Link Elements -->
                         		 <xsl:variable name="LinkFontColor">
                         			<xsl:choose>
                         				<xsl:when test="not (//config-link-element[@font-color]/@font-color) or                                           //config-link-element[@font-color]/@font-color = ''">
                         					<xsl:value-of select="'auto'"/>
                         				</xsl:when>
                         				<xsl:otherwise>
                         					<xsl:value-of select="//config-link-element[@font-color]/@font-color"/>
                         				</xsl:otherwise>
                         			</xsl:choose>
                         		</xsl:variable>
                         		
                         		 <xsl:variable name="LinkFontColorHover">
                         			<xsl:choose>
                         				<xsl:when test="not (//config-link-element[@font-color-hover]/@font-color-hover) or                                           //config-link-element[@font-color-hover]/@font-color-hover = ''">
                         					<xsl:value-of select="'auto'"/>
                         				</xsl:when>
                         				<xsl:otherwise>
                         					<xsl:value-of select="//config-link-element[@font-color-hover]/@font-color-hover"/>
                         				</xsl:otherwise>
                         			</xsl:choose>
                         		</xsl:variable>
         						
         						<xsl:variable name="LinkBorderBottom">
                         			<xsl:choose>
                         				<xsl:when test="//config-link-element[@border-bottom]/@border-bottom = 'solid'">
         									<xsl:text>text-decoration:underline;</xsl:text>								
                         				</xsl:when>
                         				<xsl:otherwise>
         									<xsl:text>border-bottom:</xsl:text> <xsl:value-of select="//config-link-element[@border-bottom]/@border-bottom"/>
      <xsl:value-of select="' '"/> 1px; 
         									<xsl:text>text-decoration:none;</xsl:text>
                         				</xsl:otherwise>
                         			</xsl:choose>
                         		</xsl:variable>                		
                         		<!-- END OF H3 Elements -->
                         		
                        
                         <!--Aqui comienza el css-->          
                         	<style type="text/css">
                         		body{ 	color:<xsl:value-of select="$fontcolor"/>;
                         			background-color:<xsl:value-of select="$color1"/>;
                                      		margin-top:<xsl:value-of select="$bodyMargin"/>px;
                                                 background-image:<xsl:value-of select="$backgroundImage"/>;
                                                 background-position:<xsl:value-of select="$backgroundPosition"/>;
                                                 background-repeat:<xsl:value-of select="$backgroundRepeat"/>;
                         		}
                         		
                         		div.main{                     		
                         			 background-color:<xsl:value-of select="$ContainerBackgroundColor"/>;
    		                         background-image:<xsl:value-of select="$ContainerBackgroundImage"/>;
    		                         background-repeat:<xsl:value-of select="$ContainerBackgroundRepeat"/>;
    		                         background-position:<xsl:value-of select="$ContainerBackgroundPosition"/>;
                         		}
                         		
                         		header#header{
                         			background-color:<xsl:value-of select="$color2"/>;
                                   		<xsl:value-of select="$headerBorderColor"/>;
                                   		<xsl:value-of select="$headerBorder"/>;
                                 		padding:30px;	
                         		}
                                       header#header h1{                      				
                                   		font-size:<xsl:value-of select="$headerTitleFontSize"/>;                                  		
                                                  color:<xsl:value-of select="$headerTitleFontColor"/>;
                                                  text-align:<xsl:value-of select="$headerTitleAlign"/>;
                                         
                         		}
                                          header#header p{                      				
                                   		font-size:<xsl:value-of select="$headerSubtitleFontSize"/>;                                  		
                                                  color:<xsl:value-of select="$headerSubtitleFontColor"/>;
                              			text-align:<xsl:value-of select="$headerSubtitleAlign"/>;
                         		}
                                    	.page-header{
                                    			border-bottom:none;
                                    	}
                         		#breadcrumb{
                         			background-color:<xsl:value-of select="$breadcrumb-background"/>;
                         		}
                         		#breadcrumb li.generic-breadcrumb
                         		{	color:<xsl:value-of select="$breadcrumb-fontcolor"/>;	}
                         		#breadcrumb li.active {
                         			color:<xsl:value-of select="$breadcrumb-current-fontcolor"/>;
                         		}
                                   .navbar-inner{
                                   	   background-color:<xsl:value-of select="$MenuBackgroundColor"/>;
    		             			   color: 	<xsl:value-of select="$MenuFontColor"/>;
                                       background-image: none;                
                                  }
                                   
                                  
                                  .navbar .nav li a.navbar-link{                    	  
                 			   color: 	 <xsl:value-of select="$MenuFontColor"/>;                          
                                  }
                                   
                                  .navbar .nav li a.navbar-link:hover{                    	   
                 			  color: 	<xsl:value-of select="$MenuHoverFontColor"/>;                           
                                  }
                                  
                                  h2{
         							color: <xsl:value-of select="$H2FontColor"/>;
         							font-size:<xsl:value-of select="$H2FontSize"/>;
         							<xsl:value-of select="$H2BorderBottom"/>;
                                  }
                                  
                                  
                                  h3{
         							color: <xsl:value-of select="$H3FontColor"/>;
         							font-size:<xsl:value-of select="$H3FontSize"/>;
         							<xsl:value-of select="$H3BorderBottom"/>
                                  }
                                  
                                  a.normal-link{
                                  	color: 	<xsl:value-of select="$LinkFontColor"/>;
                                  	<xsl:value-of select="$LinkBorderBottom"/>;                         	
                                  }
                                  
                                  a.normal-link:hover{
                                  	color: 	<xsl:value-of select="$LinkFontColorHover"/>;
                                  	<xsl:value-of select="$LinkBorderBottom"/>;                         	
                                  }
                                  
                                 .footer-links li:first-child {
   					padding-left: 0;
                                     }
                                     .footer-links li {
                                         display: inline;
                                         padding: 0 2px;
                                     }
                                    .footer {
  					   text-align: center;
 				     } 
                         	</style>
                         <!-- Fin del css -->
</xsl:template>
</xsl:stylesheet>
