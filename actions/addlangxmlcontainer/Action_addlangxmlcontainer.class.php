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



class Action_addlangxmlcontainer extends ActionAbstract {
   // Main Method: it shows the initial form
    function index() {
    	$idNode = $this->request->getParam("nodeid");
    	$node = new Node($idNode);
    	$idNode = $node->get('IdNode');

		if (empty($idNode)) {
			die(_("Error with parameters"));
		}


		$idTemplate = $this->_getVisualTemplate($idNode);
		$template = new Node($idTemplate);

		// Getting channels

		$channels = $this->_getChannels($idNode);
		$numChannels = sizeof($channels);

		// Getting languages

		$languages = $this->_getLanguages($idNode);
		$numLanguages = sizeof($languages);

		$languageData = array();

		if (is_array($languages)) {
			$id = 0;
			reset($languages);
			while (list(,$language) = each($languages)) {
				$idLanguage = $language['IdLanguage'];
				$languageData[$id] = $this->_hasLang($idNode, $idLanguage);
				$languageData[$id]['idLanguage'] = $idLanguage;
				$languageData[$id]['alias'] = $node->GetAliasForLang($idLanguage);
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
			'channels' => $channels,
			'numchannels' => $numChannels,
			'languages' => $languageData,
			'numlanguages' => $numLanguages,
			'reload_tree' => $reloadTree,
		);

		$this->render($values, null, 'default-3.0.tpl');
	}

	function updateXmlContainer() {

		$nodeid = $this->request->getParam('nodeid');
		$templateid = $this->request->getParam('templateid');
		$name = $this->request->getParam('name');
		$languages = $this->request->getParam('languages');
		$aliases = $this->request->getParam('aliases');
		$channels = $this->request->getParam('channels');


		if (empty($languages)) {	
			$this->messages->add(_('There are no specified languages'), MSG_TYPE_ERROR);
		}else if (empty($channels)) {
			$this->messages->add(_('There are no specified channels'), MSG_TYPE_ERROR);
		} else {

			$node = new Node($nodeid);
			if (!($node->get('IdNode') > 0)) {
				$msg = _('The selected node was not found:') . $nodeid;
				XMD_Log::error($msg);
				$this->messages->add($msg, MSG_TYPE_ERROR);
				$this->render(array('messages' => $this->messages->messages));
				return;
			}

			$allowedNodeTypes = $node->nodeType->GetAllowedNodeTypes();
			if (count($allowedNodeTypes) == 1) {
				$idNodeType = $allowedNodeTypes[0]['nodetype'];
			} else {
				XMD_Log::error(_('More than one allowed nodetype has been found for this folder, it is recovered returning to the first'));
				$idNodeType = $allowedNodeTypes[0]['nodetype'];
			}

			if (!isset($idNodeType)) {
				$msg = sprintf(_('The node with id %d has not any nodeAllowedContent with necessary features to store a language list'), $nodeid);
				XMD_Log::error($msg);
				$this->messages->add($msg, MSG_TYPE_ERROR);
				$this->render(array('messages' => $this->messages->messages));
				return;
			}

			$nodeType = new NodeType($idNodeType);

			$language = new Language();
			$allLanguages = $language->find('IdLanguage', NULL, NULL, MONO);

			if (!is_array($allLanguages)) {
				$msg = _('No language has been found');
				XMD_Log::error($msg);
				$this->messages->add($msg, MSG_TYPE_ERROR);
				$this->render(array('messages' => $this->messages->messages));
				return;
			}

			$asignedChannels = array();
			$_asignedChannels = $this->_getChannels($nodeid);
			foreach ($_asignedChannels as $channel) {
				$asignedChannels[] = $channel['IdChannel'];
			}

			foreach ($allLanguages as $idLanguage) {

				$child = $this->_hasLang($node->get('IdNode'), $idLanguage);
				$idNode = $child['idChildren'];

				if ($idNode > 0) {

					if (in_array($idLanguage, $languages)) {
						//update
						$data = array(
							'ID' => $idNode,
							'NODETYPENAME' => $nodeType->get('Name')
						);

						foreach ($asignedChannels as $idChannel) {

							$channelData = array(
								'NODETYPENAME' => 'CHANNEL',
								'ID' => $idChannel
							);

							if (!in_array($idChannel, $channels)) {
								$channelData['OPERATION'] = 'REMOVE';
							}

							$data['CHILDRENS'][] = $channelData;
						}

						if (isset($aliases[$idLanguage])) {
							$data['CHILDRENS'][] = array(
													'NODETYPENAME' => 'NODENAMETRANSLATION',
													'IDLANG' => $idLanguage,
													'DESCRIPTION' => $aliases[$idLanguage]);
						}

						$baseIO = new baseIO();
						$result = $baseIO->update($data);

						if (!$result > 0) {
							reset($baseIO->messages->messages);
							while(list(, $message) = each($baseIO->messages->messages)) {
								$this->messages->messages[] = $message;
							}
						}
					} else {
						//delete
						$data = array(
							'ID' => $idNode,
							'NODETYPENAME' => $nodeType->get('Name')
						);
						$baseIO = new baseIO();
						$result = $baseIO->delete($data);
						if (!$result > 0) {

							reset($baseIO->messages->messages);
							while(list(, $message) = each($baseIO->messages->messages)) {
								$this->messages->messages[] = $message;
							}
						}
					}
				} else {

					if (in_array($idLanguage, $languages)) {
						// add
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

						foreach ($channels as $idChannel) {
							$data['CHILDRENS'][] = array('NODETYPENAME' => 'CHANNEL', 'ID' => $idChannel);
						}

						if (isset($aliases[$idLanguage])) {
							$data['CHILDRENS'][] = array(
													'NODETYPENAME' => 'NODENAMETRANSLATION',
													'IDLANG' => $idLanguage,
													'DESCRIPTION' => $aliases[$idLanguage]);
						}

						$baseIO = new baseIO();
						$result = $baseIO->build($data);
						if (!$result > 0) {
							reset($baseIO->messages->messages);
							while(list(, $message) = each($baseIO->messages->messages)) {
								$this->messages->messages[] = $message; } }
					}
				}
			}
		}

		$this->reloadNode($nodeid);
		if (isset($result) && $result > 0) {
			$this->messages->add(_('Changes have been successfully done'), MSG_TYPE_NOTICE);
		}
		$values = array(
			'messages' => $this->messages->messages,
			'goback' => true
		);
		$this->render($values);
	}

	function _getVisualTemplate($idNode) {
		$node = new Node($idNode);
		if(count($node->GetChildren())){
			foreach ($node->GetChildren() as $childID) {
			     $child = new StructuredDocument($childID);
			     $idTemplate = $child->get('IdTemplate');
			     if ($idTemplate) {
			     	return $idTemplate;
			     }
			}
	    } else {
			$reltemplate = new RelTemplateContainer();
			$idTemplate = $reltemplate->getTemplate($idNode);
			return $idTemplate;
		}
		return false;
	}

	function _hasLang($idNode, $idLanguage) {
		$node = new Node($idNode);
		$children = $node->GetChildren();

		if (is_array($children)) {
			foreach ($children as $idChild) {
				$children = new StructuredDocument($idChild);
				if ($children->GetLanguage() == $idLanguage) {
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

	private function _getLanguages($nodeID) {

		$node = new Node($nodeID);
		$language = new Language();
		$languages = $language->getLanguagesForNode($nodeID);
		if (empty($languages)) $languages = array();

		return $languages;
	}

	private function _getChannels($nodeID) {

		$node = new Node($nodeID);
		$channel = new Channel();
		$channels = $channel->getChannelsForNode($nodeID);
		if (empty($channels)) $channels = array();

		$children = $node->GetChildren();
		if (empty($children)) {
			$lang = null;
		} else {
			$lang = new Node($children[0]);
		}

		foreach ($channels as &$channel) {
			$ch = new Channel($channel['IdChannel']);
			$channel['selected'] = $lang === null
				? false
				: ($lang->class->hasChannel($channel['IdChannel']) ? true : false);
		}

		return $channels;
	}

}
?>
