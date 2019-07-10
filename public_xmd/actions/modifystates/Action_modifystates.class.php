<?php

/**
 *  \details &copy; 2018 Open Ximdex Evolution SL [http://www.ximdex.org]
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
 * @author Ximdex DevTeam <dev@ximdex.com>
 * @version $Revision$
 */

use Ximdex\Models\Node;
use Ximdex\Models\NodeType;
use Ximdex\Models\Workflow;
use Ximdex\MVC\ActionAbstract;
use Ximdex\NodeTypes\NodeTypeConstants;
use Ximdex\Runtime\App;
use Ximdex\Models\WorkflowStatus;

class Action_modifystates extends ActionAbstract
{
    /**
     * Main method: shows initial form
     */
    public function index()
    {
        $idNode = $this->request->getParam('nodeid');
        $node = new Node($idNode);
        $allStatusInfo = array();
        $workflow = new Workflow($node->GetID());
        foreach ($workflow->getAllStates() as $id) {
            $status = new WorkflowStatus($id);
            $allStatusInfo[] = array(
                'id' => $status->get('id'),
                'name' => $status->get('name'),
                'description' => $status->get('description'),
                'action' => $status->get('action')
            );
        }
        $this->addJs('/actions/modifystates/resources/js/manager.js');
        $this->addCss('/assets/style/forms/tabulators.css');
        $this->addCss('/actions/modifystates/resources/css/default.css');
        $nodeType = new NodeType();
        $allNodeTypes = $nodeType->find('IdNodeType, Name', 'IsPublishable = 1', array());
        $nodeTypeValues = [];
        foreach ($allNodeTypes as $nodeTypeInfo) {
            if ($nodeTypeInfo['IdNodeType'] == NodeTypeConstants::XML_DOCUMENT ||
                $nodeTypeInfo['IdNodeType'] == NodeTypeConstants::TEXT_FILE ||
                $nodeTypeInfo['IdNodeType'] == NodeTypeConstants::IMAGE_FILE ||
                $nodeTypeInfo['IdNodeType'] == NodeTypeConstants::BINARY_FILE
            ) {
                $nodeTypeValues[] = array('id' => $nodeTypeInfo['IdNodeType'], 'name' => $nodeTypeInfo['Name']);
            }
        }
        $checkUrl = App::getUrl( '?actionid=' . $this->request->getParam('actionid') . '&nodeid=' . $this->request->getParam('nodeid')
            . '&id_nodetype=IDNODETYPE&is_workflow_master=ISWORKFLOWMASTER&method=checkNodeDependencies');
        $values = array(
            'all_status_info' => json_encode($allStatusInfo),
            'nodetype_list' => json_encode($nodeTypeValues),
            'selectedNodetype' => $node->GetNodeType(),
            'is_workflow_master' => $workflow->get('master'),
            'id_nodetype' => $node->GetNodeType(),
            'url_to_nodelist' => $checkUrl,
            'idNode' => $idNode,
            'actions' => json_encode(array('' =>  null) + Workflow::getActions()),
            'nodeTypeID' => $node->nodeType->getID(),
            'node_Type' => $node->nodeType->GetName()
        );
        $this->render($values, null, 'default-3.0.tpl');
    }

    public function update_states()
    {
        $post = file_get_contents('php://input');
        $req = json_decode($post, true);
        $idNode = $req['idNode'];
        $all_status = $req['states'];
        $toDelete = $req['toDelete'];
        $node = new Node($idNode);
        $workflow = new Workflow($node->GetID());
        $firstStatus = new WorkflowStatus($workflow->getInitialState());
        $lastStatus = new WorkflowStatus($workflow->getFinalState());
        $ids = $all_status[0]['id'] == $firstStatus->get('id') && $all_status[count($all_status) - 1]['id'] == $lastStatus->get('id');
        $names = $all_status[0]['name'] == $firstStatus->get('name') && $all_status[count($all_status) - 1]['name'] == $lastStatus->get('name');
        $descriptions = $all_status[0]['description'] == $firstStatus->get('description') && 
                $all_status[count($all_status) - 1]['description'] == $lastStatus->get('description');
        if (! $ids || ! $names || ! $descriptions) {
            $this->sendJSON(array('result' => 'fail',
                'message' => _('The first and the last status can\'t be modified')));
        }
        foreach ($all_status as $status) {
            if (empty($status['name']) || empty($status['description'])) {
                $this->sendJSON(array('result' => 'fail',
                    'message' => _('Name or description of status can\'t be empty')));
            }
        }
        $wfStatus = new WorkflowStatus();
        $wfStatus->beginTransaction();
        foreach ($toDelete as $status) {
            if (is_numeric($status['id']) && $status['id'] != $firstStatus->get('id') && $status['id'] != $lastStatus->get('id')) {
                $wfStatus = new WorkflowStatus($status['id']);
                $wfStatus->delete();
            }
        }
        for ($i = 1; $i < count($all_status) - 1; $i++) {
            $wfStatus = new WorkflowStatus($all_status[$i]['id']);
            $wfStatus->set('name', $all_status[$i]['name']);
            $wfStatus->set('description', $all_status[$i]['description']);
            $wfStatus->set('sort', $i);
            $wfStatus->set('workflowId', $workflow->get('id'));
            if (isset($all_status[$i]['action'])) {
                $wfStatus->set('action', $all_status[$i]['action']);
            }
            if ($all_status[$i]['id'] != null) {
                $wfStatus->update();
            } else {
                $all_status[$i]['id'] = $wfStatus->add();
                if ($all_status[$i]['id'] < 0) {
                    $this->sendJSON(array('result' => 'fail',
                        'message' => sprintf(_('Error adding status %s'), $all_status[$i]['name'])));
                }
            }
        }
        $this->sendJSON(array(
            'result' => 'ok',
            'all_status_info' => json_encode($all_status),
            'message' => _('The workflow has been successfully updated')
        ));
    }

    public function checkNodeDependencies()
    {
        $idNode = $this->request->getParam('nodeid');
        $idNodeType = $this->request->getParam('id_nodetype');
        $isWorkFlowMaster = (bool) $this->request->getParam('is_workflow_master');
        $search = array();
        $wfresult = array();
        $result = array();
        if ($idNodeType > 0) {
            $search[] = $idNodeType;
        }
        if (count($search) > 0) {
            $node = new Node();
            $result = $node->find('IdNode', 'IdNodeType IN (%s)', $search, MONO, false);
        }
        $workflow = new Workflow($idNode);
        if ($isWorkFlowMaster) {
            if ($workflow->get('master')) {
                $allStatus = $workflow->getAllStates();
                $node = new Node();
                $wfresult = $node->find('IdNode', 'IdState IN (%s)', array(implode(', ', $allStatus)), MONO, false);
            }
        }
        if (! is_array($result)) {
            $result = array();
        }
        if (! is_array($wfresult)) {
            $wfresult = array();
        }
        $allNodes = array_merge($result, $wfresult);
        if (empty($allNodes)) {
            $this->messages->add(_('Any node will change of workflow state'), MSG_TYPE_NOTICE);
        }
        if (! empty($allNodes)) {
            $this->messages->add(_('Nodes which will change workflow state'), MSG_TYPE_NOTICE);
            foreach ($allNodes as $idNode) {
                $node = new Node($idNode);
                $this->messages->add(sprintf(_('If you perform this modification, the node workflow state %s will be modified')
                    , $node->GetPath()), MSG_TYPE_NOTICE);
            }
        }
        
        // End of obtaining information
        $this->render(array('messages' => $this->messages->messages, 'action_with_no_return' => true), NULL, 'messages.tpl');
    }
}
