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


use Ximdex\Models\Node;
use Ximdex\Models\NodeType;
use Ximdex\MVC\ActionAbstract;
use Ximdex\Utils\Sync\SynchroFacade;
use Ximdex\Utils\Sync\SyncManager;



class Action_publicateximlet extends ActionAbstract {

	function index() {
		$docsToPublish=array();
      		$idNode	= (int) $this->request->getParam("nodeid");
		$params = $this->request->getParam("params");

		$node = new Node($idNode);
		$serverID = $node->getServer();

		$nodeServer = new Node($serverID);
		$nameServer = $nodeServer->get('Name');
		$physicalServers = $nodeServer->class->GetPhysicalServerList(true);

		if (!(sizeof($physicalServers) > 0)) {
			$this->messages->add(sprintf(_("No physical server has been defined in '%s'"), $nameServer), MSG_TYPE_ERROR);
			$this->render(array('messages' => $this->messages->messages));
			return;
		}

		$docs = $this->_getRefererDocs($idNode);

		$actionDescription = sprintf(_("Following documents and sections are going to be published:"), count($docsToPublish));
		$alertDescription = sprintf(_("Are you sure you want to publish?"));

		$this->addJs('/actions/publicateximlet/resources/js/handler.js');

		$values = array(
			'actionDescription' => $actionDescription,
			'alertDescription' => $alertDescription,
			'docs' => $docs,
			'params' => $params,
			'go_method' => 'publicate_ximlet',
		);

		if (count($this->messages->messages) > 0) {
			$values['messages'] = $this->messages->messages;
		}

		$this->render($values, NULL, 'default-3.0.tpl');
    	}

	function publicate_ximlet() {

		$idNode	= $this->request->getParam("nodeid");
		$idAction = $this->request->getParam("actionid");
		$upDate = time();

		if (ModulesManager::isEnabled('ximSYNC')) {							
			ModulesManager::file('/inc/manager/SyncManager.class.php', 'ximSYNC');
			$syncMngr = new SyncManager();
			$syncMngr->setFlag('forcePublication', true);
			$syncResult = $syncMngr->pushDocInPublishingPool($idNode, $upDate, NULL);

		}else {
			$flags = array('force' => true);
				
			$syncFacade = new SynchroFacade();
			$syncResult = $syncFacade->pushDocInPublishingPool($idNode, $upDate, NULL, $flags);
		}


		//$values = array("result" => $syncResult);
		//$this->render($values, 'publicate_ximlet', 'default-3.0.tpl');
		$this->messages->add(_("The node has been successfully sent to publish"), MSG_TYPE_NOTICE);

		$values = array(
			'messages' => $this->messages->messages,
		);

		$this->sendJSON($values);
	}

	/**
	 * Returns all documents and sections associateds to ximlet
	 *
	 * @param int idNode
	 * @return array / NULL
	 */
	 private function _getRefererDocs($ximletId) {

	 	$node = new Node($ximletId);
		$params = array();
		$params['forcePublication'] = 1;

		$docsToPublish = $node->class->getPublishabledDeps($params);

		if(sizeOf($docsToPublish) > 0 ) {
			$docsToPublish = array_unique(array_merge(array($ximletId), $docsToPublish));
		}else {
			$docsToPublish = array($ximletId);
		}


		$docs = array();
		
		foreach ($docsToPublish as $docID) {
             		$docNode = new Node($docID);
		 	$docNodeType = new NodeType($docNode->get('IdNodeType'));
		     	$docs[$docID] = array(
				'type' => $docNodeType->get('Name'),
 				'name' => $docNode->get('Name'),
				'id' => $docID,
				'path' => $docNode->getPath()
			);
		}

		return $docs;
	 }

}

?>