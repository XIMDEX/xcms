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

use Ximdex\Models\Link;
use Ximdex\Models\Node;
use Ximdex\MVC\ActionAbstract;

class Action_createlink extends ActionAbstract
{
    /**
     * Main method: shows initial form
     */
    public function index()
    {
        $idNode = $this->request->getParam('nodeid');
        $node = new Node($idNode);
        $this->addJs('/actions/createlink/resources/js/index.js');
        $values = array('go_method' => 'createlink',
            'nodeTypeID' => $node->nodeType->getID(),
            'node_Type' => $node->nodeType->GetName(),
            'name' => $node->GetNodeName());
        $this->render($values, null, 'default-3.0.tpl');
    }

    public function createlink()
    {
        $name = $this->request->getParam('name');
        $idParent = $this->request->getParam('id_node');
        $url = $this->request->getParam('url');
        $description = $this->request->getParam('description');
        $this->createNodeLink($name, $url, $description, $idParent);
        $values = [];
        $values['messages'] = $this->messages->messages;
        $values['parentID'] = $idParent;
        $this->sendJSON($values);
    }

    public function createNodeLink(string $name, string $url, string $description, int $idParent)
    {
        if (empty($description)) {
            $description = ' ';
        }
        $data = array('NODETYPENAME' => 'LINK',
            'NAME' => $name,
            'PARENTID' => $idParent,
            'IDSTATE' => 0,
            'CHILDRENS' => array(
                array('URL' => $url),
                array('DESCRIPTION' => $description)
            )
        );
        $bio = new \Ximdex\IO\BaseIO();
        $result = $bio->build($data);
        if ($result > 0) {
            $link = new Link($result);
            $link->set('ErrorString', 'not_checked');
            $link->set('CheckTime', time());
            $link->update();
            $this->messages->add(_('Link has been successfully added'), MSG_TYPE_NOTICE);
        }
        return $result;
    }
}
