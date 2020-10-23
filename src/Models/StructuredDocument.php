<?php

/**
 *  \details &copy; 2019 Open Ximdex Evolution SL [http://www.ximdex.org]
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
 * @author Ximdex DevTeam <dev@ximdex.com>
 * @version $Revision$
 */

namespace Ximdex\Models;

use Ximdex\Logger;
use Ximdex\Utils\FsUtils;
use Ximdex\Parsers\ParsingRng;
use Ximdex\XML\Validators\RNG;
use Ximdex\Runtime\DataFactory;
use Ximdex\NodeTypes\XmlContainerNode;
use Ximdex\Properties\ChannelProperty;
use Ximdex\NodeTypes\NodeTypeConstants;
use Ximdex\Parsers\ParsingDependencies;
use Ximdex\Models\ORM\StructuredDocumentsOrm;

class StructuredDocument extends StructuredDocumentsOrm
{
    public $ID;
    
    public $flagErr;
    
    public $numErr;
    
    public $msgErr;
    
    public $errorList = array(
        1 => 'Error while connecting with the database',
        2 => 'The structured document does not exist',
        3 => 'Not implemented yet',
        4 => 'A document cannot be linked to itself'
    );

    public function __construct(int $id = null)
    {
        $this->ID = $id;
        $this->flagErr = false;
        $this->autoCleanErr = true;
        parent::__construct($id);
    }

    /**
     * Devuelve un array con los ids de todos los structured documents del sistema
     *
     * @return NULL|array
     */
    public function getAllStructuredDocuments()
    {
        $sql = "SELECT idDoc FROM StructuredDocuments";
        $dbObj = new \Ximdex\Runtime\Db();
        $dbObj->query($sql);
        if ($dbObj->numErr != 0) {
            $this->setError(1);
            return null;
        }
        $salida = [];
        while (! $dbObj->EOF) {
            $salida[] = $dbObj->row["idDoc"];
            $dbObj->next();
        }
        return $salida;
    }

    /**
     * Devuelve el id del structure document actual
     *
     * @return boolean|string
     */
    public function getID()
    {
        return $this->get('IdDoc');
    }

    /**
     * Cambia el id del structure document actual
     *
     * @param int $docID
     * @return NULL|boolean|string
     */
    public function setID(int $docID)
    {
        self::__construct($docID);
        if (! $this->get('IdDoc')) {
            $this->setError(2);
            return null;
        }
        return $this->get('IdDoc');
    }

    /**
     * Devuelve el nombre del structure document actual
     * 
     * @return boolean|string
     */
    public function getName()
    {
        return $this->get("Name");
    }

    /**
     * Cambia el nombre del structure document actual
     * 
     * @param string $name
     * @return boolean|string|NULL
     */
    public function setName(string $name)
    {
        if (! $this->get('IdDoc')) {
            $this->setError(2);
            return false;
        }
        $result = $this->set('Name', $name);
        if ($result) {
            return $this->update();
        }
        return false;
    }

    /**
     * Devuelve el creador del structure document actual
     * 
     * @return boolean|string
     */
    public function getCreator()
    {
        return $this->get("IdCreator");
    }

    /**
     * Cambia el creador del structure document actual
     * 
     * @param int $IdCreator
     * @return boolean|string|NULL
     */
    public function setCreator(int $IdCreator)
    {
        if (! $this->get('IdDoc')) {
            $this->setError(2);
            return false;
        }
        $result = $this->set('IdCreator', $IdCreator);
        if ($result) {
            return $this->update();
        }
        return false;
    }

    /**
     * Devuelve el lenguaje del structure document actual
     * 
     * @return int|NULL
     */
    public function getLanguage() : ?int
    {
        return (int) $this->get("IdLanguage");
    }

    /**
     * Cambia el lenguaje del structure document actual
     * 
     * @param int $IdLanguage
     * @return boolean|string|NULL
     */
    public function setLanguage(int $IdLanguage)
    {
        if (! $this->get('IdDoc')) {
            $this->setError(2);
            return false;
        }
        $result = $this->set('IdLanguage', $IdLanguage);
        if ($result) {
            return $this->update();
        }
        return false;
    }

