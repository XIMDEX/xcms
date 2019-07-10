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

use Ximdex\Data\GenericData;

class WorkflowStatus extends GenericData
{
    const PUBLICATION_STATUS = 8;
    
    public $_idField = 'id';
    
    public $_table = 'WorkflowStatus';
    
    public $_metaData = array(
        'id' => array('type' => "int(12)", 'not_null' => 'true', 'auto_increment' => 'true', 'primary_key' => true),
        'name' => array('type' => "varchar(50)", 'not_null' => 'true'),
        'description' => array('type' => "varchar(250)", 'not_null' => 'false'),
        'action' => array('type' => "varchar(255)", 'not_null' => 'false'),
        'sort' => array('type' => "int(4)", 'not_null' => 'true'),
        'workflowId' => array('type' => "int(4)", 'not_null' => 'false')
    );
    
    public $_uniqueConstraints = ['name'];
    
    public $_indexes = array('id');
    
    public $id;
    
    public $name;
    
    public $description;
    
    public $action;
    
    public $sort = 0;
    
    public $workflowId;
    
    /**
     * Get the first status for the current workflow
     * 
     * @throws \Exception
     * @param int $workflowId
     * @return int
     */
    public function getInitial(int $workflowId = null) : int
    {
        $sql = 'SELECT id FROM WorkflowStatus WHERE workflowId IS NULL';
        if ($workflowId) {
            $sql .= ' OR workflowId = ' . $workflowId;
        }
        $sql .= ' ORDER BY sort LIMIT 1';
        if (! $res = $this->query($sql, MONO)) {
            throw new \Exception('Cannot get the initial state in workflow');
        }
        return (int) $res[0];
    }
    
    /**
     * Return the last status for current workflow
     * 
     * @throws \Exception
     * @param int $workflowId
     * @return int
     */
    public function getFinal(int $workflowId = null) : int
    {
        $sql = 'SELECT id FROM WorkflowStatus WHERE workflowId IS NULL';
        if ($workflowId) {
            $sql .= ' OR workflowId = ' . $workflowId;
        }
        $sql .= ' ORDER BY sort DESC LIMIT 1';
        if (! $res = $this->query($sql, MONO)) {
            throw new \Exception('Cannot get the final state in workflow');
        }
        return (int) $res[0];
    }
    
    /**
     * Return the next status for the current workflow
     * 
     * @param int $workflowId
     * @throws \Exception
     * @return int|NULL
     */
    public function getNext(int $workflowId = null) : ?int
    {
        if (! $this->id) {
            throw new \Exception('There is not a status loaded to get the next one');
        }
        $sql = 'SELECT id FROM WorkflowStatus WHERE (workflowId IS NULL';
        if ($workflowId) {
            $sql .= ' OR workflowId = ' . $workflowId;
        }
        $sql .= ') AND sort > ' . $this->sort . ' ORDER BY sort LIMIT 1';
        $res = $this->query($sql, MONO);
        if ($res === false) {
            throw new \Exception('Cannot obtain the next state in workflow');
        }
        if (! $res) {
            return null;
        }
        return (int) $res[0];
    }
    
    /**
     * Return the previous status for the current one
     * 
     * @param int $workflowId
     * @throws \Exception
     * @return int|NULL
     */
    public function getPrevious(int $workflowId = null) : ?int
    {
        if (! $this->id) {
            throw new \Exception('There is not a status loaded to get the previous one');
        }
        $sql = 'SELECT id FROM WorkflowStatus WHERE (workflowId IS NULL';
        if ($workflowId) {
            $sql .= ' OR workflowId = ' . $workflowId;
        }
        $sql .= ') AND sort < ' . $this->sort . ' ORDER BY sort DESC LIMIT 1';
        $res = $this->query($sql, MONO);
        if ($res === false) {
            throw new \Exception('Cannot obtain the previous state in workflow');
        }
        if (! $res) {
            return null;
        }
        return (int) $res[0];
    }
}
