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

use Ximdex\MVC\ActionAbstract;
use Ximdex\Models\Node;
use Ximdex\Models\Metadata;

class Action_metadata extends ActionAbstract
{
    public function index()
    {
        $idNode = $this->request->getParam('nodeid');
        $node = new Node($idNode);
        if ($node->GetID() != null) {
            $metadata = new Metadata();
            $info = $metadata->getMetadataSectionAndGroupByNodeType($node->GetNodeType(), $idNode);
            $values = array(
                'info' => $info,
                'nodeTypeID' => $node->nodeType->getID(),
                'node_Type' => $node->nodeType->GetName(),
                'go_method' => 'saveMetadata',
                'nodeid' => $idNode,
            );
        } else {
            $this->messages->add(_('The operation has failed'), MSG_TYPE_ERROR);
            $values = array(
                'parentID' => $idNode,
                'messages' => $this->messages->messages
            );
            $this->sendJSON($values);
        }
        $this->addJs('/actions/modifyrole/js/modifyrole.js');
        $this->addCss('/actions/modifyrole/css/modifyrole.css');
        $this->render($values, 'index.tpl', 'default-3.0.tpl');
    }

    public function saveMetadata()
    {
        $idNode = $this->request->getParam('nodeid');
        $groups = $this->request->getParam('metadata');
        $resultAdd = true;
        $node = new Node($idNode);
        if ($node->GetID() != null) {
            $metadata = new Metadata();
            foreach ($groups as $group => $meta){
                $metadata->deleteMetadataValuesByNodeIdAndGroupId($idNode, $group);
                $resultAdd = $metadata->addMetadataValuesByNodeId($meta, $idNode) && $resultAdd;
            }
            if ($resultAdd) {
                $this->messages->add(_('The metadata has been successfully added'), MSG_TYPE_NOTICE);
            }
        } else {
            $resultAdd = false;
        }
        if (! $resultAdd) {
            $this->messages->add(_('The operation has failed'), MSG_TYPE_ERROR);
        }
        $values = array(
            'parentID' => $idNode,
            'messages' => $this->messages->messages
        );
        $this->sendJSON($values);
    }

    public function getMetadataByGroup()
    {
        $idGroup = $this->request->getParam('idGroup');
        $metadata = new Metadata();
        $metagroups = $metadata->getMetadataByMetagroup($idGroup);
        $values = array('metadata' => $metagroups);
        $this->render($values, 'metadata.tpl', 'only_template.tpl');
    }
}
