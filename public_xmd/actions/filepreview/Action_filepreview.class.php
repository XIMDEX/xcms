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

use Ximdex\Models\Node;
use Ximdex\Models\Version;
use Ximdex\MVC\ActionAbstract;
use Ximdex\Runtime\App;
use Ximdex\Runtime\DataFactory;
use Ximdex\Models\NodeType;
use Ximdex\NodeTypes\NodeTypeConstants;

class Action_filepreview extends ActionAbstract
{
    /**
     * Main method: shows initial form for a single file
     */
    public function index()
    {
        $this->response->set('Cache-Control', array('no-store, no-cache, must-revalidate', 'post-check=0, pre-check=0'));
        $this->response->set('Pragma', 'no-cache');
        $idNode = (int) $this->request->getParam('nodeid');
        $version = $this->request->getParam('version');
        $subversion = $this->request->getParam('subversion');
        if (is_numeric($version) && is_numeric($subversion)) {
            $dataFactory = new DataFactory($idNode);
            $selectedVersion = $dataFactory->getVersionId($version, $subversion);
        } else {
            $dataFactory = new DataFactory($idNode);
            $selectedVersion = $dataFactory->getLastVersionId();
        }
        if (! $selectedVersion) {
            $this->messages->add(_('There is no version selected'), MSG_TYPE_ERROR);
        }
        $node = new Node($idNode);
        $nodetype = new NodeType($node->getNodeType());
        $values = array(
            'messages' => $this->messages->messages,
            'id_node' => $idNode,
            'path' => App::getValue('UrlRoot') . '/?action=rendernode&nodeid=' . $node->getID() . '&version=' . $version 
                . '&subversion=' . $subversion,
            'Name' => $node->get('Name'),
            'nodeTypeID' => $node->nodeType->getID(),
            'node_Type' => $node->nodeType->getName(),
            'type' => $nodetype->getName()
        );
        $this->render($values, null, 'default-3.0.tpl');
    }

    /**
     * Show all images contained in a node
     */
    public function showAll()
    {
        $idNode = (int) $this->request->getParam('nodeid');
        $node = new Node($idNode);
        if (! $node->get('IdNode') || ($node->get('IdNodeType') != NodeTypeConstants::IMAGES_ROOT_FOLDER 
                && $node->get('IdNodeType') != NodeTypeConstants::IMAGES_FOLDER)) {
            $message = _('Forbidden access');
            $this->render(array('mesg' => $message), null, 'default-3.0.tpl');
            return;
        }
        $parentID = $node->getParent();
        $parentNode = new Node($parentID);

        // Gets all child nodes of type image (nodetype IMAGE_FILE) of this node
        $nodes = $node->getChildren(NodeTypeConstants::IMAGE_FILE);
        $imageNodes = array();
        $nodePath = App::getValue('UrlRoot') . App::getValue('NodeRoot');
        if (count($nodes) > 0) {
            foreach ($nodes as $idNode) {
                $n = new Node($idNode);
                if (! $n->get('IdNode')) {
                    continue;
                }
                $dataFactory = new DataFactory($idNode);
                $selectedVersion = $dataFactory->getLastVersionId();
                $version = new Version($selectedVersion);
                $hash = $version->get('File');
                $filepath = XIMDEX_ROOT_PATH . App::getValue('FileRoot') . DIRECTORY_SEPARATOR . $hash;
                $imageInfo = @getimagesize($filepath);
                if (! is_array($imageInfo)) {
                    continue;
                }
                $width = $imageInfo[0];
                $height = $imageInfo[1];
                $mime = $imageInfo['mime'];
                array_push($imageNodes, array(
                    'name' => $n->getNodeName(),
                    'original_path' => $nodePath . str_replace('/Ximdex/Projects', '', $n->getPath()),
                    'src' => App::getValue('UrlRoot') . '/?action=rendernode&nodeid=' . $idNode,
                    'width' => $width,
                    'height' => $height,
                    'mime' => $mime,
                    'dimensions' => $width . ' x ' . $height,
                    'size' => $this->humanReadableFilesize(filesize($filepath)),
                    'idnode' => $n->get('IdNode')
                ));
            }
            $this->addCss('/actions/filepreview/resources/css/showAll.css');
            $this->addJs('/actions/filepreview/resources/js/showAll.js');
            $this->addJs('/actions/filepreview/resources/js/gallerizer.js');
            $values = array(
                'imageNodes' => $imageNodes,
                'serverName' => $parentNode->get('Name'),
                'folderName' => $node->get('Name'),
                'nodeTypeID' => $node->nodeType->getID(),
                'node_Type' => $node->nodeType->getName()
            );
            $this->render($values, null, 'default-3.0.tpl');
        } else {
            $message = _('No images found in this folder');
            $this->render(array('mesg' => $message), null, 'default-3.0.tpl');
        }
    }

    private function humanReadableFilesize(int $size)
    {
        $mod = 1024;
        $units = explode(' ', 'B KB MB GB TB PB');
        for ($i = 0; $size > $mod; $i ++) {
            $size /= $mod;
        }
        return round($size, 2) . ' ' . $units[$i];
    }
}
