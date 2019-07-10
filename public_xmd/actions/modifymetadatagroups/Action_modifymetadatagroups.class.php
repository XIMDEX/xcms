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

class Action_modifymetadatagroups extends ActionAbstract
{
    public function index()
    {
        $nodeId = (int) $this->request->getParam('nodeid');
        $node = new Node($nodeId);
        $metadata = new Metadata();
        $metadataList = $metadata->find('idMetadata, name', null, null, MULTI, true, 'idMetadata', 'name', null, true);
        $data = $metadata->getMetadataSchemesAndGroups(true);
        $this->addJs('/actions/modifyrole/js/modifyrole.js');
        $this->addCss('/actions/modifyrole/css/modifyrole.css');
        $this->addCss('/actions/modifymetadatagroups/resources/css/styles.css');
        $values = array
        (
            'name' => $node->get('Name'),
            'idnode' => $node->getID(),
            'metadata' => json_encode($metadataList),
            'data' => json_encode($data),
            'nodeTypeID' => $node->nodeType->getID(),
            'node_Type' => $node->nodeType->getName()
        );
        $this->render($values, null, 'default-3.0.tpl');
    }

    public function add()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $metadataId = (int) $data['metadata'] ?? null;
        $schemeId = (int) $data['scheme'] ?? null;
        $groupId = (int) $data['group'] ?? null;
        if (! $metadataId or ! $schemeId or ! $groupId) {
            $values = array
            (
                'result' => 'notok', 
                'error' => _('Metadata, scheme and group are necesary')
            );
            $this->sendJSON($values);
        }
        $required = (bool) ($data['required'] ?? false);
        $readonly = (bool) ($data['readonly'] ?? false);
        $enabled = (bool) ($data['enabled'] ?? false);
        try {
            $id = Metadata::relMetadataAndGroup($metadataId, $groupId, $required, $readonly, $enabled);
        } catch (Exception $e) {
            $values = array
            (
                'result' => 'notok', 
                'error' => $e->getMessage()
            );
            $this->sendJSON($values);
        }
        $values = array
        (
            'result' => 'ok', 
            'id' => $id
        );
        $this->sendJSON($values);
    }

    public function save()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $messages = [];
        if (isset($data['removed']) and $data['removed']) {
            $deleted = 0;
            foreach ($data['removed'] as $id) {
                if (Metadata::deleteRelMetadataAndGroup($id)) {
                    $deleted++;
                }
            }
            if ($deleted) {
                $messages[] = sprintf(_('%s metadata have been deleted'), $deleted);
            }
        }
        if (isset($data['schemes']) and $data['schemes']) {
            $updated = 0;
            foreach ($data['schemes'] as $scheme) {
                if (! isset($scheme['groups']) or ! $scheme['groups']) {
                    continue;
                }
                foreach ($scheme['groups'] as $group) {
                    if (! isset($group['metadata']) or ! $group['metadata']) {
                        continue;
                    }
                    foreach ($group['metadata'] as $metadata) {
                        $id = (int) $metadata['id'];
                        $required = (bool) $metadata['required'];
                        $readonly = (bool) $metadata['readonly'];
                        $enabled = (bool) $metadata['enabled'];
                        try {
                            if (Metadata::updateRelMetadataAndGroup($id, $required, $readonly, $enabled)) {
                                $updated++;
                            }
                        } catch (Exception $e) {
                            $values = array
                            (
                                'result' => 'notok', 
                                'error' => $e->getMessage()
                            );
                            $this->sendJSON($values);
                        }
                    }
                }
            }
            if ($updated) {
                $messages[] = sprintf(_('%s metadata have been updated'), $updated);
            }
        }
        if (! $updated and ! $deleted) {
            $messages[] = _('No changes in metadata have been made');
        }
        $values = array
        (
            'result' => 'ok', 
            'messages' => $messages
        );
        $this->sendJSON($values);
    }
}
