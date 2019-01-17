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

class NodeFramesOrm extends GenericData
{
    public $_idField = 'IdNodeFrame';
    public $_table = 'NodeFrames';
    public $_metaData = array(
        'IdNodeFrame' => array('type' => 'int(12)', 'not_null' => 'true', 'auto_increment' => 'true'),
        'NodeId' => array('type' => 'int(12)', 'not_null' => 'true'),
        'VersionId' => array('type' => 'int(12)', 'not_null' => 'true'),
        'TimeUp' => array('type' => 'int(12)', 'not_null' => 'false'),
        'TimeDown' => array('type' => 'int(12)', 'not_null' => 'false'),
        'Active' => array('type' => 'int(12)', 'not_null' => 'true'),
        'GetActivityFrom' => array('type' => 'int(12)', 'not_null' => 'true'),
        'IsProcessUp' => array('type' => 'int(12)', 'not_null' => 'true'),
        'IsProcessDown' => array('type' => 'int(12)', 'not_null' => 'true'),
        'Name' => array('type' => 'varchar(255)', 'not_null' => 'true'),
        'IdPortalFrame' => array('type' => 'int(12)', 'not_null' => 'true'),
        'TimeStampState' => array('type' => 'int(12)', 'not_null' => 'false'),
        'TimeStampProccesed' => array('type' => 'int(12)', 'not_null' => 'false'),
        'SF_Total' => array('type' => 'int(4)', 'not_null' => 'false'),
        'SF_IN' => array('type' => 'int(4)', 'not_null' => 'false')
    );
    public $IdNodeFrame;
    public $NodeId = 0;
    public $VersionId = 0;
    public $TimeUp;
    public $TimeDown;
    public $Active = 0;
    public $GetActivityFrom = 0;
    public $IsProcessUp = 0;
    public $IsProcessDown = 0;
    public $Name;
    public $IdPortalFrame;
    public $TimeStampState;
    public $TimeStampProccesed;
    public $SF_Total = 0;
    public $SF_IN = 0;
}
