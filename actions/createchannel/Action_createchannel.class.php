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


class Action_createchannel extends ActionAbstract {
	// Main method: shows initial form
    	function index () {
		$idNode = $this->request->getParam('nodeid');

		$values = array(
			'id_node' => $idNode,
			'go_method' => 'createchannel');
		$this->render($values, null, 'default-3.0.tpl');
    	}

    	function createchannel() {

    		$idNode = $this->request->getParam('id_node');
    		$name = $this->request->getParam('name');
    		$extension = $this->request->getParam('extension');
    		$description = $this->request->getParam('description');
    		$renderMode = $this->request->getParam('rendermode');

		$nodeType = new NodeType();
		$nodeType->SetByName('Channel');

		$node = new Node();
		$complexName = sprintf("%s.%s", $name, $extension);
		// Control uniqueness of tupla, channel, format.
		$result = $node->CreateNode($complexName, $idNode, $nodeType->get('IdNodeType'), NULL, $name, $extension, NULL, $description, '', $renderMode);

		if ($result > 0) {
			$node->messages->add(_('Channel has been succesfully inserted'), MSG_TYPE_NOTICE);
		}

		$this->reloadNode($idNode );
		$values = array('messages' => $node->messages->messages );
		$this->render($values, NULL);
    	}
}
?>
