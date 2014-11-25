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
ModulesManager::file('/inc/ExportXml.class.php');
ModulesManager::file('/actions/export/inc/ExportCli.class.php');

$PROCESSED_NODES = 0;
$LAST_REPORT = 0;
$TOTAL_NODES = 0;

$STOP_COUNT = 0;

ini_set('memory_limit', '512M');

$parameterCollector = new ExportCli($argc, $argv);

$nodes = $parameterCollector->getParameter('--nodes');
$fileName = $parameterCollector->getParameter('--file');

if (is_array($nodes) && !empty($nodes)) {
	echo _("Info about next nodes are going to be exported:")."\n\n";
	
	reset($nodes);
	while(list(, $idNode) = each($nodes)) {
		$node = new Node($idNode);
		echo $node->toStr(DETAIL_LEVEL_MEDIUM);
		echo "\n";
	}
	
	if ($parameterCollector->getParameter('--test')) {
		die();
	}
	
	if (!$parameterCollector->getParameter('--no-require-confirm') && 
		!CliReader::alert(array('y', 'Y', 's', 'S'), 
			_("Do you want to continue with this process? (Y/n)")."\n", 
			array('n', 'N'), 
			_("Import process has been avoided due to user request")."\n")) {
		die();
	}
}

$time = time();

$recurrence = $parameterCollector->getParameter('--recursive');

$export = new ExportXml($nodes);
echo _("Obtaining information for project...")."\n";
$xml = $export->getXml($recurrence, $files);
echo _("Writting information of backup...")."\n";
$backupName = $export->writeData($xml, $files, $fileName);

reset($export->messages->messages);
while(list(, $message) = each($export->messages->messages)) {
	echo $message['message'] . "\n";
}

echo sprintf("\n"._("Elapsed time has been of %s seconds")."\n", time() - $time);
echo sprintf(_("Generated package has been %s")."\n", $backupName);

?>