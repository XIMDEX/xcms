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



if (!defined('XIMDEX_ROOT_PATH')) {
	define('XIMDEX_ROOT_PATH', realpath(dirname(__FILE__) . '/../../../../../'));
}


//
ModulesManager::file('/inc/cli/CliParser.class.php');


class ImportCli extends CliParser  {
	var $_metadata = array(
		array (	'name' => '--node',
				'mandatory' => true,
				'message' => 'Root node where importation data will be dumped',
				'type' => TYPE_INT),
		array (	'name' => '--file',
				'mandatory' => true,
				'message' => 'Name of package which you would like to import',
				'type' => TYPE_STRING),
		array (	'name' => '--user',
				'mandatory' => true,
				'message' => 'User which will execute the importation',
				'type' => TYPE_STRING),
		array (	'name' => '--password',
				'mandatory' => false,
				'message' => 'User password to use from interface',
				'type' => TYPE_STRING),
		array (	'name' => '--interfaceWeb',
				'mandatory' => false,
				'message' => 'Information mode for the interface',
				'type' => TYPE_INT),
		array (	'name' => '--interfaceWebRun',
				'mandatory' => false,
				'message' => 'Instant execution mode for the interface',
				'type' => TYPE_INT),
		array (	'name' => '--recurrence',
				'mandatory' => false,
				'message' => 'Maximun level of recurrence you would like to import',
				'type' => TYPE_INT),
		array (	'name' => '--associations',
				'mandatory' => false,
				'message' => "Association of channels, languages and groups that are going to be made when importing (format: idSourceNode1=idDestinyNode1, idSourceNode2=idDestinyNode2, ...)",
				'type' => TYPE_HASH),
		array (	'name' => '--beginAt',
				'mandatory' => false,
				'message' => 'Node of exportation package from which you would like to start the importation',
				'type' => TYPE_INT),
		array (	'name' => '--processFirstNode',
				'mandatory' => false,
				'message' => 'If it is accompained of value 1 (--processFirstNode 1) it will be tried to insert the first node, otherwise, it will just checked if it is compatible with the first exportation node',
				'type' => TYPE_INT),
		array (	'name' => '--copyMode',
				'mandatory' => false,
				'message' => '(--copyMode 1) It indicates if available nodes in ximdex are going to be used when dependencies could not been resolved, in the case of not resolved links the node will be resolved with an -3 error',
				'type' => TYPE_INT)
	);

	function __construct ($params) {
		$this->_metadata[0]["message"] = _('Root node where importation data will be dumped');
		$this->_metadata[1]["message"] = _('Name of package which you would like to import');
		$this->_metadata[2]["message"] = _('User which will execute the importation');
		$this->_metadata[3]["message"] = _('User password to use from interface');
		$this->_metadata[4]["message"] = _('Information mode for the interface');
		$this->_metadata[5]["message"] = _('Instant execution mode for the interface');
		$this->_metadata[6]["message"] = _('Maximun level of recurrence you would like to import');
		$this->_metadata[7]["message"] = _('Association of channels, languages and groups that are going to be made when importing (format: idSourceNode1=idDestinyNode1, idSourceNode2=idDestinyNode2, ...)');
		$this->_metadata[8]["message"] = _('Node of exportation package from which you would like to start the importation');
		$this->_metadata[9]["message"] = _('If it is accompained of value 1 (--processFirstNode 1) it will be tried to insert the first node, otherwise, it will just checked if it is compatible with the first exportation node');
		$this->_metadata[10]["message"] = _('(--copyMode 1) It indicates if available nodes in ximdex are going to be used when dependencies could not been resolved, in the case of not resolved links the node will be resolved with an -3 error');

		parent::__construct($params);
	}
}

?>