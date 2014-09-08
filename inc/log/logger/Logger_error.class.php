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
 *
 */
class Logger_Error extends Logger {


	function Logger_Error($name, &$params) {

		parent::Logger($name, $params);
	}

	// Interface definition.

	function write( $msg, $level=LOGGER_LEVEL_INFO ) {
		
		$arrMethod = array();
		$arrMethod[LOGGER_LEVEL_DEBUG] = 'debug';
		$arrMethod[LOGGER_LEVEL_INFO] = 'info';
		$arrMethod[LOGGER_LEVEL_WARNING] = 'warning';
		$arrMethod[LOGGER_LEVEL_ERROR] = 'error';
		$arrMethod[LOGGER_LEVEL_FATAL] = 'fatal';
		
		$method = isset($arrMethod[ $level ]) ? $arrMethod[ $level ] : $arrMethod[LOGGER_LEVEL_INFO];
		$this->$method( $msg );
		
	}
	
	function debug($msg) {

		$this->log($msg, LOGGER_LEVEL_DEBUG);
	}

	function info($msg) {

		$this->log($msg, LOGGER_LEVEL_INFO);
	}

	function warning($msg) {

		$this->log($msg, LOGGER_LEVEL_WARNING);
	}

	function error($msg) {

		$this->log($msg, LOGGER_LEVEL_ERROR);
	}

	function fatal($msg) {

		$this->log($msg, LOGGER_LEVEL_FATAL);
		die();
	}

}

?>