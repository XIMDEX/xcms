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

class Locales_ORM extends GenericData   {
	var $_idField = 'ID';
	var $_table = 'Locales';
	var $_metaData = array(
				'ID' => array('type' => "smallint(5)", 'not_null' => 'true', 'primary_key' => true),
				'Code' => array('type' => "varchar(6)", 'not_null' => 'true', ),
				'Name' => array('type' => "varchar(20)", 'not_null' => 'true'),
				'Enabled' => array('type' => "tinyint(1)", 'not_null' => 'false')
				);
	var $_uniqueConstraints = array(
				'Code' => array('Code'),'ID' => array('ID')
				);
	var $_indexes = array('ID');
	var $ID;
	var $Code;
	var $Name;
	var $Enabled = 1;
}
?>