    public function getDocumentType()
    {
        return (int) $this->get("IdTemplate");
    }

    public function setDocumentType(int $templateID)
    {
        if (! $this->get('IdDoc')) {
            $this->setError(2);
            return false;
        }
        $result = $this->set('IdTemplate', $templateID);
        if ($result) {
            return $this->update();
        }
        return false;
    }

    public function getSymLink()
    {
        return $this->get("TargetLink");
    }

    public function setSymLink(int $docID)
    {
        if (! $this->get('IdDoc')) {
            $this->setError(2);
            return false;
        }
        if ($docID != $this->get('IdDoc')) {
            $result = $this->set('TargetLink', $docID);
            if ($result) {
                $this->update();
            }
            $dependencies = new Dependencies();
            $dependencies->insertDependence($docID, $this->get('IdDoc'), 'SYMLINK', $this->getLastVersion());
            return true;
        }
        $this->setError(4);
        return false;
    }

    public function clearSymLink()
    {
        if (! $this->get('IdDoc')) {
            $this->setError(2);
            return false;
        }
        $result = $this->set('TargetLink', null);
        if ($result) {
            $result = $this->update();

            // Elimina la dependencia
            $dependencies = new Dependencies();
            $dependencies->deleteDependenciesByDependentAndType($this->get('IdDoc'), 'SYMLINK');
            return $result;
        }
        return false;
    }

    /**
     * Devuelve el contenido xml del structure document actual
     * 
     * @param int $version
     * @param int $subversion
     * @return string|boolean
     */
    public function getContent(int $version = null, int $subversion = null)
    {
        $targetLink = $this->getSymLink();
        if ($targetLink) {
            $target = new StructuredDocument($targetLink);
            $targetContent = $target->getContent();
            $targetLang = $target->getLanguage();
            $targetContent = preg_replace('/ a_enlaceid([A-Za-z0-9|\_]+)\s*=\s*\"([^\"]+)\"/i', "' a_enlaceid\\1=\"'.\$this->UpdateLinkParseLink($targetLang , '\\2').'\"'", $targetContent);
            $targetContent = preg_replace('/ a_import_enlaceid([A-Za-z0-9|\_]+)\s*=\s*\"([^\"]+)\"/i', "' a_import_enlaceid\\1=\"'.\$this->UpdateLinkParseLink($targetLang , '\\2').'\"'", $targetContent);
            $targetContent = preg_replace('/<url>\s*([^\<]+)\s*<\/url>/i', "'<url>'.\$this->UpdateLinkParseLink($targetLang , '\\1').'</url>'", $targetContent);
            return $targetContent;
        }
        $data = new DataFactory($this->get('IdDoc'));
        $content = $data->getContent($version, $subversion);
        return $content;
    }

    /**
     * Return the content of metadata from current structure document
     *
     * @return array
     */
    public function getMetadata() : array
    {
        $node = new Node($this->getID());
        return (new Metadata)->getMetadataSchemeAndGroupByNodeType($node->getNodeType(), $this->getID());
    }

    public function setMetadata(array $metadata) : array
    {
        $result = [];
        $metadataClass = new Metadata();
        foreach ($metadata as $group => $meta) {
            $metadataClass->deleteMetadataValuesByNodeIdAndGroupId($this->getID(), $group);
            $val = $metadataClass->addMetadataValuesByNodeId($meta, $this->getID());
            if ($val) {
                $result[$group] = $meta;
            }
        }
        return $result;
    }

    public function UpdateLinkParseLink(int $sourceLang, int $linkID)
    {
        $pos = strpos($linkID, ",");
        if ($pos != false) {
            $linkID = substr($linkID, 0, $pos);
        }
        $node = new Node($linkID);
        if (($node->get('IdNode') > 0) && ($node->nodeType->get('Name') != "XmlDocument")) {
            return $linkID;
        }
        $linkDoc = new StructuredDocument($linkID);
        if ($linkDoc->getLanguage() != $sourceLang) {
            return $linkID;
        }
        $node->setID($node->getParent());
        if ($node->nodeType->get('Name') != "XmlContainer") {
            return $linkID;
        }
        $sibling = $node->class->getChildByLang($this->getLanguage());
        if ($sibling) {
            return $sibling;
        }
        return $linkID;
    }

