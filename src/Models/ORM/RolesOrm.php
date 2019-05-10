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

class RolesOrm extends GenericData
{
    public $_idField = 'IdRole';
    
    public $_table = 'Roles';
    
    public $_metaData = array(
        'IdRole' => array('type' => "int(12)", 'not_null' => 'true', 'primary_key' => true),
        'Name' => array('type' => "varchar(255)", 'not_null' => 'true'),
        'Icon' => array('type' => "varchar(255)", 'not_null' => 'false'),
        'Description' => array('type' => "varchar(255)", 'not_null' => 'false')
    );
    
    public $_uniqueConstraints = array(
        'IdRole' => array('Name')
    );
    
    public $_indexes = array('IdRole');
    
    public $IdRole;
    
    public $Name = 0;
    
    public $Icon;
    
    public $Description;
}
