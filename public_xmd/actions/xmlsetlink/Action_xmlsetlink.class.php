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
 *  @author Ximdex DevTeam <dev@ximdex.com>
 *  @version $Revision$
 */

use Ximdex\Models\Language;
use Ximdex\Models\Node;
use Ximdex\Models\StructuredDocument;
use Ximdex\MVC\ActionAbstract;

class Action_xmlsetlink extends ActionAbstract
{
    /**
     * Main method: shows the initial form
     */
    public function index()
    {
		$idNode = $this->request->getParam('nodeid');
		$node = new Node($idNode);
		if (! $node->get('IdNode')) {
		    $this->error(_('Node could not be found'));
		    return false;
		}
		$sharewf = $node->get('SharedWorkflow');
		$sharewf = empty($sharewf) ? 0 : 1;
		$structuredDocument = new StructuredDocument($idNode);
		$idTarget = $structuredDocument->get('TargetLink');
		if ($idTarget) {
            $targetNode = new Node($idTarget);
		} else {
		    try {
		        $targetNode = $structuredDocument->getDefaultLanguageDocument();
		    } catch (Exception $e) {
		        $this->error(_($e->getMessage()));
		        return false;
		    }
		}
	    $targetNodes = $this->getTargetNodes($node->GetID());
		$type = $node->get('IdNodeType');
		$this->addJs('/actions/xmlsetlink/resources/js/xmlsetlink.js');
		$this->addJs('/actions/xmlsetlink/resources/js/setinfo.js');
		$this->addCss('/actions/copy/resources/css/style.css');
		$values = array(
			'id_node' => $idNode,
			'id_target' => $idTarget,
		    'name_target' => $targetNode ? $targetNode->GetNodeName() : null,
			'type' => $type,
			'targetNodes' => $targetNodes,
		    'go_method' => empty($idTarget) ? 'setlink' : 'unlink',
			'sharewf' => $sharewf,
		    'name' => $node->GetNodeName(),
		    'nodeTypeID' => $node->nodeType->getID(),
		    'node_Type' => $node->nodeType->GetName()
		);
		$this->render($values, NULL, 'default-3.0.tpl');
    }

	public function setlink()
	{
		$idNode = $this->request->getParam('nodeid');
		$idTarget = $this->request->getParam('targetid');
		$structuredDocument = new StructuredDocument($idNode);
		if (! $idTarget) {
		    
		    // If there is no target specified, try to set to the document in default language
		    try {
		        $targetNode = $structuredDocument->getDefaultLanguageDocument();
		        $idTarget = $targetNode->GetID();
		    } catch (Exception $e) {
		        $this->error(_($e->getMessage()), true);
		        return;
		    }
		}
		$targetStructuredDocument = new StructuredDocument($idTarget);
        $targetOfTarget = $targetStructuredDocument->get('TargetLink');
        if ($targetOfTarget == $idNode) {
            $this->error(_('This document is already the master language document of ') . $targetStructuredDocument->GetName(), true);
        }
		$node = new Node($idNode);
		$node->ClearWorkFlowMaster();
		if ($this->request->getParam('sharewf')) {
			$node->SetWorkFlowMaster($idTarget);
		}
		$structuredDocument->ClearSymLink();
		$structuredDocument->SetSymLink($idTarget);
		
		// Copy content from target document
		$content = $node->GetContent();
		if (! $node->SetContent($content, true)) {
		    $this->error(_('Cannot copy the content of target document'), true);
		    return false;
		}
		
		$this->messages->add(_('The link has been modified successfully'), MSG_TYPE_NOTICE);
		$messages = new \Ximdex\Utils\Messages();
		$messages->mergeMessages($node->messages);
		$messages->mergeMessages($structuredDocument->messages);
        $values = array('messages' => $this->messages->messages, 'parentID' => $node->get('IdParent'));
        $this->sendJSON($values);
	}

	/**
	 * Removes the symbolic link of document
	 */
	public function unlink()
	{
		$idNode = $this->request->getParam('nodeid');
		$structuredDocument = new StructuredDocument($idNode);
		$node = new Node($idNode);
		$node->ClearWorkFlowMaster();
		$structuredDocument->ClearSymLink();
		$this->messages->add(_('The link has been deleted successfully'), MSG_TYPE_NOTICE);
		$values = array('messages' => $this->messages->messages, 'parentID' => $node->get('IdParent'));
        $this->sendJSON($values);
	}

	/**
	 * Shows the form with the document content translated by Google Translate
	 */
	public function show_translation()
	{
		$values = array('go_method' => 'unlink');
		$this->addJs('/actions/xmlsetlink/resources/js/show_translation.js');
		$this->render($values, 'show_translation', 'default-3.0.tpl');
	}
	
	/**
	 * Calls Google Translate service
	 */
	public function translate()
	{
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
		$googleTrans = new  \Ximdex\Rest\Providers\GoogleTranslate\GoogleTranslate();
		$translation = $googleTrans->translate($content, $langFrom, $langTo);
		echo $translation;
	}

	/**
	* @param int $idNode current Idnode
	* @return array Siblings for the current Node
	*/
	private function getTargetNodes(int $idNode) : array
	{
		$node = new Node($idNode);
		$siblings = $node->getSiblings();
		$targetNodes = array();
		foreach ($siblings as $sibling) {
			$arrayAux = array();
			if (! is_object($sibling)) {
			    $sibling = new Node($sibling);
			}
            $arrayAux['path'] = str_replace('/Ximdex/Projects/', '', $sibling->getPath());
            $arrayAux['idnode'] = $sibling->GetID();
            $targetNodes[] = $arrayAux;
        }
        return $targetNodes;
	}
	
	private function error(string $error, bool $jsonOutput = false)
	{
	    $this->messages->add($error, MSG_TYPE_ERROR);
	    $values = ['messages' => $this->messages->messages];
	    if ($jsonOutput) {
	        $this->sendJSON($values);
	    } else {
	       $this->render($values, NULL, 'messages.tpl');
	    }
	}
}