    public function setContent(string $content, bool $commitNode = false, array $metadata = null) : bool
    {
        // Refrescamos la fecha de Actualizacion del nodo
        $this->setUpdateDate();
        $data = new DataFactory($this->get('IdDoc'));
        $node = new Node($this->get('IdDoc'));
        $version = $subversion = null;
        if (! $commitNode) {
            $info = $node->getLastVersion();
            $version = $info['Version'];
            $subversion = $info['SubVersion'];
        }
        $res = $data->setContent($content, $version, $subversion, $commitNode);
        if ($res && is_array($metadata) && count($metadata) > 0) {
            $this->setMetadata($metadata);
        }

        // The document will be validate against the associated RNG schema with XML documents
        if ($node->getNodeType() == NodeTypeConstants::XML_DOCUMENT) {
            $this->validate_schema($node);
        }

        // Check possible errors
        if ($res === false) {
            if ($data->msgErr) {
                $this->messages->add($data->msgErr, MSG_TYPE_ERROR);
                return false;
            }
            if (! $this->messages->count()) {
                if (isset($GLOBALS['errorsInXslTransformation']) and $GLOBALS['errorsInXslTransformation']) {
                    $this->messages->add($GLOBALS['errorsInXslTransformation'][0], MSG_TYPE_WARNING);
                    $GLOBALS['errorsInXslTransformation'] = null;
                }
            }
        }

        // Set dependencies
        $dependeciesParser = new ParsingDependencies();
        if ($dependeciesParser->parseAllDependencies($this->get('IdDoc'), $content) === false) {
            if ($dependeciesParser->messages) {
                $this->messages->mergeMessages($dependeciesParser->messages);
            }
        }

        // Renderizamos el nodo para reflejar los cambios
        $node = new Node($this->get('IdDoc'));
        if ($node->renderizeNode() === false) {
            $this->messages->mergeMessages($node->messages);
            return false;
        }
        return true;
    }

    /**
     * Return the object node for the document node specified
     *
     * @param Node $node
     * @return Node
     */
    private function get_schema_node(Node $node): Node
    {
        $nodeTypeName = $node->nodeType->GetName();
        if ($nodeTypeName == 'RngVisualTemplate') {
            $rngPath = APP_ROOT_PATH . '/actions/xmleditor2/views/rngeditor/schema/rng-schema.xml';
            return trim(FsUtils::file_get_contents($rngPath));
        }
        $idContainer = $node->getParent();
        if (! $idContainer) {
            return null;
        }
        $relTemplate = new RelTemplateContainer();
        $idTemplate = $relTemplate->getTemplate($idContainer);
        if (! $idTemplate) {
            return null;
        }
        $templateNode = new Node($idTemplate);
        if (! $templateNode->getID()) {
            return null;
        }
        return $templateNode;
    }

    /**
     * Get the schema data for a document node given
     *
     * @param Node $docNode
     * @return array
     */
    private function get_schema_data(Node $docNode): array
    {
        $schemaData = [];
        if (! is_object($templateNode = $this->get_schema_node($docNode))) {
            return array(
                'id' => 0,
                'content' => $templateNode
            );
        }
        $schemaId = $templateNode->getID();
        if (! $schemaId) {
            return null;
        }
        $rngTemplate = new Node($schemaId);
        $content = $rngTemplate->getContent();
        $schemaData['id'] = $schemaId;
        $schemaData['content'] = $content;
        return $schemaData;
    }

    /**
     * Get the content of the schema file associated to a specified document
     *
     * @param Node $docNode
     * @return string
     */
    private function get_schema_file(Node $docNode): string
    {
        $schemaData = $this->get_schema_data($docNode);
        $content = $schemaData['content'];
        $schema = FsUtils::file_get_contents(APP_ROOT_PATH . '/actions/xmleditor2/views/common/schema/relaxng-1.0.rng.xml');
        $rngValidator = new RNG();
        $rngValidator->validate($schema, $content);
        if ($errors = $rngValidator->getErrors()) {
            foreach ($errors as $error) {
                $this->messages->add('Error in the associated RNG schema: ' . $error, MSG_TYPE_WARNING);
            }
            return null;
        }
        $content = preg_replace('/xmlns:xim="([^"]*)"/', sprintf('xmlns:xim="%s"', ParsingRng::XMLNS_XIM), $content);
        return $content;
    }

