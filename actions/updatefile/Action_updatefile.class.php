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




ModulesManager::file('/inc/io/BaseIOInferer.class.php');

class Action_updatefile extends ActionAbstract {
   // Main method: shows initial form
    function index () {

		$idNode = $this->request->getParam('nodeid');
		$values = array(
			'id_node' => $idNode,
			'go_method' => 'updatefile'
		);
		$this->render($values, NULL, 'default-3.0.tpl');
    }

    function updatefile() {
		$idNode = Request::request('nodeid');
		$node = new Node($idNode);
		if (!($node->get('IdNode') > 0)) {
			$this->messages->add(_('The node you are trying to update does not exist'), MSG_TYPE_ERROR);
		}

		$filePath = isset($_FILES['upload']) && isset($_FILES['upload']['tmp_name']) ? $_FILES['upload']['tmp_name'] : NULL;
		$fileName = isset($_FILES['upload']) && isset($_FILES['upload']['name']) ? $_FILES['upload']['name'] : NULL;

		if (!is_file($filePath)) {
			$this->messages->add(_('File could not be uploaded, contact with your administrator'), MSG_TYPE_ERROR);
		}

		$baseIoInferer = new BaseIOInferer();
		$idParent = $node->get('IdParent');
		$types = $baseIoInferer->infereType('FILE', $idParent, $filePath);
		if (!count($types) > 0) {
			$this->messages->add(_('File time could not be estimated, contact with your administrator'), MSG_TYPE_ERROR);
		}

		if ($this->messages->count(MSG_TYPE_ERROR) > 0) {
			$values = array('messages' => $this->messages->messages);
			$this->render($values, NULL, 'messages.tpl');
			return false;
		}

		$nodeTypeName = $types['NODETYPENAME'];

		$name = preg_match('/.*\/(.*)$/', $filePath, $matches);

		$data = array(
				'ID' => $idNode,
                'NODETYPENAME' => $nodeTypeName,
                'NAME' => $fileName,
                'CHILDRENS' => array (
                                array ('NODETYPENAME' => 'PATH', 'SRC' => $filePath)
                        )
                );


		$baseIO = new baseIO();
		$result = $baseIO->update($data);

		if ($result > 0) {
			$baseIO->messages->add(sprintf(_('File %s has been successfully updated'), $fileName), MSG_TYPE_NOTICE);
		} else {
			$baseIO->messages->add(_('Check that any file with same name does not exist'), MSG_TYPE_WARNING);
			$baseIO->messages->add(_('Check that path and file name are corrects'), MSG_TYPE_WARNING);
		}

		$this->reloadNode($idParent);

		$values = array( 'messages' => $baseIO->messages->messages );

		$this->render($values, NULL, 'messages.tpl');
    }
}
?>
