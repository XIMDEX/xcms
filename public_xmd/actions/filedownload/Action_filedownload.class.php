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
 *  @author Ximdex DevTeam <dev@ximdex.com>
 *  @version $Revision$
 */

use Ximdex\Models\Node;
use Ximdex\MVC\ActionAbstract;

class Action_filedownload extends ActionAbstract
{
    public function index()
    {
        $idNode = (int) $this->request->getParam('nodeid');
        $node = new Node($idNode);
        if (! $node->getID()) {
            return;
        }
        $version = $this->request->getParam('version');
        $subversion = $this->request->getParam('subversion');
        $values = [
            'node_name' => $node->get('Name'),
            'nodeTypeID' => $node->nodeType->getID(),
            'node_Type' => $node->nodeType->getName(),
            'id_node' => $node->get('IdNode'),
            'version' => $version,
            'subversion' => $subversion
        ];
        $this->addJs('/actions/filedownload/resources/js/index.js');
        $this->render($values, '', 'default-3.0.tpl');
    }

    public function downloadFile()
    {
        if ($this->request->getParam('nodeid')) {
            $idNode = (int) $this->request->getParam('nodeid');
            $version = $this->request->getParam('version');
            $subversion = $this->request->getParam('subversion');
            $this->echoNode($idNode, $version, $subversion);
        }
    }

    private function echoNode(int $idNode, int $version = null, int $subversion = null)
    {
        $fileNode = new Node($idNode);
        if (! $fileNode->getID()) {
            return;
        }
        $fileName = $fileNode->get('Name');
        $gmDate =  gmdate('D, d M Y H:i:s');
        $fileContent = $fileNode->getContent($version, $subversion);

        /// Expiration headers
        $this->response->set('Expires', 'Mon, 26 Jul 1997 05:00:00 GMT');
        $this->response->set('Last-Modified', $gmDate . ' GMT');
        $this->response->set('Cache-Control','no-store, no-cache, must-revalidate');
        $this->response->set('Pragma', 'no-cache');
        $this->response->set('ETag', md5($idNode . $gmDate));
        $this->response->set('Content-transfer-encoding', 'binary');
        $this->response->set('Content-type', 'octet/stream');
        $this->response->set('Content-Disposition', 'attachment; filename=' . $fileName);
        $this->response->set('Content-Length', strlen(strval($fileContent)));
        $this->response->sendHeaders();
        echo $fileContent;
    }
}
