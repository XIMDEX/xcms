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

ModulesManager::file('/inc/model/NodeDefaultContents.class.php');
ModulesManager::file('/inc/model/SectionType.class.php');
ModulesManager::file('/inc/model/language.php');
ModulesManager::file('/actions/manageproperties/inc/InheritedPropertiesManager.class.php');

class Action_addsectionnode extends ActionAbstract {
	
    	function index () {
		$this->loadResources();
		$this->render($this->loadValues(), null, 'default-3.0.tpl');
    	}
    
	protected function loadValues(){
		$nodeID = $this->request->getParam("nodeid");
                $action = $this->request->getParam("action");
                $type_sec = $this->request->getParam("type_sec");

		$nt=5015;       
                if(empty($type_sec)){
                        $type_sec=1;
                }

                $sectionType = new SectionType();
                $sectionTypes = $sectionType->find(ALL);
                reset($sectionTypes);
                while(list(, $sectionTypeInfo) = each($sectionTypes)) {
                        if (empty($sectionTypeInfo['module']) || ModulesManager::isEnabled($sectionTypeInfo['module'])) {
                                $sectionTypeOptions[] = array('id' => $sectionTypeInfo['idSectionType'], 'name' => $sectionTypeInfo['sectionType']);
                                if($type_sec==$sectionTypeInfo['idSectionType']){
                                        $nt=$sectionTypeInfo['idNodeType'];
                                }
                        }
                }
                $sectionTypeCount = count($sectionTypeOptions);

                // Getting languages
                $languageOptions = $this->_getLanguages($nodeID);
                $languageCount = sizeof($languageOptions);

                $subfolders=$this->_getAvailableSubfolders($nt);

                $values = array('nodeID' => $nodeID,
                                'nodeURL' => \App::getValue( 'UrlRoot').'/xmd/loadaction.php?action='.$action.'&nodeid='.$nodeID,
                                'sectionTypeOptions' => $sectionTypeOptions,
                                'sectionTypeCount' => $sectionTypeCount,
                                'selectedsectionType' => $type_sec,
                                'languageOptions' => $languageOptions,
                                'languageCount' => $languageCount,
                                'subfolders' => $subfolders,
                                'go_method' => 'addsectionnode',
                                );

		return $values;
	}

	protected function loadResources(){
                $this->addJs('/actions/addsectionnode/resources/js/index.js');
		$this->addCss('/actions/addsectionnode/resources/css/style.css');
	}

    	function addsectionnode() {
	   	$nodeID = $this->request->getParam('nodeid');
		$name = $this->request->getParam('name');
		$nodeType = $this->request->getParam('nodetype');
		$langidlst = $this->request->getParam('langidlst');
		$namelst = $this->request->getParam('namelst');
		$folderlst = $this->request->getParam('folderlst');
		$type = $this->request->getParam('nodetype');
				
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
	            'SUBFOLDERS' => $folderlst,
	            'PARENTID' => $nodeID,
	            'FORCENEW' => true
	            );

	    	$baseio = new baseIO();
	    	$id = $baseio->build($data);

	    	if ($id > 0) {
			$section = new Node($id);
			if($aliasLangArray) {
				foreach ($aliasLangArray as $langID => $longName) {
	        		$section->SetAliasForLang($langID, $longName);
				}
			}
			
	    	}
	    
		if (!($id > 0)) {
			$this->messages->mergeMessages($baseio->messages);
			$this->messages->add(_('Operation could not be successfully completed'), MSG_TYPE_ERROR);
		}else{
			$this->messages->add(sprintf(_('%s has been successfully created'), $name), MSG_TYPE_NOTICE);
		}
		
		$values = array(
			'action_with_no_return' => $id > 0,
			'messages' => $this->messages->messages,
			'nodeID' => $nodeID
		);
		
		$this->sendJSON($values);
    	}
    
	private function _getLanguages($nodeID) {
 		$properties = InheritedPropertiesManager::getValues($nodeID);
 		
 		return $properties["Language"];
	}

	private function _getAvailableSubfolders($nodetype_sec){
		$subfolders=array();
		$res=array();
		$ndc = new NodeDefaultContents();
		$subfolders=$ndc->getDefaultChilds($nodetype_sec);
		if(count($subfolders)>0){
			foreach($subfolders as $subfolder){
				$nt=$subfolder["NodeType"];
				$res[$nt][0]=$subfolder["Name"];	
				$res[$nt][1]=$this->_getDescription($nt);	
			}
		}
		asort($res);	
		return $res;
	}

	protected function _getDescription($nodetype){
		switch($nodetype){
			case "5018": return "This is the main repository for all your XML contents. It's the most important folder in a section.";
			case "5016": return "Inside this folder you can store all the image files you need in several formats (gif, png,jpg, tiff,...)";
			case "5020": return "Into this folder you could store several HTML snippets that you can add directly into your XML documents";
			case "5022": return "Use this folder if you need to store JavaScript scripts or text files like PDFs, MS Office documents, etc.";
			case "5026": return "Create here your own XSL Templates to redefine some particular appareance in your XML documents.";
			case "5054": return "Create XML snippets that you can import into your XML documents. Typical uses are menus, shared headers, shared footers between all your XML documents.";
			case "5301": return "ximNEWS module manages and organizes all the existing news into bulletins. This is a required folder.";
			case "5304": return "Into this folder you could create XML based news in several languages. This is a required folder.";
			case "5306": return "All the images used in your defined news are stored here.";
			case "5083": return "Create metadata structured documents to describe other resources stored in Ximdex CMS.";
			default: "...";
		}
	}
}

?>