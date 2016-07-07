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

/*
	DEPRECATED
*/

use Ximdex\Models\Node;
use Ximdex\Models\NodeType;
use Ximdex\MVC\ActionAbstract;

class Action_addworkflow extends ActionAbstract {
    function index () {
    	$values = array(
    		'id_node' => $this->request->getParam('nodeid'),
    		'go_method' => 'add_workflow'
    	);

    	$this->render($values, null,'default-3.0.tpl');
    }

    function add_workflow() {
    	$idNode = $this->request->getParam('nodeid');
    	$workflowName = $this->request->getParam('workflow');

    	$nodeType = new NodeType();
    	$nodeType->SetByName('Workflow');

    	$node = new Node();
    	$result = $node->CreateNode($workflowName, $idNode, $nodeType->get('IdNodeType'));

    	$this->reloadNode($idNode);
    	if (!$result) {
    		$this->messages->add(_('Workflow could not be successfully inserted'), MSG_TYPE_ERROR);
    		$this->messages->mergeMessages($node->messages);
    	} else {
	    	$this->messages->add(_('Workflow has been successfully inserted'), MSG_TYPE_NOTICE);
    	}
    	$this->render(array('messages' => $this->messages->messages), NULL, 'messages.tpl');
    }
}
?>
