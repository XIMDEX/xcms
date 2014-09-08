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


require_once(XIMDEX_ROOT_PATH.'/inc/modules/ModulesManager.class.php');
ModulesManager::file('/inc/cli/CliParser.class.php');

class FileUpdaterCli extends CliParser  {
	var $_metadata = array(
		array (	'name' => '--file',
			'mandatory' => true,
			'message' => 'The name of the package to update should be introduced',
			'type' => TYPE_STRING),
		array (	'name' => '--autodelete',
			'mandatory' => false,
			'message' => 'It indicates if the package will be auto-deleted or not once consolidation is finished, by default is 1',
			'type' => TYPE_INT)
	);

	function __construct ($params) {
		$this->_metadata[0]["message"] = _('The name of the package to update should be introduced');
		$this->_metadata[1]["message"] = _('It indicates if the package will be auto-deleted or not once consolidation is finished, by default is 1');

		parent::__construct($params);
	}
}

?>