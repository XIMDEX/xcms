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



class PVD2RNG_Filters {

	/**
	 * Returns the default rules to apply to all transformed documents.
	 * The format is FilterName => Parameters array
	 */
	static public function getDefaultRules() {
		return array(
			'SkipNode' => array('paracont', 'paraestilo', 'paraenlace', 'parasalto'),
			'FixTableItems' => array('fila', 'elemento')
		);
	}

	/**
	 * Deletes a node from a tree and inserts his children into his parent node.
	 * @param DOMDocument domdoc
	 * @param array params Array of elements names
	 */
	public function filter_SkipNode(&$domdoc, $params) {

		$xpath = new DOMXPath($domdoc);

		foreach ($params as $nodeToSkip) {

			$refs = array();
			$query = "//element[@name='$nodeToSkip']";
			$nodelist = $xpath->query($query);

			foreach ($nodelist as $item) {
				$parent = $item->parentNode;
				$children = $item->childNodes;
				$query = 'element | ref';
				$childlist = $xpath->query($query, $item);
//debug::log('>>> SkipNode: ' . $parent->parentNode->getAttribute('name') . '::' . $parent->getAttribute('name') . '::' . $item->getAttribute('name'));
				foreach ($childlist as $child) {
					$parent->appendChild($child);
					if ($child->nodeName == 'element') {
						$ref = $domdoc->createElement('ref');
						$ref->setAttribute('name', $child->getAttribute('name'));
					} else {
						$ref = $child;
					}
					$refs[] = $ref;
//debug::log('------------ ' . $parent->getAttribute('name') . '.appendChild(' . $child->getAttribute('name') . ')');
				}

//debug::log('------------ ' . $parent->getAttribute('name') . '.removeChild(' . $item->getAttribute('name') . ')');
				$parent->removeChild($item);
			}

			$query = "//ref[@name='$nodeToSkip']";
			$nodelist = $xpath->query($query);

			foreach ($nodelist as $item) {
				$parent = $item->parentNode;
				$parent->removeChild($item);
				foreach ($refs as $ref) {
					$parent->appendChild($ref->cloneNode());
				}
			}
		}

	}

	/**
	 * Deletes a node and all his children from a tree.
	 */
	public function filter_DropNode(&$domdoc, $params) {

	}

	/**
	 * Replaces a single node with the specified one. Don't modify his children.
	 */
	public function filter_ReplaceNode(&$domdoc, $params) {

	}

	/**
	 * Replaces a node and all his children by the specified tree.
	 */
	public function filter_ReplaceTree(&$domdoc, $params) {

	}

	/**
	 * Fixes the table elements named as 'item1..n' by replacing them with a 'item' named element.
	 * @param DOMDocument domdoc
	 * @param array tableElements Elements to fix
	 */
	public function filter_FixTableItems(&$domdoc, $tableElements) {

		$xpath = new DOMXPath($domdoc);
		$query = "//element | //ref";
		$nodelist = $xpath->query($query);

		foreach($nodelist as $node) {

			$name = $node->getAttribute('name');
			$aux = preg_replace('/(\d+)$/', '', $name);

			if (in_array($aux, $tableElements)) {
//debug::log("$name => $aux");
				$query = "//element[@name='$aux']";
				$list = $xpath->query($query);
				if ($list->length == 0 && $node->nodeName == 'element') {
//debug::log("Creating table element: $aux");
					$node->setAttribute('name', $aux);
				} else {

					$query = "//ref[@name='$aux']";
					$exists = $xpath->query($query, $node->parentNode);
					if ($exists->length > 0) {
						$node->parentNode->removeChild($node);
					} else {
						if ($node->nodeName == 'element') {
//debug::log("Referencing table element: $name");
							$ref = $domdoc->createElement('ref');
							$node->parentNode->appendChild($ref);
							$node->parentNode->removeChild($node);
							$node = $ref;
						}
						$node->setAttribute('name', $aux);
					}
				}
			}
		}

	}

}

?>
