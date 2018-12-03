<?php

/**
 *  \details &copy; 2018 Open Ximdex Evolution SL [http://www.ximdex.org]
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

namespace Ximdex\Models;

use Ximdex\Logger;
use Ximdex\Runtime\DataFactory;
use Ximdex\Models\ORM\NodesToPublishOrm;

class NodesToPublish extends NodesToPublishOrm
{
	/**
	 * Static method that creates a new NodeSet and returns the related object
	 */
	public function create(int $idNode, int $idNodeGenerator, int $dateUp, int $dateDown = null, int $userId = null, bool $force = false
	    , bool $lastPublishedVersion = false, int $deepLevel = 0) : ?bool
	{    
		$dataFactory = new DataFactory($idNode);
		$idVersion = $dataFactory->GetLastVersion();
		if ($idNode != $idNodeGenerator && $lastPublishedVersion) {
			$idSubversion = 0;
		} else {
			$idSubversion = $dataFactory->GetLastSubVersion($idVersion);
		}
		$versionZero = ! $idVersion && ! $idSubversion;
		if ($versionZero && $idNode != $idNodeGenerator) {
		    $myNode = new Node($idNode);
		    if ($myNode->nodeType->get('IsStructuredDocument')) {
		        Logger::warning(sprintf('Skipping 0.0 version for linked structured document: %s (%s)', $myNode->GetNodeName(), $idNode));
		        return null;
		    }
		}
		$this->set('IdNode', $idNode);
		$this->set('IdNodeGenerator', $idNodeGenerator);
		$this->set('DateUp', $dateUp);
		$this->set('DateDown', $dateDown);
		$this->set('State', 0); //Pending
		$this->set('UserId', $userId);
		if ($idNode == $idNodeGenerator) {
		    $force = true;
		}
		$this->set('ForcePublication', $force ? 1 : 0);
		$this->set('DeepLevel', $deepLevel);
		$this->set('Version', $idVersion);
		$this->set('Subversion', $idSubversion);
		if (! $this->add()) {
		    return false;
		}
		return true;
	}

	/**
	 *	Get next group of nodes to publish and mark them as locked (1) in database.
	 *	Query is order by DateUp asc to get older publication jobs first.
	 */
	static public function getNext()
	{
		$result = null;
		$docsToPublish = array();
		$docsToPublishVersion = array();
		$docsToPublishSubVersion = array();
		$db = new \Ximdex\Runtime\Db();

		// 1. Get older dateup in table
		$sql_dateup = 'select distinct DateUp, DateDown from NodesToPublish where State = 0 order by DateUp ASC limit 1';
		$db->Query($sql_dateup);
		if ($db->EOF) {
			// No nodes to publish
			Logger::info('No more documents to publish found. Returning null');
			return $result;
		}
		$dateUp = $db->getValue('DateUp');
		$dateDown = $db->getValue('DateDown');

		// 2. Mark every node with previous dateUp as locked (state=1) to start working on it.
		// (This prevent collisions if multiple batch Manager Daemons are working at the same time)
		$sql_update ='update NodesToPublish set State = 1 where DateUp = ' . $dateUp . ' and State = 0';
		if (! empty($dateDown)) {
			$sql_update .= ' and DateDown = ' . $dateDown;
		}
		else {
			$sql_update .= ' and DateDown is NULL';
		}
		$db->Query($sql_update);

		// 3. Build and array with locked nodes and their common attributes: dateUp, dateDown, forcePublication and idNodeGenerator
		$sql_nodes ='select IdNode, IdNodeGenerator, ForcePublication, DateDown, UserId, Version, SubVersion from NodesToPublish where DateUp = ' 
		      . $dateUp . ' and State = 1';
		if (! empty($dateDown)) {
			$sql_nodes .= ' and DateDown = ' . $dateDown;
		}
		else {
			$sql_nodes .= ' and DateDown is NULL';
		}
		$sql_nodes .= ' order by deepLevel DESC';
		$db->Query($sql_nodes);
		$force = [];
		$idNodeGenerator = null;
		$userId = null;
		while (! $db->EOF) {
			array_push($docsToPublish, $db->getValue('IdNode'));
			$docsToPublishVersion[$db->getValue('IdNode')] = $db->getValue('Version');
			$docsToPublishSubVersion[$db->getValue('IdNode')] = $db->getValue('SubVersion');
			if (! $idNodeGenerator) {
			    $idNodeGenerator = $db->getValue('IdNodeGenerator');
			}
			$force[$db->getValue('IdNode')] = $db->getValue('ForcePublication');
			if (! $userId) {
			    $userId = $db->getValue('UserId');
			}
			$db->Next();
		}
		$result = array (
			'docsToPublish' => $docsToPublish,
			'idNodeGenerator' => $idNodeGenerator,
			'dateUp' => $dateUp,
			'dateDown' => $dateDown,
			'forcePublication' => $force,
			'userId' => $userId,
			'docsToPublishVersion' => $docsToPublishVersion,
			'docsToPublishSubVersion' => $docsToPublishSubVersion
		);
		return $result;
	}

	/**
	 * Mark a chunk of nodes as processed (2) in database.
	 */
	static public function setProcessed($chunk, $dateUp)
	{
		$strNodes = implode (',', $chunk);
		$sql = sprintf('Update NodesToPublish set State = 2 where IdNode in (%s) and DateUp = %s', $strNodes, $dateUp);
		$db = new \Ximdex\Runtime\Db();
		$db->Query($sql);
	}

	/**
	 * @param $idNode
	 * @param int $idNodeGenerator
	 * @return array
	 */
	public function getIntervals($idNode, int $idNodeGenerator = null)
	{
		$arrayDates = array();
		$now = time();
		$j = 0;
		$fields = 'DateUp, DateDown';
		$condition = '(DateUp > %s or DateDown > %s)';
		$order = 'DateUp';
		if ($idNodeGenerator) {
		    $fields .= ', COUNT(IdNode) as nodes';
		    $condition .= ' AND IdNodeGenerator = ' . $idNodeGenerator;
		    $group = 'DateUp';
		}
		else {
		    $group = null;
		    $condition .= ' AND IdNode = ' . $idNode;
		}
		$results = $this->find($fields, $condition, array($now, $now), true, true, null, $order, $group);
		if ($results === false) {
		    return false;
		}
		if (is_null($results)) {
			return array();
		}
		foreach ($results as $row) {
			$arrayDates[$j]['start'] = $row['DateUp'];
			$arrayDates[$j]['end'] = ($row['DateDown']) ? $row['DateDown'] : null;
			if ($idNodeGenerator) {
			    $arrayDates[$j]['nodes'] = $row['nodes'];
			}
			$j++;
		}
		return $arrayDates;
	}
}
