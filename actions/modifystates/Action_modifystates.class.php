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
 * @author Ximdex DevTeam <dev@ximdex.com>
 * @version $Revision$
 */


ModulesManager::file('/inc/pipeline/Pipeline.class.php');
ModulesManager::file('/inc/workflow/Workflow.class.php');

define('NODETYPE_WORKFLOW_STATE', 5036);

class Action_modifystates extends ActionAbstract
{

    // Main method: shows initial form
    function index()
    {
        $idNode = $this->request->getParam('nodeid');
        $pipeline = new Pipeline();

        $pipeline->loadByIdNode($idNode);
        // in this case there is just one process

        $pipeProcess = $pipeline->processes->next();
        $statusTransitions = array();
        $allStatusInfo = array();
        if (!empty($pipeProcess)) {
            $pipeProcess->transitions->reset();

            while ($transition = $pipeProcess->transitions->next()) {

                $currentStatus = new PipeStatus($transition->get('IdStatusFrom'));
                $nextStatus = new PipeStatus($transition->get('IdStatusTo'));

                $statusTransitions[] = array("id" => $transition->get('id'),
                    "value"=> sprintf('%s->%s', $currentStatus->get('Name'), $nextStatus->get('Name')));

                $allStatusInfo[] =
                    array('id' => $currentStatus->get('id'),
                        'name' => $currentStatus->get('Name'),
                        'description' => $currentStatus->get('Description'));
            }

            $allStatusInfo[] =
                array('id' => $nextStatus->get('id'),
                    'name' => $nextStatus->get('Name'),
                    'description' => $nextStatus->get('Description'));
        }

        $this->addJs('/actions/modifystates/resources/js/manager.js');
        $this->addCss('/xmd/style/forms/tabulators.css');
        $this->addCss('/actions/modifystates/resources/css/default.css');

        $nodeType = new NodeType();
        $allNodeTypes = $nodeType->find('IdNodeType, Name', 'IsPublicable = 1', array());

        foreach ($allNodeTypes as $nodeTypeInfo) {
            if ($nodeTypeInfo['IdNodeType'] == 5032 ||
                $nodeTypeInfo['IdNodeType'] == 5039 ||
                $nodeTypeInfo['IdNodeType'] == 5040 ||
                $nodeTypeInfo['IdNodeType'] == 5041
            ) {
                $nodeTypeValues[] = array("id" => $nodeTypeInfo['IdNodeType'],
                    "name" => $nodeTypeInfo['Name']);
            }
        }

        $checkUrl = \App::getValue( 'UrlRoot') . '/xmd/loadaction.php?actionid='
            . $this->request->getParam('actionid') . '&nodeid=' . $this->request->getParam('nodeid')
            . '&id_nodetype=IDNODETYPE&is_workflow_master=ISWORKFLOWMASTER&method=checkNodeDependencies';

        $values = array('all_status_info' => json_encode($allStatusInfo),
            'status_transitions' => json_encode($statusTransitions),
            'nodetype_list' => json_encode($nodeTypeValues),
            'selectedNodetype' => $pipeline->get('IdNodeType'),
            'is_workflow_master' => $pipeline->isWorkflowMaster,
            'id_nodetype' => $pipeline->get('IdNodeType'),
            'url_to_nodelist' => $checkUrl,
            'idNode' => $idNode
        );

        $this->render($values, null, 'default-3.0.tpl');
    }

