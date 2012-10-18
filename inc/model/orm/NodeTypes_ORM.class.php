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

class NodeTypes_ORM extends GenericData   {
	var $_idField = 'IdNodeType';
	var $_table = 'NodeTypes';
	var $_metaData = array(
				'IdNodeType' => array('type' => "int(12)", 'not_null' => 'true', 'auto_increment' => 'true', 'primary_key' => true),
				'Name' => array('type' => "varchar(255)", 'not_null' => 'true'),
				'Class' => array('type' => "varchar(255)", 'not_null' => 'true'),
				'Icon' => array('type' => "varchar(255)", 'not_null' => 'true'),
				'Description' => array('type' => "varchar(255)", 'not_null' => 'false'),
				'IsRenderizable' => array('type' => "int(1)", 'not_null' => 'false'),
				'HasFSEntity' => array('type' => "int(1)", 'not_null' => 'false'),
				'CanAttachGroups' => array('type' => "int(1)", 'not_null' => 'false'),
				'IsSection' => array('type' => "int(1)", 'not_null' => 'false'),
				'IsFolder' => array('type' => "int(1)", 'not_null' => 'false'),
				'IsVirtualFolder' => array('type' => "int(1)", 'not_null' => 'false'),
				'IsPlainFile' => array('type' => "int(1)", 'not_null' => 'false'),
				'IsStructuredDocument' => array('type' => "int(1)", 'not_null' => 'false'),
				'IsPublicable' => array('type' => "int(1)", 'not_null' => 'false'),
				'CanDenyDeletion' => array('type' => "int(1)", 'not_null' => 'false'),
				'System' => array('type' => "int(1)", 'not_null' => 'false'),
				'Module' => array('type' => "varchar(255)", 'not_null' => 'false'),
				'isGenerator' => array('type' => "tinyint(1)", 'not_null' => 'false'),
				'IsEnriching' => array('type' => "tinyint(1)", 'not_null' => 'false')
				);
	var $_uniqueConstraints = array(
				'IdType' => array('Name')
				);
	var $_indexes = array('IdNodeType');
	var $IdNodeType;
	var $Name = 0;
	var $Class;
	var $Icon;
	var $Description;
	var $IsRenderizable;
	var $HasFSEntity;
	var $CanAttachGroups;
	var $IsSection;
	var $IsFolder;
	var $IsVirtualFolder;
	var $IsPlainFile;
	var $IsStructuredDocument;
	var $IsPublicable;
	var $CanDenyDeletion;
	var $System;
	var $Module;
	var $isGenerator;
	var $IsEnriching;
}
?>
