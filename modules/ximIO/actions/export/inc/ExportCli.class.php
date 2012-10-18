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

require_once(XIMDEX_ROOT_PATH . '/inc/cli/CliParser.class.php');

class ExportCli extends CliParser  {
	var $_metadata = array(
		array (	'name' => '--nodes',
			'mandatory' => true,
			'message' => 'Node where export is starting',
			'type' => TYPE_ARRAY),
		array (	'name' => '--recursive',
			'mandatory' => false,
			'message' => 'If this param is not indicated, the recursivity level is infinite, if a recursivity value is specified, this will be the limit',
			'type' => TYPE_INT),
		array (	'name' => '--file',
			'mandatory' => false,
			'message' => 'Package name, if it is not specified, a timestamp will be used',
			'type' => TYPE_STRING),
		array (	'name' => '--test',
			'mandatory' => false,
			'message' => 'Only export information is returned',
			'type' => TYPE_INT),
		array (	'name' => '--no-require-confirm',
			'mandatory' => false,
			'message' => 'Process is executed without asking for confirmation',
			'type' => TYPE_INT)
		);

	function __construct ($params) {
		$this->_metadata[0]["message"] = _('Node where export is starting');
		$this->_metadata[1]["message"] = _('If this param is not indicated, the recursivity level is infinite, if a recursivity value is specified, this will be the limit');
		$this->_metadata[2]["message"] = _('Package name, if it is not specified, a timestamp will be used');
		$this->_metadata[3]["message"] = _('Only export information is returned');
		$this->_metadata[4]["message"] = _('Process is executed without asking for confirmation');

		parent::__construct($params);
	}
}

?>
