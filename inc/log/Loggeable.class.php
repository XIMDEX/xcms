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




 

require_once(XIMDEX_ROOT_PATH . '/inc/log/Log.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/log/logger/Logger_error.class.php' );

class Loggeable {

	/**
	 * 
	 * @param $loggerName
	 * @return unknown_type
	 */
	private static function &_getLogger($loggerName) {
		$logger = Log::getLogger($loggerName);
		return $logger;
	}
	
	/**
	 * 
	 * @param $msg
	 * @param $loggerName
	 * @param $level
	 * @return unknown_type
	 */
	public static function write($msg, $loggerName, $level=LOGGER_LEVEL_INFO) {
		$logger = Loggeable::_getLogger($loggerName);
		if( !is_null($logger) ) $logger->write( $msg, $level );
	}
	
	/**
	 * 
	 * @param $msg
	 * @param $loggerName
	 * @return unknown_type
	 */
	public static function debug($msg, $loggerName) {
		$logger = Loggeable::_getLogger($loggerName);
		if( !is_null($logger) ) $logger->debug( $msg );
	}

	/**
	 * 
	 * @param $msg
	 * @param $loggerName
	 * @return unknown_type
	 */
	public static function info($msg, $loggerName) {
		$logger = Loggeable::_getLogger($loggerName);
		if( !is_null($logger) ) $logger->info( $msg );
	}

	/**
	 * 
	 * @param $msg
	 * @param $loggerName
	 * @return unknown_type
	 */
	public static function warning($msg, $loggerName) {
		$logger = Loggeable::_getLogger($loggerName);
		if( !is_null($logger) ) $logger->warning( $msg );
	}

	/**
	 * 
	 * @param $msg
	 * @param $loggerName
	 * @return unknown_type
	 */
	public static function error($msg, $loggerName) {
		$logger = Loggeable::_getLogger($loggerName);
		if( !is_null($logger) ) $logger->error( $msg );
	}

	/**
	 * 
	 * @param $msg
	 * @param $loggerName
	 * @return unknown_type
	 */
	public function fatal($msg, $loggerName) {
		$logger = Loggeable::_getLogger($loggerName);
		if( !is_null($logger) ) $logger->fatal( $msg );		
	}
	
}

?>
