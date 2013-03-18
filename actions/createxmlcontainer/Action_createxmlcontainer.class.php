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
ModulesManager::file('/inc/dependencies/DepsManager.class.php');
ModulesManager::file('/inc/model/language.inc');
ModulesManager::file('/inc/model/channel.inc');


class Action_createxmlcontainer extends ActionAbstract {

	// Main method: shows initial form
	function index () {

		$idNode = $this->request->getParam("nodeid");
		$node = new Node($idNode);
		$idNode = $node->get('IdNode');

		if (empty($idNode)) {
			// Why die ? show error to user.
			die(_("Error with parameters"));
		}


		// Gets default schema for XML through propInheritance

		$templates = null;
		$section = $node->getSection();
		
		if ($section > 0) {
		
			$section = new Node($section);
			$hasTheme = (bool) count($section->getProperty('theme'));
			
			if ($hasTheme) {
				$templates = $section->getProperty('theme_visualtemplates');
			}
		}

		$templates = $templates === null ? $node->getTemplates() : $templates;

		$templateArray = array();
		if (!is_null($templates)) {
			foreach ($templates as $idTemplate) {
				$templateNode = new Node($idTemplate);
				$templateArray[] = array('idTemplate' => $idTemplate, 'Name' => $templateNode->get('Name'));
			}
		}

		// Getting channels
		$channel = new Channel();
		$channels = $channel->getChannelsForNode($idNode);
	
		// Getting languages

		$language = new Language();
		$languages = $language->getLanguagesForNode($idNode);
		// if no templates or no channels show to user a new template with info.

		$reloadTree = false;
		if (isset($_REQUEST['reload_tree']) && $_REQUEST['reload_tree']) {
			$reloadTree = true;
		}

//		$this->reloadNode($idNode);

		$values = array(
			'idNode' => $idNode,
			'nodeName' => htmlentities($node->get('Name')),
			'templates' => $templateArray,
			'channels' => $channels,
			'languages' => $languages,
			'go_method' => 'createxmlcontainer',
			'reload_tree' => $reloadTree
		);

		$this->render($values, null, 'default-3.0.tpl');

    }

