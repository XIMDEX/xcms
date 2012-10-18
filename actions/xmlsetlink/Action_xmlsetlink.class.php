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



ModulesManager::file('/inc/model/RelPvds.class.php');

class Action_xmlsetlink extends ActionAbstract {

   //Main method: shows the initial form
    function index () {

		$idNode = $this->request->getParam('nodeid');
		$node = new Node($idNode);

		$sharewf = $node->get('SharedWorkflow');
		$sharewf = empty($sharewf) ? 0 : 1;

		if (!($node->get('IdNode') > 0)) {
			$this->messages->add(_('Node could not be found'), MSG_TYPE_ERROR);
			$this->render(array('messages' => $this->messages->message), NULL, 'messages.tpl');
			return ;
		}

		$structuredDocument = new StructuredDocument($idNode);
		$idTarget = $structuredDocument->get('TargetLink');

		$targetNode = new Node($idTarget);
		$targetNodePath = ($targetNode->get('IdNode') > 0) ? $targetNode->GetPath() : '';


		$type = $node->get('IdNodeType');
		$this->addJs('/actions/xmlsetlink/resources/js/xmlsetlink.js');
		$this->addJs('/actions/xmlsetlink/resources/js/setinfo.js');

		$values = array(
			'id_node' => $idNode,
			'id_target' => $idTarget,
			'type' => $type,
			'go_method' => 'setlink',
			'target_node_path' => $targetNodePath,
			'sharewf' => $sharewf
		);

		$this->render($values, NULL, 'default-3.0.tpl');
    }

	function setlink() {

		$idNode = $this->request->getParam('nodeid');
		$idTarget = $this->request->getParam('targetfield');

		$structuredDocument = new StructuredDocument($idNode);
		$idNodeTemplate = $structuredDocument->get('IdTemplate');

		$targetStructuredDocument = new StructuredDocument($idTarget);
		$idTargetTemplate = $targetStructuredDocument->get('IdTemplate');

		$rel = new RelPvds();

		if (($idTarget > 0)
			&& (!$rel->isCompatible($idNodeTemplate, $idTargetTemplate))) {
			$this->messages->add(_('The document to link has a different view template. The link cannot be created'), MSG_TYPE_ERROR);
			$this->render(array('messages' => $this->messages->messages), NULL, 'messages.tpl');
			return false;
		}

		$node = new Node($idNode);

		$node->ClearWorkFlowMaster();
		if ($this->request->getParam('sharewf')) {
			$node->SetWorkFlowMaster($idTarget);
		}

		$structuredDocument->ClearSymLink();
		$structuredDocument->SetSymLink($idTarget);
		$this->messages->add(_('The link has been modified successfully'), MSG_TYPE_NOTICE);

		$messages = new Messages();
		$messages->mergeMessages($node->messages);
		$messages->mergeMessages($structuredDocument->messages);

		$url = sprintf(
			Config::getValue('UrlRoot') . '/xmd/loadaction.php?method=includeDinamicJs&%s&js_file=reloadNode',
			'xparams[reload_node_id]='.$node->get('IdParent')
		);
		$jsFiles = array($url);


		$this->render(
			array(
				'messages' => $this->messages->messages,
				'js_files' => $jsFiles,
				'on_load_functions' => sprintf("reloadNode(%s);", $node->get('IdParent')),
				'goback' => true
			),
			NULL, 'messages.tpl');
	}

/*
*	Removes the symbolic link of document
*/

	function unlink() {

		$idNode = $this->request->getParam('nodeid');
		$idTarget = $this->request->getParam('targetfield');

		$structuredDocument = new StructuredDocument($idNode);
		$targetStructuredDocument = new StructuredDocument($idTarget);

		$content = ($this->request->getParam('keepcontent') || $this->request->getParam('delete_method') == 'unlink')
			? $structuredDocument->GetContent() : $this->request->getParam('editor');

		$structuredDocument->SetContent($content);

		$node = new Node($idNode);
		$idParent = $node->get('IdParent');
		$node->ClearWorkFlowMaster();
		$structuredDocument->ClearSymLink();

		$this->messages->add(_('The link has been deleted successfully'), MSG_TYPE_NOTICE);

		$values = array('messages' => $this->messages->messages);

		$this->reloadNode($idParent);

		$this->render($values,	NULL, 'messages.tpl');

	}


/*
*	Shows the form with the document content translated by Google Translate
*	@param
*	@return
*/

	function show_translation() {

		$idNode = $this->request->getParam('nodeid');
		$values = array('go_method' => 'unlink');

		$this->addJs('/actions/xmlsetlink/resources/js/show_translation.js');
		$this->render($values, 'show_translation', 'default-3.0.tpl');
	}

/*
*	Calls Google Translate service
*	@param
*	@return string
*/

	function translate() {
		$idNode = $this->request->getParam('nodeid');

		$strDoc = new StructuredDocument($idNode);
		$content = $strDoc->GetContent();
		$langId = $strDoc->get('IdLanguage');
		$masterId = $strDoc->get('TargetLink');

		$lang = new Language($langId);
		$langTo = $lang->get('IsoName');

		$masterDoc = new StructuredDocument($masterId);
		$masterLang = $masterDoc->get('IdLanguage');

		$lang = new Language($masterLang);
		$langFrom = $lang->get('IsoName');

		ModulesManager::file("/inc/rest/providers/google_translate/GoogleTranslate.class.php");

		$googleTrans = new GoogleTranslate();
		$translation = $googleTrans->translate($content, $langFrom, $langTo);
		echo $translation;
	}

}

