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

class Action_createmetadata extends ActionAbstract
{
    public function index()
    {
        $nodeId = $this->request->getParam('nodeid');
        $node = new Node($nodeId);
        $types = Metadata::META_TYPES;
        $metadata = new Metadata();
        $metadataList = $metadata->find(ALL, null, null, MULTI, true, null, 'name', null, true);
        $this->addCss('/actions/createmetadata/resources/css/styles.css');
        $values = array('name' => $node->get('Name'),
            'idnode' => $node->getID(),
            'metadata' => json_encode($metadataList),
            'metadataTypes' => json_encode($types),
            'nodeTypeID' => $node->nodeType->getID(),
            'node_Type' => $node->nodeType->getName()
        );
        $this->render($values, null, 'default-3.0.tpl');
    }

    public function add()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $name = $data['name'] ?? null;
        $type = $data['type'] ?? null;
        if (! $type or ! $name) {
            $values = array('result' => 'notok', 'error' => _('Name and type fields are necesary to create a new metadata'));
            $this->sendJSON($values);
        }
        $metadata = new Metadata();
        $res = $metadata->find(ALL, 'name = \'' . $name . '\'');
        if ($res) {
            $values = array('result' => 'notok', 'error' => sprintf(_('A metadata with the name %s already exists'), $name));
            $this->sendJSON($values);
        }
        if (! in_array($type, Metadata::META_TYPES)) {
            $values = array('result' => 'notok', 'error' => sprintf(_('Type %s is not a valid metadata type'), $type));
            $this->sendJSON($values);
        }
        $defaultValue = $data['defaultValue'] ?? null;
        $metadata->set('name', $name);
        $metadata->set('type', $type);
        $metadata->set('defaultValue', $defaultValue);
        $id = $metadata->add();
        if ($id === false) {
            $values = array('result' => 'notok');
            $this->sendJSON($values);
        }
        $values = array('result' => 'ok', 'id' => $id);
        $this->sendJSON($values);
    }

    public function save()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $messages = [];
        if (isset($data['removed']) and $data['removed']) {
            $deleted = 0;
            foreach ($data['removed'] as $id) {
                $meta = new Metadata($id);
                if (! $meta->get('idMetadata')) {
                    continue;
                }
                if ($meta->delete()) {
                    $deleted++;
                }
            }
            if ($deleted) {
                $messages[] = sprintf(_('%s metadata have been deleted'), $deleted);
            }
        }
        if (isset($data['metadata']) and $data['metadata']) {
            $updated = 0;
            foreach ($data['metadata'] as $metadata) {
                $meta = new Metadata($metadata['idMetadata']);
                if (! $meta->get('idMetadata')) {
                    continue;
                }
                $meta->set('name', $metadata['name']);
                $meta->set('defaultValue', $metadata['defaultValue']);
                if ($meta->update()) {
                    $updated++;
                }
            }
            if ($updated) {
                $messages[] = sprintf(_('%s metadata have been updated'), $updated);
            }
        }
        $values = array('result' => 'ok', 'messages' => $messages);
        $this->sendJSON($values);
    }
}
