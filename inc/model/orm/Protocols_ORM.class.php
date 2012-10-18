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
	define('XIMDEX_ROOT_PATH', realpath(dirname(__FILE__) . '/../../../'));

include_once (XIMDEX_ROOT_PATH . '/inc/helper/GenericData.class.php');

class Protocols_ORM extends GenericData   {
	var $_idField = 'IdProtocol';
	var $_table = 'Protocols';
	var $_metaData = array(
				'IdProtocol' => array('type' => "varchar(255)", 'not_null' => 'true', 'primary_key' => true),
				'DefaultPort' => array('type' => "int(12)", 'not_null' => 'false'),
				'Description' => array('type' => "varchar(255)", 'not_null' => 'false'),
				'UsePassword' => array('type' => "int(1)", 'not_null' => 'false')
				);
	var $_uniqueConstraints = array(
				'IdProtocol' => array('IdProtocol')
				);
	var $_indexes = array('IdProtocol');
	var $IdProtocol;
	var $DefaultPort = 0;
	var $Description = 0;
	var $UsePassword = 0;
}
?>
