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

class ProgrammingLanguage extends GenericData
{
    public $_idField = 'id';
    
    public $_table = 'ProgrammingLanguage';
    
    public $_metaData = array(
        'id' => array('type' => "varchar(20)", 'not_null' => 'true', 'primary_key' => true),
        'description' => array('type' => 'varchar(50)', 'not_null' => 'true')
    );
    
    protected $id;
    
    protected $description;
    
	public function getId() : string
	{
		return $this->get('id');
	}
    
	public function setId(string $id) : void
	{
	   $this->set('id', $id);
	}
	
	public function getDescription() : string
	{
	    return $this->get('description');
	}
	
	public function setDescription(string $description) : void
	{
	    $this->set('description', $description);
	}
}
