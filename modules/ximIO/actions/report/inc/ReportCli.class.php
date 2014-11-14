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



ModulesManager::file('/inc/cli/CliParser.class.php');

class ReportCli extends CliParser  {
	var $_metadata = array(
		array (	'name' => '--file',
				'mandatory' => true,
				'message' => 'Name of the package from where to get information',
				'type' => TYPE_STRING),
		array (	'name' => '--nodeList',
				'mandatory' => false,
				'message' => 'Showing a list of package nodes with the format IdNode, State (usage --nodeList 1)',
				'type' => TYPE_INT),
		array (	'name' => '--filterByStatus',
				'mandatory' => false,
				'message' => 'This param should be used with the param --nodeList, and it shows just the nodes which are in a determined state (-1, -2, -3, -4). -1 Not inserted due to a lack of permits, -2 General error, -3 Father node not resolved, -4 Node destiny not allowed, 0 Node successfully inserted',
				'type' => TYPE_STRING)
	);
	function __construct ($params) {
		$this->_metadata[0]["message"] = _('Name of the package from where to get information');
		$this->_metadata[1]["message"] = _('Showing a list of package nodes with the format IdNode, State (usage --nodeList 1)');
		$this->_metadata[2]["message"] = _('This param should be used with the param --nodeList, and it shows just the nodes which are in a determined state (-1, -2, -3, -4). -1 Not inserted due to a lack of permits, -2 General error, -3 Father node not resolved, -4 Node destiny not allowed, 0 Node successfully inserted');
		parent::__construct($params);
	}

}

?>