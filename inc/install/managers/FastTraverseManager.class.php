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


include_once(XIMDEX_ROOT_PATH . '/inc/db/db.inc');
include_once(XIMDEX_ROOT_PATH . '/inc/model/node.inc');
require_once(XIMDEX_ROOT_PATH . '/inc/install/managers/InstallManager.class.php');


class FastTraverseManager extends InstallManager{

	/**
	 * Build FastTraverse and full path to every node in Ximdex	 
	 */
	public function buildFastTraverse(){
		$this->deleteFastTraverse();
		$node = new Node();
		$results = $node->find("IdNode",'',array(), MONO);
		$dbUpdate = new DB();
		foreach ($results as $i => $idNode) {
			$node = new Node($idNode);
			$node->updateFastTraverse();
			$path = pathinfo($node->GetPath());
			$this->installMessages->printIteration($i);
			$dbUpdate->execute(sprintf("update Nodes set Path = '%s' where idnode = %s", $path['dirname'], $idNode));			
		}

	}

	/**
	 * Empty fast traverse table in DB
	 */
	private function deleteFastTraverse(){
		$sql = "DELETE FROM FastTraverse";
		$db = new DB();
		$db->Execute($sql);
	}
}

?>