    /**
     * Validate a structured document against its own RNG schema
     *
     * @param Node $docNode
     * @return bool
     */
    public function validate_schema(Node $docNode): bool
    {
        $schema = $this->get_schema_file($docNode);
        if ($schema === null) {
            return false;
        }
        $xmlDoc = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL . '<docxap>' . PHP_EOL
            . \Ximdex\Utils\Strings::stripslashes($docNode->getContent()) . PHP_EOL . '</docxap>';
        $rngValidator = new RNG();
        $valid = $rngValidator->validate($schema, $xmlDoc);
        if (! $valid) {
            foreach ($rngValidator->getErrors() as $error) {
                $this->messages->add('Error parsing with the RNG schema: ' . $error, MSG_TYPE_WARNING);
            }
            return false;
        }
        return true;
    }

    /**
     * Devuelve el timestamp de creacion del structure document actual
     * 
     * @return boolean|string
     */
    public function getCreationDate()
    {
        return $this->get("CreationDate");
    }

    /**
     * Devuelve el timestamp de modificacion del structure document actual.
     * 
     * @return boolean|string
     */
    public function getUpdateDate()
    {
        return $this->get("UpdateDate");
    }

    /**
     * Cambia el UpdateDate del structure document actual.
     * 
     * @return boolean|string|NULL
     */
    public function setUpdateDate()
    {
        if (! $this->get('IdDoc')) {
            $this->setError(2);
            return false;
        }
        $result = $this->set('UpdateDate', date('Y/m/d H:i:s'));
        if ($result) {
            return $this->update();
        }
        return false;
    }

    /**
     * Return true if the specified channel ID is in the node's properties
     *
     * @param int $idChannel
     * @return boolean
     */
    public function hasChannel(int $idChannel)
    {
        $values = $this->getChannels();
        if ($values === false) {
            return false;
        }
        if (isset($values[$idChannel])) {
            return true;
        }
        return false;
    }

    /**
     * Return an array with all the channels ID for the current node
     *
     * @return array|bool
     */
    public function getChannels()
    {
        $channelProperty = new ChannelProperty($this->get('IdDoc'));
        $values = $channelProperty->getValues(true);
        if ($values === false) {
            return false;
        }
        $res = [];
        foreach ($values as $channel) {
            $res[$channel['Id']] = $channel['Id'];
        }
        return $res;
    }

    /**
     * {@inheritDoc}
     * @see \Ximdex\Data\GenericData::add()
     */
    public function add(bool $useAutoIncrement = true)
    {
        $this->createNewStrDoc($this->get('IdDoc'), $this->get('Name'), $this->get('IdCreator'), $this->get('CreationDate')
            , $this->get('UpdateDate'), $this->get('IdLanguage'), $this->get('IdTemplate'));
    }

    /**
     * Crea un nuevo structure document y carga su id en el docID de la clase
     * return docID - lo carga como atributo
     * 
     * @param int $docID
     * @param string $name
     * @param int $IdCreator
     * @param int $IdLanguage
     * @param int $templateID
     * @param array $IdChannelList
     * @param string $content
     */
    public function createNewStrDoc(int $docID, string $name, ?int $IdCreator, int $IdLanguage, int $templateID, array $IdChannelList = null
        , string $content = '')
    {
        $this->set('Name', $name);
        $this->set('IdCreator', $IdCreator);
        $now = date('Y/m/d H:i:s');
        $this->set('CreationDate', $now);
        $this->set('UpdateDate', $now);
        $this->set('IdLanguage', $IdLanguage);
        $this->set('IdTemplate', $templateID);
        if ($docID) {
            $this->set('IdDoc', $docID);
        }
        parent::add();
        if ($this->get('IdDoc') > 0) {
            $this->ID = $docID;

            // Guardamos su contenido
            $this->setContent($content);
        } else {
            $this->setError(1);
        }
    }

    public function delete()
    {
        parent::delete();
        $this->ID = null;
    }

