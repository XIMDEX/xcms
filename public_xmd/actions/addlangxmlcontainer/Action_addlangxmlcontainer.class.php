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

use Ximdex\Logger;
use Ximdex\Models\Language;
use Ximdex\Models\Node;
use Ximdex\Models\NodeType;
use Ximdex\Models\StructuredDocument;
use Ximdex\MVC\ActionAbstract;

class Action_addlangxmlcontainer extends ActionAbstract {
    
    /**
     * Main Method: it shows the initial form
     */
    public function index()
    {        
    	$idNode = $this->request->getParam("nodeid");
    	$node = new Node($idNode);
    	$idNode = $node->get('IdNode');
		if (empty($idNode)) {
			die(_("Error with parameters"));
		}
		$idTemplate = $this->getVisualTemplate($idNode);
		$template = new Node($idTemplate);
        
		// Getting languages
		$languages = $this->_getLanguages($idNode);
		$numLanguages = count($languages);
		$languageData = array();
		if (is_array($languages)) {
			$id = 0;
			reset($languages);
			while (list(,$language) = each($languages)) {
				$idLanguage = $language['IdLanguage'];
				$languageData[$id] = $this->_hasLang($idNode, $idLanguage);
				$languageData[$id]['idLanguage'] = $idLanguage;
				$languageData[$id]['alias'] = utf8_decode($node->GetAliasForLang($idLanguage));
				$languageData[$id]['name'] = $language['Name'];
				$id ++;
			}
		}
		$reloadTree = false;
		if (isset($_REQUEST['reload_tree']) && $_REQUEST['reload_tree']) {
			$reloadTree = true;
		}
		$values = array(
			'go_method' => 'updateXmlContainer',
			'idNode' => $idNode,
			'nodeName' => htmlentities($node->get('Name')),
			'idTemplate' => $template->get('IdNode'),
			'templateName' => htmlentities($template->get('Name')),
			'languages' => $languageData,
			'numlanguages' => $numLanguages,
		    'node_Type' => $node->nodeType->GetName(),
			'reload_tree' => $reloadTree,
		);
		$this->render($values, null, 'default-3.0.tpl');
	}

