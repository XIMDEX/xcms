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

class SchemaProperty extends InheritableProperty {

	public function getPropertyName() {
		return 'DefaultSchema';
	}

	public function getValues() {

		// All system schemas
		$_availableSchemas = array(
			array(
				'IdSchema' => 5045,
				'Name' => 'PVD'
			),
			array(
				'IdSchema' => 5078,
				'Name' => 'RNG'
			)
		);

		// Selected schemas on the node
		$nodeSchemas = $this->getProperty(false);
		if (empty($nodeSchemas)) $nodeSchemas = array();

		$availableSchemas = array();


		if ($this->nodeTypeId == 5013) {

			// The Project node shows all the system schemas
			$availableSchemas = $_availableSchemas;

		} else {

			// Nodes below the Project shows only inherited schemas
			$parentId = $this->node->getParent();
			$parent = new Node($parentId);
			$inheritedSchemas = $parent->getProperty($this->getPropertyName(), true);

			if (empty($inheritedSchemas)) {

				// Inherits all the system properties
				$availableSchemas = $_availableSchemas;
			} else {

				foreach ($_availableSchemas as $schema) {

					if (in_array($schema['IdSchema'], $inheritedSchemas)) {
						$availableSchemas[] = $schema;
					}
				}
			}
		}

		foreach ($availableSchemas as &$schema) {

			$schema['Checked'] = in_array($schema['IdSchema'], $nodeSchemas) ? true : false;
		}

		return $availableSchemas;
	}

	public function setValues($values) {

		if (!is_array($values)) $values = array();

		$affected = $this->updateAffectedNodes($values);
		$this->deleteProperty($values);

		if (is_array($values) && count($values) > 0) {

			$this->setProperty($values);
		}

		return array('affectedNodes' => $affected, 'values' => $values);
	}

	public function getAffectedNodes($values) {

		return false;
	}

	protected function updateAffectedNodes($values) {

		return false;
	}

	public function applyPropertyRecursively($values) {

		return false;
	}

}
