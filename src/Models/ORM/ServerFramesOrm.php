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

class ServerFramesOrm extends GenericData
{
    public $_idField = 'IdSync';
    public $_table = 'ServerFrames';
    public $_metaData = array(
        'IdSync' => array('type' => 'int(12)', 'not_null' => 'true', 'auto_increment' => 'true'),
        'IdServer' => array('type' => 'int(12)', 'not_null' => 'true'),
        'DateUp' => array('type' => 'int(14)', 'not_null' => 'true'),
        'DateDown' => array('type' => 'int(14)', 'not_null' => 'false'),
        'State' => array('type' => 'varchar(255)', 'not_null' => 'false'),
        'Error' => array('type' => 'varchar(255)', 'not_null' => 'false'),
        'ErrorLevel' => array('type' => 'varchar(3)', 'not_null' => 'false'),
        'RemotePath' => array('type' => 'blob', 'not_null' => 'false'),
        'FileName' => array('type' => 'varchar(255)', 'not_null' => 'true'),
        'FileSize' => array('type' => 'int(11)', 'not_null' => 'true'),
        'Retry' => array('type' => 'int(12)', 'not_null' => 'false'),
        'Linked' => array('type' => 'tinyint(3)', 'not_null' => 'true'),
        'IdNodeFrame' => array('type' => 'int(12)', 'not_null' => 'false'),
        'IdBatchUp' => array('type' => 'int(12)', 'not_null' => 'false'),
        'IdChannelFrame' => array('type' => 'int(12)', 'not_null' => 'false'),
        'PumperId' => array('type' => 'int(12)', 'not_null' => 'false'),
        'cache' => array('type' => 'tinyint(1)', 'not_null' => 'true'),
        'NodeId' => array('type' => 'int(12)', 'not_null' => 'false'),
        'ChannelId' => array('type' => 'int(12)', 'not_null' => 'false'),
        'IdBatchDown' => array('type' => 'int(12)', 'not_null' => 'false'),
        'IdPortalFrame' => array('type' => 'int(12)', 'not_null' => 'true')
    );
    public $IdSync;
    public $IdServer = 0;
    public $DateUp = 0;
    public $DateDown = 0;
    public $State = 'DUE';
    public $Error;
    public $ErrorLevel =  null;
    public $RemotePath;
    public $FileName;
    public $FileSize;
    public $Retry = 0;
    public $Linked = 1;
    public $IdNodeFrame = 0;
    public $IdBatchUp = 0;
    public $IdChannelFrame = 0;
    public $PumperId;
    public $cache;
    public $NodeId;
    public $ChannelId;
    public $IdBatchDown = null;
    public $IdPortalFrame;
}