	public function updateXmlContainer()
	{
		$nodeid = $this->request->getParam('nodeid');
		$templateid = $this->request->getParam('templateid');
		$name = $this->request->getParam('name');
		$languages = $this->request->getParam('languages');
		$aliases = $this->request->getParam('aliases');
		if (empty($languages)) {
			$this->messages->add(_('There are no specified languages'), MSG_TYPE_ERROR);
		}
		else {
			$node = new Node($nodeid);
			if (!($node->get('IdNode') > 0)) {
			    
				Logger::error("Selected Node " . $nodeid . " was not found");
				$msg = _('The selected node was not found:') . $nodeid;
				$this->messages->add($msg, MSG_TYPE_ERROR);
				$this->render(array('messages' => $this->messages->messages));
				return false;
			}
			$allowedNodeTypes = $node->nodeType->GetAllowedNodeTypes();
			if (count($allowedNodeTypes) == 1) {
				$idNodeType = $allowedNodeTypes[0]['nodetype'];
			}
			else {   
				Logger::error('More than one allowed nodetype found for this folder. Returning the first one');
				$idNodeType = $allowedNodeTypes[0]['nodetype'];
			}
			if (!isset($idNodeType)) {
				Logger::error("Nodeid: ". $nodeid . "has no NodeAllowedContent with able of storing a language list");
				$msg = sprintf(_('The node with id %d has not any nodeAllowedContent with necessary features to store a language list'), $nodeid);
				$this->messages->add($msg, MSG_TYPE_ERROR);
				$this->render(array('messages' => $this->messages->messages));
				return false;
			}
			$nodeType = new NodeType($idNodeType);
			$language = new Language();
			$allLanguages = $language->find('IdLanguage', NULL, NULL, MONO);
			if (!$allLanguages) {
				Logger::error("No language found");
				$msg = _('No language has been found');
				$this->messages->add($msg, MSG_TYPE_ERROR);
				$this->render(array('messages' => $this->messages->messages));
				return false;
			}
			foreach ($allLanguages as $idLanguage) {
				$child = $this->_hasLang($node->get('IdNode'), $idLanguage);
				$idNode = $child['idChildren'];
				if ($idNode > 0) {
					if (in_array($idLanguage, $languages)) {
					    
						// Update
						$data = array(
							'ID' => $idNode,
							'NODETYPENAME' => $nodeType->get('Name')
						);
						if (isset($aliases[$idLanguage])) {
							$data['CHILDRENS'][] = array(
								'NODETYPENAME' => 'NODENAMETRANSLATION',
								'IDLANG' => $idLanguage,
								'DESCRIPTION' => utf8_encode($aliases[$idLanguage]));
						}
						$baseIO = new \Ximdex\IO\BaseIO();
						$result = $baseIO->update($data);
						if ($result <= 0) {
							reset($baseIO->messages->messages);
							while(list(, $message) = each($baseIO->messages->messages))
								$this->messages->messages[] = $message;
							break;
						}
					} else {
					    
						// Delete
						$data = array(
							'ID' => $idNode,
							'NODETYPENAME' => $nodeType->get('Name')
						);
						$baseIO = new \Ximdex\IO\BaseIO();
						$result = $baseIO->delete($data);
						if ($result <= 0) {
							reset($baseIO->messages->messages);
							while(list(, $message) = each($baseIO->messages->messages))
								$this->messages->messages[] = $message;
							break;
						}
					}
				} else {
					if (in_array($idLanguage, $languages)) {
					    
						// Add
						$data = array(
							'NODETYPENAME' => $nodeType->get('Name'),
							'NAME' => $node->get('Name'),
							'PARENTID' => $nodeid,
							'ALIASNAME' => $aliases[$idLanguage],
							"CHILDRENS" => array (
								array ("NODETYPENAME" => "VISUALTEMPLATE", "ID" => $templateid),
								array ("NODETYPENAME" => "LANGUAGE", "ID" => $idLanguage)
							)
						);
						if (isset($aliases[$idLanguage])) {
							$data['CHILDRENS'][] = array(
    							'NODETYPENAME' => 'NODENAMETRANSLATION',
    							'IDLANG' => $idLanguage,
    							'DESCRIPTION' => $aliases[$idLanguage]);
						}
						$baseIO = new \Ximdex\IO\BaseIO();
						$result = $baseIO->build($data);
						if ($result <= 0) {
							reset($baseIO->messages->messages);
							while(list(, $message) = each($baseIO->messages->messages))
								$this->messages->messages[] = $message;
							break;
					   }
					}
				}
			}
		}
		if (isset($result) && $result > 0) {
			$this->messages->add(_('Changes have been successfully done'), MSG_TYPE_NOTICE);
		}
        $values = array('messages' => $this->messages->messages, "parentID" => $nodeid);
        $this->sendJSON($values);
	}

	private function getVisualTemplate($idNode)
	{
		$node = new Node($idNode);
		if (count($node->GetChildren())) {
			foreach ($node->GetChildren() as $childID) {
			     $child = new StructuredDocument($childID);
			     $idTemplate = $child->get('IdTemplate');
			     if ($idTemplate) {
			     	return $idTemplate;
			     }
			}
	    }
	    else {
			$reltemplate = new \Ximdex\Models\RelTemplateContainer();
			$idTemplate = $reltemplate->getTemplate($idNode);
			return $idTemplate;
		}
		return false;
	}

	private function _hasLang($idNode, $idLanguage)
	{
		$node = new Node($idNode);
		$children = $node->GetChildren();
		if (is_array($children)) {
			foreach ($children as $idChild) {
				$childrenDoc = new StructuredDocument($idChild);
				if ($childrenDoc->GetLanguage() == $idLanguage) {
					$node = new Node($idChild);
					return array(
						'idChildren' => $idChild,
						'aliasLanguage' => $node->GetAliasForLang($idLanguage)
					);
				}
			}
		}
		return array('idChildren' => NULL);
	}

	private function _getLanguages($nodeID)
	{
		$node = new Node($nodeID);
		$language = new Language();
		$languages = $language->getLanguagesForNode($nodeID);
		if (empty($languages)) $languages = array();
		return $languages;
	}
}
