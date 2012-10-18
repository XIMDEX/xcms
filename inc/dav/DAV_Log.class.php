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



include_once(XIMDEX_ROOT_PATH . "/inc/log/Loggeable.class.php");


/**
 *  
 */
class DAV_Log {

	/**
	 * Constructor
	 * @return unknown_type
	 */
	private function __construct() {
		// Singleton
	}	
	
	/**
	 * 
	 * @param $msg
	 * @param $level
	 * @return unknown_type
	 */
	public function write($msg, $level=LOGGER_LEVEL_INFO) {
		Loggeable::write($msg, 'dav_logger', $level);
	}
	
	/**
	 * 
	 * @param $msg
	 * @return unknown_type
	 */
	public function debug($msg) {
		Loggeable::debug($msg, 'dav_logger');
	}

	/**
	 * 
	 * @param $msg
	 * @return unknown_type
	 */
	public function info($msg) {
		Loggeable::info($msg, 'dav_logger');
	}

	/**
	 * 
	 * @param $msg
	 * @return unknown_type
	 */
	public function warning($msg) {
		Loggeable::warning($msg, 'dav_logger');
	}

	/**
	 * 
	 * @param $msg
	 * @return unknown_type
	 */
	public function error($msg) {
		Loggeable::error($msg, 'dav_logger');
	}

	/**
	 * 
	 * @param $msg
	 * @return unknown_type
	 */
	public function fatal($msg) {
		Loggeable::fatal($msg, 'dav_logger');		
	}

}

?>
