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

// We're not using the orm, bacuse this code has to be also compatible with v2.5 
ModulesManager::file('/inc/db/db.inc');
ModulesManager::file('/actions/report/inc/ReportCli.class.php', 'ximIO');

$parameterCollector = new ReportCli($argc, $argv);

$file = $parameterCollector->getParameter('--file');
$nodeList = $parameterCollector->getParameter('--nodeList');
$filterByStatus = $parameterCollector->getParameter('--filterByStatus');

$dbObj = new DB();
$query = sprintf("SELECT idXimIOExportation FROM XimIOExportations"
		. " WHERE timeStamp = '%s'",
		mysql_real_escape_string($file));
$dbObj->Query($query);
if (!$dbObj->numRows > 0) {
	die (sprintf(_("The  package %s has not been imported with ximIO")."\n", $file));
}
unset($dbObj);
$neededResults = array(' > 0' => "%d nodes has been successfully imported\n",
		' = -1' => "%d nodes has not been successfully imported due to a lack of permits\n",
		' = -2' => "%d nodes has not been successfully imported due to a lack of information in the XML\n",
		' = -3' => "%d nodes has not been successfully imported due to its father was not inserted\n",
		' = -4' => "%d nodes has not been successfully imported due to they were not allowed in its father (NodeAllowedContents restriction)\n"
		);
$neededResults[' > 0']= _("%d nodes has been successfully imported")."\n";
$neededResults[' = -1']= _("%d nodes has not been successfully imported due to a lack of permits")."\n";
$neededResults[' = -2']= _("%d nodes has not been successfully imported due to a lack of information in the XML")."\n";
$neededResults[' = -3']= _("%d nodes has not been successfully imported due to its father was not inserted")."\n";
$neededResults[' = -4']= _("%d nodes has not been successfully imported due to they were not allowed in its father (NodeAllowedContents restriction)")."\n";



reset($neededResults);
while(list($condition, $message) = each($neededResults)) {
	$dbObj = new DB();
	$query = sprintf("SELECT count(*) as total FROM XimIONodeTranslations xnt"
			. " INNER JOIN XimIOExportations xe ON xnt.IdXimioExportation = xe.IdXimioExportation AND xe.timeStamp = %s"
			. " WHERE xnt.status %s",
			$dbObj->sqlEscapeString($file),
			$condition);

	$dbObj->Query($query);
	$totalResults = $dbObj->GetValue('total');
	if ((int)$totalResults > 0) {
		echo sprintf($message, $totalResults);
	}
	unset($dbObj);
}
if ($nodeList == '1') {
	$dbObj = new DB();
	$query = sprintf("SELECT IdExportationNode, status FROM XimIONodeTranslations xnt"
			. " INNER JOIN XimIOExportations xe ON xnt.IdXimioExportation = xe.IdXimioExportation AND xe.timeStamp = %s", 
			$dbObj->sqlEscapeString($file));
	$validFilters = array('-1', '-2', '-3', '-4', '0');
	
	if (in_array($filterByStatus, $validFilters)) {
		if ($filterByStatus == '0') {
			$filterByStatus = '> 0';
		}
		$query .= sprintf(" WHERE xnt.status = %s", $dbObj->sqlEscapeString($filterByStatus));
	}
	
	$dbObj->Query($query);
	echo _("Node, Status")."\n";
	while(!$dbObj->EOF) {
		echo sprintf("%s, %s\n", $dbObj->GetValue('IdExportationNode'), $dbObj->GetValue('status'));
		$dbObj->Next();
	}
}
?>