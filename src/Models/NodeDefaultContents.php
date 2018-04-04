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

namespace Ximdex\Models;

use Ximdex\Data\GenericData;

class NodeDefaultContents extends GenericData
{
    var $_idField = 'IdNodeDefaultContent';
    var $_table = 'NodeDefaultContents';
    var $_metaData = array(
        'IdNodeDefaultContent' => array('type' => "int(12)", 'not_null' => 'true', 'auto_increment' => 'true', 'primary_key' => true),
        'IdNodeType' => array('type' => "int(12)", 'not_null' => 'true'),
        'NodeType' => array('type' => "int(12)", 'not_null' => 'true'),
        'Name' => array('type' => "varchar(255)", 'not_null' => 'true'),
        'State' => array('type' => "int(12)", 'not_null' => 'false'),
        'Params' => array('type' => "varchar(255)", 'not_null' => 'false')
    );
    var $_uniqueConstraints = array(
        'UniqueName' => array('Name', 'IdNodeType')
    );
    var $_indexes = array('IdNodeDefaultContent');
    var $IdNodeDefaultContent;
    var $IdNodeType = 0;
    var $NodeType = 0;
    var $Name = 0;
    var $State;
    var $Params;

    /**
     * Returns all the allowed children for a given nodetype. 
     * 
     * Request params: 
     * 
     * * idnodetype: Nodetype identificator. 
     * @return Array result: 
     */
    function getDefaultChilds($idnodetype)
    {
        $result = $this->find('NodeType, Name', 'IdNodeType = %s AND Nodetype <> %s', array($idnodetype, $idnodetype), MULTI, true, null, 'Name');
        return $result;
    }

    /**
     * Returns the name of the default folder a given nodetype. 
     * 
     * Request params: 
     *  
     * * idnodetype: Nodetype identificator. 
     * @return Array result: 
     */
    function getDefaultName($idnodetype)
    {
        $result = $this->find('Name', 'NodeType = %s', array($idnodetype), MONO);
        return $result[0];
    }
}