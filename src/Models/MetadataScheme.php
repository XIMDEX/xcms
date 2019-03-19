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

namespace Ximdex\Models;

use Ximdex\Data\GenericData;

class MetadataScheme extends GenericData
{
    public $_idField = 'idMetadataScheme';
    
    public $_table = 'MetadataScheme';
    
    public $_metaData = array
    (
        'idMetadataScheme' => array('type' => "int(12)", 'not_null' => 'true', 'primary_key' => true),
        'name' => array('type' => "varchar(255)", 'not_null' => 'true')
    );
    
    public $_uniqueConstraints = array('name');
    
    public $_indexes = array();
    
    public $idMetadataScheme;
    
    public $name;
    
    public function getNodeTypes() : array
    {
        if (! $this->idMetadataScheme) {
            throw new \Exception('No schema selected');
        }
        $query = 'SELECT nt.IdNodeType, nt.Name
            FROM NodeTypes nt 
            JOIN RelMetadataSchemeNodeType rel ON rel.idNodeType = nt.IdNodeType AND rel.idMetadataScheme = ' . $this->idMetadataScheme;
        $dbObj = new \Ximdex\Runtime\Db();
        if ($dbObj->query($query) === false) {
            throw new \Exception('Query error in metadata scheme node types retrieve operation');
        }
        $nodeTypes = [];
        while (! $dbObj->EOF) {
            $nodeTypes[$dbObj->getValue('IdNodeType')] = $dbObj->getValue('Name');
            $dbObj->next();
        }
        return $nodeTypes;
    }
}
