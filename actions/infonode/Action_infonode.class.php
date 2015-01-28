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

ModulesManager::file('/inc/model/language.php');
ModulesManager::file('/inc/model/channel.php');
ModulesManager::file('/actions/manageproperties/inc/InheritedPropertiesManager.class.php');
ModulesManager::file('/inc/model/RelStrdocAsset.class.php');
ModulesManager::file('/inc/model/RelStrdocCss.class.php');
ModulesManager::file('/inc/model/RelStrdocNode.class.php');
ModulesManager::file('/inc/model/RelStrdocScript.class.php');
ModulesManager::file('/inc/model/RelStrdocStructure.class.php');
ModulesManager::file('/inc/model/RelStrdocTemplate.class.php');
ModulesManager::file('/inc/model/RelStrdocXimlet.class.php');
ModulesManager::file('/inc/model/RelNodeMetadata.class.php');


class Action_infonode extends ActionAbstract
{

    function index()
    {
        $this->addCss('/actions/infonode/resources/css/style.css');
        $this->addCss('/actions/infonode/resources/css/svg.css');

        $this->addJs('/actions/infonode/resources/js/colorbrewer.js');
        $this->addJs('/actions/infonode/resources/js/geometry.js');
        $this->addJs('/actions/infonode/resources/js/script.js');

        $idNode = (int)$this->request->getParam("nodeid");
        $node = new Node($idNode);
        $info = $node->loadData();

        //channels
        $channel = new Channel();
        $channels = $channel->getChannelsForNode($idNode);

        //languages
        $nodeLanguages = $node->getProperty('language', true);
        $languages = array();
        if (!empty($nodeLanguages)) {
            $i = 0;
            foreach ($nodeLanguages as $_lang) {
                $_node = new Node($_lang);
                $languages[$i]["Id"] = $_lang;
                $languages[$i]["Name"] = $_node->get("Name");
                $i++;
            }
        }

        $urlRoot = App::getValue('UrlRoot');
        $jsonUrl = $urlRoot . "/xmd/loadaction.php?action=infonode&method=getDependencies&nodeid=" . $idNode;

        $values = array(
            'id_node' => $idNode,
            'info' => $info,
            'channels' => $channels,
            'languages' => $languages,
            'jsonUrl' => $jsonUrl
        );
        $this->render($values, 'index', 'default-3.0.tpl');
    }

    function getDependencies()
    {
        $idNode = (int)$this->request->getParam("nodeid");

        $classes = array(new RelStrdocAsset(), new RelStrdocCss(), new RelStrdocNode(),
            new RelStrdocScript(), new RelStrdocStructure(), new RelStrdocTemplate(),
            new RelStrdocXimlet());

        $relnodemetadata = new RelNodeMetadata();

        $depMasterList = array();
        foreach ($classes as $c) {
            $res = $c->find("target", "source=" . $idNode, null, MONO);
            if (count($res) > 0) {
                $depMasterList = array_merge($depMasterList, $res);
            }
        }
        $res = $relnodemetadata->find("IdMetadata", "IdNode=" . $idNode, null, MONO);
        if (count($res) > 0) {
            $depMasterList = array_merge($depMasterList, $res);
        }

        $depDependentList = array();
        foreach ($classes as $c) {
            $res = $c->find("source", "target=" . $idNode, null, MONO);
            if (count($res) > 0) {
                $depDependentList = array_merge($depDependentList, $res);
            }
        }
        $res = $relnodemetadata->find("IdNode", "IdMetadata=" . $idNode, null, MONO);
        if (count($res) > 0) {
            $depDependentList = array_merge($depDependentList, $res);
        }

        $depMasterNameList = array();
        foreach ($depMasterList as $i) {
            $id = (int)$i;
            $node = new Node($i);
            $depMasterNameList[] = $node->GetNodeName();
        }

        $objs = array();

        $node = new Node($idNode);
        $centerName = $node->GetNodeName();
        $centerType = $node->GetTypeName();


        $obj = array(
            "name" => $centerName,
            "type" => $centerType,
            "depends" => $depMasterNameList,
            "position" => "center"
        );
        $objs[$centerName] = $obj;
        foreach ($depMasterList as $d) {
            $i = (int)$d;
            $node = new Node($i);
            if ($node->GetId() && $node->GetNodeType() != 5085) {
                $obj["name"] = $node->GetNodeName();
                $obj["type"] = $node->GetTypeName();
                $obj["depends"] = array();
                $obj["position"] = "master";
                $objs[$node->GetNodeName()] = $obj;
                $obj = null;
            }
        }


        foreach ($depDependentList as $d) {
            $i = (int)$d;
            $node = new Node($i);
            if ($node->GetId() && $node->GetNodeType() != 5085) {
                $obj = array(
                    "name" => $node->GetNodeName(),
                    "type" => $node->GetTypeName(),
                    "depends" => array($centerName),
                    "position" => "child"
                );
                $objs[$node->GetNodeName()] = $obj;
                $obj = null;
            }
        }

        $obj = array();
        $data = array();
        $errors = array();

        foreach ($objs as $obj) {
            $data[$obj['name']] = $obj;
        }

        foreach ($data as &$obj) {
            $obj['dependedOnBy'] = array();
        }
        foreach ($data as &$obj) {
            foreach ($obj['depends'] as $name) {
                if ($data[$name]) {
                    $data[$name]['dependedOnBy'][] = $obj['name'];
                } else {
                    $errors[] = "Unrecognized dependency: '$obj[name]' depends on '$name'";
                }
            }
        }
        unset($obj);

        $this->sendJSON(array(
            'data' => $data,
            'errors' => $errors
        ));
    }

}

?>