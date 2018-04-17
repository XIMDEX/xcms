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


use Ximdex\Models\PipeStatus;
use Ximdex\Models\Role;
use Ximdex\MVC\ActionAbstract;
use Ximdex\Runtime\App;
use Ximdex\Workflow\WorkFlow;


class Action_modifystatesrole extends ActionAbstract
{

    function index()
    {
        $idNode = $this->request->getParam('nodeid');
        $role = new Role($idNode);
        $idRoleStates = $role->GetAllStates();

        $workflow = new WorkFlow(NULL, NULL, App::getValue('IdDefaultWorkflow'));
        $idAllStates = $workflow->GetAllStates();
        foreach ($idAllStates as $idStatus) {
            $pipeStatus = new PipeStatus($idStatus);
            $states[] = array("id" => $idStatus, "name" => $pipeStatus->get('Name'));
        }

        foreach ($states as $i => $state) {
            if ($state["id"] != null && is_array($idRoleStates) && in_array($state["id"], $idRoleStates)) {
                $states[$i]["asociated"] = true;
            } else {
                $states[$i]["asociated"] = false;
            }
        }
        $values = array('all_states' => json_encode($states),
            'node_Type' => $role->nodeType->GetName(),
            'idRole' => $idNode);

        $this->render($values, null, 'default-3.0.tpl');
    }

    function update_states()
    {   
        $post = file_get_contents('php://input');
        $request = json_decode($post, true);
        $states = $request["states"];
        $idRole = $request["idRole"];
        $role = new Role($idRole);

        foreach ($states as $i => $state) {
            if ($state["asociated"] && $role->HasState($state["id"]) == 0) {
                $role->AddState($state["id"]);
            } elseif (!$state["asociated"] && $role->HasState($state["id"]) > 0) {
                $role->DeleteState($state["id"]);
            }
        }
        $this->sendJSON(array("result" => "ok",
            "message" => _("The rol has been successfully updated")));
    }
}