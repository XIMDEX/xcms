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




ModulesManager::file('/inc/model/orm/NodesToPublish_ORM.class.php', 'ximSYNC');
ModulesManager::file('/inc/helper/DebugLog.class.php');


class NodesToPublish extends NodesToPublish_ORM {

	public function __construct($id=null) {
		parent::GenericData($id);
	}

	/**
	 *	Static method that creates a new NodeSet and returns the related object
	 */
	static public function & create($idNode, $idNodeGenerator, $dateUp, $dateDown, $userId, $force) {
		$node = new NodesToPublish();
		$node->set('IdNode', $idNode);
		$node->set('IdNodeGenerator', $idNodeGenerator);
		$node->set('DateUp', $dateUp);
		$node->set('DateDown', $dateDown);
		$node->set('State', 0); //Pending
		$node->set('UserId', $userId);

		$force = $force == true ? 1 : 0;
		$node->set('ForcePublication', $force);

		$dataFactory = new DataFactory($idNode);
		$idVersion = $dataFactory->GetLastVersion();
		$idSubversion = $dataFactory->GetLastSubVersion($idVersion);
		$node->set('Version', $idVersion);
		$node->set('Subversion', $idSubversion);
		$node->add();
		return $node;
	}

	/**
	 *	Get next group of nodes to publish and mark them as locked (1) in database.
	 *	Query is order by DateUp asc to get older publication jobs first.
	 */
	static public function & getNext() {

		$result = null;
		$docsToPublish = array();
		$db = new DB();

		// 1. Get older dateup in table
		$sql_dateup = "select distinct DateUp from NodesToPublish where State = 0 order by DateUp ASC limit 1";
		$db->Query($sql_dateup);

		if ($db->EOF) {
			// No nodes to publish
			Publication_Log::info("No more documents to publish found. Returning null");
			return $result;
		}
		$dateUp = $db->getValue("DateUp");

		// 2. Mark every node with previous dateUp as locked (state=1) to start working on it.
		// (This prevent collisions if multiple BatchManagerDaemons are working at the same time)
		$sql_update ="update NodesToPublish set State = 1 where DateUp = ".$dateUp." and State = 0";
		$db->Query($sql_update);

		// 3. Build and array with locked nodes and their common attributes: dateUp, dateDown, forcePublication and idNodeGenerator
		$sql_nodes ="select IdNode,IdNodeGenerator,ForcePublication,DateDown,UserId from NodesToPublish where DateUp = ".$dateUp." and State = 1";
		$db->Query($sql_nodes);

		$force = true;
		$idNodeGenerator = null;
		$dateDown = null;
		$userId = null;

		while (!$db->EOF) {
			array_push($docsToPublish, $db->getValue('IdNode'));

			$idNodeGenerator = $db->getValue('IdNodeGenerator');
			$force = $db->getValue('ForcePublication');
			$dateDown = $db->getValue('DateDown');
			$userId = $db->getValue('UserId');

			$db->Next();
		}
		$force = $force == 1 ? true : false;
		$dateDown = $dateDown === 0 ? null : $dateDown;
		$result = array (
			'docsToPublish' => $docsToPublish,
			'idNodeGenerator' => $idNodeGenerator,
			'dateUp' => $dateUp,
			'dateDown' => $dateDown,
			'forcePublication' => $force,
			'userId' => $userId
		);

		return $result;
	}

	/**
	 *	Mark a chunk of nodes as processed (2) in database.
	 */
	static public function  setProcessed($chunk, $dateUp) {
		$db = new DB();
		$strNodes = implode (",", $chunk);
		$sql = sprintf("Update NodesToPublish set State = 2 where IdNode in (%s) and DateUp = %s", $strNodes, $dateUp);
		$db->Query($sql);
	}
}
