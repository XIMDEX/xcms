<?php

/**
 *  \details &copy; 2019 Open Ximdex Evolution SL [http://www.ximdex.org]
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

namespace Ximdex\Properties;

use Ximdex\Models\Node;
use Ximdex\NodeTypes\NodeTypeConstants;
use Ximdex\NodeTypes\NodeTypeGroupConstants;

class SchemaProperty extends InheritableProperty
{
	public function getPropertyName()
	{
		return 'DefaultSchema';
	}

	public function getValues()
	{
		// All system schemas
		$_availableSchemas = array(
			array(
			    'IdSchema' => NodeTypeConstants::VISUAL_TEMPLATE,
				'Name' => 'PVD'
			),
			array(
			    'IdSchema' => NodeTypeConstants::RNG_VISUAL_TEMPLATE,
				'Name' => 'RNG'
			)
		);

		// Selected schemas on the node
		$nodeSchemas = $this->getProperty(false);
		if (empty($nodeSchemas)) {
		    $nodeSchemas = array();
		}
		$availableSchemas = array();
		if ( in_array( $this->nodeTypeId,NodeTypeGroupConstants::NODE_PROJECTS ) ) {

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
		foreach ($availableSchemas as & $schema) {
			$schema['Checked'] = in_array($schema['IdSchema'], $nodeSchemas) ? true : false;
		}
		return $availableSchemas;
	}

	/**
	 * {@inheritDoc}
	 * @see \Ximdex\Properties\InheritableProperty::applyPropertyRecursively()
	 */
	public function applyPropertyRecursively(array $values)
	{
		return true;
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Ximdex\Properties\InheritableProperty::get_system_properties()
	 */
	protected function get_system_properties()
    {
	    return [];
	}

	/**
	 * {@inheritDoc}
	 * @see \Ximdex\Properties\InheritableProperty::get_inherit_properties()
	 */
    protected function get_inherit_properties(array $availableProperties)
    {
        return [];
    }
    
    /**
     * {@inheritDoc}
     * @see \Ximdex\Properties\InheritableProperty::updateAffectedNodes()
     */
    protected function updateAffectedNodes(array $values)
    {
        return true;
    }
}
