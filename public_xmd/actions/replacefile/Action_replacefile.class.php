<?php

/**
 *  \details &copy; 2018  Open Ximdex Evolution SL [http://www.ximdex.org]
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

use Ximdex\MVC\ActionAbstract;
use Ximdex\Models\Node;
use Ximdex\Runtime\App;
use Ximdex\Utils\FsUtils;
use Ximdex\IO\BaseIO;

class Action_replacefile extends ActionAbstract
{
    private $tmpFolder;
    
    public function __construct($_render = null)
    {
        $this->tmpFolder = XIMDEX_ROOT_PATH . App::getValue('TempRoot') . '/' . App::getValue('UploadsFolder');
        parent::__construct($_render);
    }
    
    public function index()
    {
        $idNode = (int) $this->request->getParam("nodeid");
        $actionID = (int) $this->request->getParam("actionid");
        $type = $this->request->getParam('type');
        $node = new Node($idNode);
        if (!$node->GetID()) {
            $this->messages->add(_('Cannot load a node with ID ' . $idNode), MSG_TYPE_ERROR);
        }
        
        // Checking permits
        $userid = \Ximdex\Runtime\Session::get('userID');
        if (empty($userid)) {
            $this->messages->add(_('It is necessary to be an active user to upload files'), MSG_TYPE_ERROR);
        }
        $user = new \Ximdex\Models\User($userid);
        if (!$user->canWrite(array('node_id' => $idNode)) ) {
            $this->messages->add(_('Files cannot be added because of lack of permits'), MSG_TYPE_ERROR);
        }
        $this->addJs('/actions/replacefile/resources/js/index.js');
        $values = array(
            'messages' => $this->messages->messages,
            'id_node' => $idNode,
            'go_method' => 'replace',
            'type' => $type,
            'name' => $node->GetNodeName(),
            'node_Type' => $node->nodeType->GetName(),
            'maxSize' => ini_get('upload_max_filesize')
        );
        $this->render($values, 'index', 'default-3.0.tpl');
    }
    
    public function replace() : void
    {
        $idNode = $this->request->getParam('nodeid');
        $node = new Node($idNode);
        if (!$node->GetID()) {
            $this->messages->add(_('Cannot load a node with ID ' . $idNode), MSG_TYPE_ERROR);
            $this->sendResponse();
        }
        $fileName = isset($_FILES['upload']) && isset($_FILES['upload']['name']) ? $_FILES['upload']['name'] : NULL;
        $filePath = isset($_FILES['upload']) && isset($_FILES['upload']['tmp_name']) ? $_FILES['upload']['tmp_name'] : NULL;
        if (!is_file($filePath) or !$fileName) {
            $this->messages->add(_('File could not be uploaded, contact with your administrator'), MSG_TYPE_ERROR);
            $this->sendResponse();
        }
        
        // Check allowed extensions for the current nodetype
        $parent = new Node($node->GetParent());
        $allowedExtensions = $parent->nodeType->getAllowedExtensions(true);
        if (!in_array('*', $allowedExtensions)) {
            $info = pathInfo($fileName);
            $extension = strtolower($info['extension']);
            if (!$extension) {
                $this->messages->add(_('Cannot replace a file without extension'), MSG_TYPE_ERROR);
                $this->sendResponse();
            }
            if (!in_array($extension, $allowedExtensions)) {
                $this->messages->add(_('File to replace must be the same type (' . $node->nodeType->GetDescription() . ')'), MSG_TYPE_ERROR);
                $this->sendResponse();
            }
        }
        $tmpFile = FsUtils::getUniqueFile($this->tmpFolder);
        if (!move_uploaded_file($filePath, $this->tmpFolder . $tmpFile)) {
            $this->messages->add('Cannot create the new file to replace', MSG_TYPE_ERROR);
            $this->sendResponse();
        }
        if ($tmpFile) {
            $res = $this->update($idNode, $tmpFile, $node->nodeType->GetName(), $fileName);
            if ($res) {
                $this->reloadNode($node->get('IdParent'));
            }
        }
        $this->sendResponse();
    }

    private function update(int $idNode, string $tmpFile, string $nodeTypeName, string $nodeName) : bool
    {
    	$data = array(
    	    'NAME' => $nodeName,
			'NODETYPENAME' => $nodeTypeName,
			'ID' => $idNode,
			'CHILDRENS' => array(array('NODETYPENAME' => 'PATH', 'SRC' => $this->tmpFolder . $tmpFile)));
		$baseIO = new BaseIO();
		$result = $baseIO->update($data);
		unlink($this->tmpFolder . $tmpFile);
		if ($result > 0) {
			$this->messages->add(_('Document has been successfully replaced'), MSG_TYPE_NOTICE);
			return true;
		}
	    $this->messages->add(_('An unexpected error has occurred while replacing the document'), MSG_TYPE_ERROR);
        $this->messages->mergeMessages($baseIO->messages);
		return false;
    }
    
    private function sendResponse() : void
    {
        $values = array('messages' => $this->messages->messages);
        $this->sendJSON($values);
    }
}