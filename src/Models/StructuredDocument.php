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
 * @author Ximdex DevTeam <dev@ximdex.com>
 * @version $Revision$
 */

namespace Ximdex\Models;

use Ximdex\Runtime\DataFactory;
use Ximdex\Utils\FsUtils;
use Ximdex\XML\Validators\RNG;
use Ximdex\Logger;
use Ximdex\Models\ORM\StructuredDocumentsOrm;
use Ximdex\Parsers\ParsingDependencies;
use Ximdex\Parsers\ParsingRng;
use Ximdex\Properties\ChannelProperty;
use Ximdex\NodeTypes\NodeTypeConstants;
use Ximdex\NodeTypes\XmlContainerNode;

class StructuredDocument extends StructuredDocumentsOrm
{

    var $ID;

    var $flagErr;

    var $numErr;

    var $msgErr;

    var $errorList = array(
        1 => 'Error while connecting with the database',
        2 => 'The structured document does not exist',
        3 => 'Not implemented yet',
        4 => 'A document cannot be linked to itself'
    );

    public function __construct($id = null)
    {
        $this->ID = $id;
        $this->flagErr = FALSE;
        $this->autoCleanErr = TRUE;
        parent::__construct($id);
    }

    // Devuelve un array con los ids de todos los structured documents del sistema.
    // return array of idDoc
    function GetAllStructuredDocuments()
    {
        $sql = "SELECT idDoc FROM StructuredDocuments";
        $dbObj = new \Ximdex\Runtime\Db();
        $dbObj->Query($sql);
        if ($dbObj->numErr != 0) {
            $this->SetError(1);
            return null;
        }
        while (!$dbObj->EOF) {
            $salida[] = $dbObj->row["idDoc"];
            $dbObj->Next();
        }
        return $salida;
    }

    // Devuelve el id del structure document actual.
    function GetID()
    {
        return $this->get('IdDoc');
    }

    // Cambia el id del structure document actual.
    // return int (status)
    function SetID($docID)
    {
        self::__construct($docID);
        if (!($this->get('IdDoc') > 0)) {
            $this->SetError(2);
            return null;
        }
        return $this->get('IdDoc');
    }

    // Devuelve el nombre del structure document actual.
    // return string(name)
    function GetName()
    {
        return $this->get("Name");
    }

    // Cambia el nombre del structure document actual.
    // return int (status)
    function SetName($name)
    {
        if (!($this->get('IdDoc') > 0)) {
            $this->SetError(2);
            return false;
        }

        $result = $this->set('Name', $name);
        if ($result) {
            return $this->update();
        }
        return false;
    }

    // Devuelve el creador del structure document actual.
    // return string (idcreator)
    function GetCreator()
    {
        return $this->get("IdCreator");
    }

    // Cambia el creador del structure document actual.
    // return int (status)
    function SetCreator($IdCreator)
    {
        if (!($this->get('IdDoc') > 0)) {
            $this->SetError(2);
            return false;
        }

        $result = $this->set('IdCreator', $IdCreator);
        if ($result) {
            return $this->update();
        }
        return false;
    }

    // Devuelve el lenguaje del structure document actual.
    // return int (IdLanguage)
    function GetLanguage()
    {
        return $this->get("IdLanguage");
    }

    // Cambia el lenguaje del structure document actual.
    // return int (status)
    function SetLanguage($IdLanguage)
    {
        if (!($this->get('IdDoc') > 0)) {
            $this->SetError(2);
            return false;
        }

        $result = $this->set('IdLanguage', $IdLanguage);
        if ($result) {
            return $this->update();
        }
        return false;
    }

    function GetDocumentType()
    {
        return $this->get("IdTemplate");
    }

    function SetDocumentType($templateID)
    {
        if (!($this->get('IdDoc') > 0)) {
            $this->SetError(2);
            return false;
        }

        $result = $this->set('IdTemplate', $templateID);
        if ($result) {
            return $this->update();
        }
        return false;
    }

