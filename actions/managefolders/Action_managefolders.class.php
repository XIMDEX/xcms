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

class Action_managefolders extends ActionAbstract {

	/** 
	* Main function 
	*
	* Load the manage folders form. 
	*
	* Request params: 
 	*  
	* * nodeid 
	* 
 	*/ 
    	function index () {
		$this->addCss('/actions/addsectionnode/resources/css/style.css');
		$selectedFolders=array();
		$nodeID = $this->request->getParam("nodeid");
		$node = new Node($nodeID);

		$nodeType = new NodeType($node->GetNodeType());
                $nodetype_name = $nodeType->get('Name');
		$selectedFolders = $this->_getChildrenNodeTypes($nodeID);		

		$subfolders=$this->_getAvailableSubfolders($node->GetNodeType());

		foreach($subfolders as $nt => $folder){
			if(!empty($selectedFolders) && in_array($nt,$selectedFolders)){
				$subfolders[$nt][]='selected';
			}else{
				$subfolders[$nt][]='unselected';
			}
		}
		
		$this->addJs('/actions/managefolders/resources/js/index.js');

        	$values = array('nodeID' => $nodeID,
				'sectionName' => $node->get('Name'),
				'sectionType' => $nodetype_name,
				'subfolders' => $subfolders,
				'go_method' => 'configure_section',
				);
		$this->render($values, null, 'default-3.0.tpl');
    	}
    
	/** 
        * Data processing function 
        *
        * Performs the actions depending on the users choices on the form. 
        *
        * Request params: 
        *  
        * * nodeid: section ID. 
        * * folderlst: list of the selected folders by the user. 
        * 
        */
    	function configure_section() {
		$error=false;
		$folderlst = $this->request->getParam('folderlst');
	   	$nodeID = $this->request->getParam('nodeid');
		$parent = new Node($nodeID);
				
		$existingChildren = $this->_getChildrenNodeTypes($nodeID);
		$addfolders=false;
		if(count($folderlst)>count($existingChildren)){
			$addfolders=true;
			//If the user wants to create all the containing folders.
			if(empty($existingChildren)){
				$existingChildren=$folderlst;
			}else{
				$folderlst=array_diff($folderlst,$existingChildren);
			}
		}
		else{
			//If the user wants to delete all the containing folders.
			if(empty($folderlst)){
				$folderlst=$existingChildren;
			}else{
				$folderlst=array_diff($existingChildren,$folderlst);		
			}
		}

		//Only creating the new folders selected.
		if($addfolders){
			foreach($folderlst as $folderNt){
				$folder = new Node();
				$ndc = new NodeDefaultContents();
				$name=$ndc->getDefaultName($folderNt);
        	        	$idFolder = $folder->CreateNode($name, $nodeID, $folderNt, null);
				if(!$idFolder){
					$error=true;
					break;
				}
			}
		}
		else{
			foreach($folderlst as $folderNt){
                                $ndc = new NodeDefaultContents();
                                $name=$ndc->getDefaultName($folderNt);

				$nodeid = $parent->GetChildByName($name);
				$deleteFolder = new Node($nodeid);

				$res = $deleteFolder->DeleteNode();
				if(!$res){
					$error=true;
					break;
				}
			}
		}

		$this->reloadNode($nodeID);

		if ($error) {
			$this->messages->add(_('This operation could not be successfully completed.'), MSG_TYPE_ERROR);
		}else{
			$this->messages->add(_('This section has been successfully configured.'), MSG_TYPE_NOTICE);
		}
		
		$values = array(
			'action_with_no_return' => !$error,
			'messages' => $this->messages->messages
		);
		
		$this->render($values, NULL, 'messages.tpl');
    	}
    
	/** 
        * Getting all the children folders. 
        *
        * Using the NodeDefaultContents of the data model, returns all the avaliable children folders 
	* with a description for a given nodetype. 
        *
        * Request params: 
        *  
        * * nodetype_sec: noderype ID for the containing folder. 
        * 
        */
	private function _getAvailableSubfolders($nodetype_sec){
		$subfolders=array();
		$res=array();
		$ndc = new NodeDefaultContents();

		$subfolders=$ndc->getDefaultChilds($nodetype_sec);
		foreach($subfolders as $subfolder){
			$nt=$subfolder["NodeType"];
			$res[$nt][0]=$subfolder["Name"];	
			$res[$nt][1]=$this->_getDescription($nt);	
		}
		asort($res);	
		return $res;
	}

	/** 
        * Human readable descriptions for subfolders
        *
        * Returns a proper description for the given nodetype, helping the user to decide if the folder is needed or not. 
        *
        * Request params: 
        *  
        * * nodetype: nodetype ID. 
        * 
        */
	private function _getDescription($nodetype){
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

	/** 
        * Nodetypes for subfolders.
        *       
        * Returns an array of nodetype for all the children of the given parent node ID. 
        *
        * Request params: 
        *  
        * * idParent: Parent node ID. 
        * 
        */
	private function _getChildrenNodeTypes($idParent){
		$children_nt=array();
		$parentNode = new Node($idParent);
		$children = $parentNode -> GetChildren();		

		if(!empty($children)){
			foreach($children as $child){
				$ch = new Node ($child);
				$children_nt[]=$ch->GetNodeType();
			}
		}
		return $children_nt;
	}
}

?>
