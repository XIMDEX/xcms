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

/**
 * @deprecated
 */
class PipeTransitionsOrm extends GenericData
{
    public $_idField = 'id';
    public $_table = 'PipeTransitions';
    public $_metaData = array(
        'id' => array('type' => "int(11)", 'not_null' => 'true', 'auto_increment' => 'true', 'primary_key' => true),
        'IdStatusFrom' => array('type' => "int(11)", 'not_null' => 'false'),
        'IdStatusTo' => array('type' => "int(11)", 'not_null' => 'true'),
        'IdPipeProcess' => array('type' => "int(11)", 'not_null' => 'true'),
        'Cacheable' => array('type' => "tinyint(1)", 'not_null' => 'true'),
        'Name' => array('type' => "varchar(255)", 'not_null' => 'true'),
        'Callback' => array('type' => "varchar(255)", 'not_null' => 'false')
    );
    public $_uniqueConstraints = array();
    public $_indexes = array('id');
    public $id;
    public $IdStatusFrom = null;
    public $IdStatusTo;
    public $IdPipeProcess = null;
    public $Cacheable = 0;
    public $Name;
    public $Callback = null;
}
