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


require_once( XIMDEX_ROOT_PATH . '/inc/log/Loggeable.class.php' ); 
class Action_log {
	
	const LOGGER_NAME="action_logger";
	/**
	 * 
	 * @param $msg
	 * @param $level
	 * @return unknown_type
	 */
	public static function write($msg, $level=LOGGER_LEVEL_INFO) {
		Loggeable::write($msg, self::LOGGER_NAME, $level);
	}
	
	/**
	 * 
	 * @param $msg
	 * @return unknown_type
	 */
	public static function debug($msg) {
		Loggeable::debug($msg, self::LOGGER_NAME);
	}

	/**
	 * 
	 * @param $msg
	 * @return unknown_type
	 */
	public static function info($msg) {
		Loggeable::info($msg, self::LOGGER_NAME);
	}

	/**
	 * 
	 * @param $msg
	 * @return unknown_type
	 */
	public static function warning($msg) {
		Loggeable::warning($msg, self::LOGGER_NAME);
	}

	/**
	 * 
	 * @param $msg
	 * @return unknown_type
	 */	
	public static function error($msg) {
		Loggeable::error($msg, self::LOGGER_NAME);
	}

	/**
	 * 
	 * @param $msg
	 * @return unknown_type
	 */
	public static function fatal($msg) {
		Loggeable::fatal($msg, self::LOGGER_NAME);		
	}

	/**
	 * 
	 * @param $msg
	 * @return unknown_type
	 */
	public static function display($msg) {
		// detect environment (cli / web)
		$output = sprintf("[%s]: %s\n", strftime("%d-%m-%y %T"), $msg);	
		echo $output;
	}
		
}

?>
