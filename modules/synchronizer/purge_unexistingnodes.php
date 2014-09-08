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



define('XIMDEX_ROOT_PATH', realpath(dirname(__FILE__) . "/../../"));
include_once(XIMDEX_ROOT_PATH . '/inc/modules/ModulesManager.class.php');
ModulesManager::file('/inc/db/db.inc');
ModulesManager::file('/inc/synchro.inc');


$db = new DB();
$sync = new Synchronizer();

$n = 0;
$m = 0;

// Unexisting nodes in Synchronizer table

$sql = "SELECT Distinct(Synchronizer.IdNode) FROM Synchronizer LEFT JOIN Nodes ON Synchronizer.IdNode = Nodes.IdNode 
	WHERE Nodes.IdNode IS NULL";
$db->Query($sql);

if($db->numRows > 0) {
	while (!$db->EOF) {
		$idNode = $db->GetValue('IdNode');

		// Moving frames to history
		$sync = new Synchronizer();
		$sync->moveToHistory($idNode, 'node');

		$n++;
		$db -> Next();
	}
}

// Unexisting servers in Synchronizer table

$sql = "SELECT Distinct(Synchronizer.IdServer) FROM Synchronizer LEFT JOIN Servers 
	ON Synchronizer.IdServer = Servers.IdServer WHERE Servers.IdServer IS NULL";
$db->Query($sql);

if($db->numRows > 0) {
	while (!$db->EOF) {
		$idServer = $db->GetValue('IdServer');

		// Moving frames to history
		$sync = new Synchronizer();
		$sync->moveToHistory($idServer, 'server');
		
		$m++;
		$db -> Next();
	}
}

echo "Nodes found: $n - Servers found: $m\n";
?>