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

if (!defined('XIMDEX_ROOT_PATH')) {
	define ('XIMDEX_ROOT_PATH', realpath(dirname(__FILE__) . '/../../'));
}

require_once(XIMDEX_ROOT_PATH . "/inc/nodetypes/filenode.inc");
ModulesManager::file('/inc/io/BaseIOInferer.class.php');
ModulesManager::file('/inc/model/language.inc');
ModulesManager::file('/inc/model/channel.inc');
ModulesManager::file('/inc/model/node.inc');
ModulesManager::file('/inc/model/RelNodeMetadata.class.php');

/***
	Class for NodeType Image
*/
class ImageNode extends FileNode{

	const IMAGE_METADATA_SCHEMA = "image-metadata.xml";

	/**
	 * Build a new image node file.
	 * Use parent CreateNode method and generate a new metadata document for the new image node created.	
         * @return boolean true.
         */
	function CreateNode($name, $parentID, $nodeTypeID, $stateID=7, $sourcePath=""){
		parent::CreateNode($name, $parentID, $nodeTypeID, $stateID, $sourcePath);
		$this->generateMetadata();
	}	

	/** 
         * Build a metadata container for the current image.
	 * @return 
        */
        public function generateMetadata(){
                //create the specific name for this node: NODEID-metadata
                $name = $this->getMetadataFileName();

		//check if this node already exists
                $idContent = $this->getMetadataContainer($name);

                //If not exist already the metadata container.
                if (empty($idContent)){  
                	$idm = $this->getMetadataSectionId();
                        $aliases = array();
			$image_schema=new Node();
			$idSchema =  $image_schema->find("Idnode","Name=%s",array(self::IMAGE_METADATA_SCHEMA),MONO);
                        $languages = $this->getLanguages();
                        $channels = $this->getMetadataChannels();
                        //We use the action like a service layer
                        $this->createXmlContainer($idm,$name,$idSchema[0],$aliases,$languages,$channels);
                        $this->addRelation($name);
                }    
		else{
			error_log("The metadata file $name already exists!");
		}
        }

	/**
	 * Build a new name for the new node file.
         * @return string.
         */
	private function getMetadataFileName(){
                return $this->nodeID."-metadata";
        }

	/**
	 * Get XmlContainer with name $contentName
	 * @return int
	*/
	private function getMetadataContainer($contentName){
		//Gets ximdoc folder id
            	$idOpenDataContainer = $this->getMetadataSectionId();
            	$openDataContainer = new Node($idOpenDataContainer);
		//Get xmlcontainer by name
            	$idContent = $openDataContainer->GetChildByName($contentName);
		return $idContent;
	}

	/**
	 * Delete image node and the metadata asociated.
	*/
	function DeleteNode (){
		parent::DeleteNode();
		$contentName = $this->getMetadataFileName();
		$idContent = $this->getMetadataContainer($contentName);
		if ($idContent){
			$nodeContainer = new Node($idContent);
			$nodeContainer->DeleteNode();
		}
		$rnm = new RelNodeMetadata();
		$id=$rnm->find("id","target=%s",array($idContent),MONO);		
		$rnm->set('id', $id[0]);
                $res = $rnm->delete();
                if($res<0){
                        error_log("Relation not deleted.");
                }
	}	

	/**
	 * Add a new row on table RelNodeMetadata.
	*/
	//TODO: this will be done in RelNodeMetadata model
	private function addRelation($name){
		$rnm = new RelNodeMetadata();
		$idimage_metadata = $this->getMetadataContainer($name);
		$rnm->set('source', $this->nodeID);
                $rnm->set('target', $idimage_metadata);
		$res = $rnm->add();
		if($res<0){
			error_log("Relation not added.");
		}
	}

	private function getMetadataSectionId(){
		$idServer = $this->parent->getServer();
		$nodeServer = new Node($idServer);
		$idSection = $nodeServer->GetChildByName("metadata");
		return $idSection;
	}

	public function getLanguages(){

		$result = array();
		$language = new Language();
		$arrayLanguagesObject = $language->getLanguagesForNode($this->parent->getServer());
		foreach($arrayLanguagesObject as $languageObject){
			$result[] = $languageObject["IdLanguage"];
		}
		return $result;
	}

	//TODO: this will be done in RelNodeMetadata model
	public function getRelatedDocument($language=""){

		$contentName = $this->getMetadataFileName();
		$idContent = $this->getMetadataContainer($contentName);
		if (!$language || $language==""){
			return $idContent;
		}else{
			$content=new Node($idContent);
			$idDoc = $content->GetChildByName($contentName."-id".$language);
			return $idDoc;
		}

	}

	public function getMetadataChannels(){
		$result = array();
		$channel = new Channel();
		$channels = $channel->GetAllChannels();
		foreach($channels as $idChannel){
			$auxChannel = new Channel($idChannel);
			switch ($auxChannel->GetName()){
				case "solr":
				case "web":
				case "html":
					$result[] = $idChannel;
					break;
			}
		}
		return $result;
	}


 /**
	Build a XmlContainer
    */
    private function createXmlContainer($idNode,$name,$idschema,&$aliases,&$languages,&$channels, $master=null){

	$result = true;
    	$node = new Node($idNode);
    	$idNode = $node->get('IdNode');
    	if (!($idNode > 0)) {
    		$this->messages->add(_('An error ocurred estimating parent node,')
    			._(' operation will be aborted, contact with your administrator'), MSG_TYPE_ERROR);
    		$values = array('name' => 'Desconocido',
    			'messages' => $this->messages->messages);
		$result["error"] = $values;
    		return $result;
    	}

    	
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
		        	array('NODETYPENAME' => 'VISUALTEMPLATE', 'ID' => $idschema)
		        )
        );
        $baseIO = new baseIO();
        $idContainer = $result = $baseIO->build($data);

//	$this->reloadNode($idNode);

        if (!($result > 0)) {
        	$this->messages->add(_('An error ocurred creating the container node'), MSG_TYPE_ERROR);
        	foreach ($baseIO->messages->messages as $message) {
        		$this->messages->messages[] = $message;
        	}
        	$values = array(
				'idNode' => $idNode,
				'nodeName' => $name,
				'messages' => $this->messages->messages
        	);
		$result["error"] = $values;
        	return $result;
        } else {
        	$this->messages->add(sprintf(_('Container %s has been successfully created'), $name), MSG_TYPE_NOTICE);
        }
        

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


			foreach ($channels as $idChannel) {
				$formChannels[] = array('NODETYPENAME' => 'CHANNEL', 'ID' => $idChannel);
			}

			// structureddocument inserts content document
			$setSymLinks = array();

			foreach ($languages as $idLanguage) {
				$result = $this->_insertLanguage($idLanguage, $nodeType->get('Name'), $name, $idContainer, $idschema, 
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

		return true;
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
			'ALIASNAME' => (isset($aliases[$idLanguage]))?$aliases[$idLanguage]:'',
			'CHILDRENS' => array (
				array ("NODETYPENAME" => "VISUALTEMPLATE", "ID" => $idTemplate),
				array ("NODETYPENAME" => "LANGUAGE", "ID" => $idLanguage)
			)
		);

		foreach ($formChannels as $channel) {
			$data['CHILDRENS'][] = $channel;
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
