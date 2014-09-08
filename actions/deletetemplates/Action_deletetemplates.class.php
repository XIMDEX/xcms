<?php

/******************************************************************************
 *  Ximdex a Semantic Content Management System (CMS)    							*
 *  Copyright (C) 2011  Open Ximdex Evolution SL <dev@ximdex.org>	      *
 *                                                                            *
 *  This program is free software: you can redistribute it and/or modify      *
 *  it under the terms of the GNU Affero General Public License as published  *
 *  by the Free Software Foundation, either version 3 of the License, or      *
 *  (at your option) any later version.                                       *
 *                                                                            *
 *  This program is distributed in the hope that it will be useful,           *
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of            *
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the             *
 *  GNU Affero General Public License for more details.                       *
 *                                                                            *
 * See the Affero GNU General Public License for more details.                *
 * You should have received a copy of the Affero GNU General Public License   *
 * version 3 along with Ximdex (see LICENSE).                                 *
 * If not, see <http://gnu.org/licenses/agpl-3.0.html>.                       *
 *                                                                            *
 * @version $Revision: $                                                      *
 *                                                                            *
 *                                                                            *
 ******************************************************************************/


class Action_deletetemplates extends ActionAbstract {

    function index() {
 		$idNode	= (int) $this->request->getParam("nodeid");

		$folder = new Node($idNode);
		$nodes_templates = $folder->GetChildren();

		$templates = array();
		if(!empty($nodes_templates) ) {
			foreach($nodes_templates as $id => $_template) {
				$node_template = new Node($_template);
				$templates[$id]["Id"] = $_template;
				$templates[$id]["Name"] = $node_template->get("Name");
			}
		}


 		$values = array(
			'id_node' => $idNode,
			'templates' => $templates,
			'go_method' => 'delete'
		);

		$this->addJs('/actions/deletetemplates/js/delete_templates.js');
		$this->addCSS('/actions/deletetemplates/css/style.css');


		$this->render($values, 'index', 'default-3.0.tpl');
    }

	function delete() {
		$templates = $this->request->getParam("templates");
 		$idNode	= (int) $this->request->getParam("nodeid");

		$new_templates = array();
		if(!empty($templates) )  {
			$i = 0;
			foreach($templates as $_template) {
				$node = new Node($_template);
				$new_templates[$i]["Id"] = $_template;
				$new_templates[$i]["Name"] = $node->get("Name");
				$new_templates[$i]["Result"] = $node->delete();
				$i++;
			}
		}

 		$values = array(
			'id_node' => $idNode,
			'templates' => $new_templates,
			'go_method' => 'delete'
		);

		$this->addCSS('/actions/deletetemplates/css/style.css');
		$this->addJs('/actions/deletetemplates/js/delete_templates.js');

		$this->reloadNode($idNode);
		$this->render($values, 'result', 'default-3.0.tpl');
	}

}