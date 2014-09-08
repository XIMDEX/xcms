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

if (!defined('LOG_DIR'))
{
	
	define('LOG_DIR', realpath(dirname(__FILE__) . "/logs"));
}
if (!defined('DEBUG'))
{
	
	define('DEBUG', true);
}

/**
 *	@brief Logging for the publication incidences.
 */

class Publication_Log {

	private function __construct() {
		// Singleton
	}

	/**
	 * Writes a message.
	 * @param $msg
	 * @param $loggerName
	 * @param $level
	 * @return unknown_type
	 */

	public function write($msg, $level=LOGGER_LEVEL_INFO) {
		Loggeable::write($msg, 'publication_logger', $level);
	}

	/**
	 * Writes a message as Debug.
	 * @param $msg
	 * @param $loggerName
	 * @return unknown_type
	 */

	public function debug($msg) {
		Loggeable::debug($msg, 'publication_logger');
	}

	/**
	 * Writes a message as Info.
	 * @param $msg
	 * @param $loggerName
	 * @return unknown_type
	 */

	public function info($msg) {
		Loggeable::info($msg, 'publication_logger');
	}

	/**
	 * Writes a message as Warning.
	 * @param $msg
	 * @param $loggerName
	 * @return unknown_type
	 */

	public function warning($msg) {
		Loggeable::warning($msg, 'publication_logger');
	}

	/**
	 * Writes a message as Error.
	 * @param $msg
	 * @param $loggerName
	 * @return unknown_type
	 */

	public function error($msg) {
		Loggeable::error($msg, 'publication_logger');
	}

	/**
	 * Writes a message as Fatal.
	 * @param $msg
	 * @param $loggerName
	 * @return unknown_type
	 */

	public function fatal($msg) {
		Loggeable::fatal($msg, 'publication_logger');		
	}

}

?>