    function createxmlcontainer() {

    	$idNode = $this->request->getParam('nodeid');
    	$node = new Node($idNode);
    	$idNode = $node->get('IdNode');
		$formChannels = array();


    	if (!($idNode > 0)) {
    		$this->messages->add(_('An error ocurred estimating parent node,')
    			._(' operation will be aborted, contact with your administrator'), MSG_TYPE_ERROR);
    		$values = array('name' => 'Desconocido',
    			'messages' => $this->messages->messages);
    		$this->render($values, null, 'default-3.0.tpl');
    		return false;
    	}

    	$aliases = $this->request->getParam('aliases');
    	$name = $this->request->getParam('name');
    	$idTemplate = $this->request->getParam('id_template');

    	// Creating container

    	$baseIoInferer = new BaseIOInferer();
    	$inferedNodeType = $baseIoInferer->infereType('FOLDER', $idNode);
    	$nodeType = new NodeType();
    	$nodeType->SetByName($inferedNodeType['NODETYPENAME']);
    	if (!($nodeType->get('IdNodeType') > 0)) {
    		$this->messages->add(_('A nodetype could not be estimated to create the container folder,')
    			. _(' operation will be aborted, contact with your administrator'), MSG_TYPE_ERROR);
    	}
        $data = array(
		        'NODETYPENAME' => $nodeType->get('Name'),
		        'NAME' => $name,
		        'PARENTID' => $idNode,
		        'FORCENEW' => true,
		        'CHILDRENS' => array(
		        	array('NODETYPENAME' => 'VISUALTEMPLATE', 'ID' => $idTemplate)
		        )
        );
        $baseIO = new baseIO();
        $idContainer = $result = $baseIO->build($data);

//		$this->reloadNode($idNode);

        if (!($result > 0)) {
        	$this->messages->add(_('An error ocurred creating the container node'), MSG_TYPE_ERROR);
        	foreach ($baseIO->messages->messages as $message) {
        		$this->messages->messages[] = $message;
        	}
        	$values = array(
				'idNode' => $idNode,
				'nodeName' => $name,
				'messages' => $this->messages->messages,
        	);
        	$this->render($values, null, 'default-3.0.tpl');
        	return false;
        } else {
        	$this->messages->add(sprintf(_('Container %s has been successfully created'), $name), MSG_TYPE_NOTICE);
        }
        
        $languages = $this->request->getParam('languages');

		if ($result && is_array($languages)) {
	    	$baseIoInferer = new BaseIOInferer();
	    	$inferedNodeType = $baseIoInferer->infereType('FILE', $idContainer);
	    	$nodeType = new NodeType();
	    	$nodeType->SetByName($inferedNodeType['NODETYPENAME']);
	    	if (!($nodeType->get('IdNodeType') > 0)) {
	    		$this->messages->add(_('A nodetype could not be estimated to create the document,')
	    			. _(' operation will be aborted, contact with your administrator'), MSG_TYPE_ERROR);
	    		// aborts language insertation 
	    		$languages = array();
	    	}

	    	$channels = $this->request->getParam('channels');
			if(!empty($channels) ) {
				foreach ($channels as $idChannel) {
					$formChannels[] = array('NODETYPENAME' => 'CHANNEL', 'ID' => $idChannel);
				}
			}

			// structureddocument inserts content document
			$setSymLinks = array();
			$master = $this->request->getParam('master');
			foreach ($languages as $idLanguage) {
				$result = $this->_insertLanguage($idLanguage, $nodeType->get('Name'), $name, $idContainer, $idTemplate, 
					$formChannels, $aliases);

				if ($master > 0) {
					if ($master != $idLanguage) {
						$setSymLinks[] = $result;
					} else {
						$idNodeMaster = $result;
					}
				}
			}
			
			foreach ($setSymLinks as $idNodeToLink) {
				$structuredDocument = new StructuredDocument($idNodeToLink);
				$structuredDocument->SetSymLink($idNodeMaster);

				$slaveNode = new Node($idNodeToLink);
				$slaveNode->set('SharedWorkflow', $idNodeMaster);
				$slaveNode->update();
			}
		}

		$this->reloadNode($idNode);


		$values = array(
			'messages' => $this->messages->messages,
		);
		$this->render($values, NULL, 'messages.tpl');
    }
	
    function _insertLanguage($idLanguage, $nodeTypeName, $name, $idContainer, $idTemplate, $formChannels, $aliases) {
		$language = new Language($idLanguage);
		if (!($language->get('IdLanguage') >  0)) {
			$this->messages->add(sprintf(_("Language %s insertion has been aborted because it was not found"),  $idLanguage), MSG_TYPE_WARNING);
			return NULL;
		}
		$data = array(
			'NODETYPENAME' => $nodeTypeName,
			'NAME' => $name,
			'PARENTID' => $idContainer,
			'ALIASNAME' => $aliases[$idLanguage],
			"CHILDRENS" => array (
				array ("NODETYPENAME" => "VISUALTEMPLATE", "ID" => $idTemplate),
				array ("NODETYPENAME" => "LANGUAGE", "ID" => $idLanguage)
			)
		);

		if(!empty($formChannels ) ) {
			foreach ($formChannels as $channel) {
				$data['CHILDRENS'][] = $channel;
			}
		}

		if (isset($aliases[$idLanguage])) {
			$data['CHILDRENS'][] = array(
									'NODETYPENAME' => 'NODENAMETRANSLATION',
									'IDLANG' => $idLanguage,
									'DESCRIPTION' => $aliases[$idLanguage]);
		}

		$baseIO = new baseIO();
		$result = $baseIO->build($data);
		if ($result > 0) {
			$insertedNode = new Node($result);
			$this->messages->add(sprintf(_('Document %s has been successfully inserted'), $insertedNode->get('Name')), MSG_TYPE_NOTICE);
		} else {
			$this->messages->add(sprintf(_('Insertion of document %s with language %s has failed'),
				$name, $language->get('Name')), MSG_TYPE_ERROR);
			foreach ($baseIO->messages->messages as $message) {
				$this->messages->messages[] = $message;
			}
		}
		return $result;
    	
    }
}
?>
