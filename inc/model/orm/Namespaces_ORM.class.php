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

class Namespaces_ORM extends GenericData   {
	public $_idField = 'idNamespace';
	public $_table = 'Namespaces';
	public $_metaData = array(
				'idNamespace' => array('type' => "int(11)", 'not_null' => 'true', 'auto_increment' => 'true', 'primary_key' => true),
				'service' => array('type' => "varchar(255)", 'not_null' => 'true'),
				'type' => array('type' => "varchar(255)", 'not_null' => 'true'),
				'nemo' => array('type' => "varchar(255)", 'not_null' => 'true'),
				'uri' => array('type' => "varchar(255)", 'not_null' => 'true'),
				'recursive' => array('type' => "mediumint(8)", 'not_null' => 'true'),
				'category' => array('type' => "varchar(255)", 'not_null' => 'true'),
				'isSemantic' => array('type' => "mediumint(1)", 'not_null' => 'true'),
				);
	public $_uniqueConstraints = array(
				"Nemo" => array("nemo"),
				"Type" => array("type")
			);
	public $_indexes = array('idNamespace');
	public $idNamespace; //Autoincrement id.
	public $service; //Source which provice the type. P.e Ximdex, DBpedia
	public $type; //Specific type for a tag. P.e. DBPediaPeople
	public $nemo; //mnemonic for ximdex document tag. it could be an attribute.
	public $uri; //To source
	public $recursive = 0; //If the type has more descendant types.
	public $category; //Kind of the source. P.e. Images, Article, Generic.
	public $isSemantic = 0; //Boolean
}
?>