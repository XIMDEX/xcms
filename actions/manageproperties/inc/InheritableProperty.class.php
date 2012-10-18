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



abstract class InheritableProperty {

	protected $nodeId = null;
	protected $node = null;
	protected $nodeTypeId = null;
	protected $nodeType = null;

	public function __construct($nodeId) {
		$this->nodeId = $nodeId;
		$this->node = new Node($nodeId);
		if ($this->node->get('IdNode') < 1) {
			// TODO: Log error
		}
		$this->nodeTypeId = $this->node->get('IdNodeType');
		$this->nodeType = new NodeType($this->nodeTypeId);
	}

	/**
	 * Returns the property name that class represents
	 */
	abstract public function getPropertyName();

	/**
	 * Returns the property values
	 */
	abstract public function getValues();

	/**
	 * Sets the property values
	 * @param mixed $values
	 */
	abstract public function setValues($values);

	/**
	 * Applies the property values recursively
	 * @param mixed $values
	 */
	abstract public function applyPropertyRecursively($values);

	/**
	 * Returns the affected nodes when deleting a property value
	 * @param mixed $values Values to be deleted
	 */
	abstract public function getAffectedNodes($values);

	/**
	 * Updates the affected nodes when deleting a property value
	 * @param unknown_type $values
	 */
	abstract protected function updateAffectedNodes($values);

	/**
	 * Returns the affected properties
	 * @param mixed $values
	 */
	protected function getAffectedProperties($values) {

		if (!is_array($values) || count($values) == 0) return array();

		// Selected properties on node
		$nodeProperties = $this->getProperty(false);
		if (empty($nodeProperties)) $nodeProperties = array();

		// Properties to be deleted
		$propertiesToDelete = array_diff($nodeProperties, $values);

		return $propertiesToDelete;
	}

	/**
	 * Returns the property values
	 * @param boolean $inherited
	 */
	protected function getProperty($inherited=true) {
		$prop = $this->getPropertyName();
		return $this->node->getProperty($prop, $inherited);
	}

	/**
	 * Sets the property values
	 * @param mixed $values
	 */
	protected function setProperty($values) {
		$prop = $this->getPropertyName();
		return $this->node->setProperty($prop, $values);
	}

	/**
	 * Deletes the property values
	 * @param mixed $values
	 */
	protected function deleteProperty($values) {
		$this->deleteChildrenProperties($values);
		$prop = $this->getPropertyName();
		$ret = $this->node->deleteProperty($prop);
		return $ret;
	}

	/**
	 * Delete the properties of children nodes
	 * @param mixed $values
	 */
	protected function deleteChildrenProperties($values) {

		$propertiesToDelete = $this->getAffectedProperties($values);
		if (count($propertiesToDelete) == 0) return;

		$prop = $this->getPropertyName();
		$db = new DB();

		$sql = "select distinct(p.IdNode) as IdNode
				from FastTraverse f  join NodeProperties p on f.idchild = p.idnode
				where f.idnode = %s
					and f.depth > 0
					and p.property = '%s'
					and p.value in ('%s')";
		$sql = sprintf($sql, $this->nodeId, $prop, implode("', '", $propertiesToDelete));

		$db->query($sql);
		while (!$db->EOF) {
			$childId = $db->getValue('IdNode');
			$child = new Node($childId);
			foreach ($propertiesToDelete as $value) {
				$child->deletePropertyValue($prop, $value);
			}
			$db->next();
		}
	}
}
