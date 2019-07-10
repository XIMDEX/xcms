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

class RelRolesActionsOrm extends GenericData
{
    public $_idField = 'IdRel';
    
    public $_table = 'RelRolesActions';
    
    public $_metaData = array(
        'IdRel' => array('type' => 'int(12)', 'not_null' => 'true', 'auto_increment' => 'true', 'primary_key' => true),
        'IdRol' => array('type' => 'int(12)', 'not_null' => 'true'),
        'IdAction' => array('type' => 'int(12)', 'not_null' => 'true'),
        'IdState' => array('type' => 'int(12)', 'not_null' => 'false')
    );
    
    public $_uniqueConstraints = array();
    
    public $_indexes = array('PRIMARY', 'IdRol', 'IdAction', 'RelRolesActions_Status', 'IdRol_2');
    
    public $IdRel;
    
    public $IdRol;
    
    public $IdAction;
    
    public $IdState;
}
