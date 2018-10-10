<?php

/**
 *  \details &copy; 2018 Open Ximdex Evolution SL [http://www.ximdex.org]
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
        'StartTime' => array('type' => 'int(12)', 'not_null' => 'false'),
        'EndTime' => array('type' => 'int(12)', 'not_null' => 'false'),
        'Status' => array('type' => 'varchar(255)', 'not_null' => 'true'),
        'StatusTime' => array('type' => 'int(12)', 'not_null' => 'false'),
        'SFtotal' => array('type' => 'int(12)', 'not_null' => 'true'),
        'SFactive' => array('type' => 'int(12)', 'not_null' => 'true'),
        'SFpending' => array('type' => 'int(12)', 'not_null' => 'true'),
        'SFsuccess' => array('type' => 'int(12)', 'not_null' => 'true'),
        'SFfatalError' => array('type' => 'int(12)', 'not_null' => 'true'),
        'SFsoftError' => array('type' => 'int(12)', 'not_null' => 'true')
    );
    public $_uniqueConstraints = array();
    public $_indexes = array('id');
    public $id;
    public $IdNodeGenerator = null;
    public $Version = 0;
    public $CreationTime;
    public $CreatedBy = null;
    public $StartTime = null;
    public $EndTime = null;
    public $Status;
    public $StatusTime = null;
    public $SFtotal = 0;
    public $SFactive = 0;
    public $SFpending = 0;
    public $SFsuccess = 0;
    public $SFfatalError = 0;
    public $SFsoftError = 0;
}
