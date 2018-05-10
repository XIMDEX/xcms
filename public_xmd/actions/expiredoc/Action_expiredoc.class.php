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

use Ximdex\Models\Node;
use Ximdex\MVC\ActionAbstract;
use Ximdex\Runtime\DataFactory;
use Ximdex\Sync\SynchroFacade;

class Action_expiredoc extends ActionAbstract
{
    /**
     * Main method: shows initial form
     */
    public function index()
    {
        $idNode = $this->request->getParam('nodeid');
        $node = new Node($idNode);
        $docName = $node->get('Name');
        $values = array('doc_name' => $docName, 'go_method' => 'result', 'name' => $node->GetNodeName());
        $this->render($values, '', 'default-3.0.tpl');
    }

    public function result()
    {
        $idNode = $this->request->getParam('nodeid');
        $node = new Node($idNode);
        $synchroFacade = new SynchroFacade();
        $synchroFacade->deleteAllTasksByNode($idNode, true);
        $df = new DataFactory($idNode);
        $df->AddVersion();
        $this->messages->add(sprintf(_("Document <strong>%s</strong> has been successfully expired"), $node->get('Name')), MSG_TYPE_NOTICE);
        $values = array('messages' => $this->messages->messages);
        $this->sendJSON($values);
    }
}