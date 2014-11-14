<?php
/**
 *  \details &copy; 2011  Open Ximdex Evolution SL [http://www.ximdex.org]
 *
 *  Ximdex a Semantic Content Management System (CMS)
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as published
 *  by the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  See the Affero GNU General Public License for more details.
 *  You should have received a copy of the Affero GNU General Public License
 *  version 3 along with Ximdex (see LICENSE file).
 *
 *  If not, visit http://gnu.org/licenses/agpl-3.0.html.
 *
 *  @author Ximdex DevTeam <dev@ximdex.com>
 *  @version $Revision$
 */



 
 
require_once( XIMDEX_ROOT_PATH . '/inc/log/SyntaxParser.class.php' );
 
 class SyntaxParser_PublicationLog extends SyntaxParser {
 	
 	function parse($text, $level, $sort) {
 		
 		// %s es el parametro para sustituir el nivel
 		$regexp = '#^(.*)$#m';
 		
 		// %s es el nombre de la clase en la hoja de estilos para representar el nivel de error
 		//$colorSyntax = '<span class="%s">\\1</span><br />';
 		
 		// Se extraen las lineas que corresponden con el nivel de error indicado
 		if ($level == LOGGER_LEVEL_ALL) $level = '\d';
		preg_match_all(sprintf($regexp, $level), $text, $res, PREG_PATTERN_ORDER);
		
		if($sort == 'DESC') krsort($res[0]);
		$text = implode("\n", $res[0]);
		
		/*echo sprintf('<pre>%s</pre>', print_r($res[0], true));
		exit;*/
		
		
		// Se buscan las lineas de error en el texto...
		/*$search = array(
			sprintf($regexp, LOGGER_LEVEL_DEBUG),
			sprintf($regexp, LOGGER_LEVEL_INFO),
			sprintf($regexp, LOGGER_LEVEL_WARNING),
			sprintf($regexp, LOGGER_LEVEL_ERROR),
			sprintf($regexp, LOGGER_LEVEL_FATAL)
		);*/
		
		// ...para aplicar un color dependiendo de la severidad.
		/*$replace = array(
			sprintf($colorSyntax, 'level_debug'),
			sprintf($colorSyntax, 'level_info'),
			sprintf($colorSyntax, 'level_warning'),
			sprintf($colorSyntax, 'level_error'),
			sprintf($colorSyntax, 'level_fatal')
		);*/
		
		//$text = preg_replace($search, $replace, $text);
		$resLen = count($res[0]);
		return $res[0][$resLen - 1];
 		
 	}
 	
 }
 
?>