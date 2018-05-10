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
 * @author Ximdex DevTeam <dev@ximdex.com>
 * @version $Revision$
 */

use Ximdex\Data\GenericData;

class Batchs_ORM extends GenericData
{
    var $_idField = 'IdBatch';
    var $_table = 'Batchs';
    var $_metaData = array(
        'IdBatch' => array('type' => "int(12)", 'not_null' => 'true', 'auto_increment' => 'true', 'primary_key' => true),
        'TimeOn' => array('type' => "int(12)", 'not_null' => 'true'),
        'State' => array('type' => "varchar(255)", 'not_null' => 'true'),
        'ServerFramesTotal' => array('type' => "int(12)", 'not_null' => 'false'),
        'ServerFramesSucess' => array('type' => "int(12)", 'not_null' => 'false'),
        'ServerFramesError' => array('type' => "int(12)", 'not_null' => 'false'),
        'Playing' => array('type' => "int(12)", 'not_null' => 'false'),
        'Type' => array('type' => "varchar(255)", 'not_null' => 'true'),
        'IdBatchDown' => array('type' => "int(12)", 'not_null' => 'false'),
        'IdNodeGenerator' => array('type' => "int(12)", 'not_null' => 'false'),
        'Priority' => array('type' => "float(3, 2)", 'not_null' => 'false'),
        'MajorCycle' => array('type' => "int(12)", 'not_null' => 'false'),
        'MinorCycle' => array('type' => "int(12)", 'not_null' => 'false'),
        'IdPortalVersion' => array('type' => "int(12)", 'not_null' => 'true'),
        'UserId' => array('type' => "int(12)", 'not_null' => 'false'),
    );
    var $_uniqueConstraints = array();
    var $_indexes = array('IdBatch');
    var $IdBatch;
    var $TimeOn;
    var $State;
    var $ServerFramesTotal = 0;
    var $ServerFramesSucess = 0;
    var $ServerFramesError = 0;
    var $Playing;
    var $Type = 0;
    var $IdBatchDown;
    var $IdNodeGenerator;
    var $Priority = 0.00;
    var $MajorCycle = 0;
    var $MinorCycle = 0;
    var $IdPortalVersion;
    var $UserId;
}