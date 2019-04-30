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

class ChannelsOrm extends GenericData
{
    public $_idField = 'IdChannel';
    
    public $_table = 'Channels';
    
    public $_metaData = array
    (
        'IdChannel' => array('type' => "int(12)", 'not_null' => 'true', 'primary_key' => true),
        'Name' => array('type' => "varchar(255)", 'not_null' => 'true'),
        'Description' => array('type' => "varchar(255)", 'not_null' => 'false'),
        'DefaultExtension' => array('type' => "varchar(255)", 'not_null' => 'false'),
        'Format' => array('type' => "varchar(255)", 'not_null' => 'false'),
        'Filter' => array('type' => "varchar(255)", 'not_null' => 'false'),
        'RenderMode' => array('type' => "varchar(255)", 'not_null' => 'false'),
        'OutputType' => array('type' => "varchar(100)", 'not_null' => 'false'),
        'Default_Channel' => array('type' => "tinyint(1)", 'not_null' => 'true'),
        'RenderType' => array('type' => 'varchar(50)', 'not_null' => 'false'),
        'idLanguage' => array('type' => 'varchar(20)', 'not_null' => 'false')
    );
    
    public $_uniqueConstraints = array();
    
    public $_indexes = array('IdChannel');
    
    public $IdChannel;
    
    public $Name;
    
    public $Description;
    
    public $DefaultExtension;
    
    public $Format;
    
    public $Filter;
    
    public $RenderMode;
    
    public $OutputType;
    
    public $Default_Channel;
    
    public $RenderType;
    
    public $idLanguage;
}
