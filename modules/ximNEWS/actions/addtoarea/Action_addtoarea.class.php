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




ModulesManager::file('/inc/model/XimNewsAreas.php', 'ximNEWS');
ModulesManager::file('/inc/model/XimNewsColector.php', 'ximNEWS');


class Action_addtoarea extends ActionAbstract {

    function index() {

      	$idNode	= (int) $this->request->getParam("nodeid");
		$actionID = (int) $this->request->getParam("actionid");
		$params = $this->request->getParam("params");

	    $node = new Node($idNode);
		$nodes = $node->GetChildren();
		$childNode = new Node($nodes[0]);

		// gets areas

		$areas = array();
		$areasRelated = array();
		$obj_areas = new  XimNewsAreas();
		$allAreas = $obj_areas->GetAllAreas();
		
		if (sizeof($allAreas) > 0) {

			foreach ($allAreas as $dataArea) {
				$colector = new XimNewsColector();
				$colectors = $colector->getColectorsByArea($dataArea['IdArea']);

				if ($childNode->get('IdNode') > 0) {
					if ($childNode->class->hasArea($dataArea['IdArea'])) {

						$areasRelated[] = array('id' => $dataArea['IdArea'], 'name' => $dataArea['Name'], 
								'description' => $dataArea['Description'], 'colectores' => array_reduce((array) $colectors, 
								create_function('$text, $input', 'return $text .= " ".$input["Name"];')));
					} else {
						$areas[] = array('id' => $dataArea['IdArea'], 'name' => $dataArea['Name'], 
								'description' => $dataArea['Description'], 'colectores' => array_reduce((array) $colectors, 
								create_function('$text, $input', 'return $text .= " ".$input["Name"];')));
					}
				}
			}
		}

		$values = array(
			'areascount' => sizeof($allAreas),
			'id_node' => $idNode,
			'go_method' => 'addToArea',
			'areas' => $areas,
			'areasrelated' => $areasRelated,
			'nodeUrl' => \App::getValue( 'UrlRoot')."/xmd/loadaction.php?actionid=$actionID&nodeid=$idNode"
		);

		$this->render($values, 'index', 'default-3.0.tpl');
    }

	/**
	* Adds relation news-area
	*/

	function addToArea() {

		$areas = (array) $this->request->getParam('areas');
		$nodeID = $this->request->getParam('nodeid');

		$node = new Node($nodeID);
		$nodes = $node->GetChildren();

		foreach($nodes as $idNews) {

			foreach($areas as $idArea) {

				$ximNewsArea = new XimNewsAreas($idArea);
				$areaName = $ximNewsArea->get('Name');

				$newsNode = new Node($idNews);

				if (!$newsNode->class->addToArea($idArea)) {
					$this->messages->add(sprintf(_("The association with the area %s was NOT successfully performed."),$areaName), MSG_TYPE_NOTICE);
				} else {
					$this->messages->add(sprintf(_("The association with the area %s was successfully performed."),$areaName), MSG_TYPE_NOTICE);
				}
			}
		}

		$values = array(
			'messages' => $this->messages->messages,
		);

		$this->render($values);
	}
}
?>