    public function GetLastVersion()
    {
        $sql = sprintf("select max(Version) as UltimaVersion from Versions where IdNode=%d", $this->get('IdDoc'));
        $dbObj = new \Ximdex\Runtime\Db();
        $dbObj->Query($sql);
        if ($dbObj->numErr != 0) {
            $this->SetError(1);
        } else {
            $salida = null;
            while (! $dbObj->EOF) {
                $salida = $dbObj->GetValue("UltimaVersion");
                $dbObj->Next();
            }
            return $salida;
        }
        return null;
    }

    public function isximletlink()
    {
        $sql = sprintf("select IdNodeDependent from Dependencies WHERE IdNodeMaster = %d and DepType='LINK'", $this->get('IdDoc'));
        $dbObj = new \Ximdex\Runtime\Db();
        $dbObj->Query($sql);
        if ($dbObj->numErr != 0) {
            $this->SetError(1);
        } else {
            $salida = null;
            $links = [];
            while (!$dbObj->EOF) {
                $links[] = $dbObj->GetValue("IdNodeDependent");
                $dbObj->Next();
            }
            if (is_array($links)) {
                foreach ($links as $link) {
                    $node_ximlet = new Node($link);
                    $node_type = new NodeType($node_ximlet->GetNodeType());

                    if ($node_type->GetName() == 'Ximlet') {
                        $salida[] = $link;
                    }
                }
            }
            return $salida;
        }
        return null;
    }

    public function ximletLinks(int $ximletID, int $nodeID)
    {
        $sql = sprintf("SELECT IdNodeMaster FROM Dependencies WHERE IdNodeDependent= %d AND DepType='LINK' AND IdNodeMaster!= %d"
            , $ximletID, $nodeID);
        $dbObj = new \Ximdex\Runtime\Db();
        $dbObj->Query($sql);
        if ($dbObj->numErr != 0) {
            $this->SetError(1);
        } else {
            $link = null;
            while (! $dbObj->EOF) {
                $link[] = $dbObj->GetValue("IdNodeMaster");
                $dbObj->Next();
            }
            return $link;
        }
        return null;
    }

    // limpia el ultimo error
    public function ClearError()
    {
        $this->flagErr = false;
    }

    public function SetAutoCleanOn()
    {
        $this->autoCleanErr = true;
    }

    public function SetAutoCleanOff()
    {
        $this->autoCleanErr = false;
    }

    // Carga un error en la clase
    public function setError(int $code)
    {
        $this->flagErr = true;
        $this->numErr = $code;
        $this->msgErr = $this->errorList[$code];
    }

    // devuelve true si en la clase se ha producido un error
    public function hasError()
    {
        $aux = $this->flagErr;
        if ($this->autoCleanErr) {
            $this->ClearError();
        }
        return $aux;
    }

    public function getXsltErrors()
    {
        return $this->get('XsltErrors');
    }

    public function setXsltErrors(string $xsltErrors)
    {
        if (! $this->get('IdDoc')) {
            $this->setError(2);
            return false;
        }
        $result = $this->set('XsltErrors', $xsltErrors);
        if ($result) {
            return $this->update();
        }
        return false;
    }

