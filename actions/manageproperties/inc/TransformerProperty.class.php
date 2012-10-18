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

class TransformerProperty extends InheritableProperty {

	public function getPropertyName() {
		return 'Transformer';
	}

	public function getValues() {

		// All system transformers
		$_availableTransformers = array(
			array(
				'IdTransformer' => -1,
				'Name' => 'Obtener valor heredado'
			),
			array(
				'IdTransformer' => 'dext',
				'Name' => 'DEXT'
			),
			array(
				'IdTransformer' => 'xslt',
				'Name' => 'XSLT'
			)
		);

		// Selected transformers on the node
		$nodeTransformers = $this->getProperty(false);
		if (empty($nodeTransformers)) $nodeTransformers = array();

		$availableTransformers = array();

		if ($this->nodeTypeId == 5013) {

			// The Project node shows all the system transformers
			$availableTransformers = $_availableTransformers;
			unset($availableTransformers[0]);
			$nodeTransformers = $this->getProperty(true);

		} else {

			// Nodes below the Project shows only inherited transformers
			$inheritedTransformers = $this->getProperty(true);
			if (empty($inheritedTransformers)) $inheritedTransformers = array();

			foreach ($_availableTransformers as $trans) {

				if (in_array($trans['IdTransformer'], $inheritedTransformers)) {
					$availableTransformers[] = $trans;
				}
			}
		}

		foreach ($availableTransformers as &$trans) {

			$trans['Checked'] = in_array($trans['IdTransformer'], $nodeTransformers) ? true : false;
		}

		return $availableTransformers;
	}

	public function setValues($values) {

		$affected = $this->updateAffectedNodes($values);
		$this->deleteProperty($values);

		if (intval($values) != -1) {

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
