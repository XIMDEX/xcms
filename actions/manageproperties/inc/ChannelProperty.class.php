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



ModulesManager::file('/actions/manageproperties/inc/InheritableProperty.class.php');
ModulesManager::file('/inc/model/channel.inc');


class ChannelProperty extends InheritableProperty {

	public function getPropertyName() {
		return 'channel';
	}

	public function getValues() {

		// Selected channels on the node
		$nodeChannels = $this->getProperty(false);
		if (empty($nodeChannels)) $nodeChannels = array();

		$channel = new Channel();

		// The Project node shows all the system channels
		$availableChannels = $channel->find('IdChannel, Name');

		if ($this->nodeTypeId != 5013) {

			// Nodes below the Project shows only inherited channels
			$parentId = $this->node->getParent();
			$parent = new Node($parentId);
			$inheritedChannels = $parent->getProperty($this->getPropertyName(), true);
			

			if (empty($inheritedChannels)) {

				// Inherits all the system properties
				$inheritedChannels = $availableChannels;

			} else {

				$availableChannels = $channel->find(
					'IdChannel, Name', 'IdChannel in (%s)',
					array(implode(', ', $inheritedChannels)),
					MULTI, false
				);
			}
		}


		foreach ($availableChannels as &$channel) {

			unset($channel[0], $channel[1]);			
			//If is availableChannel and nodeChannels is empty, we use the availableChannels
			if (count($nodeChannels))
				$channel['Checked'] = in_array($channel['IdChannel'], $nodeChannels) ? true : false;
			else
				$channel['Checked'] = 1;
		}
		return $availableChannels;
	}

	public function setValues($values) {

		if (!is_array($values)) $values = array();

		$affectedNodes = $this->updateAffectedNodes($values);
		$this->deleteProperty($values);

		if (is_array($values) && count($values) > 0) {

			$this->setProperty($values);
		}

		return array('affectedNodes' => $affectedNodes, 'values' => $values);
	}

	public function getAffectedNodes($values) {

		$channelsToDelete = $this->getAffectedProperties($values);
		$strChannels = implode(', ', $channelsToDelete);

		if (count($values) == 0 || count($channelsToDelete) == 0) {
			// Inherits all the channels or there are channels to delete
			return false;
		}

		$sql = 'select distinct(r.IdDoc) as affectedNodes
				from FastTraverse f join RelStrDocChannels r on f.IdChild = r.IdDoc
				where f.IdNode = %s and r.IdChannel in (%s)';

		$sqlAffectedNodes = sprintf(
			$sql,
			$this->nodeId,
			$strChannels
		);

		// Nodes to unjoin from channels
		$affectedNodes = array();
		$db = new DB();
		$db->query($sqlAffectedNodes);
		while (!$db->EOF) {
			$affectedNodes[] = $db->getValue('affectedNodes');
			$db->next();
		}

		if (count($affectedNodes) == 0) return false;

		return array('nodes' => $affectedNodes, 'props' => $channelsToDelete);
	}

	protected function updateAffectedNodes($values) {

		$affectedNodes = $this->getAffectedNodes($values);
		if (!$affectedNodes) return false;

		$sql = 'delete from RelStrDocChannels where IdDoc in (%s) and IdChannel in (%s)';
		$sql = sprintf($sql, implode(', ', $affectedNodes['nodes']), implode(', ', $affectedNodes['props']));

		$db = new DB();
		$db->execute($sql);
		if ($db->numErr != 0) {
	 		XMD_Log::error($this->desErr);
		}

		return array('affectedNodes' => $affectedNodes, 'messages' => array());
	}

	public function applyPropertyRecursively($values) {

		if (empty($values)) $values = array();
		if (!is_array($values)) $values = array($values);
		if (count($values) == 0) return false;

		$sql = "select n.IdNode
			from FastTraverse f
				join Nodes n on f.IdChild = n.IdNode
			where f.IdNode = %s
				and n.IdNodeType in (5032, 5309)";
		$sql = sprintf($sql, $this->nodeId);
//debug::log($sql);

		$nodes = 0;

		$db = new DB();
		$db->query($sql);
		while (!$db->EOF) {

			$nodeId = $db->getValue('IdNode');
			$node = new StructuredDocument($nodeId);
			if (!($node->get('IdDoc') > 0)) {
				XMD_Log::error(_('StructuredDocument cannot be instantiate with ID ') . $nodeID);
				continue;
			}

			$nodes++;

			foreach ($values as $propId) {
				if ($node->hasChannel($propId) == 0) {
					$node->addChannel($propId);
//					$messages[] = array(
//
//					);
				}
			}

			$db->next();
		}

		return array(
			'nodes' => $nodes,
			'values' => $values
		);
	}
}
