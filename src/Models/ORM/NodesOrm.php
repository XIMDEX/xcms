<?php

/**
 *  \details &copy; 2019 Open Ximdex Evolution SL [http://www.ximdex.org]
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

namespace Ximdex\Models\ORM;

use Ximdex\Data\GenericData;

/**
 * Class NodesOrm
 * 
 * @package Ximdex\Models
 */
class NodesOrm extends GenericData
{
    public $_idField = 'IdNode';
    
    public $_table = 'Nodes';
    
    public $_metaData = array(
        'IdNode' => array('type' => "int(12)", 'not_null' => 'true', 'auto_increment' => 'true', 'primary_key' => true),
        'IdParent' => array('type' => "int(12)", 'not_null' => 'false'),
        'IdNodeType' => array('type' => "int(12)", 'not_null' => 'true'),
        'Name' => array('type' => "varchar(255)", 'not_null' => 'true'),
        'IdState' => array('type' => "int(12)", 'not_null' => 'false'),
        'BlockTime' => array('type' => "int(12)", 'not_null' => 'false'),
        'BlockUser' => array('type' => "int(12)", 'not_null' => 'false'),
        'CreationDate' => array('type' => "int(12)", 'not_null' => 'false'),
        'ModificationDate' => array('type' => "int(12)", 'not_null' => 'false'),
        'Description' => array('type' => "varchar(255)", 'not_null' => 'false'),
        'SharedWorkflow' => array('type' => "int(12)", 'not_null' => 'false'),
        'ActiveNF' => array('type' => "int(12)", 'not_null' => 'false')
    );
    
    public $_uniqueConstraints = array(
        'IdNode' => array('IdNode', 'IdParent')
    );
    
    public $_indexes = array('IdNode');
    
    public $IdNode;
    
    public $IdParent;
    
    public $IdNodeType;
    
    public $Name;
    
    public $IdState;
    
    public $BlockTime;
    
    public $BlockUser;
    
    public $CreationDate;
    
    public $ModificationDate;
    
    public $Description;
    
    public $SharedWorkflow;
    
    public $ActiveNF;
}
