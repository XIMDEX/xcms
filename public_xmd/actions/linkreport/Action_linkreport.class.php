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

use Ximdex\Models\Link;
use Ximdex\Models\Node;
use Ximdex\Models\NodeType;
use Ximdex\Models\Role;
use Ximdex\Models\User;
use Ximdex\MVC\ActionAbstract;


class Action_linkreport extends ActionAbstract
{
    function index()
    {
        $idNode = $this->request->getParam("nodeid");
        $actionID = $this->request->getParam("actionid");
        $node = new Node($idNode);

        $this->addCss('/actions/linkreport/resources/css/linkreport.css');

        $values = array(
            'id_node' => $idNode,
            'id_action' => $actionID,
            'node_name' => $node->GetNodeName(),
            'field' => $this->request->getParam("field"),
            'criteria' => $this->request->getParam("criteria"),
            'stringsearch' => $this->request->getParam("stringsearch"),
            'rec' => ($this->request->getParam('rec') != 'on' ? NULL : 1),
            'node_Type' => $node->nodeType->GetName(),
            'go_method' => 'get_links'
        );
        $this->render($values, NULL, 'default-3.0.tpl');
    }

    function get_links()
    {
        $idNode = $this->request->getParam("nodeid");
        $field = $this->request->getParam("field");
        $criteria = $this->request->getParam("criteria");
        $stringsearch = $this->request->getParam("stringsearch");
        $rec = $this->request->getParam('rec');

        $criteria = $criteria == 'undefined' ? NULL : $criteria;
        $field = $field == 'undefined' ? NULL : $field;

        $userID = \Ximdex\Runtime\Session::get("userID");
        $node = new Node($idNode);

        // get link folders
        $folderList = empty($rec) ? array($idNode) : self::folderNodes($idNode);

        // get links
        $ximLinks = array();

        $nodeType = new NodeType();
        $nodeType->setByName('Link');
        $idNodeType = $nodeType->get('IdNodeType');

        $nodesTableCondition = 'IdNodeType = %s';

        $linksTableCondition = "";
        $linksCond = "";


        if (!empty($stringsearch)) {
            if ($stringsearch != "*") {
                switch ($criteria) {
                    case "contains":
                        $linksTableCondition = " like '%%$stringsearch%%'";
                        break;
                    case "nocontains":
                        $linksTableCondition = " not like '%%$stringsearch%%'";
                        break;
                    case "equal":
                        $linksTableCondition = " = '$stringsearch'";
                        break;
                    case "nonequal":
                        $linksTableCondition = " != '$stringsearch'";
                        break;
                    case "startswith":
                        $linksTableCondition = " like '$stringsearch%%'";
                        break;
                    case "endswith":
                        $linksTableCondition = " like '%%$stringsearch'";
                        break;
                    default:
                        $linksTableCondition = " = '$stringsearch'";
                        break;
                }
                if ($field != 'all') {
                    $linksCond = " AND $field" . $linksTableCondition;
                } else {
                    $linksCond = " AND (Name $linksTableCondition OR Description $linksTableCondition)";
                }
            }
            $nodesTableCondition .= $field != 'Url' ? $linksCond : '';
        }

        $links = array();
        foreach ($folderList as $idFolder) {
            $findInNodes = $node->find('IdNode', $nodesTableCondition . ' AND IdParent = %s', array($idNodeType, $idFolder), MONO);

            $link = new Link();
            if ($field == 'Url') {
                $finds = $link->find('IdLink', 'IdLink in (' . implode(',', $findInNodes) . ') ' . $linksCond, NULL, MONO);
            } elseif ($field == 'all') {
                $finds = $link->find('IdLink', 'Url ' . $linksTableCondition, NULL, MONO);
            }

            if (!empty($findInNodes) && count($findInNodes) > 0) {
                if ($field == 'Url') {
                    $links = $finds;
                } else {
                    $links = array_merge($links, $findInNodes);
                }
            }
            if (!empty($finds) && count($finds) > 0) {
                $links = array_merge($links, $finds);
            }
        }
        $links = array_unique($links);
        $this->addJs('/actions/linkreport/resources/js/index.js');

        $records = count($links);
        if ($records > 0) {
            foreach ($links as $idLink) {
                $link = new Link($idLink);
                $state = $link->get('ErrorString');
                $type = "email";
                preg_match('/^http(s)?:\/\//', $link->get('Url'), $res);
                if (count($res) > 0) {
                    $type = "web";
                }

                $user = new User($userID);
                $arr_roles = $user->GetRolesOnNode($idNode);
                $n_roles = count($arr_roles);
                $r = 0;
                $has = false;

                while (($r < $n_roles) && !$has) {
                    $role = new Role($arr_roles[$r]);
                    $has = $role->HasAction(6073);
                    $r++;
                }

                $linkNode = new Node($idLink);

                $ximLinks[] = array('nodeid' => $idLink, 'name' => $linkNode->get('Name'),
                    'modifiable' => $has, 'desc' => $linkNode->get('Description'),
                    'url' => $link->get('Url'), 'status' => $state, 'type' => $type, 'lastcheck' => $link->get('CheckTime'));
            }//end foreach $pages
        }
        $values = array(
            'links' => $ximLinks,
            'totalLinks' => count($ximLinks),
        );
        $this->render($values, 'searchresult', 'default-3.0.tpl');
    }

    //for recursive search
    private function folderNodes($idNode)
    {
        $node = new Node($idNode);
        $childList = $node->GetChildren();
        $nodeList = array($idNode);

        if (count($childList) > 0) {
            foreach ($childList as $idChild) {
                $childNode = new Node($idChild);
                if ($childNode->nodeType->get('Name') == "LinkFolder") {
                    $nodeList = array_merge($nodeList, self::folderNodes($idChild));
                }
            }
        }
        return $nodeList;
    }

    public function checkLink()
    {
        $linkUrl = $this->request->getParam('linkurl');
        $nodeid = $this->request->getParam('nodeid');

        $link = new Link($nodeid);
        $st = Link::LINK_FAIL;
        if ($link->get("IdLink")) {
            $st = Link::LINK_WAITING;
            $link->set('ErrorString', $st);
            $link->set('CheckTime', time());
            $link->update();
        }
        $cmd = 'php ' . XIMDEX_ROOT_PATH . '/bootstrap.php '.APP_ROOT_PATH.'/actions/linkreport/resources/scripts/links_checker.php ' . $nodeid;
        $pid = shell_exec(sprintf("%s > /dev/null & echo $!", $cmd));
        echo json_encode(array('state' => $st, 'date' => date('d/m/Y H:i', time())));
        die();

    }

    public function readLinkState()
    {
        $nodeid = $this->request->getParam('nodeid');
        $link = new Link($nodeid);
        if ($link->get("IdLink")) {
            $st = $link->get("ErrorString");
            $time = $link->get("CheckTime");
        }
        echo json_encode(array('state' => $st, 'date' => date('d/m/Y H:i', $time)));
        die();

    }
}