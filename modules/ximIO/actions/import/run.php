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
require_once(XIMDEX_ROOT_PATH.'/inc/modules/ModulesManager.class.php');
ModulesManager::file('/inc/ImportXml.class.php', 'ximIO');
ModulesManager::file('/actions/file_import/inc/FileUpdaterCli.class.php', 'ximIO');
ModulesManager::file('/inc/cli/CliReader.class.php');
ModulesManager::file('/inc/io/BaseIOConstants.php');
ModulesManager::file('/inc/auth/Authenticator.class.php');

$parameterCollector = new ImportCli($argc, $argv);


$rootNode = $parameterCollector->getParameter('--node');
$file = $parameterCollector->getParameter('--file');
$recurrence = $parameterCollector->getParameter('--recurrence');
$nodeAssociations = $parameterCollector->getParameter('--associations');
$beginAt = $parameterCollector->getParameter('--beginAt');
$processFirstNode = $parameterCollector->getParameter('--processFirstNode');
$copyMode = $parameterCollector->getParameter('--copyMode');
$user = $parameterCollector->getParameter('--user');
$interfaceWeb = (bool)$parameterCollector->getParameter('--interfaceWeb');
$interfaceWebRun = (bool)$parameterCollector->getParameter('--interfaceWebRun');
$password = $parameterCollector->getParameter('--password');

$node = new Node($rootNode);
if (empty($password)) {
	$attempts = 0;
	do {
		$password = CliReader::getString(_('Password:'));
		$auth = new Authenticator();
		if ($auth->login($user, $password)) {
			break;
		}
		++$attempts;
	} while ($attempts < 3);
} else {
	$auth = new Authenticator();
	if (!$auth->login($user, $password)) {
		echo _("Incorrect login")."\n";
		die(); 
	}
}

echo _("Information about root node:")."\n\n";
echo $node->toStr(DETAIL_LEVEL_MEDIUM);

if (!$interfaceWeb && !$interfaceWebRun) {
	if (!CliReader::alert(array('y', 'Y', 's', 'S'), _("Do you want to continue with process? (Y/n)")."\n", array('n', 'N'), _("Import process has been avoided due to user request")."\n")) {
		die();
	}
} else {
	if ($interfaceWeb) {
		die();
	}
}

$time = time();

$importer = new ImportXml($rootNode, $file, $nodeAssociations, RUN_IMPORT_MODE, $recurrence, $beginAt, $processFirstNode);
if ($copyMode) {
	$importer->mode = COPY_MODE;
}
$importer->import();

$correct = $importer->processedNodes['success'];
$incorrectPermissions = $importer->processedNodes['failed'][ERROR_NO_PERMISSIONS];
$incorrectIncorrectData = $importer->processedNodes['failed'][ERROR_INCORRECT_DATA];
$incorrectNotReached = $importer->processedNodes['failed'][ERROR_NOT_REACHED];
$incorrectNotAllowed = $importer->processedNodes['failed'][ERROR_NOT_ALLOWED];

$totalIncorrectNodes = 0;
reset($importer->processedNodes['failed']);
while(list(, $nodes) = each($importer->processedNodes['failed'])) {
	$totalIncorrectNodes += $nodes;
}

$timeConsumed = time() - $time;

$sugestedPackages = implode(', ', $importer->sugestedPackages);
if (!empty($sugestedPackages)) {
	$sugestedPackages = sprintf(_("It is suggested to launch below the next packages: %s")."\n", $sugestedPackages);
} else {
	$sugestedPackages = '';
}

if (count($importer->messages) > 0) {
	reset($importer->messages);
	while(list(, $message) = each($importer->messages)) {
		echo $message . "\n";
	}
}

echo sprintf(_("These nodes %s have been successfully imported"), $correct)."\n";
echo sprintf(_("These nodes %s have not been imported"), $totalIncorrectNodes)."\n";
echo sprinft(_("%s node/s because of lack of permits"), $incorrectPermissions)."\n";
echo sprinft(_("%s node/s because of lack of info in the xml"). $incorrectIncorrectData)."\n";
echo sprinft(_("%s node/s because its parent has not been inserted"),$incorrectNotReached)."\n";
echo sprinft(_("%s node/s because have not been allowed (it is not scheduled in NodeAllowedContents)"), $incorrectNotAllowed)."\n";
		
echo sprinft(_("The elapsed time by procedure has been %s seconds"), $timeConsumed)."\n";
	
echo $sugestedPackages."\n";



?>
