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

class PortalFramesOrm extends GenericData
{
    public $_idField = 'id';
    
    public $_table = 'PortalFrames';
    
    public $_metaData = array(
        'id' => array('type' => "int(12)", 'not_null' => 'true', 'auto_increment' => 'true', 'primary_key' => true),
        'IdNodeGenerator' => array('type' => 'int(12)', 'not_null' => 'false'),
        'Version' => array('type' => 'int(12)', 'not_null' => 'true'),
        'CreationTime' => array('type' => 'int(12)', 'not_null' => 'true'),
        'PublishingType' => array('type' => 'varchar(255)', 'not_null' => 'true'),
        'CreatedBy' => array('type' => 'int(12)', 'not_null' => 'false'),
        'ScheduledTime' => array('type' => 'int(12)', 'not_null' => 'true'),
        'StartTime' => array('type' => 'int(12)', 'not_null' => 'false'),
        'EndTime' => array('type' => 'int(12)', 'not_null' => 'false'),
        'Status' => array('type' => 'varchar(255)', 'not_null' => 'true'),
        'StatusTime' => array('type' => 'int(12)', 'not_null' => 'false'),
        'SFtotal' => array('type' => 'int(12)', 'not_null' => 'true'),
        'SFactive' => array('type' => 'int(12)', 'not_null' => 'true'),
        'SFpending' => array('type' => 'int(12)', 'not_null' => 'true'),
        'SFsuccess' => array('type' => 'int(12)', 'not_null' => 'true'),
        'SFfatalError' => array('type' => 'int(12)', 'not_null' => 'true'),
        'SFsoftError' => array('type' => 'int(12)', 'not_null' => 'true'),
        'SFdelayed' => array('type' => 'int(12)', 'not_null' => 'true'),
        'SFstopped' => array('type' => 'int(12)', 'not_null' => 'true'),
        'Playing' => array('type' => 'tinyint(1)', 'not_null' => 'true'),
        'SuccessRate' => array('type' => 'float', 'not_null' => 'true'),
        'Boost' => array('type' => 'varchar(1)', 'not_null' => 'true'),
        'BoostCycles' => array('type' => 'float', 'not_null' => 'true'),
        'CyclesTotal' => array('type' => 'int(12)', 'not_null' => 'true'),
        'Hidden' => array('type' => 'tinyint(1)', 'not_null' => 'true')
    );
    
    public $_uniqueConstraints = array();
    
    public $_indexes = array('id');
    
    public $id;
    
    public $IdNodeGenerator;
    
    public $Version = 0;
    
    public $CreationTime;
    
    public $PublishingType;
    
    public $CreatedBy;
    
    public $ScheduledTime;
    
    public $StartTime;
    
    public $EndTime;
    
    public $Status;
    
    public $StatusTime;
    
    public $SFtotal = 0;
    
    public $SFactive = 0;
    
    public $SFpending = 0;
    
    public $SFsuccess = 0;
    
    public $SFfatalError = 0;
    
    public $SFsoftError = 0;
    
    public $SFdelayed = 0;
    
    public $SFstopped = 0;
    
    public $Playing = 0;
    
    public $SuccessRate = 0;
    
    public $Boost = 1;
    
    public $BoostCycles = 0;
    
    public $CyclesTotal = 0;
    
    public $Hidden = 0;
}
