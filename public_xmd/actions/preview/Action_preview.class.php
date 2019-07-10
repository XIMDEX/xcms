<?php

/**
 * \details &copy; 2019 Open Ximdex Evolution SL [http://www.ximdex.org]
 *
 * Ximdex a Semantic Content Management System (CMS)
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * See the Affero GNU General Public License for more details.
 * You should have received a copy of the Affero GNU General Public License
 * version 3 along with Ximdex (see LICENSE file).
 *
 * If not, visit http://gnu.org/licenses/agpl-3.0.html.
 *
 * @author Ximdex DevTeam <dev@ximdex.com>
 * @version $Revision$
 */

use Ximdex\Models\Node;
use Ximdex\Models\StructuredDocument;
use Ximdex\Models\User;
use Ximdex\MVC\ActionAbstract;
use Ximdex\Runtime\App;
use Ximdex\Runtime\DataFactory;
use Ximdex\Models\Channel;

class Action_preview extends ActionAbstract
{
    /**
     * Main method: shows initial form
     */
    public function index()
    {
        $idNode = (int) $this->request->getParam('nodeid');
        $params = $this->request->getParam('params');
        $channelId = (int) $this->request->getParam('channel');
        $node = new Node($idNode);
        $data = new DataFactory($idNode);
        if (is_null($this->request->getParam('version'))) {
            $version = $data->getLastVersion();
        } else {
            $version = (int) $this->request->getParam('version');
        }
        if (is_null($this->request->getParam('subversion'))) {
            $subversion = $data->getLastSubVersion($version);
        } else {
            $subversion = (int) $this->request->getParam('subversion');
        }
        setlocale(LC_TIME, 'es_ES');
        $date = strftime('%a, %d/%m/%G %R', $data->getDate($version, $subversion));
        $user = new User($data->getUserID($version, $subversion));
        $userName = $user->getRealName();
        if ($node->nodeType->getName() != 'TextFile' && $node->nodeType->getName() != 'ImageFile' 
                && $node->nodeType->getName() != 'BinaryFile' && $node->nodeType->getName() != 'NodeHt') {
            $channel_title = 'channel';
        } else {
            $channel_title = '';
        }
        $doc = new StructuredDocument($idNode);
        $channelList = $doc->getChannels();
        $channels = array();
        if (count($channelList)) {
            foreach ($channelList as $id) {
                $channel = new Channel($id);
                $channels[] = array(
                    'Id' => $id,
                    'Name' => $channel->getName()
                );
            }
        }
        $this->addCss('/actions/preview/resources/css/style.css');
        $queryManager = App::get('\Ximdex\Utils\QueryManager');
        $this->addJs('/actions/preview/resources/js/preview.js');
        $values = array(
            'id_node' => $idNode,
            'params' => $params,
            'version' => $version,
            'subversion' => $subversion,
            'channel_title' => $channel_title,
            'nameNodeType' => $node->nodeType->getName(),
            'date' => $date,
            'user_name' => $userName,
            'channels' => $channels,
            'nodeURL' => $queryManager->getPage() . $queryManager->build(),
            'go_method' => 'preview',
            'name' => $node->getNodeName(),
            'token' => uniqid(),
            'channelId' => $channelId
        );
        $this->render($values, null, 'default-3.0.tpl');
    }
}