    /**
     * Get the include HTML document for the given name provided
     *
     * @param string $include
     * @return boolean| StructuredDocument
     */
    public function getInclude(string $include)
    {
        if (! $this->getID()) {
            $this->messages->add('The structured document has not been loaded', MSG_TYPE_ERROR);
            return false;
        }

        // Load parent nodes
        $parents = FastTraverse::getParents($this->getID(), 'IdNodeType', 'ft.IdNode');
        if ($parents === false) {
            Logger::error('An error ocurred getting the parent nodes for the document with node ID: ' . $this->getID());
            return false;
        }
        foreach ($parents as $nodeID => $nodeTypeID) {
            switch ($nodeTypeID) {
                case NodeTypeConstants::SERVER:
                case NodeTypeConstants::SECTION:

                    // Load the node for the section, or server ID given
                    $node = new Node($nodeID);
                    if (! $node->GetID()) {
                        $this->messages->add('Cannot load a node with the ID: ' . $nodeID, MSG_TYPE_ERROR);
                        return false;
                    }

                    // Load the documents root folder inside the previous node, if it exists
                    $documentsFolder = new Node($node->getChildByType(NodeTypeConstants::XML_ROOT_FOLDER));
                    if (! $documentsFolder->getID()) {
                        continue 2;
                    }

                    // Load the correspondant documents container folder for the include string given
                    $documentFolder = new Node($documentsFolder->getChildByName($include));
                    if (! $documentFolder->getID()) {
                        continue 2;
                    }

                    // Create an instance of the XML container handler
                    $xmlContainer = new XmlContainerNode($documentFolder->getID());

                    // Load the document for the required language
                    $includeDocId = $xmlContainer->getChildByLang($this->getLanguage());
                    if (! $includeDocId) {
                        
                        // Try to load the included node for default project language
                        $language = $this->getLanguage();
                        $nodeProperty = new NodeProperty();
                        if ($property = $nodeProperty->getProperty($documentFolder->getServer(), NodeProperty::DEFAULTSERVERLANGUAGE)) {
                            if ($language != $property[0]) {
                                $language = $property[0];
                                if ($includeDocId = $xmlContainer->getChildByLang($language)) {
                                    $language = null;
                                }
                            }
                        }
                        if ($language) {
                            $this->messages->add('Cannot load the document for include: ' . $include . ' and language: ' . $language
                                , MSG_TYPE_ERROR);
                            return false;
                        }
                    }
                    $includeDoc = new StructuredDocument($includeDocId);
                    if (! $includeDoc->getID()) {
                        $this->messages->add('Cannot load the include document for ID: ' . $includeDocId . ' (name: ' . $include . ')'
                            , MSG_TYPE_ERROR);
                        return false;
                    }
                    return $includeDoc;
            }
        }
        $this->messages->add('Cannot load the include document with name: ' . $include, MSG_TYPE_ERROR);
        return false;
    }

    /**
     * Get correspondant layout template to the current document
     *
     * @return boolean|\Ximdex\Models\Node
     */
    public function getLayout()
    {
        if (! $this->getID()) {
            $this->messages->add('The structured document has not been loaded', MSG_TYPE_ERROR);
            return false;
        }
        if (! $this->get('IdTemplate')) {
            $this->messages->add('There is not defined a layout ID in the document', MSG_TYPE_ERROR);
            return false;
        }
        $layout = new Node($this->get('IdTemplate'));
        if (! $layout->getID()) {
            $this->messages->add('The layout with ID: ' . $layout->getID() . ' does not exist', MSG_TYPE_ERROR);
            return false;
        }
        return $layout;
    }

    /**
     * Get a HTML component by name into the nearest components folder for the current document
     *
     * @param string $component
     * @return boolean|\Ximdex\Models\Node
     */
    public function getComponent(string $component)
    {
        if (! $this->GetID()) {
            $this->messages->add('The structured document has not been loaded', MSG_TYPE_ERROR);
            return false;
        }

        // Load parent nodes
        $parents = FastTraverse::getParents($this->GetID(), 'IdNodeType', 'ft.IdNode');
        if ($parents === false) {
            Logger::error('An error ocurred getting the parent nodes for the document with node ID: ' . $this->GetID());
            return false;
        }
        foreach ($parents as $nodeID => $nodeTypeID) {
            switch ($nodeTypeID) {
                case NodeTypeConstants::PROJECT:
                case NodeTypeConstants::XLMS_PROJECT:
                case NodeTypeConstants::SERVER:
                case NodeTypeConstants::SECTION:

                    // Load the node for the section, server or project ID given
                    $node = new Node($nodeID);
                    if (! $node->GetID()) {
                        $this->messages->add('Cannot load a node with the ID: ' . $nodeID, MSG_TYPE_ERROR);
                        return false;
                    }

                    // Load the layouts root folder inside the previous node, if it exists
                    $layoutsFolder = new Node($node->GetChildByType(NodeTypeConstants::HTML_LAYOUT_FOLDER));
                    if (! $layoutsFolder->getID()) {
                        continue 2;
                    }

                    // Load the components folder
                    $componentsFolder = new Node($layoutsFolder->GetChildByType(NodeTypeConstants::HTML_COMPONENTS_FOLDER));
                    if (! $componentsFolder->GetID()) {
                        $this->messages->add('Cannot load the components folder for the layout folder with ID: '
                            . $layoutsFolder->GetID(), MSG_TYPE_ERROR);
                        return false;
                    }

                    // Load the component by the name given and json extension
                    $componentNode = new Node($componentsFolder->GetChildByName($component . '.json'));
                    if (! $componentNode->GetID()) {
                        continue 2;
                    }
                    return $componentNode;
            }
        }
        $this->messages->add('Cannot load the component with name: ' . $component . '.json', MSG_TYPE_ERROR);
        return false;
    }

