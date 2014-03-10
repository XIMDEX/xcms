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

ModulesManager::file('/inc/model/RelNodeMetadata.class.php');
ModulesManager::file('/inc/model/RelNodeVersionMetadataVersion.class.php');
ModulesManager::file('/inc/io/BaseIOInferer.class.php');
ModulesManager::file('/inc/model/language.inc');
ModulesManager::file('/inc/model/channel.inc');
ModulesManager::file('/inc/model/node.inc');

/***
    Class for Metadata Manegement
*/
class MetadataManager{

    const IMAGE_METADATA_SCHEMA = "image-metadata.xml";
    const COMMON_METADATA_SCHEMA = "common-metadata.xml";
    const DOCUMENT_METADATA_SCHEMA = "document-metadata.xml";

    private $node;
    private $array_metadata;


        // constructor method //

    public function __construct($source_idnode){
            $this->node= new Node($source_idnode);
            $this->array_metadata=array();
    }

        // getters & setters methods //

/** 
 * Returns the source node id.
 * @param null
 * @return int
*/
    public function getSourceNode(){
        return $this->node;    
    }

/** 
 * Returns the last version of the associated metadata file for a given idnode or NULL if not exists
 * @param int $source_idnode
 * @return ...
*/
    public function getMetadataNodes(){
        return $this->array_metadata;    
    }

/** 
 * Returns the last version of the associated metadata file for a given idnode or NULL if not exists
 * @param int $source_idnode
 * @return ...
*/
    public function getLastMetadataVersion(){
        //TODO
    }


        // generator methods //

/** 
 * Returns the last version of the associated metadata file for a given idnode or NULL if not exists
 * @param int $source_idnode
 * @param string $field
 * @param string $value
 * @return ...
*/
    public function updateMetadata($field,$value){
        //TODO    
    }
    

