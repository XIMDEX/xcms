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

use Ximdex\Models\Action;

/**
 * @brief Handles the Ximdex actions
 */
class ActionNode extends Root
{
    /**
     * Calls to method for adding a row to Actions table
     * 
     * @param string name
     * @param int parentID
     * @param int nodeTypeID
     * @param int stateID
     * @param string command
     * @param string icon
     * @param string description
     */
    public function createNode(string $name = null, int $parentID = null, int $nodeTypeID = null, int $stateID = null, string $command = null
        , string $icon = null, string $description = null)
    {
        $action = new Action();
        $action->CreateNewAction($this->parent->get('IdNode'), $parentID, $name, $command, $icon, $description, $stateID);
        $this->updatePath();
        return true;
    }

    /**
     * {@inheritDoc}
     * @see \Ximdex\NodeTypes\Root::renameNode()
     */
    public function renameNode(string $name) : bool
    {
        $action = new Action($this->nodeID);
        $action->SetName($name);
        $this->UpdatePath();
        return true;
    }

    /**
     *  Calls to method for deleting
     */
    public function deleteNode() : bool
    {
        $action = new Action($this->nodeID);
        return $action->deleteAction();
    }
}

