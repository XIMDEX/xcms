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



if (!defined('XIMDEX_ROOT_PATH'))
	define('XIMDEX_ROOT_PATH', realpath(dirname(__FILE__) . "/../../"));

/**
 * Requires access to database
 */
include_once(XIMDEX_ROOT_PATH . '/inc/db/db.php');

/**
 *
 */
class Appender_sql extends Appender {

	var $_db;
	var $_table;

	/**
	 * @param object params['layout']
	 * @param string params['table']
	 */
	function Appender_sql(&$params) {

		parent::Appender($params);

		$this->_table = $params['table'];
		$this->_db = new DB();
	}

//     function open($file) {
//     }

    // TODO: pasar la prioridad de alguna manera humana...
    function write(&$event) {

    	parent::write($event);

    	// prepare sql query
    	$consulta = "INSERT INTO ".$this->_table;
    	$consulta .= " (IdLog, Priority, LogText) ";
    	$consulta .= " VALUES (";
    	$consulta .= "NULL, ";
    	$consulta .= "0, ";
    	$consulta .= "'".$this->_msg."') ";

    	// insert into LogTable table log data.
    	$this->_db->Execute($consulta);
	}

//     function close() {
//     }
}

?>