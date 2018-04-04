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

namespace Ximdex\Models\ORM;

use Ximdex\Data\GenericData;

class IsoCodesOrm extends GenericData
{
    var $_idField = 'IdIsoCode';
    var $_table = 'IsoCodes';
    var $_metaData = array(
        'IdIsoCode' => array('type' => "int(12)", 'not_null' => 'true', 'auto_increment' => 'true', 'primary_key' => true),
        'Iso2' => array('type' => "char(2)", 'not_null' => 'false'),
        'Iso3' => array('type' => "char(3)", 'not_null' => 'false'),
        'Name' => array('type' => "varchar(255)", 'not_null' => 'false')
    );
    var $_uniqueConstraints = array(
        'iso3' => array('Iso3'), 'iso2' => array('Iso2'), 'name' => array('Name')
    );
    var $_indexes = array('IdIsoCode');
    var $IdIsoCode;
    var $Iso2;
    var $Iso3;
    var $Name;
}