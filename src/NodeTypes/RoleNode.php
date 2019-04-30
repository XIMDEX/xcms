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

namespace Ximdex\NodeTypes;

use Ximdex\Models\Role;

/**
 * @brief Handles roles
 */
class RoleNode extends Root
{
	/**
	 * Calls to method for adding a row to Roles table
	 * 
	 * {@inheritDoc}
	 * @see \Ximdex\NodeTypes\Root::createNode()
	 */
	public function createNode(string $name = null, int $parentID = null, int $nodeTypeID = null, int $stateID = null, string $icon = null
	    , string $description = null)
	{
		$role = new Role();
		$role->createNewRole($name, $icon, $description, $this->parent->get('IdNode'));
		$this->updatePath();
		return true;
	}

	/**
	 * Calls to method for deleting the Role from database
	 * 
	 * {@inheritDoc}
	 * @see \Ximdex\NodeTypes\Root::deleteNode()
	 */
	public function deleteNode() : bool
	{
		$role = new Role($this->parent->get('IdNode'));
		if ($role->delete() === false) {
		    $this->messages->mergeMessages($role->messages);
		    return false;
		}
		return true;
	}
}