    /**
     * Get a HTML view by name into the nearest views folder for the current document
     *
     * @param string $view
     * @return boolean|\Ximdex\Models\Node
     */
    public function getView(string $view)
    {
        if (! $this->GetID()) {
            $this->messages->add('The structured document has not been loaded', MSG_TYPE_ERROR);
            return false;
        }

        // Load parent nodes
        $parents = FastTraverse::getParents($this->GetID(), 'IdNodeType', 'ft.IdNode');
        if ($parents === false) {
            Logger::error('An error ocurred getting the parent nodes for the document with node ID: ' . $this->GetID());
            return false;
        }
        foreach ($parents as $nodeID => $nodeTypeID) {
            switch ($nodeTypeID) {
                case NodeTypeConstants::PROJECT:
                case NodeTypeConstants::XLMS_PROJECT:
                case NodeTypeConstants::SERVER:
                case NodeTypeConstants::SECTION:

                    // Load the node for the section, server or project ID given
                    $node = new Node($nodeID);
                    if (! $node->GetID()) {
                        $this->messages->add('Cannot load a node with the ID: ' . $nodeID, MSG_TYPE_ERROR);
                        return false;
                    }

                    // Load the layouts root folder inside the previous node, if it exists
                    $layoutsFolder = new Node($node->GetChildByType(NodeTypeConstants::HTML_LAYOUT_FOLDER));
                    if (! $layoutsFolder->getID()) {
                        continue 2;
                    }

                    // Load the components folder
                    $viewsFolder = new Node($layoutsFolder->GetChildByType(NodeTypeConstants::HTML_VIEWS_FOLDER));
                    if (! $viewsFolder->GetID()) {
                        $this->messages->add('Cannot load the views folder for the layout folder with ID: ' . $layoutsFolder->GetID()
                            , MSG_TYPE_ERROR);
                        return false;
                    }

                    // Load the view by the name given and html extension
                    $viewNode = new Node($viewsFolder->GetChildByName($view . '.html'));
                    if (! $viewNode->GetID()) {
                        continue 2;
                    }
                    return $viewNode;
            }
        }
        $this->messages->add('Cannot load the view with name: ' . $view . '.view', MSG_TYPE_ERROR);
        return false;
    }
    
    /**
     * Returns the node in the same container with the default server language
     *
     * @throws \Exception
     * @return Node
     */
    public function getDefaultLanguageDocument() : Node
    {
        // Search a target node in this document with default server language
        $node = new Node($this->IdDoc);
        $nodeProperty = new NodeProperty();
        $property = $nodeProperty->getProperty($node->getServer(), NodeProperty::DEFAULTSERVERLANGUAGE);
        if (empty($property[0])) {
            throw new \Exception('There is not a default server language defined');
        }
        $xmlContainer = new XmlContainerNode($node->getParent());
        $defaultLangDocId = $xmlContainer->getChildByLang($property[0]);
        $targetNode = new Node($defaultLangDocId);
        if (! $targetNode->getID()) {
            throw new \Exception('A document in default server language could not be found. Define it in server properties');
        }
        
        // Document in master language for its server cannot be linked
        if ($property[0] == $this->getLanguage()) {
            throw new \Exception('This document is the master language');
        }
        return $targetNode;
    }
    
    public function getByTemplate(int $templateId) : array
    {
        $docs = $this->find('IdDoc', "IdTemplate = {$templateId}", null, MONO);
        if ($docs === false) {
            throw new \Exception("Cannot get the documents related to the template {$templateId}");
        }
        return $docs;
    }
}
