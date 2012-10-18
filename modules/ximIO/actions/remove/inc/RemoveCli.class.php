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


class RemoveCli extends CliParser  {
	var $_metadata = array(
		array (	'name' => '--file',
				'mandatory' => true,
				'message' => 'Name of package to delete',
				'type' => TYPE_STRING),
		array (	'name' => '--delete',
				'mandatory' => false,
				'message' => 'Element to delete, possible values are ONLY_FILES, which deletes the importation package only from the hard disk, ONLY_DB, which deletes just the importation associations from the database',
				'type' => TYPE_STRING,
				'validValues' => array('ONLY_FILES', 'ONLY_DB'))
	);
	function __construct ($params) {
		$this->_metadata[0]["message"] = _('Name of package to delete');
		$this->_metadata[1]["message"] = _('Element to delete, possible values are ONLY_FILES, which deletes the importation package only from the hard disk, ONLY_DB, which deletes just the importation associations from the database');

		parent::__construct($params);
	}
}

?>
