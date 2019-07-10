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

class RelNodeSetsUsersOrm extends GenericData
{
    public $_idField = 'Id';
    
    public $_table = 'RelNodeSetsUsers';
    
    public $_metaData = array(
        'Id' => array('type' => "int(10)", 'not_null' => 'true', 'auto_increment' => 'true', 'primary_key' => true),
        'IdSet' => array('type' => "int(10)", 'not_null' => 'true'),
        'IdUser' => array('type' => "int(12)", 'not_null' => 'true'),
        'Owner' => array('type' => "tinyint(1)", 'not_null' => 'true')
    );
    
    public $_uniqueConstraints = array(
        'U_SETUSERS' => array('IdSet', 'IdUser')
    );
    
    public $_indexes = array('Id');
    
    public $Id;
    
    public $IdSet;
    
    public $IdUser;
    
    public $Owner = 0;
}
