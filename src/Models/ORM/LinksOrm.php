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

class LinksOrm extends GenericData
{
    public $_idField = 'IdLink';
    
    public $_table = 'Links';
    
    public $_metaData = array
    (
        'IdLink' => array('type' => "int(12)", 'not_null' => 'true', 'auto_increment' => 'false', 'primary_key' => true),
        'Url' => array('type' => "blob", 'not_null' => 'true'),
        'Error' => array('type' => "int(12)", 'not_null' => 'false'),
        'ErrorString' => array('type' => "varchar(255)", 'not_null' => 'false'),
        'CheckTime' => array('type' => "int(12)", 'not_null' => 'false')
    );
    
    public $_uniqueConstraints = array
    (
        'IdLink' => array('IdLink')
    );
    
    public $_indexes = array('IdLink');
    
    public $IdLink;
    
    public $Url;
    
    public $Error;
    
    public $ErrorString;
    
    public $CheckTime;
}
