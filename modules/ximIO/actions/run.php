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
	define('XIMDEX_ROOT_PATH', realpath(dirname(__FILE__) . '/../../../'));
}
require_once(XIMDEX_ROOT_PATH.'/inc/modules/ModulesManager.class.php');
ModulesManager::file('/inc/cli/CliParser.class.php');

class RunnerCli extends CliParser  {
	var $_metadata = array(
		array (	'name' => '--mode',
				'mandatory' => false,
				'message' => 'Available command to execute, to see a list, type --mode without command',
				'type' => TYPE_STRING)
	);

	function __construct ($params) {
		$this->_metadata[0]["message"] = _('Available command to execute, to see a list, type --mode without command');
	
		parent::__construct($params);
	}
}

	
	$parameterCollector = new RunnerCli($argc, $argv, false);
	
	$mode = $parameterCollector->getParameter('--mode');
	if (empty($mode) || !is_dir(XIMDEX_ROOT_PATH .ModulesManager::path('ximIO'). '/actions/' . $mode)) {
		echo _("The param --mode is obligatory")."\n";
		echo _("Available modes are:")."\n";
		$handler = opendir(XIMDEX_ROOT_PATH .ModulesManager::path('ximIO').'/actions/');
		while (false !== ($file = readdir($handler))) {
			if ($file == '.' || $file == '..') {
				continue;
			}
			// This is going to cause that in a dev environment folder .svn could be seen, and in a production environment folders without command were also seen. It will be treated in the future 
			if (is_dir($file)) {
				echo "\t$file\n";
			}
		}
		die();
	}
	
	include "$mode/run.php";

?>
