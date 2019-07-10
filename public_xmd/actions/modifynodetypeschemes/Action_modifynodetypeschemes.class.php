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
use Ximdex\Models\Metadata;
use Ximdex\Models\Node;
use Ximdex\Models\MetadataScheme;
use Ximdex\Models\NodeType;

class Action_modifynodetypeschemes extends ActionAbstract
{
    public function index()
    {
        $nodeId = (int) $this->request->getParam('nodeid');
        $node = new Node($nodeId);
        $nodeType = new NodeType();
        $nodeTypes = $nodeType->find('IdNodeType as id, Name as name, Description as description', 'HasMetadata IS TRUE', null, MULTI, true
            , null, null, null, true);
        $metadataScheme = new MetadataScheme();
        $schemes = $metadataScheme->find('idMetadataScheme as id, name', null, null, MULTI, true, null, null, null, true);
        foreach ($schemes as & $data) {
            $scheme = new MetadataScheme($data['id']);
            $data['nodeTypes'] = $scheme->getNodeTypes();
        }
        $this->addCss('/actions/modifynodetypeschemes/resources/css/styles.css');
        $this->addJs('/actions/modifynodetypeschemes/resources/js/index.js');
        $values = array
        (
            'go_method' => 'save',
            'name' => $node->getNodeName(),
            'nodeTypeID' => $node->nodeType->getID(),
            'node_Type' => $node->nodeType->getName(),
            'schemes' => $schemes,
            'nodeTypes' => $nodeTypes
        );
        $this->render($values, '', 'default-3.0.tpl');
    }

    public function save()
    {
        $schemes = (array) $this->request->getParam('nodeTypes');
        try {
            Metadata::clearSchemeNodeTypes();
            foreach ($schemes as $schemeId => $nodeTypes) {
                foreach ($nodeTypes as $nodeTypeId) {
                    Metadata::relSchemeAndNodeType($schemeId, $nodeTypeId);
                }
            }
            $this->messages->add(_('Changes to schemes made successfully'), MSG_TYPE_NOTICE);
        } catch (Exception $e) {
            $this->messages->add(_($e->getMessage(), MSG_TYPE_ERROR));
        }
        $values = array
        (
            'messages' => $this->messages->messages,
            'goback' => true
        );
        $this->sendJSON($values);
    }
}
