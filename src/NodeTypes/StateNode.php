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

namespace Ximdex\NodeTypes;

use Ximdex\Logger;
use Ximdex\Models\Workflow;

class StateNode extends Root
{
    /**
     * {@inheritDoc}
     * @see \Ximdex\NodeTypes\Root::createNode()
     */
    public function createNode(string $name = null, int $parentID = null, int $nodeTypeID = null, int $stateID = null, string $description = ''
        , int $idTransition = null)
    {
        $this->updatePath();
        return true;
    }

    /**
     * {@inheritDoc}
     * @see \Ximdex\NodeTypes\Root::renameNode()
     */
    public function renameNode(string $name) : bool
    {
        $this->updatePath();
        return true;
    }

    public function CanDenyDeletion()
    {
        $workflow = new Workflow(null, $this->nodeID);
        if ($workflow->isInitialState() or $workflow->isFinalState()) {
            $this->messages->add(_('Unable to delete the first and last states of the workflow'), MSG_TYPE_ERROR);
            Logger::warning('Imposible eliminar estado primero y último de workflow');
            return true;
        }
        return false;
    }

    public function getDependencies() : array
    {
        $sql = 'SELECT DISTINCT IdNode FROM Nodes WHERE IdState = \'' . $this->nodeID . '\'';
        $this->dbObj->Query($sql);
        $deps = array();
        while (! $this->dbObj->EOF) {
            $deps[] = $this->dbObj->row['IdNode'];
            $this->dbObj->Next();
        }
        return $deps;
    }
}
