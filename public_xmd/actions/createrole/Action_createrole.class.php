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

use Ximdex\Models\Node;
use Ximdex\Models\NodeType;
use Ximdex\MVC\ActionAbstract;

class Action_createrole extends ActionAbstract
{
    /**
     * Main method: shows initial form
     */
    public function index()
    {
        $idNode = $this->request->getParam('nodeid');
        $node = new Node($idNode);
        $values = array('go_method' => 'createrole',
            'nodeTypeID' => $node->nodeType->getID(),
            'node_Type' => $node->nodeType->GetName()
        );
        $this->render($values, null, 'default-3.0.tpl');
    }

    public function createrole()
    {
        $idNode = $this->request->getParam('id_node');
        $name = $this->request->getParam('name');
        $description = $this->request->getParam('description');
        $nodeType = new NodeType();
        $nodeType->SetByName('Role');
        $rol = new Node();
        $result = $rol->CreateNode($name, $idNode, $nodeType->get('IdNodeType'), null, null, $description);
        if ($result > 0) {
            $rol->messages->add(_('Role has been successfully added'), MSG_TYPE_NOTICE);
        }
        $values = array('messages' => $rol->messages->messages, 'parentID' => $idNode);
        $this->sendJSON($values);
    }
}
