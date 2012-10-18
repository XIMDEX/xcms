#!/usr/bin/php -q
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



/**
 *  Update Fast Traverse
 */

if (!defined('XIMDEX_ROOT_PATH'))
	define('XIMDEX_ROOT_PATH', realpath(dirname(__FILE__) . "/../../../"));

include_once(XIMDEX_ROOT_PATH . '/inc/db/db.inc');
include_once(XIMDEX_ROOT_PATH . '/inc/model/node.inc');

function updateFT($node_id) {

	$n = new Node($node_id);
	$n->updateFastTraverse();
	//unset($n);
	xObject::destroy($n);
}

function deleteTraverse() {

	$sql = "DELETE FROM FastTraverse";
	$db = new DB();

	$db->Execute($sql);
}

function updateTraverse() {

	$sql = "SELECT IdNode FROM Nodes";
	$db = new DB();

	$db->Query($sql);

	$i = 0;
	while (!$db->EOF) {
		$i++;
		if(!($i%10)) {
			echo ".";
		}

		$node_id = $db->GetValue('IdNode');
		updateFT($node_id);
		$db->next();
	}

	//echo "Nodos procesados: $i\n";
}


function main($argc, $argv) {

	deleteTraverse();
	updateTraverse();
}


// Entry point.
main($argc, $argv);

?>
