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

namespace Ximdex\Models;

use Ximdex\Logger;
use Ximdex\Data\GenericData;

class Workflow extends GenericData
{
    public $_idField = 'id';
    public $_table = 'Workflow';
    public $_metaData = array(
        'id' => array('type' => "int(4)", 'not_null' => 'true', 'auto_increment' => 'true', 'primary_key' => true),
        'name' => array('type' => "varchar(50)", 'not_null' => 'true'),
        'description' => array('type' => "varchar(250)", 'not_null' => 'false'),
        'master' => array('type' => "int(1)", 'not_null' => 'true')
    );
    public $_uniqueConstraints = ['name'];
    public $_indexes = ['id'];
    public $id;
    public $name;
    public $description;
    public $master;
    
    const WORKFLOW_ACTIONS_DIR = 'src/Workflow/Actions';
    const WORKFLOW_ACTIONS_NAMESPACE = 'Ximdex\\Workflow\\Actions\\';
    
    private $workflowStatus;
    
    public function __construct(int $id = null, int $statusId = null)
    {
        parent::__construct($id);
        $this->workflowStatus = new WorkflowStatus($statusId);
        if (! $statusId and $id) {
            
            // Load the first status for the workflow loaded
            $statusId = $this->getInitialState();
            $this->workflowStatus = new WorkflowStatus($statusId);
        }
        if (! $id and $this->workflowStatus->get('workflowId')) {
            
            // Get the workflow by related status given
            $id = $this->workflowStatus->get('workflowId');
            parent::__construct($id);
        }
        if ($id and ! $this->id) {
            throw new \Exception('Could not load the workflow for code: ' . $id);
        }
        if ($statusId and ! $this->workflowStatus->get('id')) {
            throw new \Exception('Could not load the workflow status for code: ' . $statusId);
        }
    }
    
    public function getStatusID() : ?int
    {
        return $this->workflowStatus->get('id');
    }
    
    public function setID(int $id)
    {
        $this->__construct($id);
    }
    
    public function getAllStates(bool $common = true)
    {
        $condition = 'FALSE';
        if ($common) {
            $condition .= ' OR workflowId IS NULL';
        }
        if ($this->id) {
            $condition .= ' OR workflowId = ' . $this->id;
        }
        return $this->workflowStatus->find('id', $condition, null, MONO, true, null, 'sort');
    }
    
    public function getAllowedStates(int $roleID) : array
    {
        $role = new Role($roleID);
        return $role->GetAllowedStates();
    }
    
    /**
     * Return the status name
     *
     * @return string
     */
    public function getStatusName() : string
    {
        return $this->workflowStatus->get('name');
    }
    
    /**
     * Change the status name
     *
     * @param string $name
     */
    public function setStatusName(string $name)
    {
        $this->workflowStatus->set('name', $name);
        $this->workflowStatus->update();
    }
    
    /**
     * Return the status description
     *
     * @return string|null
     */
    public function getStatusDescription() : ?string
    {
        return $this->workflowStatus->get('description');
    }
    
    public function setStatusDescription(string $description = null)
    {
        $this->workflowStatus->set('description', $description);
        $this->workflowStatus->update();
    }
    
    public function getInitialState() : ?int
    {
        try {
            return $this->workflowStatus->getInitial($this->id);
        } catch (\Exception $e) {
            Logger::error($e->getMessage());
            return null;
        }
    }
    
    public function getFinalState() : ?int
    {
        try {
            return $this->workflowStatus->getFinal($this->id);
        } catch (\Exception $e) {
            Logger::error($e->getMessage());
            return null;
        }
    }
    
    public function isInitialState() : bool
    {
        $id = $this->getPreviousState();
        if (! $id) {
            return true;
        }
        return false;
    }
    
    public function isFinalState() : bool
    {
        $id = $this->getNextState();
        if (! $id) {
            return true;
        }
        return false;
    }
    
    /**
     * Return the next state for the current one, or null if is the final state
     *
     * @return int|null
     */
    public function getNextState() : ?int
    {
        try {
            return $this->workflowStatus->getNext($this->id);
        } catch (\Exception $e) {
            Logger::error($e->getMessage());
            return null;
        }
    }
    
    /**
     * Return the previous state for the current one, or null if is the initial state
     *
     * @return int|null
     */
    public function getPreviousState() : ?int
    {
        try {
            return $this->workflowStatus->getPrevious($this->id);
        } catch (\Exception $e) {
            Logger::error($e->getMessage());
            return null;
        }
    }
    
    /**
     * Get all class names and its public methods, inside the workflow actions directory
     *
     * @return array
     */
    public static function & getActions() : array
    {
        $actions = array();
        if ($dir = opendir(XIMDEX_ROOT_PATH . '/' . self::WORKFLOW_ACTIONS_DIR)) {
            do {
                $file = readdir($dir);
                if (is_dir($file)) {
                    continue;
                }
                $info = pathinfo($file);
                if (! isset($info['extension']) or $info['extension'] != 'php') {
                    continue;
                }
                $className = $info['filename'];
                /*
                if ($className == 'WorkflowAction') {
                    continue;
                }
                */
                $class = self::WORKFLOW_ACTIONS_NAMESPACE . $className;
                if (! class_exists($class)) {
                    continue;
                }
                $methods = get_class_methods($class);
                foreach ($methods as $method) {
                    if (strpos($method, '_') === 0) {
                        continue;
                    }
                    $actions[$className . '@' . $method] = $className . '@' . $method;
                }
            }
            while ($file !== false);
            closedir($dir);
        }
        return $actions;
    }
    
    public function loadMaster()
    {
        $res = $this->find('id', 'master IS TRUE', null, MONO);
        if (! $res) {
            throw new \Exception('Could not load the master workflow (was it defined?)');
        }
        $this->__construct($res[0]);
        if (! $this->id) {
            throw new \Exception('Could not load the master workflow with code: ' . $res[0]);
        }
    }
}
