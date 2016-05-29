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

use Ximdex\Models\Channel;
use Ximdex\Models\Language;
use Ximdex\Models\Node;
use Ximdex\Models\NodeType;
use Ximdex\Models\StructuredDocument;
use Ximdex\MVC\ActionAbstract;

ModulesManager::file('/inc/metadata/MetadataManager.class.php');
ModulesManager::file('/inc/io/BaseIOInferer.class.php');

class Action_createxmlcontainer extends ActionAbstract {

	// Main method: shows initial form
	function index () {

		$idNode = $this->request->getParam("nodeid");
		$node = new Node($idNode);
		$nt = $node->GetNodeType();
		$idNode = $node->get('IdNode');

		if (empty($idNode)) {
			// Why die ? show error to user.
			die(_("Error with parameters"));
		}

		// Gets default schema for XML through propInheritance
		$schemes = null;
		$section = $node->getSection();
		
		if ($section > 0) {
			$section = new Node($section);
			$hasTheme = (bool) count($section->getProperty('theme'));
			
			if ($hasTheme) {
				$schemes = $section->getProperty('theme_visualtemplates');
			}
		}
		
		if($schemes === null){
			$schemes = $nt == 5083 ? $node->getSchemas('metadata_schema') : $node->getSchemas();
		}
		
		$schemaArray = array();
		if (!is_null($schemes)) {
			foreach ($schemes as $idSchema) {
                $np = new NodeProperty();
                $res = $np->find('IdNodeProperty','IdNode = %s AND Property = %s AND Value = %s', array($idSchema,'SchemaType','metadata_schema'));
                if(!$res){
				    $sch = new Node($idSchema);
				    $schemaArray[] = array('idSchema' => $idSchema, 'Name' => $sch->get('Name'));
                }
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

		$values = array(
			'idNode' => $idNode,
			'nodeName' => htmlentities($node->get('Name')),
			'schemes' => $schemaArray,
			'channels' => $channels,
			'languages' => $languages,
			'go_method' => 'createxmlcontainer',
			'reload_tree' => $reloadTree
		);
		$this->render($values, null, 'default-3.0.tpl');
    }

    /**
     * Method called from View. Create a new xml container. Get the params and check them.     
     */
    function createxmlcontainer() {
		$idNodeMaster = null ;
    	$idNode = $this->request->getParam('nodeid');    	
		$aliases = $this->request->getParam('aliases');
		$name = $this->request->getParam('name');
		$idSchema = $this->request->getParam('id_schema');
		$channels = $this->request->getParam('channels');
		$languages = $this->request->getParam('languages');
		$master = $this->request->getParam('master');

		$node = new Node($idNode);
    	$idNode = $node->get('IdNode');
		$formChannels = array();
	
    	if (!($idNode > 0)) {
    		$this->messages->add(_('An error ocurred estimating parent node,')
    			._(' operation will be aborted, contact with your administrator'), MSG_TYPE_ERROR);
    		$values = array('name' => 'Desconocido','messages' => $this->messages->messages);
    		$this->render($values, null, 'messages.tpl');
    		return false;
    	}
    	
    	$idContainer = $this->buildXmlContainer($idNode, $aliases, $name, $idSchema, $channels, $languages, $master);

    	if (!($idContainer > 0)) {
    		$this->messages->add(_('An error ocurred creating the container node'), MSG_TYPE_ERROR);


        		$values = array(
				'idNode' => $idNode,
				'nodeName' => $name,
				'messages' => $this->messages->messages,
        		);
        		$this->sendJSON($values);
        		return false;
        	} else {
        		$this->messages->add(sprintf(_('Container %s has been successfully created'), $name), MSG_TYPE_NOTICE);
        	}
        
        	$languages = $this->request->getParam('languages');

		if (isset($result) && $result && is_array($languages)) {
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
				$result = $this->_insertLanguage($idLanguage, $nodeType->get('Name'), $name, $idContainer, $idSchema,$formChannels,$aliases);

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

		//$this->reloadNode($idNode);

		$values = array(
			'messages' => $this->messages->messages,
			'parentID' => $idNode,
			'nodeID' => $idContainer
		);
		$this->sendJSON($values);
		return true ;

    }
    private function buildXmlContainer($idNode, $aliases, $name, $idSchema, $channels, $languages, $master){

    	// Creating container
		$baseIoInferer = new BaseIOInferer();
		$inferedNodeType = $baseIoInferer->infereType('FOLDER', $idNode);
		$nodeType = new NodeType();
		$nodeType->SetByName($inferedNodeType['NODETYPENAME']);
		if (!($nodeType->get('IdNodeType') > 0)) {
			$this->messages->add(_('A nodetype could not be estimated to create the container folder,')
			. _(' operation will be aborted, contact with your administrator'), MSG_TYPE_ERROR);
		}

		//Just the selected checks will be created.
		$selectedAlias = array();
		foreach ($languages as $idLang) {
			$selectedAlias[$idLang] = $aliases[$idLang];
		}

    	$data = array(
	        'NODETYPENAME' => $nodeType->get('Name'),
	        'NAME' => $name,
	        'PARENTID' => $idNode,
	        'FORCENEW' => true,
	        'CHILDRENS' => array(
	        	array('NODETYPENAME' => 'VISUALTEMPLATE', 'ID' => $idSchema)
	        ),
	        "CHANNELS" => $channels,
	        "LANGUAGES" => $languages,
	        "ALIASES" => $selectedAlias,
	        "MASTER" => $master
    	);
    	$baseIO = new baseIO();
    	return $baseIO->build($data);
    }
}