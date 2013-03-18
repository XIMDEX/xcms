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




/**
 * XIMDEX_ROOT_PATH
 */
if (!defined('XIMDEX_ROOT_PATH'))
	define('XIMDEX_ROOT_PATH', realpath(dirname(__FILE__) . "/../../"));


/**
 *  
 * 
 */
class String {
	/**
	 * 
	 * @param $texto
	 * @return unknown_type
	 */
	public static function convertirTexto($texto) {
	
		//$texto = str_replace('\"','',$texto);
		$texto = str_replace(" ","_",$texto);
		$texto = str_replace("!","",$texto);
		$texto = str_replace("?","",$texto);
		$texto = str_replace("?","",$texto);
		$texto = str_replace("?","",$texto);
		$texto = str_replace("(","",$texto);
		$texto = str_replace(")","",$texto);
		$texto = str_replace("[","",$texto);
		$texto = str_replace("]","",$texto);
		$string = htmlentities($texto);
		$texto = preg_replace("/\&(.)[^;]*;/", "\\1", $string);
		$texto = str_replace("\'","",$texto);
		$texto = str_replace('\q','',$texto);

		return $texto;
	}
	/**
	 * 
	 * @param $string
	 * @return unknown_type
	 */
	public static function stripslashes($string) {

		if (get_magic_quotes_gpc())
			return stripslashes($string);

		return $string;
	}
}

?>
