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

use Ximdex\NodeTypes\NodeTypeConstants;
use Ximdex\NodeTypes\NodeTypeGroupConstants;

class TransformerProperty extends InheritableProperty
{
    /**
     * {@inheritDoc}
     * @see \Ximdex\Properties\InheritableProperty::getPropertyName()
     */
	public function getPropertyName()
	{
		return 'Transformer';
	}

	/**
	 * {@inheritDoc}
	 * @see \Ximdex\Properties\InheritableProperty::getValues()
	 */
	public function getValues()
	{
		// All system transformers
		$_availableTransformers = array(
			array(
				'IdTransformer' => -1,
				'Name' => 'Obtener valor heredado'
			),
			array(
				'IdTransformer' => 'xslt',
				'Name' => 'XSLT'
			)
		);

		// Selected transformers on the node
		$nodeTransformers = $this->getProperty(false);
		if (empty($nodeTransformers)) {
		    $nodeTransformers = array();
		}
		$availableTransformers = array();
		if ( in_array( $this->nodeTypeId,NodeTypeGroupConstants::NODE_PROJECTS ) ) {
			// The Project node shows all the system transformers
			$availableTransformers = $_availableTransformers;
			unset($availableTransformers[0]);
			$nodeTransformers = $this->getProperty(true);
		} else {

			// Nodes below the Project shows only inherited transformers
			$inheritedTransformers = $this->getProperty(true);
			if (empty($inheritedTransformers)) {
			    $inheritedTransformers = array();
			}
			foreach ($_availableTransformers as $trans) {
				if (in_array($trans['IdTransformer'], $inheritedTransformers)) {
					$availableTransformers[] = $trans;
				}
			}
		}
		foreach ($availableTransformers as & $trans) {
			$trans['Checked'] = in_array($trans['IdTransformer'], $nodeTransformers) ? true : false;
		}
		return $availableTransformers;
	}

	/**
	 * {@inheritDoc}
	 * @see \Ximdex\Properties\InheritableProperty::setValues()
	 */
	public function setValues(array $values)
	{
		$affected = $this->updateAffectedNodes($values);
		$this->deleteProperty($values);
		if (intval($values) != -1) {
			$this->setProperty($values);
		}
		return array('affectedNodes' => $affected, 'values' => $values);
	}

	/**
	 * {@inheritDoc}
	 * @see \Ximdex\Properties\InheritableProperty::updateAffectedNodes()
	 */
	protected function updateAffectedNodes(array $values)
	{
		return [];
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
}
