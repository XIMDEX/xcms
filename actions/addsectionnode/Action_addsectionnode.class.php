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



ModulesManager::file('/inc/model/SectionType.class.php');
ModulesManager::file('/inc/model/language.inc');
ModulesManager::file('/actions/manageproperties/inc/InheritedPropertiesManager.class.php');

class Action_addsectionnode extends ActionAbstract {
   // Main method: shows initial form
    function index () {
		$nodeID = $this->request->getParam("nodeid");
		
		$sectionType = new SectionType();
		$sectionTypes = $sectionType->find(ALL);
		reset($sectionTypes);
		while(list(, $sectionTypeInfo) = each($sectionTypes)) {
			if (empty($sectionTypeInfo['module']) || ModulesManager::isEnabled($sectionTypeInfo['module'])) {
				$sectionTypeOptions[] = array('id' => $sectionTypeInfo['idSectionType'], 'name' => $sectionTypeInfo['sectionType']);
			}
		}
		$sectionTypeCount = count($sectionTypeOptions);

		// Getting languages

		$languageOptions = $this->_getLanguages($nodeID);
		$languageCount = sizeof($languageOptions);

		//Get if otf is up
		if (ModulesManager::isEnabled('ximOTF')){
			$otfAvailable=true;
		}else{
			$otfAvailable=false;
		}
		
		$availableThemes = $this->_getAvailableThemes();
		$availableThemesCount = count($availableThemes);
		
        $values = array('nodeID' => $nodeID,
						'sectionTypeOptions' => $sectionTypeOptions,
						'sectionTypeCount' => $sectionTypeCount,
						'languageOptions' => $languageOptions,
						'languageCount' => $languageCount,
						'otfAvailable' => $otfAvailable,
						'go_method' => 'addsectionnode',
						'availableThemes' => $availableThemes,
						'availableThemesCount' => $availableThemesCount);

		$this->render($values, null, 'default-3.0.tpl');
    }
    
    function addsectionnode() {
	   	$nodeID = $this->request->getParam('nodeid');
		$name = $this->request->getParam('name');
		$nodeType = $this->request->getParam('nodetype');
		$langidlst = $this->request->getParam('langidlst');
		$namelst = $this->request->getParam('namelst');
		$type = $this->request->getParam('nodetype');
		$sectionOTF = $this->request->getParam('sectionOTF');
		$selectedTheme = $this->request->getParam('selectedTheme');
		
		
		$aliasLangArray = array();
		if($langidlst) {
			foreach ($langidlst as $key) {
				$aliasLangArray[$key] = $namelst[$key];
			}
		}
	
		$sectionType = new SectionType($type);
		if ($sectionType->get('idSectionType') > 0) {
			$idNodeType = $sectionType->get('idNodeType');
		} else {
			XMD_Log::warning(_('Error obtaining section type'));
			$idNodeType = 5015;
		}
		
		$nodeType = new NodeType($idNodeType);
		$nodeTypeName = $nodeType->get('Name');

	    $data = array(
	            'NODETYPENAME' => $nodeTypeName,
	            'NAME' => $name,
	            'PARENTID' => $nodeID,
	            'FORCENEW' => true
	            );
	            
	    $baseio = new baseIO();
	    $id = $baseio->build($data);

		$themeMessages = array();
		
	    if ($id > 0) {
	    
			$section = new Node($id);
			
			if($aliasLangArray) {
				foreach ($aliasLangArray as $langID => $longName) {
	        		$section->SetAliasForLang($langID, $longName);
				}
			}

			$themeMessages = $this->_setSectionTheme($section, $selectedTheme);
			$this->reloadNode($nodeID);
	    }
	    
		if (!($id > 0)) {
			$this->messages->mergeMessages($baseio->messages);
			$this->messages->add(_('Operation could not be successfully completed'), MSG_TYPE_ERROR);
		}else{
			$this->messages->add(sprintf(_('%s has been successfully created'), $name), MSG_TYPE_NOTICE);
			//set the OTF property
			if ($sectionOTF){
				$node = new Node($id);
				$node->setProperty('otf', "true");
			}
		}
		
		$values = array(
			'action_with_no_return' => $id > 0,
			'messages' => $this->messages->messages
		);
		
		$this->render($values, NULL, 'messages.tpl');
    }
    
    private function _setSectionTheme(&$section, $theme) {

		$messages = array();
		
		if ($theme == '0') {
			return $messages;
		}
				
		$rngPath = sprintf('%s'.ModulesManager::path('ximTHEMES').'/themes/%s/rng', XIMDEX_ROOT_PATH, $theme);
		$ptdPath = sprintf('%s'.ModulesManager::path('ximTHEMES').'s/%s/ptd', XIMDEX_ROOT_PATH, $theme);
		$baseio = new baseIO();
		
		$arrRNG = FsUtils::readFolder($rngPath, false);
		if (!is_array($arrRNG)) $arrRNG = array();
		$project = new Node($section->getProject());
		$rngFolder = new Node($project->GetChildByName(Config::GetValue('VisualTemplateDir')));
		
		$arrIdRNG = array();
		
		foreach ($arrRNG as $rng) {
			if (preg_match('/.ini/', $rng) > 0) {
				continue;
			}

			$idRNG = $rngFolder->GetChildByName($rng);
			if ($idRNG === false) {

				$data = array(
					'NODETYPENAME' => 'RNGVISUALTEMPLATE',
					'NAME' => $rng,
					'PARENTID' => $rngFolder->get('IdNode'),
					'CHILDRENS' => array(
						array ('NODETYPENAME' => 'PATH', 'SRC' => sprintf('%s/%s', $rngPath, $rng))
					)
			    );
			    
				$idRNG = $baseio->build($data);
	        }
			
			if (!($idRNG > 0)) {
//				$messages[] = sprintf(_('RNG %s could not be successfully inserted'), $rng);
			} else {
				$arrIdRNG[] = $idRNG;
			}
		}
				
		$arrPTD = FsUtils::readFolder($ptdPath, false);
		if (!is_array($arrPTD)) $arrPTD = array();
		$ptdFolder = new Node($section->GetChildByName(Config::GetValue('GeneratorTemplateDir')));
				
		foreach ($arrPTD as $ptd) {

			$idPTD = $ptdFolder->GetChildByName($ptd);
			if ($idPTD === false) {

				$data = array(
					'NODETYPENAME' => 'TEMPLATE',
					'NAME' => $ptd,
					'PARENTID' => $ptdFolder->get('IdNode'),
					'CHILDRENS' => array(
						array ('NODETYPENAME' => 'PATH', 'SRC' => sprintf('%s/%s', $ptdPath, $ptd))
					)
			    );
					    
				$idPTD = $baseio->build($data);		
			}
			
			if (!($idPTD > 0)) {
//				$messages[] = sprintf(_('PTD %s could not be successfully inserted'), $ptd);
			}
		}
		
		$section->setProperty('theme', array($theme));
		$section->setProperty('theme_visualtemplates', $arrIdRNG);
		
//		return $messages;
		return $baseio->messages->messages;
    }


	private function _getLanguages($nodeID) {
 		$properties = InheritedPropertiesManager::getValues($nodeID);
 		
 		return $properties["Language"];
	}

	private function _getAvailableThemes() {
			
		$themes = FsUtils::readFolder(XIMDEX_ROOT_PATH .ModulesManager::path('ximTHEMES'). '/themes', false);
		
		if ($themes === null) $themes = array();
		$values = array_merge(array(_('--- Ninguno ---')), $themes);
		$keys = array_merge(array('0'), $themes);
		$themes = array_combine($keys, $values);
		
		return $themes;
	}

}
?>
