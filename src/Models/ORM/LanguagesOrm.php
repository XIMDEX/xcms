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

class LanguagesOrm extends GenericData
{
    public $_idField = 'IdLanguage';
    
    public $_table = 'Languages';
    
    public $_metaData = array
    (
        'IdLanguage' => array('type' => "int(12)", 'not_null' => 'true', 'primary_key' => true),
        'Name' => array('type' => "varchar(255)", 'not_null' => 'true'),
        'IsoName' => array('type' => "varchar(255)", 'not_null' => 'false'),
        'Enabled' => array('type' => "tinyint(1)", 'not_null' => 'false')
    );
    
    public $_uniqueConstraints = array
    (
        'Name' => array('Name'), 'IdLanguage' => array('IdLanguage')
    );
    
    public $_indexes = array('IdLanguage');
    
    public $IdLanguage;
    
    public $Name;
    
    public $IsoName;
    
    public $Enabled = 1;
}
