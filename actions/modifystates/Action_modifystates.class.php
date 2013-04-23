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








ModulesManager::file('/inc/pipeline/Pipeline.class.php');
ModulesManager::file('/inc/workflow/Workflow.class.php');

define('NODETYPE_WORKFLOW_STATE', 5036);

class Action_modifystates extends ActionAbstract {
   // Main method: shows initial form
    function index () {
    	$idNode = $this->request->getParam('nodeid');
    	$pipeline = new Pipeline();

    	$pipeline->loadByIdNode($idNode);
    	// in this case there is just one process

    	$pipeProcess = $pipeline->processes->next();


    	/**
		 *  @var $transition PipeTransition
    	 */
    	$statusTransitions = array();
    	$allStatusInfo = array();
    	if (!empty($pipeProcess)) {
	    	$pipeProcess->transitions->reset();

	    	while ($transition = $pipeProcess->transitions->next()) {

	    		$currentStatus = new PipeStatus($transition->get('IdStatusFrom'));
	    		$nextStatus = new PipeStatus($transition->get('IdStatusTo'));

	    		$statusTransitions[$transition->get('id')] = sprintf('%s->%s',
							$currentStatus->get('Name'), $nextStatus->get('Name'));

				$allStatusInfo[$currentStatus->get('id')] =
						array('NAME' => $currentStatus->get('Name'),
								'DESCRIPTION' => $currentStatus->get('Description'));
	    	}


			$allStatusInfo[$nextStatus->get('id')] =
					array('NAME' => $nextStatus->get('Name'),
					'DESCRIPTION' => $nextStatus->get('Description'));
    	}

        $query = App::get('QueryManager');
        $actionCreate = $query->getPage() . $query->buildWith(array('method' => 'create_state'));
        $actionUpdate = $query->getPage() . $query->buildWith(array('method' => 'update_states'));


		$this->addJs('/actions/modifystates/javascript/manager.js');

		$this->addCss('/xmd/style/forms/tabulators.css');
		$this->addCss('/actions/modifystates/resources/css/default.css');

		$nodeType = new NodeType();
		$allNodeTypes = $nodeType->find('IdNodeType, Name', 'IsPublicable = 1', array());

		foreach ($allNodeTypes as $nodeTypeInfo) {
			$nodeTypeValues[$nodeTypeInfo['IdNodeType']] = $nodeTypeInfo['Name'];
		}

		$checkUrl = Config::getValue('UrlRoot') . '/xmd/loadaction.php?actionid='
			. $this->request->getParam('actionid') . '&nodeid=' . $this->request->getParam('nodeid')
			. '&id_nodetype=IDNODETYPE&is_workflow_master=ISWORKFLOWMASTER&method=checkNodeDependencies';

		$values = array('all_status_info' => $allStatusInfo,
						'status_transitions' => $statusTransitions,
						'action_create' => $actionCreate,
						'action_update' => $actionUpdate,
						'nodetype_list' => $nodeTypeValues,
						'selectedNodetype' => $pipeline->get('IdNodeType'),
						'is_workflow_master' => $pipeline->isWorkflowMaster,
						'id_nodetype' => $pipeline->get('IdNodeType'),
						'url_to_nodelist' => $checkUrl,
						'idNode' => $idNode
						);

		$this->render($values, null, 'default-3.0.tpl');
    }

    function create_state() {
    	$name = $this->request->getParam('name');
    	$description = $this->request->getParam('description');
    	$idTransition = $this->request->getParam('transition');

    	$pipeTransition = new PipeTransition($idTransition);
    	$result = $pipeTransition->addStatus($name, $description);

    	if ($result > 0) {
    		$this->messages->add(_('State has been successfully added'), MSG_TYPE_NOTICE);
	    	$this->redirectTo('index');
    	}

    	$this->render($this->messages->messages, NULL, 'messages.tpl');
    }

    function update_states() {
    	$idNode = $this->request->getParam('nodeid');
    	$names = $this->request->getParam('name');
    	$descriptions = $this->request->getParam('description');
    	$idNodeType = $this->request->getParam('id_nodetype');
    	$isWorkFlowMaster = (bool) $this->request->getParam('is_workflow_master');

    	foreach ($names as $key => $name) {
    		$pipeStatus = new PipeStatus($key);
    		$pipeStatus->set('Name', $name);
    		$pipeStatus->set('Description', $descriptions[$key]);
    		$pipeStatus->update();
			//There aren't nodes for pipestatus
    		//$node = new Node($idNode);
    		//$node->set('Name', $name);
    		//$node->update();
    	}

		$workflow = new WorkFlow(NULL, NULL, $idNode);

		$pipeProcess = $workflow->pipeProcess;

		$transitionOrder = array_keys($names);
		$order = 0;
		while ($transition = $pipeProcess->transitions->next()) {
			$transition->set('IdStatusFrom', $transitionOrder[$order]);
			$transition->set('IdStatusTo', $transitionOrder[($order +1)]);
			$transition->update();
			$order ++;
		}

		$workflow->setNodeType($idNodeType);

		if ($isWorkFlowMaster) {
			if ($workflow->pipeline->get('IdNode') != Config::getValue('IdDefaultWorkflow')) {
				$workflow->setWorkflowMaster();
			}
		}
    	$this->redirectTo('index');
    }

    function checkNodeDependencies() {
    	$idNode = $this->request->getParam('nodeid');
    	$idNodeType = $this->request->getParam('id_nodetype');
    	$isWorkFlowMaster = (bool) $this->request->getParam('is_workflow_master');
	$search=array();
	$wfresult=array();
	$result=array();

    	$workflow = new WorkFlow(NULL, NULL, $idNode);
    	// obtaining information
    	$oldNodeType = $workflow->pipeline->get('IdNodeType');
		if ($oldNodeType > 0) {
			$search[] = $oldNodeType;
		}

		if ($idNodeType > 0) {
			$search[] = $idNodeType;
		}

		//if (is_array($search)) {
		if(count($search)>0) {
			$node = new Node();
			$result = $node->find('IdNode', 'IdNodeType IN (%s)', $search, MONO, false);
		}

		if ($isWorkFlowMaster) {
			if ($workflow->pipeline->get('IdNode') != Config::getValue('IdDefaultWorkflow')) {
				$allStatus = $workflow->GetAllStates();
				$node = new Node();
				$wfresult = $node->find('IdNode', 'IdState IN (%s)', array(implode(', ', $allStatus)), MONO, false);
			}
		}

		if (!is_array($result)) {
			$result = array();
		}

		if (!is_array($wfresult)) {
			$wfresult = array();
		}

		$allNodes = array_merge($result, $wfresult);
		if(empty($allNodes))
			$this->messages->add(_('Any node will change of workflow state'), MSG_TYPE_NOTICE);

		if (!empty($allNodes)) {
			$this->messages->add(_('Nodes which will change workflow state'), MSG_TYPE_NOTICE);
			foreach ($allNodes as $idNode) {
				$node = new Node($idNode);
				$this->messages->add(sprintf(_('If you perform this modification, the node workflow state %s will be modified'), $node->GetPath()), MSG_TYPE_NOTICE);
			}
		}
		// end of obtaining information 
		$this->render(array('messages' => $this->messages->messages, 'action_with_no_return' => true), NULL, 'messages.tpl');
	}
}
?>