    function GetSymLink()
    {
        return $this->get("TargetLink");
    }

    function SetSymLink($docID)
    {
        if (!($this->get('IdDoc') >= 0)) {
            $this->SetError(2);
            return false;
        }

        if ($docID != $this->get('IdDoc')) {
            $result = $this->set('TargetLink', $docID);
            if ($result) {
                $this->update();
            }

            $dependencies = new Dependencies();
            $dependencies->insertDependence($docID, $this->get('IdDoc'), 'SYMLINK', $this->GetLastVersion());
            return true;
        } else
            $this->SetError(4);
        return false;
    }

    function ClearSymLink()
    {
        if (!($this->get('IdDoc') > 0)) {
            $this->SetError(2);
            return false;
        }

        $result = $this->set('TargetLink', '');
        if ($result) {

            $result = $this->update();
            $this->SetContent($this->GetContent());

            // Elimina la dependencia
            $dependencies = new Dependencies();
            $dependencies->deleteDependenciesByDependentAndType($this->get('IdDoc'), 'SYMLINK');

            return $result;
        }
        return false;
    }

    // Devuelve el contenido xml del structure document actual.
    // return string (Content)
    function GetContent($version = null, $subversion = null)
    {
        $targetLink = $this->GetSymLink();
        if ($targetLink) {
            $target = new StructuredDocument($targetLink);
            $targetContent = $target->GetContent();
            $targetLang = $target->GetLanguage();
            $targetContent = preg_replace('/ a_enlaceid([A-Za-z0-9|\_]+)\s*=\s*\"([^\"]+)\"/i', "' a_enlaceid\\1=\"'.\$this->UpdateLinkParseLink($targetLang , '\\2').'\"'", $targetContent);
            $targetContent = preg_replace('/ a_import_enlaceid([A-Za-z0-9|\_]+)\s*=\s*\"([^\"]+)\"/i', "' a_import_enlaceid\\1=\"'.\$this->UpdateLinkParseLink($targetLang , '\\2').'\"'", $targetContent);
            $targetContent = preg_replace('/<url>\s*([^\<]+)\s*<\/url>/i', "'<url>'.\$this->UpdateLinkParseLink($targetLang , '\\1').'</url>'", $targetContent);
            return $targetContent;
        }

        $data = new DataFactory($this->get('IdDoc'));
        $content = $data->GetContent($version, $subversion);
        return $content;
    }

    function UpdateLinkParseLink($sourceLang, $linkID)
    {
        $pos = strpos($linkID, ",");
        if ($pos != FALSE) {
            $linkID = substr($linkID, 0, $pos);
        }

        $node = new Node($linkID);
        if (($node->get('IdNode') > 0) && ($node->nodeType->get('Name') != "XmlDocument")) {
            return $linkID;
        }
        $linkDoc = new StructuredDocument($linkID);
        if ($linkDoc->GetLanguage() != $sourceLang)
            return $linkID;

        $node->SetID($node->GetParent());
        if ($node->nodeType->get('Name') != "XmlContainer")
            return $linkID;

        $sibling = $node->class->GetChildByLang($this->GetLanguage());

        if ($sibling)
            return $sibling;
        else
            return $linkID;
    }

