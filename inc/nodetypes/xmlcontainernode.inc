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

ModulesManager::file('/inc/nodetypes/foldernode.inc');
ModulesManager::file('/inc/model/RelTemplateContainer.class.php');
ModulesManager::file('/inc/model/language.inc');
ModulesManager::file('/inc/model/structureddocument.inc');
ModulesManager::file('/inc/metadata/MetadataManager.class.php');

class XmlContainerNode extends FolderNode{

	function RenderizeNode(){
        return null;
	}

	function getVisualTemplate(){
		$query = sprintf("SELECT IdTemplate FROM RelTemplateContainer WHERE IdContainer = %d",$this->nodeID);
	  	$this->dbObj->Query($query);
		if (!($this->dbObj->numRows)) {
			return false;
		}
		return $this->dbObj->GetValue('IdTemplate');
	}

    //TODO: check if this function is called in any moment. All the logic to create xml containers it's done in createxmlcontainer action
	function CreateNode($name = null, $parentID = null, $nodeTypeID = null, $stateID = null, $templateID = null, $aliasLangList = null, $channelList = null, $master = null,$dataChildren=null){
		$nodeMaster =  NULL;
		if (is_array($aliasLangList)){
		    foreach ($aliasLangList as $IdLanguage => $alias){
 			    $xmldoc = new Node();
			    $childrenNodeType = new NodeType();
			    switch($this->nodeType->GetName()){
    				case 'XimNewsBulletin':
	    				$childrenNodeType->SetByName('XimNewsBulletinLanguage');
		    			break;
			    	case 'XimNewsNew':
				    	$childrenNodeType->SetByName('XimNewsNewLanguage');
					    break;
    				case 'XmlContainer':
	    				$childrenNodeType->SetByName('XmlDocument');
		    			break;
			    	case 'XimletContainer':
				    	$childrenNodeType->SetByName('Ximlet');
					    break;
    				case 'XimPdfDocumentFolder':
	    				$childrenNodeType->SetByName('XimPdfDocumentLang');
		    			break;
			    	default:
				    	return;
	            }

			    if($childrenNodeType->HasError()){
    			    $this->parent->SetError(1);
				    return;
				}

			    $lang = new Language($IdLanguage);
			    if($lang->HasError()){
				    $this->parent->SetError(1);
				    return;
				}

			    $nameDoc = $name."-id".$lang->GetIsoName();

	 		    $xmldoc->CreateNode($nameDoc, $this->nodeID, $childrenNodeType->GetID(),$stateID = null, $templateID, $IdLanguage, $alias, $channelList, $dataChildren);
			    if(!$nodeMaster && $master && $master == $IdLanguage){
				    $nodeMaster = $xmldoc->GetID();
				}

			    if($xmldoc->HasError()){
			 	    $this->parent->SetError(1);
				    return;
				}
			}// end foreach
		}//end if

		if($nodeMaster) {
			$children = $this->parent->GetChildren();
			foreach($children as $childID){
				if($childID != $nodeMaster) {
					$child = new Node($childID);
					$child->SetWorkflowMaster($nodeMaster);
					$strDoc = new StructuredDocument($childID);
					$strDoc->SetSymLink($nodeMaster);
				}
			}
		}
        $reltemplate = new RelTemplateContainer();
        $reltemplate->createRel($templateID,$this->nodeID);

        $this->updatePath();
	}

    function DeleteNode(){
        $templatecontainer = new RelTemplateContainer();
        $templatecontainer->deleteRel($this->nodeID);
        $mm = new MetadataManager($this->nodeID);
        $mm->deleteMetadata();
    }

	function RenameNode($name = null) {
		if(!$name) return false;
 		$listaDocs = $this->parent->GetChildren();
		if (sizeof($listaDocs) > 0) {
			foreach ($listaDocs as $docID) {
				$node = new Node($docID);
				$strdoc = new StructuredDocument($docID);
				$langId = $strdoc->GetLanguage();
				$lang = new Language($langId);
				$nameDoc = $name . "-id" . $lang->GetIsoName() ;
				$node->RenameNode($nameDoc);
			}
		}
        $this->updatePath();
	}

	function GetLanguages(){
		$node	 = new Node($this->nodeID);
 		$docList = $node->GetChildren();

		if ($node->HasError()){
			$this->parent->SetError(1);
			return;
	    }

		foreach ($docList as $docID){
			$strDoc = new StructuredDocument($docID);
			$langList[] = $strDoc->GetLanguage();
		}
		return $langList;
	}

	function GetChildByLang($langID){
		$node = new Node($this->nodeID);
 		$docList = $node->GetChildren();

		if ($node->HasError()){
			$this->parent->SetError(1);
			return;
		}

		foreach ($docList as $docID){
			$strDoc = new StructuredDocument($docID);
			$docLang = $strDoc->GetLanguage();
			if($docLang == $langID){
				return $docID;
            }
		}
		return null;
	}

	function ToXml($depth, & $files, $recurrence){
		$xml = '';
		$query = sprintf("SELECT IdTemplate FROM `RelTemplateContainer` WHERE IdContainer = %d",$this->parent->nodeID);
		$this->dbObj->Query($query);
		while (!$this->dbObj->EOF) {
			$idTemplate = $this->dbObj->GetValue('IdTemplate');
			if (!(int) $idTemplate > 0) continue;
			$template = new Node($idTemplate);
			$xml .= $template->ToXml($depth, $files, $recurrence);
			$this->dbObj->Next();
		}
		return $xml;
	}
}
?>
