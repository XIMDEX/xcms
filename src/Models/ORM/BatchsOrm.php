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

class BatchsOrm extends GenericData
{
    public $_idField = 'IdBatch';
    
    public $_table = 'Batchs';
    
    public $_metaData = array(
        'IdBatch' => array('type' => "int(12)", 'not_null' => 'true', 'auto_increment' => 'true', 'primary_key' => true),
        'TimeOn' => array('type' => "int(12)", 'not_null' => 'true'),
        'State' => array('type' => "varchar(255)", 'not_null' => 'true'),
        'ServerFramesTotal' => array('type' => "int(12)", 'not_null' => 'true'),
        'ServerFramesPending' => array('type' => "int(12)", 'not_null' => 'true'),
        'ServerFramesActive' => array('type' => "int(12)", 'not_null' => 'true'),
        'ServerFramesSuccess' => array('type' => "int(12)", 'not_null' => 'true'),
        'ServerFramesFatalError' => array('type' => "int(12)", 'not_null' => 'true'),
        'ServerFramesTemporalError' => array('type' => "int(12)", 'not_null' => 'true'),
        'Type' => array('type' => "varchar(255)", 'not_null' => 'true'),
        'IdBatchDown' => array('type' => "int(12)", 'not_null' => 'false'),
        'IdNodeGenerator' => array('type' => "int(12)", 'not_null' => 'false'),
        'Priority' => array('type' => "float(3, 2)", 'not_null' => 'true'),
        'Cycles' => array('type' => "int(12)", 'not_null' => 'true'),
        'IdPortalFrame' => array('type' => "int(12)", 'not_null' => 'true'),
        'UserId' => array('type' => "int(12)", 'not_null' => 'false'),
        'ServerId' => array('type' => "int(12)", 'not_null' => 'true')
    );
    
    public $_uniqueConstraints = array();
    
    public $_indexes = array('IdBatch');
    
    public $IdBatch;
    
    public $TimeOn;
    
    public $State;
    
    public $ServerFramesTotal = 0;
    
    public $ServerFramesPending = 0;
    
    public $ServerFramesActive = 0;
    
    public $ServerFramesSuccess = 0;
    
    public $ServerFramesFatalError = 0;
    
    public $ServerFramesTemporalError = 0;
    
    public $Type;
    
    public $IdBatchDown;
    
    public $IdNodeGenerator;
    
    public $Priority = 1.0;
    
    public $Cycles = 0;
    
    public $IdPortalFrame;
    
    public $UserId;
    
    public $ServerId;
}