    /**
     *
     * @param string $content
     * @param boolean $commitNode
     */
    function SetContent($content, $commitNode = NULL)
    {
        $symLinks = $this->find('IdDoc', 'TargetLink = %s', array(
            $this->get('IdDoc')
        ), MONO);

        // Repetimos para todos los nodos que son enlaces simbolicos a este
        if (!empty($symLinks)) {
            foreach ($symLinks as $link) {

                $node = new Node($link);
                $node->RenderizeNode();
            }
        }
        $node = new Node($this->get('IdDoc'));
        if (\Ximdex\NodeTypes\NodeTypeConstants::METADATA_DOCUMENT == $node->GetNodeType()) {
            $content = \Ximdex\Metadata\MetadataManager::addSystemMetadataToContent($node->nodeID, $content);
            if ($content === false) {
                // invalid XML
                $this->msgErr = 'Invalid XML document content';
                Logger::error('Invalid XML for metadata node: ' . $node->GetDescription());
                return false;
            }
        }

        // refrescamos la fecha de Actualizacion del nodo
        $this->SetUpdateDate();
        $data = new DataFactory($this->get('IdDoc'));
        $node = new Node($this->get('IdDoc'));
        if ($commitNode == false) {
            $info = $node->GetLastVersion();
            $res = $data->SetContent($content, $info['Version'], $info['SubVersion'], $commitNode);
        } else {
            $res = $data->SetContent($content, NULL, NULL, $commitNode);
        }

        // the document will be validate against the associated RNG schema with XML documents
        if ($node->GetNodeType() == NodeTypeConstants::XML_DOCUMENT) {
            $this->validate_schema($node);
        }

        // check possible errors
        if ($res === false) {
            if ($data->msgErr) {
                $this->messages->add($data->msgErr, MSG_TYPE_ERROR);
                return false;
            }
            if (!$this->messages->count()) {
                if (isset($GLOBALS['errorsInXslTransformation']) and $GLOBALS['errorsInXslTransformation']) {
                    $this->messages->add($GLOBALS['errorsInXslTransformation'][0], MSG_TYPE_WARNING);
                    $GLOBALS['errorsInXslTransformation'] = null;
                }
            }
        }

        // set dependencies
        $dependeciesParser = new ParsingDependencies();
        if ($dependeciesParser->parseAllDependencies($this->get('IdDoc'), $content) === false) {
            if (!$this->messages->count())
                $this->messages->mergeMessages($dependeciesParser->messages);
        }

        // Renderizamos el nodo para reflejar los cambios
        $node = new Node($this->get('IdDoc'));
        if ($node->RenderizeNode() === false) {
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
        if (!$idContainer)
            return null;
        $relTemplate = new RelTemplateContainer();
        $idTemplate = $relTemplate->getTemplate($idContainer);
        if (!$idTemplate)
            return null;
        $templateNode = new Node($idTemplate);
        if (!$templateNode->GetID())
            return null;
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
        if (!is_object($templateNode = $this->get_schema_node($docNode))) {
            return array(
                'id' => 0,
                'content' => $templateNode
            );
        }
        $schemaId = $templateNode->getID();
        if (!$schemaId)
            return null;
        $rngTemplate = new Node($schemaId);
        $content = $rngTemplate->GetContent();
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
        $valid = $rngValidator->validate($schema, $content);
        $errors = $rngValidator->getErrors();
        if ($errors) {
            foreach ($errors as $error)
                $this->messages->add('Error in the associated RNG schema: ' . $error, MSG_TYPE_WARNING);
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
        $xmlDoc = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL . '<docxap>' . PHP_EOL . \Ximdex\Utils\Strings::stripslashes($docNode->GetContent()) . PHP_EOL . '</docxap>';
        $rngValidator = new RNG();
        $valid = $rngValidator->validate($schema, $xmlDoc);
        if (!$valid) {
            foreach ($rngValidator->getErrors() as $error) {
                $this->messages->add('Error parsing with the RNG schema: ' . $error, MSG_TYPE_WARNING);
            }
            return false;
        }
        return true;
    }

    // Devuelve el timestamp de creacion del structure document actual.
    // return string (CreationDate)
    function GetCreationDate()
    {
        return $this->get("CreationDate");
    }

    // Devuelve el timestamp de modificacion del structure document actual.
    // return string (UpdateDate)
    function GetUpdateDate()
    {
        return $this->get("UpdateDate");
    }

    // Cambia el UpdateDate del structure document actual.
    // return int (status)
    function SetUpdateDate()
    {
        if (!($this->get('IdDoc') > 0)) {
            $this->SetError(2);
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
     * @param $idChannel
     * @return boolean
     */
    public function HasChannel($idChannel)
    {
        $values = $this->GetChannels();
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
     * @return array
     */
    public function GetChannels()
    {
        $channelProperty = new ChannelProperty($this->get('IdDoc'));
        $values = $channelProperty->getValues($this->get('IdDoc'));
        if ($values === false) {
            return false;
        }
        $res = [];
        foreach ($values as $channel) {
            if ($channel['Checked'] or $channel['Inherited']) {
                $res[$channel['Id']] = $channel['Id'];
            }
        }
        return $res;
    }

    function add()
    {
        $this->CreateNewStrDoc($this->get('IdDoc'), $this->get('Name'), $this->get('IdCreator'), $this->get('CreationDate')
            , $this->get('UpdateDate'), $this->get('IdLanguage'), $this->get('IdTemplate'));
    }

    /**
     * Crea un nuevo structure document y carga su id en el docID de la clase
     * return docID - lo carga como atributo
     *
     * @param $docID
     * @param $name
     * @param $IdCreator
     * @param $IdLanguage
     * @param $templateID
     * @param array $IdChannelList
     * @param string $content
     */
    public function CreateNewStrDoc($docID, $name, $IdCreator, $IdLanguage, $templateID, $IdChannelList = [], $content = '')
    {
        $this->set('Name', $name);
        $this->set('IdCreator', $IdCreator);
        $now = date('Y/m/d H:i:s');
        $this->set('CreationDate', $now);
        $this->set('UpdateDate', $now);
        $this->set('IdLanguage', $IdLanguage);
        $this->set('IdTemplate', $templateID);
        if ((int)$docID > 0) {
            $this->set('IdDoc', $docID);
        }
        $result = parent::add();
        if ($this->get('IdDoc') > 0) {

            $this->ID = $docID;

            // Guardamos su contenido
            $this->SetContent($content);
        } else {

            $this->SetError(1);
        }
    }

    function delete()
    {
        $this->DeleteStrDoc();
    }

    /**
     * Elimina el structure document actual
     */
    public function DeleteStrDoc()
    {
        parent::delete();
        $this->ID = null;
    }

    function GetLastVersion()
    {
        $sql = sprintf("select max(Version) as UltimaVersion from Versions where IdNode=%d", $this->get('IdDoc'));
        $dbObj = new \Ximdex\Runtime\Db();
        $dbObj->Query($sql);
        if ($dbObj->numErr != 0) {
            $this->SetError(1);
        } else {
            $salida = NULL;
            while (!$dbObj->EOF) {
                $salida = $dbObj->GetValue("UltimaVersion");
                $dbObj->Next();
            }
            return $salida;
        }
        return NULL;
    }

    function isximletlink()
    {
        $sql = sprintf("select IdNodeDependent from Dependencies WHERE IdNodeMaster = %d and DepType='LINK'", $this->get('IdDoc'));
        $dbObj = new \Ximdex\Runtime\Db();
        $dbObj->Query($sql);
        if ($dbObj->numErr != 0) {
            $this->SetError(1);
        } else {
            $salida = NULL;
            while (!$dbObj->EOF) {
                $links[] = $dbObj->GetValue("IdNodeDependent");
                $dbObj->Next();
            }

            if (is_array($links))
                foreach ($links as $link) {
                    $node_ximlet = new Node($link);
                    $node_type = new NodeType($node_ximlet->GetNodeType());

                    if ($node_type->GetName() == 'Ximlet') {
                        $salida[] = $link;
                    }
                }
            return $salida;
        }
        return NULL;
    }

    function ximletLinks($ximletID, $nodeID)
    {
        $sql = sprintf("SELECT IdNodeMaster FROM Dependencies" . " WHERE IdNodeDependent= %d AND DepType='LINK' AND IdNodeMaster!= %d", $ximletID, $nodeID);
        $dbObj = new \Ximdex\Runtime\Db();
        $dbObj->Query($sql);
        if ($dbObj->numErr != 0) {
            $this->SetError(1);
        } else {
            $link = NULL;
            while (!$dbObj->EOF) {
                $link[] = $dbObj->GetValue("IdNodeMaster");
                $dbObj->Next();
            }
            return $link;
        }
        return NULL;
    }

    // limpia el ultimo error
    function ClearError()
    {
        $this->flagErr = FALSE;
    }

    function SetAutoCleanOn()
    {
        $this->autoCleanErr = TRUE;
    }

    function SetAutoCleanOff()
    {
        $this->autoCleanErr = FALSE;
    }

    // Carga un error en la clase
    function SetError($code)
    {
        $this->flagErr = TRUE;
        $this->numErr = $code;
        $this->msgErr = $this->errorList[$code];
    }

    // devuelve true si en la clase se ha producido un error
    function HasError()
    {
        $aux = $this->flagErr;
        if ($this->autoCleanErr)
            $this->ClearError();
        return $aux;
    }

    function GetXsltErrors()
    {
        return $this->get('XsltErrors');
    }

    function SetXsltErrors($xsltErrors)
    {
        if (!$this->get('IdDoc')) {
            $this->SetError(2);
            return false;
        }
        $result = $this->set('XsltErrors', $xsltErrors);
        if ($result)
            return $this->update();
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
        if (!$this->GetID()) {
            $this->messages->add('The structured document has not been loaded', MSG_TYPE_ERROR);
            return false;
        }

        // Load parent nodes
        $parents = FastTraverse::get_parents($this->GetID(), 'IdNodeType');
        if ($parents === false) {
            Logger::error('An error ocurred getting the parent nodes for the document with node ID: ' . $this->GetID());
            return false;
        }
        foreach ($parents as $nodeID => $nodeTypeID) {
            switch ($nodeTypeID) {
                case NodeTypeConstants::SERVER:
                case NodeTypeConstants::SECTION:

                    // Load the node for the section, or server ID given
                    $node = new Node($nodeID);
                    if (!$node->GetID()) {
                        $this->messages->add('Cannot load a node with the ID: ' . $nodeID, MSG_TYPE_ERROR);
                        return false;
                    }

                    // Load the documents root folder inside the previous node, if it exists
                    $documentsFolder = new Node($node->GetChildByType(NodeTypeConstants::XML_ROOT_FOLDER));
                    if (!$documentsFolder->getID()) {
                        continue;
                    }

                    // Load the correspondant documents container folder for the include string given
                    $documentFolder = new Node($documentsFolder->GetChildByName($include));
                    if (!$documentFolder->GetID()) {
                        continue;
                    }

                    // Create an instance of the XML container handler
                    $xmlContainer = new XmlContainerNode($documentFolder->GetID());

                    // Load the document for the required language
                    $includeDocId = $xmlContainer->GetChildByLang($this->GetLanguage());
                    if (!$includeDocId) {
                        $this->messages->add('Cannot load the document for include: ' . $include . ' and language: '
                            . $this->GetLanguage(), MSG_TYPE_ERROR);
                        return false;
                    }
                    $includeDoc = new StructuredDocument($includeDocId);
                    if (!$includeDoc->GetID()) {
                        $this->messages->add('Cannot load the include document for ID: ' . $includeDocId . ' (name: ' . $include . ')', MSG_TYPE_ERROR);
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
        if (!$this->GetID()) {
            $this->messages->add('The structured document has not been loaded', MSG_TYPE_ERROR);
            return false;
        }
        if (!$this->get('IdTemplate')) {
            $this->messages->add('There is not defined a layout ID in the document', MSG_TYPE_ERROR);
            return false;
        }
        $layout = new Node($this->get('IdTemplate'));
        if (!$layout->GetID()) {
            $this->messages->add('The layout with ID: ' . $layout->GetID() . ' does not exist', MSG_TYPE_ERROR);
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
        if (!$this->GetID()) {
            $this->messages->add('The structured document has not been loaded', MSG_TYPE_ERROR);
            return false;
        }

        // Load parent nodes
        $parents = FastTraverse::get_parents($this->GetID(), 'IdNodeType');
        if ($parents === false) {
            Logger::error('An error ocurred getting the parent nodes for the document with node ID: ' . $this->GetID());
            return false;
        }
        foreach ($parents as $nodeID => $nodeTypeID) {
            switch ($nodeTypeID) {
                case NodeTypeConstants::PROJECT:
                case NodeTypeConstants::SERVER:
                case NodeTypeConstants::SECTION:

                    // Load the node for the section, server or project ID given
                    $node = new Node($nodeID);
                    if (!$node->GetID()) {
                        $this->messages->add('Cannot load a node with the ID: ' . $nodeID, MSG_TYPE_ERROR);
                        return false;
                    }

                    // Load the layouts root folder inside the previous node, if it exists
                    $layoutsFolder = new Node($node->GetChildByType(NodeTypeConstants::HTML_LAYOUT_FOLDER));
                    if (!$layoutsFolder->getID()) {
                        continue;
                    }

                    // Load the components folder
                    $componentsFolder = new Node($layoutsFolder->GetChildByType(NodeTypeConstants::HTML_COMPONENTS_FOLDER));
                    if (!$componentsFolder->GetID()) {
                        $this->messages->add('Cannot load the components folder for the layout folder with ID: '
                            . $layoutsFolder->GetID(), MSG_TYPE_ERROR);
                        return false;
                    }

                    // Load the component by the name given and json extension
                    $componentNode = new Node($componentsFolder->GetChildByName($component . '.json'));
                    if (!$componentNode->GetID()) {
                        continue;
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
        if (!$this->GetID()) {
            $this->messages->add('The structured document has not been loaded', MSG_TYPE_ERROR);
            return false;
        }

        // Load parent nodes
        $parents = FastTraverse::get_parents($this->GetID(), 'IdNodeType');
        if ($parents === false) {
            Logger::error('An error ocurred getting the parent nodes for the document with node ID: ' . $this->GetID());
            return false;
        }
        foreach ($parents as $nodeID => $nodeTypeID) {
            switch ($nodeTypeID) {
                case NodeTypeConstants::PROJECT:
                case NodeTypeConstants::SERVER:
                case NodeTypeConstants::SECTION:

                    // Load the node for the section, server or project ID given
                    $node = new Node($nodeID);
                    if (!$node->GetID()) {
                        $this->messages->add('Cannot load a node with the ID: ' . $nodeID, MSG_TYPE_ERROR);
                        return false;
                    }

                    // Load the layouts root folder inside the previous node, if it exists
                    $layoutsFolder = new Node($node->GetChildByType(NodeTypeConstants::HTML_LAYOUT_FOLDER));
                    if (!$layoutsFolder->getID()) {
                        continue;
                    }

                    // Load the components folder
                    $viewsFolder = new Node($layoutsFolder->GetChildByType(NodeTypeConstants::HTML_VIEWS_FOLDER));
                    if (!$viewsFolder->GetID()) {
                        $this->messages->add('Cannot load the views folder for the layout folder with ID: ' . $layoutsFolder->GetID(), MSG_TYPE_ERROR);
                        return false;
                    }

                    // Load the view by the name given and html extension
                    $viewNode = new Node($viewsFolder->GetChildByName($view . '.html'));
                    if (!$viewNode->GetID()) {
                        continue;
                    }
                    return $viewNode;
            }
        }
        $this->messages->add('Cannot load the view with name: ' . $view . '.view', MSG_TYPE_ERROR);
        return false;
    }
}