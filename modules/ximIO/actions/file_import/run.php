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
		define('XIMDEX_ROOT_PATH', realpath(dirname(__FILE__)) . '/../../../../');
	}
	//
	ModulesManager::file('/inc/FileUpdater.class.php', 'ximIO');
	ModulesManager::file('/actions/file_import/inc/FileUpdaterCli.class.php', 'ximIO');
	ModulesManager::file('/inc/cli/CliReader.class.php');

	$parameterCollector = new FileUpdaterCli($argc, $argv);
	$revision = $parameterCollector->getParameter('--file');
	$autoDelete = $parameterCollector->getParameter('--autodelete');

	if (empty($autoDelete)) {
		$autoDelete = true;
	}
	
	echo _("It is going to proceed the content importation of nodes for package ")."{$revision}:\n\n";
	
	if (!CliReader::alert(array('y', 'Y', 's', 'S'), _("Do you want to continue with the process? (Y/n)")."\n", array('n', 'N'), _("The importation process has been aborted as user applied")."\n")) {
		die();
	}
	
	$time = time();
	$fileUpdater = new FileUpdater($revision);
	$fileUpdater->updateFiles(IMPORT_FILES);
	$timeConsumed = time() - $time;

	echo sprintf(_("The time consumed by the process has been of %s seconds"),$timeConsumed);


	if ($autoDelete) {
		passthru(sprintf('php %s'.ModulesManager::path('ximIO').'/actions/remove/run.php --file %s', 
			XIMDEX_ROOT_PATH,  $revision));
	}
	
?>