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
 *  @author Ximdex DevTeam <dev@ximdex.com>
 *  @version $Revision$
 */


use Ximdex\Models\Node;
use Ximdex\Models\NodeAllowedContent;
use Ximdex\Models\NodeType;
use Ximdex\MVC\ActionAbstract;
use Ximdex\Runtime\App;
use Ximdex\Utils\FsUtils;



class Action_fileupload extends ActionAbstract {

    function index() {
		$idNode = $this->request->getParam('nodeid');
		$type = $this->request->getParam("type");

		$node = new Node($idNode);
		if (!($node->get('IdNode') > 0)) {
			$this->messages->add(_('The folder where you want to upload the files does not exist'), MSG_TYPE_ERROR);
			$this->renderMessages();
		}

		if ($node->nodeType->get('IsFolder')) {
			$nodeTypeLookUp = $node->get('IdNode');
			$lookUpType = 'FOLDER';
		} else {
			$nodeTypeLookUp = $node->get('IdParent');
			$lookUpType = 'FILE';
		}

		/** ********* VERIFYING PERMITS **************************** */
		$baseIoInferer = new \Ximdex\IO\BaseIOInferer();
		$typeInfo = $baseIoInferer->infereType($lookUpType, $nodeTypeLookUp);
		$nodeTypeName = $typeInfo["NODETYPENAME"];

		if(empty($nodeTypeName)) {
			$this->messages->add(_('A node type allowed in this folder could not be estimated, contact with your administrator.'), MSG_TYPE_ERROR);
		}

		$values = array(
			'messages' => $this->messages->messages,
			'id_node' => $idNode,
			'go_method' => 'fileupload',
			'type' => $type,
			'name' => $node->get('Name')
		);

		$template = $type == 'pvd' ? 'index_pvd.tpl' : 'index.tpl';

		$this->render($values, $template, 'default-3.0.tpl');
    }

    function fileupload() {
		$params = $this->request->getParam("type");
		$idNode = $this->request->getParam('nodeid');

		//Pvd has it own upload manager system. Then if it not a pvd we use the generic

        $messages = $this->_uploadCommonFile($idNode);

		if ($messages === false) {
			return; // makes a redirect;
		}

		if (!($messages->count(MSG_TYPE_ERROR) > 0)) {
			$node = new Node($idNode);
			if ($node->nodeType->get('IsFolder')) {
				$this->reloadNode($node->get('IdNode'));
			} else {
				$this->reloadNode($node->get('IdParent'));
			}
		}
		$values = array('messages' => $messages->messages, 'goback' => true);
		$this->render($values, NULL, 'messages.tpl');
		die();
    }

