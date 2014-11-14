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



class Action_deleterelnewsarea extends ActionAbstract {

    function index() {

		$idNode	= $this->request->getParam("nodeid");
		$actionID = $this->request->getParam("actionid");
		$areas = array();

		$relNewsArea = new RelNewsArea();
		$listAreas = $relNewsArea->getAreasByNew($idNode);

		if (sizeof($listAreas) > 0) {

			foreach ($listAreas as $idArea) {

				$area = new XimNewsAreas($idArea);
				$areas[] = array('id' => $idArea, 'name' => $area->get('Name'), 'desc' => $area->get('Description'));
			}
		}

		$values = array(
			'id_node' => $idNode,
			'areas' => $areas,
			'num_areas' => sizeof($areas),
			'go_method' => 'delete_relation'
		);

		$this->render($values, 'index', 'default-3.0.tpl');
    }

	function delete_relation() {

		$idNode	= $this->request->getParam('nodeid');
		$areas = $this->request->getParam('areas');

		$node = new Node($idNode);
		$news = $node->GetChildren();

		foreach($news as $idNews) {
			$node = new Node($idNews);
			$nameNews = $node->get('Name');

			foreach ($areas as $idArea) {
				$ximNewsArea = new XimNewsAreas($idArea);
				$areaName = $ximNewsArea->get('Name');

				if (!$node->class->deleteFromArea($idArea)) {
					$this->messages->add(sprintf(_('Error removing the relationship between category %s and news s%.'), $areaName, $nameNews),MSG_TYPE_ERROR);
				} else {
					$this->messages->add(sprintf(_('Deletion of the relationship between category %s and news %s completed successfully.'), $areaName, $nameNews),MSG_TYPE_NOTICE);
				}
			}
		}

		$values = array('messages' => $this->messages->messages);

		$this->render($values, NULL, 'messages.tpl');
	}
}
?>