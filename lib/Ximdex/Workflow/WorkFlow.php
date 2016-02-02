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


namespace Ximdex\Workflow;

use Ximdex\Models\Pipeline;
use Ximdex\Models\PipeNodeTypes;
use Ximdex\Models\PipeProcess;
use Ximdex\Models\PipeStatus;
use Ximdex\Models\Role;
use Ximdex\Models\Node;
use Ximdex\Logger as XMD_Log;
use Ximdex\Runtime\App;

define('WORKFLOW_PROCESS_NAME', 'workflow');

class WorkFlow
{
    /**
     * @var $pipeStatus PipeStatus
     */
    var $pipeStatus;
    /**
     * @var $pipeProcess PipeProcess
     */
    var $pipeProcess;
    /**
     * @var $pipeline Pipeline
     */
    var $pipeline;

    public function __construct($idNode, $idStatus = NULL, $idPipelineNode = NULL)
    {
        if (!($idPipelineNode > 0)) {
            $node = new Node($idNode);
            $propertyPipeline = $node->getProperty('Pipeline');
            $propertyPipeline = $propertyPipeline[0];
            if ($propertyPipeline > 0) {
                $idPipelineNode = $propertyPipeline;
            } else {
                $idNodeType = $node->get('IdNodeType');
                $pipeNodetype = new PipeNodeTypes();
                $result = $pipeNodetype->find('IdPipeline', 'IdNodeType = %s', array($idNodeType), MONO);
                if (count($result) === 1) {
                    $idPipelineNode = $result[0];
                }
            }

            if ($idPipelineNode == NULL) {
                $idPipelineNode =  App::getValue('IdDefaultWorkflow');
            }
        }

        $this->pipeStatus = new PipeStatus($idStatus);

        $this->pipeline = new Pipeline($idPipelineNode);
        if (!$this->pipeline->get('id') > 0) {
            $this->pipeline->loadByIdNode($idPipelineNode);
        }
        $this->pipeProcess = new PipeProcess($this->pipeline->processes->first()->id);
    }

    function GetID()
    {
        return $this->pipeStatus->get('id');
    }

    function SetID($id)
    {
        $this->WorkFlow($id);

    }

    function GetAllStates()
    {
        return $this->pipeProcess->getAllStatus();
    }

    function GetAllowedStates($roleID)
    {
        $role = new Role($roleID);
        return $role->GetAllowedStates();
    }


    function IsAllowedState($roleID)
    {
        $allowedStates = $this->GetAllowedStates($roleID);
        if (is_array($allowedStates)) {
            return in_array($this->pipeStatus->get('IdNode'), $allowedStates);
        }
        return false;
    }


    // Devuelve el nombre del estado correspondiente
    function GetName()
    {
        return $this->pipeStatus->get('Name');
    }

    // Nos permite cambiar el nombre a un estado
    function SetName($name)
    {
        $this->pipeStatus->set('Name', $name);
        return $this->pipeStatus->update();
    }

    // Devuelve la descripcion del estado correspondiente
    function GetDescription()
    {
        return $this->pipeStatus->get('Description');
    }

    /**
     * @param $description
     */
     function SetDescription($description)
    {
        $this->pipeStatus->set('Description', $description);
        $this->pipeStatus->update();
    }

    /**
     * @return bool|string
     */
    function GetInitialState()
    {
        if  (!is_object( $this->pipeProcess)) {
            return null ;
        }
        $idStatus = $this->pipeProcess->getFirstStatus();
        $pipeStatus = new PipeStatus($idStatus);
        return $pipeStatus->get('id');
    }

    /**
     * @return mixed
     */
    function GetFinalProcess()
    {
        return $this->pipeline->processes->last();
    }

    /**
     * @return bool|string
     */
    function GetFinalState()
    {
        $idStatus = $this->pipeProcess->getLastStatus();
        $pipeStatus = new PipeStatus($idStatus);
        return $pipeStatus->get('id');
    }

    /**
     * @return mixed
     */
    function IsInitialState()
    {
        return $this->pipeProcess->isStatusFirst($this->pipeStatus->get('id'));
    }

    function IsFinalState()
    {
        return $this->pipeProcess->isStatusLast($this->pipeStatus->get('id'));
    }

    // Nos devuelve los siguientes estados posibles al estado actual
    function GetNextState()
    {
        $idStatus = $this->pipeProcess->getNextStatus($this->pipeStatus->get('id'));
        $pipeStatus = new PipeStatus($idStatus);
        return $pipeStatus->get('id');

    }

    /**
     * @return bool|string
     */
    function GetPreviousState()
    {
        $idStatus = $this->pipeProcess->getPreviousStatus($this->pipeStatus->get('id'));
        $pipeStatus = new PipeStatus($idStatus);
        return $pipeStatus->get('id');
    }

    /**
     * @param $name
     * @return bool|string
     */
    function setByName($name)
    {
        $pipeStatus = new PipeStatus();
        $pipeStatus->loadByName($name);
        $this->WorkFlow($pipeStatus->get('IdNode'));
        return $pipeStatus->get('IdNode');
    }

    /**
     * @param $idNodeType
     * @return bool
     */
    function setNodeType($idNodeType)
    {

        $pipeNodeTypes = new PipeNodeTypes();
        $result = $pipeNodeTypes->find('id, IdPipeline', 'IdNodeType = %s', array($idNodeType));
        if (count($result) > 0) {
            XMD_Log::warning(_('This nodetype is already associated to other workflow'));
            return false;
        }
        $this->pipeline->set('IdNodeType', $idNodeType);
        $this->pipeline->update();
        return true ;
    }

    /**
     *
     */
    function setWorkflowMaster()
    {
        $this->pipeline->activatePipelineForNodes( App::getValue('IdDefaultWorkflow'));
         App::setValue('IdDefaultWorkflow', $this->pipeline->get('id'));
    }
}
