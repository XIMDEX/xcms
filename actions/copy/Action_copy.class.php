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
 *  @version $Revision: 8029 $
 */




ModulesManager::file('/inc/utils.inc');
ModulesManager::file('/inc/persistence/XSession.class.php');
ModulesManager::file('/inc/model/orm/XimIOExportations_ORM.class.php');
ModulesManager::file('/inc/model/orm/NodeAllowedContents_ORM.class.php');
ModulesManager::file('/actions/copy/baseIO.php');

class Action_copy extends ActionAbstract {

    function index () {
		$this->addJs('/actions/copy/resources/js/treeSelector.js');

		$ximIOExportations = new XimIOExportations_ORM();
		$result = $ximIOExportations->find('idXimIOExportation');
		$father = $this->request->getParam('changeName');

		//Checking for ximIO module
   		 if (!ModulesManager::isEnabled('ximIO')) {
			$this->messages->add(_('The ximIO module should be activated to allow copy of nodes.'), MSG_TYPE_ERROR);
			$this->render(array('messages' => $this->messages->messages));
			$this->renderMessages();
			//return;
		}

/*		if (count($result) > 0) {
			$this->messages->add(_('No se puede ejecutar la acción puesto que hay paquetes ximIO pendientes, si necesita ejecutar la acción consulte con su administrador'), MSG_TYPE_ERROR);
			$values = array('messages' => $this->messages->messages);
			$this->renderMessages();
		} else {
*/
			$node = new Node($this->request->getParam('nodeid'));

			if (!($node->get('IdNode') > 0)) {
				$this->messages->add(_('Error with parameters'), MSG_TYPE_ERROR);
				$values = array('messages' => $this->messages->messages);
				$this->renderMessages();
			} else {
				$targetFile = sprintf(Config::getValue("UrlRoot").
							'/inc/widgets/treeview/helpers/treeselector.php?nodetype=%s&filtertype=%s&targetid=%s',
							urlencode($node->nodeType->get('IdNodeType')),
							urlencode($node->nodeType->Get('Name')),
							urlencode($node->get('IdNode')));

				$values = array(
					'id_node' => $node->get('IdNode'),
					'nodetypeid' => $node->nodeType->get('IdNodeType'),
					'filtertype' => $node->nodeType->get('Name'),
					'target_file' => $targetFile,
					'node_path' => $node->GetPath(),
					'go_method' => 'copyNodes'
				);

				$this->render($values, NULL, 'default-3.0.tpl');
			}
		//}
    }

    function copyNodes() {
//Extracts info of actual node which the action is executed
		$nodeID	= $this->request->getParam("nodeid");
		$node= new Node($nodeID);
		$destIdNode = $this->request->getParam('targetid');
		$target=new Node($destIdNode);

		$nodename=$node->Get('Name');
		$idnode=$node->Get('IdNode');
		$idnodetype=$node->nodeType->get('IdNodeType');

		$nodeID	= $this->request->getParam("nodeid");
		$destIdNode = $this->request->getParam('targetid');

		$recursive = $this->request->getParam('recursive');
		$recursive = $recursive == 'on' ? true : false;

		if ($nodeID == $destIdNode) {
			$this->messages->add(_('Source node cannot be the same as destination node'), MSG_TYPE_ERROR);
			$this->render(array('messages' => $this->messages->messages));
			return;
		}

		$this->messages = copyNode($nodeID, $destIdNode, $recursive);
		$this->reloadNode($destIdNode);

		$values = array('messages' => $this->messages->messages);
		$this->render($values, 'index');
	}

	function checkNodeName(){

		$actionNodeId=$this->request->getParam("nodeid"); //node to copy
		$destNodeId = $this->request->getParam('targetid');//destination node
		$actionNode= new Node($actionNodeId);
		$data = $actionNode->checkTarget($destNodeId);

		$this->sendJSON($data);
	}
}
?>
