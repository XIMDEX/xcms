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

use Ximdex\Logger;
use Ximdex\Models\Channel;
use Ximdex\Models\Node;
use Ximdex\Models\WorkflowStatus;
use Ximdex\MVC\ActionAbstract;
use Ximdex\Runtime\App;
use Ximdex\Models\RelNode2Asset;
use Ximdex\Models\RelXml2Xml;
use Ximdex\Models\RelStrdocTemplate;

Ximdex\Modules\Manager::file('/actions/manageversions/Action_manageversions.class.php');

class Action_infonode extends ActionAbstract
{
    const MAX_VERSIONS = 10;
    
    public function index()
    {
        $this->addCss('/actions/infonode/resources/css/style.css');
        $this->addCss('/actions/infonode/resources/css/svg.css');
        $this->addJs('/actions/infonode/resources/js/colorbrewer.js');
        $this->addJs('/actions/infonode/resources/js/geometry.js');
        $this->addJs('/actions/infonode/resources/js/script.js');
        $idNode = (int) $this->request->getParam('nodeid');
        $node = new Node($idNode);
        $info = $node->loadData();

        // Obtain name of current status
        if (isset($info['state'])) {
            $wfStatus = new WorkflowStatus($info['state']);
            $statusInfo = $wfStatus->get('name');
        } else {
            $statusInfo = null;
        }
        
        // Channels
        $channel = new Channel();
        $channels = $channel->getChannelsForNode($idNode);

        // Languages
        $nodeLanguages = $node->getProperty('language', true);
        $languages = array();
        if (! empty($nodeLanguages)) {
            $i = 0;
            foreach ($nodeLanguages as $_lang) {
                $_node = new Node($_lang);
                $languages[$i]['Id'] = $_lang;
                $languages[$i]['Name'] = $_node->get('Name');
                $i++;
            }
        }
        $jsonUrl = App::getUrl('/?action=infonode&method=getDependencies&nodeid=' . $idNode);
        $manageVersions = new Action_manageversions();
        $valuesManageVersion = $manageVersions->values($idNode, self::MAX_VERSIONS);
        $this->addJs('/actions/manageversions/resources/js/index.js');
        $this->addCss('/actions/manageversions/resources/css/index.css');
        $values = array(
            'id_node' => $idNode,
            'info' => $info,
            'statusInfo' => $statusInfo,
            'channels' => $channels,
            'languages' => $languages,
            'jsonUrl' => $jsonUrl,
            'node_Type' => $node->nodeType->GetName(),
            'valuesManageVersion' => $valuesManageVersion,
            'maxVersions' => self::MAX_VERSIONS
        );
        $this->render($values, 'index', 'default-3.0.tpl');
    }

    public function getDependencies()
    {
        $idNode = (int) $this->request->getParam('nodeid');
        $masterNode = new Node($idNode);
        if (!$masterNode->GetID()) {
            Logger::error('Cannot load the node with ID: ' . $idNode);
            return false;
        }
        $depMasterList = array();
        $classes = array(new RelNode2Asset(), new RelXml2Xml(), new RelStrdocTemplate());
        foreach ($classes as $c) {
            $res = $c->find('target', 'source=' . $idNode, null, MONO);
            if (count($res) > 0) {
                $depMasterList = array_merge($depMasterList, $res);
            }
        }
        $data = array();
        $errors = array();
        $depMasterNameList = array();
        foreach ($depMasterList as $idDependentNode) {
            $node = new Node((int) $idDependentNode);
            if ($node->GetID() and $node->GetID() != $idNode) {
                $obj = [];
                $name = $node->GetNodeName() . ' (' . $node->GetID() . ')';
                $obj['name'] = $name;
                $obj['type'] = $node->GetTypeName();
                $obj['depends'] = array();
                $obj['position'] = 'master';
                $obj['dependedOnBy'] = [$name];
                $data[$name] = $obj;
                $depMasterNameList[] = $name;
            }
        }
        $data[$masterNode->GetNodeName()] = array(
            'name' => $masterNode->GetNodeName(),
            'type' => $masterNode->GetTypeName(),
            'depends' => $depMasterNameList,
            'position' => 'center',
            'dependedOnBy' => []
        );
        $this->sendJSON(array(
            'data' => $data,
            'errors' => $errors
        ));
    }
}