    private function _uploadCommonFile($idNode) {
   		$filePath = isset($_FILES['upload']) && isset($_FILES['upload']['tmp_name']) ? $_FILES['upload']['tmp_name'] : NULL;
		$fileName = isset($_FILES['upload']) && isset($_FILES['upload']['name']) ? $_FILES['upload']['name'] : NULL;

		$type = $this->request->getParam("type");

		if (!is_file($filePath)) {
			$this->messages->add(_('File could not be uploaded, contact with your administrator'), MSG_TYPE_ERROR);
		}

		//Searching parent node type (folder)
		$baseIoInferer = new \Ximdex\IO\BaseIOInferer();
		//Searching node type
		$nodeTypeName = !empty($type) ? $baseIoInferer->infereFileType($_FILES['upload'], $type)
			: $baseIoInferer->infereFileType($_FILES['upload']);

		if (!$nodeTypeName) {
			$this->messages->add(_('File type could not be estimated or it is an invalid type.'), MSG_TYPE_ERROR);
			$node = new Node($idNode);
			$parentNodeType = $node->get('IdNodeType');

			$nac = new NodeAllowedContent();
			$allowedNodeTypes = $nac->find('NodeType', 'IdNodeType = %s', array($parentNodeType), MONO);
			$inSearch = implode("', '", $allowedNodeTypes);
			if (!empty($inSearch)) {
				$inSearch = sprintf("'%s'", $inSearch);
			}

			$rntmt = new \Ximdex\Models\RelNodeTypeMimeType();
			$types = $rntmt->find('distinct extension', 'idNodeType in (%s)', array($inSearch), MONO, false);
			if (empty($types)) {
				$this->messages->add(_('No type of file which can be inserted in this folder has been found, contact with your administrator'), MSG_TYPE_ERROR);
			} else {
				$allowedExtensions = array();
				foreach($types as $type) {
					$expTypes = explode(';', $type);
					foreach($expTypes as $expType) {
						if (empty($expType)) {
							continue;
						}
						if (!in_array($expType, $allowedExtensions)) {
							$allowedExtensions[] = $expType;
						}
					}
				}
				$this->messages->add(sprintf(_('List of allowed extension files is as follows: %s'), implode(', ', $allowedExtensions)), MSG_TYPE_ERROR);
			}
		}

		if ($this->messages->count(MSG_TYPE_ERROR) > 0) {
			return $this->messages;
		}

		//File does not exist previously. It inserts it
		$node = new Node($idNode);
		if ($node->nodeType->get('IsFolder')) {
			$data = array(
					'NODETYPENAME' => $nodeTypeName,
					'NAME' => $fileName,
					'PARENTID' => $idNode,
					'CHILDRENS' => array (
									array ('NODETYPENAME' => 'PATH', 'SRC' => $filePath)
							)
					);
			$baseIO = new \Ximdex\IO\BaseIO();
			$result = $baseIO->build($data);
			if ($result > 0) {
				$baseIO->messages->add(sprintf(_('File %s has been successfully inserted'), $fileName), MSG_TYPE_NOTICE);
				if ($node->nodeType->get('Name') == 'TemplatesRootFolder') $node->class->updateChooseTemplates();
			} else {
				$baseIO->messages->add(_('Please, Check if there is any file with same name'), MSG_TYPE_WARNING);
				$baseIO->messages->add(_('Please, check if path and name are correct'), MSG_TYPE_WARNING);
			}
			return $baseIO->messages;
		}

		// File alredy exists, we store it temporaly and ask for confirmation
		$tmpFolder = XIMDEX_ROOT_PATH . '/data/tmp/uploaded_files/';
		$tmpFile = FsUtils::getUniqueFile($tmpFolder);
		move_uploaded_file($filePath, $tmpFolder . $tmpFile);

		$this->redirectTo('confirm', NULL,
			array('tmp_file' => $tmpFile,
					'tmp_name' => $fileName,
					'id_node' => $idNode,
					'node_type_name' => $nodeTypeName));
		return false;
    }

    function confirm() {
    	$idNode = $this->request->getParam('id_node');
    	$tmpFile = $this->request->getParam('tmp_file');
    	$tmpName = $this->request->getParam('tmp_name');
    	$nodeTypeName = $this->request->getParam('node_type_name');

    	$node = new Node($idNode);
	if(strcmp($nodeTypeName,"ImageFile")==0){
    		$this->messages->add(sprintf(_('The Image file %s is going to be replaced with %s, but the name in the system will not change.'),
    		$node->get('Name'), $tmpName), MSG_TYPE_WARNING);
	}
	else{
    		$this->messages->add(sprintf(_('File content %s is going to be replaced with %s'),
    		$node->get('Name'), $tmpName), MSG_TYPE_WARNING);
	}

    	$queryManager = \Ximdex\Runtime\App::get('\Ximdex\Utils\QueryManager');
    	$action = $queryManager->getPage() . $queryManager->buildWith(array('method' => 'update'));
    	$values = array('messages' => $this->messages->messages,
    					'action' => $action,
    					'tmp_file' => $tmpFile,
    					'tmp_name' => $tmpName,
    					'id_node' => $idNode,
    					'node_type_name' => $nodeTypeName);
    	$this->render($values, null , 'default-3.0.tpl');
    }

    function update() {
    	$idNode = $this->request->getParam('id_node');
    	$tmpFile = $this->request->getParam('tmp_file');
    	$nodeTypeName = $this->request->getParam('node_type_name');
    	$tmpFolder = XIMDEX_ROOT_PATH . '/data/tmp/uploaded_files/';
    	$data = array(
					'NODETYPENAME' => $nodeTypeName,
					'ID' => $idNode,
					'CHILDRENS' => array (
									array ('NODETYPENAME' => 'PATH', 'SRC' => $tmpFolder .$tmpFile)
							)
					);

			$baseIO = new \Ximdex\IO\BaseIO();
			$result = $baseIO->update($data);
			if ($result > 0) {
				$this->messages->add(_('Document has been successfully replaced'), MSG_TYPE_NOTICE);
			} else {
				$this->messages->add(_('An unexpected error has occurred while replacing the document'), MSG_TYPE_ERROR);
				$this->messages->mergeMessages($baseIO->messages);
			}
		$this->render(array('messages' => $this->messages->messages));
    }



}