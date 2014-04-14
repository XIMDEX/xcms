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


class Action_renamenode extends ActionAbstract {
	// Main method: shows initial form
    	function index () {
    		$idNode = $this->request->getParam('nodeid');
    		$node = new Node($idNode);

    		$isSection = $node->nodeType->get('IsSection');
    		$allLanguages = NULL;
    		if ($isSection) {
			$language = new Language();
			$allLanguages = $language->find('IdLanguage, Name');
			if (!empty($allLanguages)) {
				foreach ($allLanguages as $key => $languageInfo) {
					$allLanguages[$key]['alias'] = $node->GetAliasForLang($languageInfo['IdLanguage']);
				}
			}
    		}

		$isProject =  $node->nodeType->get('Name') == 'Project' ? 1 : 0;

		$pipelineInfo = array();
		$idPipeline = NULL;

		if ($node->nodeType->get('IsFolder') ||	
		    $node->nodeType->get('IsSection') || 
		    $node->nodeType->get('IsVirtualFolder')) {

			// master pipeline
			$IdNodeForWorkflowMaster = Config::getValue('IdDefaultWorkflow');
			$pipelineMaster = new Pipeline();
			$pipelineMaster->loadByIdNode($IdNodeForWorkflowMaster);
			$diffPipelines = array($pipelineMaster->get('id'));
		
			// pipelines associated with nodetypes
			$pipeNodeTypes = new PipeNodeTypes();
			$pipeNodeTypesList = $pipeNodeTypes->find('IdPipeline', '', NULL, MONO);
			if (!is_array($pipeNodeTypesList)) {
				$pipeNodeTypesList = array();
			}
			$diffPipelines = array_merge($diffPipelines, $pipeNodeTypesList);

			$pipeline = new Pipeline();
			$pipelineList = $pipeline->find('id', 'IdNode > 0', NULL, MONO);
			$pipelineList = array_diff($pipelineList, $diffPipelines);

			$pipelineInfo = array();
			foreach ($pipelineList as $idPipeline) {
				$pipeline = new Pipeline($idPipeline);
				$pipelineInfo[$idPipeline] = $pipeline->get('Pipeline');
			}

			$idPipeline = $node->getProperty('Pipeline');
			if (count($idPipeline) > 0) {
				$idPipeline = $idPipeline[0];
			}
		}

		$nt = $node->nodeType->get('IdNodeType');

		$moduleXimNews = ($nt==5078) && (ModulesManager::isEnabled('ximNEWS'));

		$schemaType = '';
		
		$schemaType = $node->getProperty('SchemaType');
		if (is_array($schemaType) && count($schemaType) == 1) {
			$schemaType = $schemaType[0];
		}
		

		$checkUrl = Config::getValue('UrlRoot') . '/xmd/loadaction.php?actionid='
			. $this->request->getParam('actionid') . '&nodeid=' . $this->request->getParam('nodeid')
			. '&id_pipeline=IDPIPELINE&method=checkNodeDependencies';

		$this->addJs('/actions/renamenode/resources/js/renamenode.js');
    		$values = array('name' => $node->get('Name'),
				'is_section' => $isSection,
    				'is_project' => $isProject,
    				'all_languages' => $allLanguages,
    				'module_ximnews' => $moduleXimNews,
    				'schema_type' => $schemaType,
    				'go_method' => 'update',
    				'valid_pipelines' => $pipelineInfo,
    				'selected_pipeline' => $idPipeline,
    				'check_url' => $checkUrl,
    				'id_node' => $idNode,
    				'id_nodetype' => $nt
    		);
    		$this->render($values, NULL, 'default-3.0.tpl');
    	}

    
	function update() {
    		$idNode = $this->request->getParam('id_node');
    		$name = $this->request->getParam('name');

    		$languages = $this->request->getParam('language');

    		$node = new Node($idNode);
    		if (!$node->get('IdNode') > 0) {
    			$this->messages->add(_('Node could not be successfully loaded'), MSG_TYPE_NOTICE);
    		} else {
			$result = $node->RenameNode($name);
    			$node->deleteProperty('SchemaType');

    			$schemaType = $this->request->getParam('schema_type');
    			if (!empty($schemaType) && $schemaType != 'generic_schema') {
    				$node->setProperty('SchemaType', $schemaType);
    			}
    			$node->update();
    			if ($result) {
    				$this->messages->add(_('Node name has been successfully updated'), MSG_TYPE_NOTICE);
    			} else {
    				$this->messages->add(_('Node name could not be updated'), MSG_TYPE_ERROR);
    			}

    			if ($node->nodeType->get('IsSection')) {
    				foreach ($languages as $idLanguage => $alias) {
    					if ($node->SetAliasForLang($idLanguage, $alias)) {
    						$language = new Language($idLanguage);
    						$this->messages->add(sprintf(_('Alias for language %s has been successfully updated'), $language->get('Name')), MSG_TYPE_NOTICE);
    					}
    				}
    			}

    		}

    		$oldIdPipeline = $node->getProperty('Pipeline');
    		$newIdPipeline = $this->request->getParam('id_pipeline');
    		if (!($newIdPipeline > 0)) {
    			$newIdPipeline = NULL;
    		}

    		if (count($oldIdPipeline > 0)) {
    			$oldIdPipeline = $oldIdPipeline[0];
    		}

    		if ($oldIdPipeline != $newIdPipeline) {
    			$node->updateToNewPipeline($newIdPipeline);
    			$node->setProperty('Pipeline', $newIdPipeline);
    		}

   		$this->messages->mergeMessages($node->messages);
		// $this->reloadNode($node->get('IdParent') );

    		$values = array('messages' => $this->messages->messages, 'parentID' => $node->get('IdParent'));
    		$this->sendJSON($values);
    	}


   	function checkNodeDependencies() {
    		$idNode = $this->request->getParam('nodeid');
    		$idPipeline = $this->request->getParam('id_pipeline');

    		$node = new Node($idNode);
    		$oldIdPipeline = $node->getProperty('Pipeline');
    		if (is_array($oldIdPipeline)) {
    			$oldIdPipeline = $oldIdPipeline[0];
    		}

    		if ($idPipeline != $oldIdPipeline) {
			$db = new Db();
			$query = sprintf("SELECT IdChild FROM FastTraverse WHERE IdNode = %s", $idNode);
			$db->Query($query);

			if($db->numRows==0 || $db->numRows==1){
				$this->messages->add(_('Any node will change its workflow status'), MSG_TYPE_NOTICE);
			}
			else{
				$this->messages->add(_('Nodes which are going to change their workflow status:'), MSG_TYPE_NOTICE);
			}
			
			while(!$db->EOF) {
				$idNode = $db->GetValue('IdChild');
				$node = new Node($idNode);
				if ($node->get('IdState') > 0) {
					$this->messages->add(sprintf(_('If you perform this modification, workflow status of node <b>%s</b> will be modified.'), $node->GetPath()), MSG_TYPE_NOTICE);
				}
				$db->Next();
			}
    		}

		//$this->sendJSON(array('messages' => $this->messages->messages, 'nodeID' => $idNode));
		$this->render(array('messages' => $this->messages->messages), NULL, 'messages.tpl');
    }

}
?>