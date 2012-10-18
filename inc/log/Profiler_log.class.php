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

class Profiler_Log {

	/**
	 * 
	 * @param $name
	 * @return unknown_type
	 */
	private function getGetter($name) {
		$logger =& Log::getLogger('profiler_logger');
		$getter = $logger->getGetters();
		$getter = $getter[$name];
		return $getter;
	}

	/**
	 * 
	 * @param $conditions
	 * @param $order
	 * @param $limit
	 * @return unknown_type
	 */
	public function read($conditions='', $order='', $limit='') {
		$getter = self::getGetter('profilersql');
		if (is_null($getter)) return array();
		$response = $getter->read($conditions, $order, $limit);
		return $response;
	}

	/**
	 * 
	 * @param $tests
	 * @param $label
	 * @return unknown_type
	 */
	public function getAvarage($tests, $label) {
		$getter = self::getGetter('profilersql');
		if (is_null($getter)) return array();
		$response = $getter->getAvarage($tests, $label);
		return $response;
	}

	/**
	 * 
	 * @param $msg
	 * @param $level
	 * @return unknown_type
	 */
	public function write($msg, $level=LOGGER_LEVEL_INFO) {
		Loggeable::write($msg, 'profiler_logger', $level);
	}

	/**
	 * 
	 * @param $msg
	 * @return unknown_type
	 */
	public function debug($msg) {
		Loggeable::debug($msg, 'profiler_logger');
	}

	/**
	 * 
	 * @param $msg
	 * @return unknown_type
	 */
	public function info($msg) {
		Loggeable::info($msg, 'profiler_logger');
	}

	/**
	 * 
	 * @param $msg
	 * @return unknown_type
	 */
	public function warning($msg) {
		Loggeable::warning($msg, 'profiler_logger');
	}

	/**
	 * 
	 * @param $msg
	 * @return unknown_type
	 */
	public function error($msg) {
		Loggeable::error($msg, 'profiler_logger');
	}

	/**
	 * 
	 * @param $msg
	 * @return unknown_type
	 */
	public function fatal($msg) {
		Loggeable::fatal($msg, 'profiler_logger');
	}

	/**
	 * 
	 * @param $msg
	 * @return unknown_type
	 */
	public function display($msg) {
		// detect environment (cli / web)
		$output = sprintf("[%s]: %s\n", strftime("%d-%m-%y %T"), $msg);
		echo $output;
	}

}

?>
