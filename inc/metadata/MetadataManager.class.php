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

//ModulesManager::file('/inc/model/RelNodeMetadata.class.php');

/***
    Class for Metadata Manegement
*/
class MetadataManager{

    const IMAGE_METADATA_SCHEMA = "image-metadata.xml";
    const COMMON_METADATA_SCHEMA = "common-metadata.xml";
    const DOCUMENT_METADATA_SCHEMA = "document-metadata.xml";

/** 
 * Returns the last version of the associated metadata file for a given idnode or NULL if not exists
 * @param int $source_idnode
 * @return ...
*/
    public function getLastMetadataVersion($source_idnode){
        //TODO
    }

/** 
 * Returns the last version of the associated metadata file for a given idnode or NULL if not exists
 * @param int $source_idnode
 * @param string $field
 * @param string $value
 * @return ...
*/
    public function updateMetadata($source_idnode,$field,$value){
        //TODO    
    }
    
/** 
 * Main public function. Returns the last version of the associated metadata file for a given idnode or NULL if not exists
 * @param int $source_idnode
 * @return null
*/
    public function generateMetadata($source_idnode){
        //create the specific name with the format: NODEID-metadata
        $name = $this->metadataFileName($source_idnode);

        //check if the metadata node already exists in metadata hidden folder
        $idContent = $this->getMetadataDocument($name);

        //If not exist already the metadata folder.
        if (empty($idContent)){
            $idm = $this->getMetadataSectionId();
            $aliases = array();
            $schema=new Node();
            $idSchema =  $schema->find("Idnode","Name=%s",array(self::IMAGE_METADATA_SCHEMA),MONO);
            $languages = $this->getMetadataLanguages($source_idnode);
            $channels = $this->getMetadataChannels();
            //We use the action like a service layer
            $this->createXmlContainer($idm,$name,$idSchema[0],$aliases,$languages,$channels);
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
    private function metadataFileName($idnode){
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
    private function getMetadataSectionId($source_idnode){
        $node = new Node($source_idnode);
        $idServer = $node->parent->getServer();
        $nodeServer = new Node($idServer);
        $idSection = $nodeServer->GetChildByName("metadata");
        return $idSection;
    }

/** 
 * Returns an array of language identifiers.
 * @param int $source_idnode
 * @return array
*/
    public function getMetadataLanguages($source_idnode){
        $result = array();
        $node = new Node($source_idnode);
        $l = new Language();
        $arrayLanguagesObject = $l->getLanguagesForNode($node->parent->getServer());
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
 * Main public function. Returns the last version of the associated metadata file for a given idnode or NULL if not exists
 * @param int $source_idnode
 * @return ...
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

/** 
 * Main public function. Returns the last version of the associated metadata file for a given idnode or NULL if not exists
 * @param int $source_idnode
 * @return ...
*/
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
?>