    public function getMetadataSchema(){
        $node = new Node($this->node->GetID());
        $projectNode = new Node($node->getProject());
        $schemesFolder = $projectNode->getChildren(NodetypeService::TEMPLATE_VIEW_FOLDER);

        $nt = $this->node->nodeType->GetID(); 
        //TODO: switch
        $name = "image-metadata.xml";
        $schema = new Node();
        $res = $schema->find("Idnode","Name=%s AND IdParent=%s",array($name,$schemesFolder[0]),MONO);
        return $res[0];
    }
/** 
 * Main public function. Returns the last version of the associated metadata file for a given idnode or NULL if not exists
 * @param int $source_idnode
 * @return null
*/
    public function generateMetadata($lang = null){
        //create the specific name with the format: NODEID-metadata
        $name = $this->metadataFileName();
        //check if the metadata node already exists in metadata hidden folder
        $idContent = $this->getMetadataDocument($name);

        //If not exist already the metadata folder.
        if (empty($idContent)){
            $idm = $this->getMetadataSectionId();
            $aliases = array();
            $idSchema = $this->getMetadataSchema();

            if(!$lang){
                $languages = $this->getMetadataLanguages();
            }
            else{
                $languages = $lang;
            }
            $channels = $this->getMetadataChannels();
            //We use the action like a service layer
            $this->createXmlContainer($idm,$name,$idSchema,$aliases,$languages,$channels);
            $this->addRelation($name);
        }
        else{
            //$this->updateMetadata(); ¿?
            error_log("The metadata file $name already exists!");
        }
    }

/** 
 * Returns the name for the metadata file.
 * @param int $source_idnode
 * @return string
*/
    private function metadataFileName(){
        $idnode = $this->node->GetID();
        return $idnode."-metadata";
    }

/** 
 * Returns the id of a given metadata document name, if exists.
 * @param string $metafileName
 * @return int 
*/
    private function getMetadataDocument($metafileName){
        //getting the metadata folder id
        $idMetadataFolder = $this->getMetadataSectionId();
        $metadataFolder = new Node($idMetadataFolder);

        //getting the metadata document by name
        $id = $metadataFolder->GetChildByName($metafileName);
        return $id;
    }

/** 
 * Returns the id for the metadata section that is unique for each project on Ximdex CMS.
 * @param int $source_idnode
 * @return int 
*/
    private function getMetadataSectionId(){
        $idServer = $this->node->getServer();
        $nodeServer = new Node($idServer);
        $idSection = $nodeServer->GetChildByName("metadata");
        return $idSection;
    }

/** 
 * Returns an array of language identifiers.
 * @param int $source_idnode
 * @return array
*/
    public function getMetadataLanguages(){
        $result = array();
        $l = new Language();
        $arrayLanguagesObject = $l->getLanguagesForNode($this->node->getServer());
        foreach($arrayLanguagesObject as $languageObject){
            $result[] = $languageObject["IdLanguage"];
        }
        return $result;
    }
/** 
 * Returns all the channels defined on Ximdex CMS.
 * @param int $source_idnode
 * @return array
 * TODO: only return the associated channels.
*/
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
 * Most important method. Creates the structured documents.
 * @param int $idNode
 * @param string $name
 * @param int $idSchema
 * @param array $aliases
 * @param array $languages
 * @param array $channels
 * @return int
*/
    private function createXmlContainer($idNode,$name,$idschema,&$aliases,&$languages,&$channels, $master=null){

        $result = true;
        $node = new Node($idNode);
        $idNode = $node->get('IdNode');
        if (!($idNode > 0)) {
            error_log("An error ocurred estimating parent node, operation will be aborted, contact with your administrator");
            $values = array('name' => 'Unknown');
            $result["error"] = $values;
            return $result;
        }

        // Creating container
        $baseIoInferer = new BaseIOInferer();
        $inferedNodeType = $baseIoInferer->infereType('FOLDER', $idNode);
        $nodeType = new NodeType();
        $nodeType->SetByName($inferedNodeType['NODETYPENAME']);
        if (!($nodeType->get('IdNodeType') > 0)) {
            error_log("A nodetype could not be estimated to create the container folder, operation will be aborted, contact with your administrator");
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

        if (!($result > 0)) {
            error_log("An error ocurred creating the container node");
            $values = array(
            'idNode' => $idNode,
            'nodeName' => $name
            );
            $result["error"] = $values;
            return $result;
        } else {
            error_log("Container $name has been successfully created");
        }

        if ($result && is_array($languages)) {
            $baseIoInferer = new BaseIOInferer();
            $inferedNodeType = $baseIoInferer->infereType('FILE', $idContainer);
            $nodeType = new NodeType();
            $nodeType->SetByName($inferedNodeType['NODETYPENAME']);
            if (!($nodeType->get('IdNodeType') > 0)) {
                error_log("A nodetype could not be estimated to create the document, operation will be aborted, contact with your administrator");
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

/** 
 * Main public function. Returns the last version of the associated metadata file for a given idnode or NULL if not exists
 * @param int $source_idnode
 * @return ...
*/
    function _insertLanguage($idLanguage, $nodeTypeName, $name, $idContainer, $idTemplate, $formChannels, $aliases) {
        $language = new Language($idLanguage);
        if (!($language->get('IdLanguage') >  0)) {
            error_log("Language $idLanguage insertion has been aborted because it was not found");
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
            error_log("Document ".$insertedNode->get('Name')." has been successfully inserted");
        } else {
            error_log("Insertion of document $name with language ".$language->get('Name')." has failed");
        }
        return $result;

    }

    private function addRelation($name){
        $rnm = new RelNodeMetadata();
        $idm = $this->getMetadataDocument($name);
        //TODO: foreach language version, one entry
        $rnm->set('IdNode', $this->node->GetID());
        $rnm->set('IdMetadata', $idm);
        $res = $rnm->add();
        if($res<0){
            error_log("Relation betwween nodes not added.");
        }
        //TODO: move this logic to the RelNodeMetadata class
        else{
            //getting the source node's last version id
            $dtf = New DataFactory($this->node->GetID());
            $idNodeVersion = $dtf->GetLastVersionId();

            //getting all the language children
            $idmNode = new Node($idm);
            $metadocs = $idmNode->GetChildren();
            foreach($metadocs as $idMetadataLanguage){

                //getting the last version of each child.
                $dtf = New DataFactory($idMetadataLanguage);
                $idMetadataVersion = $dtf->GetLastVersionId();

                //adding the info
                $rnvmv = new RelNodeVersionMetadataVersion();
                $rnvmv->set('idrnm',$res);
                $rnvmv->set('idNodeVersion',$idNodeVersion);
                $rnvmv->set('idMetadataVersion',$idMetadataVersion);
                $res2 = $rnvmv->add();
                if($res<0){
                    error_log("Relation between versions not added.");
                }
            }
        }
    }

    public function deleteMetadata(){
        $name = $this->metadataFileName();
        $idContent = $this->getMetadataDocument($name);
        if ($idContent){
            $nodeContainer = new Node($idContent);
            $nodeContainer->DeleteNode();
        }
        $rnm = new RelNodeMetadata();
        $id=$rnm->find("idRel","IdMetadata=%s",array($idContent),MONO);
        $rnm->set('idRel', $id[0]);
        $res = $rnm->delete();
        if($res<0){
            error_log("Relation between nodes not deleted.");
        }
        else{
            $rnvmv = new RelNodeVersionMetadataVersion();
            $ids=$rnvmv->find("id","idrnm=%s",array($id[0]),MONO);
            foreach($ids as $id){
                $rnvmv->set('id', $id);
                $res2 = $rnvmv->delete();
                if($res2<0){
                    error_log("Relation between versions not deleted.");
                }
            }
        }
    }

}
?>