    function update_states()
    {
        $req = json_decode($GLOBALS['HTTP_RAW_POST_DATA'],true);
        $idNode = $req["idNode"];
        $all_status = $req["states"];
        $toDelete = $req["toDelete"];

        $workflow = new WorkFlow(NULL, NULL, $idNode);
        $pipeProcess = $workflow->pipeProcess;
        $first = $pipeProcess->transitions->first();
        $firstStatus = new PipeStatus($first->get('IdStatusFrom'));
        $last = $pipeProcess->transitions->last();
        $lastStatus = new PipeStatus($last->get('IdStatusTo'));

        $ids = $all_status[0]["id"]==$firstStatus->id &&
            $all_status[count($all_status)-1]["id"]==$lastStatus->id;
        $names = $all_status[0]["name"]==$firstStatus->Name &&
            $all_status[count($all_status)-1]["name"]==$lastStatus->Name;
        $descriptions = $all_status[0]["description"]==$firstStatus->Description &&
            $all_status[count($all_status)-1]["description"]==$lastStatus->Description;

        if(!$ids | !$names | !$descriptions){
            $this->sendJSON(array("result" => "fail",
                "message" => _("The first and the last status can't be modified")));
        }

        foreach($all_status as $status){
            if(empty($status["name"]) | empty($status["description"])){
                $this->sendJSON(array("result" => "fail",
                    "message" => _("Name or description of status can't be empty")));
            }
        }

        while ($transition = $pipeProcess->transitions->next()) {
                $transition->delete();
        }

        foreach($toDelete as $status){
            if(is_numeric($status["id"]) && $status["id"] != $firstStatus->id && $status["id"] != $lastStatus->id){
                $pipeStatus = new PipeStatus($status["id"]);
                $pipeStatus->delete();
            }
        }

        for($i=0; $i<count($all_status); $i++){
            $pipeStatus = new PipeStatus($all_status[$i]["id"]);
            $pipeStatus->set('Name', $all_status[$i]["name"]);
            $pipeStatus->set('Description', $all_status[$i]["description"]);
            if($all_status[$i]["id"]!=null){
                $pipeStatus->update();
            }else{
                $all_status[$i]["id"]=$pipeStatus->add();
                if($all_status[$i]["id"]<0){
                    $this->sendJSON(array("result" => "fail",
                        "message" => sprintf(_("Error adding status %s"), $all_status[$i]["name"])));
                }
            }
            if($i>0){
                $pipeTransition = new PipeTransition();
                $pipeTransition->set('IdStatusFrom', $all_status[$i-1]["id"]);
                $pipeTransition->set('IdStatusTo', $all_status[$i]["id"]);
                $pipeTransition->set('Name', sprintf('%s_to_%s', $all_status[$i-1]["name"], $all_status[$i]["name"]));
                $pipeTransition->set('IdPipeProcess', $pipeProcess->id);
                $pipeTransition->set('Cacheable', 0);
                $pipeTransition->set('Callback', '-');
                $idNewTransition = $pipeTransition->add();
                if($idNewTransition<0){
                    $this->sendJSON(array("result" => "fail",
                        "message" => sprintf(_("Error adding transition %s"),
                            sprintf('%s_to_%s', $all_status[$i-1]["name"], $all_status[$i]["name"]))));
                }
            }
        }
        $action = new Action();
        if(count($all_status)>2){
            $actions = $action->find(ALL,"Command=%s AND Name=%s", array("workflow_forward","Publish"));
            foreach($actions as $a){
                $act = new Action($a["IdAction"]);
                $act->set('Name', "Move to next state");
                $act->update();
            }

            $actions = $action->find(ALL,"Command=%s", array("workflow_backward"));
            foreach($actions as $a){
                $act = new Action($a["IdAction"]);
                $act->set('Sort', 73);
                $act->update();
            }

        }else{
            $actions = $action->find(ALL,"Command=%s AND Name=%s", array("workflow_forward","Move to next state"));
            foreach($actions as $a){
                $act = new Action($a["IdAction"]);
                $act->set('Name', "Publish");
                $act->update();
            }
            $actions = $action->find(ALL,"Command=%s", array("workflow_backward"));
            foreach($actions as $a){
                $act = new Action($a["IdAction"]);
                $act->set('Sort', -10);
                $act->update();
            }
        }

        $this->sendJSON(array(
            "result" => "ok",
            "all_status_info" => json_encode($all_status),
            "message" => _("The workflow has been successfully updated")
        ));
    }

    function checkNodeDependencies()
    {
        $idNode = $this->request->getParam('nodeid');
        $idNodeType = $this->request->getParam('id_nodetype');
        $isWorkFlowMaster = (bool)$this->request->getParam('is_workflow_master');
        $search = array();
        $wfresult = array();
        $result = array();

        $workflow = new WorkFlow(NULL, NULL, $idNode);
        $oldNodeType = $workflow->pipeline->get('IdNodeType');
        if ($oldNodeType > 0) {
            $search[] = $oldNodeType;
        }

        if ($idNodeType > 0) {
            $search[] = $idNodeType;
        }

        if (count($search) > 0) {
            $node = new Node();
            $result = $node->find('IdNode', 'IdNodeType IN (%s)', $search, MONO, false);
        }

        if ($isWorkFlowMaster) {
            if ($workflow->pipeline->get('IdNode') != \App::getValue( 'IdDefaultWorkflow')) {
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
        if (empty($allNodes))
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