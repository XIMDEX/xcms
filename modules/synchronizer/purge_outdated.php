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
//
ModulesManager::file('/inc/db/db.php');
ModulesManager::file('/inc/sync/synchro.php');

$db = new DB();
$dbDep = new DB();

$sync = new Synchronizer();

$db->Query("SELECT IdSync FROM Synchronizer WHERE State = 'OUT' OR State = 'OUTDATED'");

$numFrames = 0;
$numDep = 0;

if ($db->numRows != 0) {
	while (!$db->EOF) {
		$frameID = $db->GetValue('IdSync');

		$sql = "DELETE FROM SynchronizerDependencies WHERE IdSync = $frameID";
		$dbDep->Execute($sql);

		if ($dbDep->numRows > 0) {
			$numDep++;
		}

		$sync->DeleteSyncFile($frameID);

		XMD_Log::info("Deleting frame $frameID dependencies");
		echo "Deleting frame $frameID dependencies\n";
		$db->Next();
	}
} else {
	XMD_Log::info("Any OUT or OUTDATED frames");
	echo "Any OUT or OUTDATED frames";
}

$db->Execute("DELETE FROM Synchronizer WHERE State = 'OUT' OR State = 'OUTDATED'");

$numFrames = $db->numRows;

XMD_Log::info("Frames deleted: $numFrames - Dependencies deleted: $numDep");
echo "Frames deleted: $numFrames - Dependencies deleted: $numDep\n";
?>