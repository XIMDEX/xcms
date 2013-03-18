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



class Action_htmleditor extends ActionAbstract {
	// Main method: shows initial form
	function index() {
		$idNode = $this->request->getParam('nodeid');
		$node = new Node($idNode);
		if (!($node->get('IdNode') > 0)) {
			$this->messages->add(_('El documento solicitado no existe'), MSG_TYPE_ERROR);
			$this->renderMessages();
			return;
		}

		//$this->addJs(Extensions::JQUERY);
		$this->addJs('/extensions//ckeditor/ckeditor.js');
		$this->addJs('/actions/htmleditor/resources/js/htmleditor.js');


		$values = array(
			'html' => $node->GetContent(),
			'go_method' => 'save',
			'id_editor' => $idNode.uniqid()
		);
		$this->render($values, NULL, 'default-3.0.tpl');
	}

	function save() {
		$idNode = $this->request->getParam('nodeid');
		$content = $this->request->getParam('htmleditor');

		$node = new Node($idNode);
		if (!($node->get('IdNode') > 0)) {
			$this->messages->add(_('Requested document does not exist'), MSG_TYPE_ERROR);
			$this->renderMessages();
			return;
		}

		if ($node->SetContent($content, true));
		$this->messages->add(_('Document has been successfully updated'), MSG_TYPE_NOTICE);
		$this->renderMessages();
	}
}
?>
