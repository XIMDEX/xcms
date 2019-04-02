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

namespace Ximdex\NodeTypes;

use Ximdex\Runtime\App;
use Ximdex\Deps\DepsManager;
use Ximdex\Models\NodeType;
use Ximdex\Models\Dependencies;
use Ximdex\Models\RelSemanticTagsNodes;
use Ximdex\Models\Channel;
use Ximdex\Models\Language;
use Ximdex\Models\Node;
use Ximdex\Models\NodeDependencies;
use Ximdex\Models\StructuredDocument;
use Ximdex\Utils\Messages;
use Ximdex\Logger;
use Ximdex\Runtime\Session;
use Ximdex\Properties\ChannelProperty;
use Ximdex\Models\NodeProperty;
use Ximdex\Models\SemanticNamespaces;

define('DOCXAP_VIEW', 1);
define('SOLR_VIEW', 2);
define('XIMIO_VIEW', 3);

/**
 * Class AbstractStructuredDocument
 * @package Ximdex\NodeTypes
 */
abstract class AbstractStructuredDocument extends FileNode
{
    /**
     * Creates a new structured node
     * 
     * {@inheritDoc}
     * @see \Ximdex\NodeTypes\FileNode::createNode()
     */
    public function createNode(string $name = null, int $parentID = null, int $nodeTypeID = null, int $stateID = null, string $templateID = null
        , int $IdLanguage = null, string $aliasName = '', array $channelList = null)
    {
        $loginID = Session::get("userID");
        $templateNode = new Node($templateID);
        $content = '';
        if ($templateNode->get('IdNode') > 0) {
            
            // Relaxng schema
            $templateNodeType = new NodeType($templateNode->get('IdNodeType'));
            if ($templateNodeType->get('Name') == 'RngVisualTemplate') {
                $content = $templateNode->class->buildDefaultContent();
            } else {
                $templateContent = $templateNode->class->getContent();
                $templateContent = explode('##########', $templateContent);
                if (isset($templateContent[1])) {
                    $content = str_replace("'", "\'", $templateContent[1]);
                }
            }
        }
        $doc = new StructuredDocument();
        $doc->createNewStrDoc($this->nodeID, $name, $loginID, $IdLanguage, $templateID, $channelList, $content);
        if ($doc->hasError()) {
            $this->parent->SetError(5);
        }
        $nodeContainer = new Node($this->parent->GetParent());
        $nodeContainer->setAliasForLang($IdLanguage, $aliasName);
        if ($nodeContainer->HasError()) {
            $this->parent->SetError(5);
        }
        $this->updatePath();
        return true;
    }

    /**
     * Name for the file/resource on production servers
     * 
     * @param int $channel
     * @return string
     */
    public function getPublishedNodeName(int $channel = null)
    {
        $channel = new Channel($channel);
        if (!$channel->GetID()){
            $error = 'Channel not found for ID: ' . $channel;
            $this->messages->add($error, MSG_TYPE_ERROR);
            Logger::error($error);
            return false;
        }
        if (App::getValue('PublishPathFormat') == App::SUFFIX) {
            $fileName = $this->parent->GetNodeName();
            $fileName .= '-id' . $channel->GetName();
        }
        else {
            
            // In prefix mode the name of the document will be obtained from the parent document folder 
            if (!$this->parent->GetParent()) {
                $error = 'There is not specified a parent node ID for document: ' . $this->parent->GetNodeName();
                $this->messages->add($error, MSG_TYPE_ERROR);
                Logger::error($error);
                return false;
            }
            $docFolder = new Node($this->parent->GetParent());
            if (!$docFolder->GetID()) {
                $error = 'Document folder not found for ID: ' . $this->parent->GetParent();
                $this->messages->add($error, MSG_TYPE_ERROR);
                Logger::error($error);
                return false;
            }
            $fileName = $docFolder->GetNodeName();
        }
        $fileName .= '.' . $channel->GetExtension();
        return $fileName;
    }

    /**
     * Get the documents that must be publicated when the template is published
     * 
     * @param array $params
     * @return array
     */
    public function getPublishabledDeps(array $params = []) : ?array
    {
        $idDoc = $this->parent->get('IdNode');

        // Only for dependences with ximlets
        $dependencies = new Dependencies();
        $idDepXimlets = [$dependencies->getDepTypeId(Dependencies::XIMLET)];
        $depsMngr = new DepsManager();
        $structure = $depsMngr->getBySource(DepsManager::XML2XML, $idDoc, $idDepXimlets);
        $asset = empty($params['withstructure']) ? array() : $depsMngr->getBySource(DepsManager::NODE2ASSET, $idDoc);
        return array_merge($asset, $structure);
    }

    /**
     * @return mixed|string
     */
    public function GetContent()
    {
        $strDoc = new StructuredDocument($this->nodeID);
        return $strDoc->GetContent();
    }

    /**
     * {@inheritDoc}
     * @see \Ximdex\NodeTypes\FileNode::setContent()
     */
    public function setContent(string $content, bool $commitNode = false, Node $node = null) : bool
    {
        // Checking the valid XML of the given content, if it is necessary
        if ($node) {
            switch ($node->getNodeType()) {
                case NodeTypeConstants::XML_DOCUMENT:
                case NodeTypeConstants::XIMLET:
                    
                    // In this case we will format the XML content with correct indentation
                    $domDoc = new \DOMDocument();
                    $domDoc->formatOutput = true;
                    $domDoc->preserveWhiteSpaces = false;
                    $res = @$domDoc->loadXML($content);
                    $domDoc->encoding = 'UTF-8';
                    if ($res) {
                        $content = $domDoc->saveXML();
                        $content = str_replace('<?xml version="1.0" encoding="UTF-8"?>', '', $content);
                        $content = trim($content);
                    }
                    break;
                default:
                    $res = true;
            }
            if ($res === false) {
                Logger::error('Invalid XML for idNode: ' . $node->getIdNode());
                $error = Messages::error_message('DOMDocument::loadXML(): ');
                if ($error) {
                    $this->messages->add($error, MSG_TYPE_WARNING);
                    Logger::error($error . ' (' . $node->GetNodeName() . ')');
                }
            }
        }
        $strDoc = new StructuredDocument($this->nodeID);
        $res = $strDoc->setContent($content, $commitNode);
        $this->messages->mergeMessages($strDoc->messages);
        if ($res === false) {
            return false;
        }
        
        // Update workflow slaves for this node
        if (! $node) {
            $node = new Node($this->nodeID);
        }
        $wfSlaves = $node->GetWorkflowSlaves();
        if ($wfSlaves) {
            foreach ($wfSlaves as $docID) {
                $strDocSlave = new StructuredDocument($docID);
                $strDocSlave->SetContent($content, $commitNode);
            }
        }
        return true;
    }

    public function getIcon()
    {
        $strDoc = new StructuredDocument($this->nodeID);
        if ($strDoc->GetSymLink()) {
            $icon = pathinfo($this->parent->nodeType->GetIcon());
            
            // Separa la extension del nombre del archivo
            $fileName = preg_replace('/(.+)\..*$/', '$1', $icon["basename"]);
            return $fileName . "-link." . $icon["extension"];
        }
        return $this->parent->nodeType->GetIcon();
    }

    public function view(string $viewType, int $channel, string $content = null, int $idVersion = null)
    {
        switch ($viewType) {
            case DOCXAP_VIEW:
                return $this->renderizeNode($channel, $content);
        }
        return null;
    }
    
    /**
     * Builds the docxap header for a structured document
     *
     * @param int $channel
     * @param int $idLanguage
     * @param int $documentType
     * @param string $tagName
     * @return string
     */
    public function getDocHeader(int $channel, int $idLanguage, int $documentType, string $tagName = 'docxap') : string
    {
        $schema = new Node($documentType);
        $schemaName = $schema->get('Name');
        $schemaTag = 'schema="' . $schemaName . '"';
        $layoutName = str_replace('.xml', '', $schemaName);
        $layoutTag = 'layout ="' . $layoutName . '"';
        $node = new Node($this->nodeID);
        $nt = $node->nodeType->get('IdNodeType');
        $metadata = '';
        if ($nt == NodeTypeConstants::XML_DOCUMENT) {
            $metadata = 'metadata_id=""';
        }
        
        // Include the associated semantic tags of the document into the docxap tag.
        $xtags = '';
        $rtn = new RelSemanticTagsNodes();
        $nodeTags = $rtn->getTags($this->nodeID);
        if (! empty($nodeTags)) {
            foreach ($nodeTags as $tag) {
                $ns = new SemanticNamespaces();
                $idns = $ns->getNemo($tag['IdNamespace']);
                $xtags .= $tag['Name'] . ":" . $idns . ",";
            }
        }
        $xtags = substr_replace($xtags, "", -1);
        $xtags = 'xtags = "' . $xtags . '"';
        $docxap = sprintf('<' . $tagName . ' %s %s %s %s %s %s %s %s %s>',
            $layoutTag,
            $this->_langXapAttrib($idLanguage),
            $schemaTag,
            $this->ChannelsXapAttrib($channel),
            $this->_buildDocXapAttribs($idLanguage),
            $this->_getDocXapPropertiesAttrib(true),
            $xtags,
            $metadata,
            null
        );
        return $docxap;
    }

    public function getRenderizedContent(int $channel = null, string $content = null, $onlyDocXap = null)
    {
        $strDoc = new StructuredDocument($this->nodeID);
        if (!($strDoc->get('IdDoc') > 0)) {
            return NULL;
        }
        $idLanguage = $strDoc->GetLanguage();
        if (is_null($content)) {
            $content = $strDoc->GetContent();
        }
        if ($this->parent->GetNodeType() != NodeTypeConstants::HTML_DOCUMENT) {
            
            // XML documents
            $doctypeTag = App::getValue("DoctypeTag");
            $encodingTag = App::getValue("EncodingTag");
            $documentType = $strDoc->GetDocumentType();
            $docXapHeader = $this->getDocHeader($channel, $idLanguage, $documentType);
            if ($onlyDocXap) {
                return $docXapHeader;
            }
            return $encodingTag . "\n" . $doctypeTag . "\n\n" . $docXapHeader . $this->InsertLinkedximletS($idLanguage) . "\n" 
                . $content . "\n" . "</docxap>\n";
        }
        
        // HTML documents
        return $this->InsertLinkedximletS($idLanguage) . "\n" . $content;
    }

    public function deleteNode() : bool
    {
        $parent = new Node($this->parent->get('IdParent'));
        $st = new StructuredDocument($this->parent->get('IdNode'));
        $dbObj = new \Ximdex\Runtime\Db();
        $query = sprintf('DELETE FROM NodeNameTranslations WHERE IdNode = %s AND IdLanguage = %s'
            , $dbObj->sqlEscapeString($parent->get('IdNode')), $dbObj->sqlEscapeString($st->get('IdLanguage')));
        if ($dbObj->execute($query) === false) {
            return false;
        }
        $doc = new StructuredDocument();
        $doc->setID($this->nodeID);
        if ($doc->hasError()) {
            $this->parent->setError(5);
            return false;
        }
        
        // Deletes dependencies in rel tables
        $depsMngr = new DepsManager();
        if ($depsMngr->deleteByTarget(DepsManager::XML2XML, $this->parent->get('IdNode')) === false) {
            return false;
        }
        if ($depsMngr->deleteBySource(DepsManager::XML2XML, $this->parent->get('IdNode')) === false) {
            return false;
        }
        if ($depsMngr->deleteBySource(DepsManager::NODE2ASSET, $this->parent->get('IdNode')) === false) {
            return false;
        }
        Logger::info('StrDoc dependencies deleted');
        
        // Delete document
        $doc->delete();
        if ($doc->hasError()) {
            $this->parent->setError(5);
            return false;
        }
        return true;
    }

    /**
     * {@inheritDoc}
     * @see \Ximdex\NodeTypes\FileNode::renameNode()
     */
    public function renameNode(string $name) : bool
    {
        $doc = new StructuredDocument($this->nodeID);
        $doc->setName($name);
        $this->updatePath();
        return true;
    }

    /**
     * Return true if the specified channel ID is in the node's properties
     * 
     * @param int $channelID
     * @return bool
     */
    public function hasChannel(int $channelID) : bool
    {
        $values = $this->getChannels();
        if ($values === false) {
            return false;
        }
        if (isset($values['Channel'][$channelID])) {
            return true;
        }
        return false;
    }

    /**
     * Return an array with all the channels ID for the current node
     * 
     * @return array
     */
    public function getChannels()
    {
        $channelProperty = new ChannelProperty($this->nodeID);
        $values = $channelProperty->getValues(true);
        if ($values === false) {
            return false;
        }
        $res = [];
        foreach ($values as $channel) {
            $res[] = $channel['Id'];
        }
        return $res;
    }

    /**
     * {@inheritDoc}
     * @see \Ximdex\NodeTypes\FileNode::ToXml()
     */
    public function toXml(int $depth, array & $files, bool $recurrence = false)
    {
        $xmlBody = parent::ToXML($depth, $files, $recurrence);
        $channelList = $this->GetChannels();
        if (is_array($channelList)) {
            foreach ($channelList as $idChannel) {
                $node = new Node($idChannel);
                $xmlBody .= $node->ToXml($depth, $files, $recurrence);
                unset($node);
            }
        }
        unset($channelList);
        $indexTabs = str_repeat("\t", $depth + 1);
        $query = sprintf("SELECT nt.IdLanguage, nt.Name"
            . " FROM NodeNameTranslations nt"
            . " INNER JOIN StructuredDocuments sd ON sd.IdLanguage = nt.IdLanguage AND sd.IdDoc = %d"
            . " WHERE IdNode = %d",
            $this->nodeID,
            $this->parent->getParent());
        $this->dbObj->Query($query);
        while (!$this->dbObj->EOF) {
            $idLanguage = $this->dbObj->getValue('IdLanguage');
            $name = $this->dbObj->getValue('Name');
            $xmlBody .= sprintf("%s<NodeNameTranslation IdLang=\"%d\">\n", $indexTabs, $idLanguage);
            $xmlBody .= sprintf("%s\t<![CDATA[%s]]>\n", $indexTabs, utf8_encode($name));
            $xmlBody .= sprintf("%s</NodeNameTranslation>\n", $indexTabs);
            $this->dbObj->next();
        }
        return $xmlBody;
    }

    public function getXmlTail()
    {
        $returnValue = '';
        $query = sprintf("SELECT TargetLink FROM StructuredDocuments WHERE IdDoc = %d", $this->nodeID);
        $this->dbObj->query($query);
        if ($this->dbObj->numRows == 1) {
            $targetLink = $this->dbObj->getValue('TargetLink');
            if ((int)$targetLink > 0) {
                $returnValue = sprintf(' targetLink="%d"', $targetLink);
            }
        }
        return $returnValue;
    }

    public function getTemplate()
    {
        $structuredDocument = new StructuredDocument($this->nodeID);
        if ($structuredDocument->get('IdDoc') > 0) {
            return $structuredDocument->get('IdTemplate');
        }
        return false;
    }

    public function getLanguage()
    {
        $structuredDocument = new StructuredDocument($this->nodeID);
        $idLanguage = $structuredDocument->get('IdLanguage');
        return $idLanguage > 0 ? $idLanguage : null;
    }

    private function _langXapAttrib(int $idLang)
    {
        // Inserting languages
        $outPut2 = null;
        $colectible = ' languages="';
        $node = new Node($this->parent->get('IdNode'));
        $idParent = $node->get('IdParent');
        $nodeParent = new Node($idParent);
        $docList = $nodeParent->getChildren();
        foreach ($docList as $docID) {
                
            // Getting the language
            $strDoc = new StructuredDocument($docID);
            $langID = $strDoc->getLanguage();
            $lang = new Language($langID);
            $colectible .= $lang->getIsoName() . ',';
        }
        $colectible = substr($colectible, 0, strlen($colectible) - 1);
        $outPut2 .= $colectible . '"';
        $lang = new Language($idLang);
        $outPut2 .= ' language="' . $lang->getIsoName() . '"';
        return $outPut2;

    }

    private function _buildDocXapAttribs(int $idLang)
    {
        return $this->docXapAttribLevels($idLang);
    }

    public function channelsXapAttrib(int $channelID = null)
    {
        $doc = new StructuredDocument($this->nodeID);
        $channelList = $doc->getChannels();
        $outPut = null;
        if ($channelList) {
            if (in_array($channelID, $channelList)) {
                $channel = new Channel($channelID);
                $outPut = 'channel="' . $channel->getName() . '"';
                $outPut .= ' extension="' . $channel->getExtension() . '"';
            } else {
                $outPut = 'channel="" ';
            }
            $channelNames = [];
            foreach ($channelList as $channelID) {
                $channel = new Channel($channelID);
                $channelNames[] = $channel->get('Name');
            }
            $outPut .= ' channels="' . implode(",", $channelNames) . '"';
        }
        return $outPut;
    }

    public function insertLinkedximletS(int $langID, int $sectionId = null) : string
    {
        $linkedXimlets = $this->getLinkedXimlets($langID, $sectionId);
        $output = '';
        if (sizeof($linkedXimlets) > 0) {
            foreach ($linkedXimlets as $ximletId) {
                $output .= "<ximlet>@@@GMximdex.ximlet($ximletId)@@@</ximlet>";
            }
        }
        return $output;
    }

    public function getLinkedximletS(int $langID, int $sectionId = null) : array
    {
        if (is_null($sectionId)) {
            $node = New Node($this->nodeID);
            $sectionId = $node->getSection();
        }
        $depsMngr = new DepsManager();
        $ximletContainers = $depsMngr->getBySource(DepsManager::SECTION_XIMLET, $sectionId);
        $linkedXimlets = array();
        if (! empty($ximletContainers) > 0) {
            foreach ($ximletContainers as $ximletContaineId) {
                $node = new Node($ximletContaineId);
                $ximlets = $node->getChildren();
                foreach ($ximlets as $ximletId) {
                    $strDoc = new StructuredDocument($ximletId);
                    if ($strDoc->get('IdLanguage') == $langID) {
                        $linkedXimlets[] = $ximletId;
                    }
                }
            }
        }
        return $linkedXimlets;
    }

    public function DocXapDynamicAttrib(int $nodeID) : string
    {
        $prop = new NodeProperty();
        $array_prop = $prop->getPropertiesByNode($nodeID);
        $nprop = count($array_prop);
        $str_props = "";
        for ($i = 0; $i < $nprop; $i++) {
            $str_props = $str_props . ' ' . $array_prop[$i]["Name"] . '="' . $array_prop[$i]["Value"] . '"';
        }
        return $str_props;
    }

    public function docXapAttribLevels(int $langID) : string
    {
        $node = new Node($this->parent->get('IdNode'));
        $parent = new Node($node->get('IdParent'));
        $s = ' node_id="' . $node->get('IdNode') . '"  parent_node_id="' . $parent->get('IdNode') . '"';
        $s .= ' nodetype_name="' . $node->nodeType->get('Name') . '"  nodetype_id="' . $node->nodeType->get('IdNodeType') . '"';
        $s .= ' document_name="' . $parent->get('Name') . '" alias="' . $parent->getAliasForLang($langID) . '"';
        $tree = $node->traverseToRoot();

        // It must exclude from length the node itself, its container, and its folder
        $length = count($tree) - 3;
        $j = 0;
        for ($i = 1; $i < $length; $i++) {
            $ancestor = new Node($tree[$i]);
            switch ($i) {
                case 1:
                    $s .= ' project_name="' . $ancestor->get('Name') . '"';
                    continue;
                case 2:
                    $s .= ' server="' . $ancestor->get('Name') . '"';
                    continue;
                default:
                    if ($ancestor->nodeType->get('IsSection') == 1) {
                        $j++;
                        $s .= " level$j=\"" . $ancestor->get('Name') . "\" level_name$j=\"" .
                            $ancestor->getAliasForLang($langID) . "\"\n";
                    }
                    continue;
            }
        }
        return $s;
    }

    public function getDependencies() : array
    {
        $nodeDependencies = new NodeDependencies();
        return $nodeDependencies->getByTarget($this->nodeID);
    }

    public function getChildrenByLanguage($idLanguage) : ?int
    {
        $childrens = $this->parent->getChildren();
        if (is_array($childrens) && ! empty($childrens)) {
            foreach ($childrens as $idChildren) {
                $node = new Node($idChildren);
                if ($node->class->getLanguage == $idLanguage) {
                    return (int) $node->get('IdNode');
                }
            }
        }
        return null;
    }

    private function _getDocXapPropertiesAttrib(bool $withInheritance = false) : string
    {
        $docxapPropAttrs = '';
        $node = new Node($this->nodeID);
        $properties = $node->getAllProperties($withInheritance);
        if ($properties) {
            foreach ($properties as $idProperty => $propertyValue) {
                if ($idProperty == 'channel') {
                    $docxapPropAttrs .= 'channel_id';
                }
                elseif ($idProperty == 'language') {
                    $docxapPropAttrs .= 'language_iso_id';
                }
                else {
                    $docxapPropAttrs .= 'property_' . strtolower($idProperty);
                }
                $docxapPropAttrs .= '="' . $propertyValue[0] . '" ';
            }
        }
        return $docxapPropAttrs;
    }
    
    /**
     * {@inheritDoc}
     * @see \Ximdex\NodeTypes\Root::getPublishedPath()
     */
    public function getPublishedPath(int $channelID = null, bool $addNodeName = false, bool $structure = false, bool $addLanguagePrefix = true)
    {
        if (! $this->parent->getID()) {
            $error = 'Missing ID for structured document';
            $this->messages->add($error, MSG_TYPE_ERROR);
            Logger::error($error);
            return false;
        }
        $structuredDocument = new StructuredDocument($this->parent->getID());
        if (! $structuredDocument->get('IdLanguage')) {
            $error = 'Language has not been specified for document: ' . $this->parent->getNodeName();
            $this->messages->add($error, MSG_TYPE_ERROR);
            Logger::error($error);
            return false;
        }
        if (App::getValue('PublishPathFormat') == App::PREFIX or App::getValue('PublishPathFormat') == App::SUFFIX) {
            $language = new Language($structuredDocument->get("IdLanguage"));
            if (! $language->getID()) {
                $error = 'Language not found for ID: ' . $structuredDocument->get("IdLanguage");
                $this->messages->add($error, MSG_TYPE_ERROR);
                Logger::error($error);
                return false;
            }
        }
        if (App::getValue('PublishPathFormat') == App::SUFFIX) {
            $addNodeName = false;
        }
        $nodes = parent::GetPublishedPath($channelID, $addNodeName, true);
        $path = '/' . implode('/', $nodes);
        switch (App::getValue('PublishPathFormat')) {
            case App::PREFIX:
                if (! $addLanguagePrefix) {
                    break;
                }
                
                // If the language is different than the default server one, the path include its ISO name
                $nodeProperty = new NodeProperty();
                $property = $nodeProperty->getProperty($this->parent->getServer(), NodeProperty::DEFAULTSERVERLANGUAGE);
                if ($property) {
                    if ($language->getID() != $property[0]) {
                        $path = '/'. $language->get("IsoName") . $path;
                    }
                } else {
                    
                    // No default language in prefix mode, always include the language in path
                    $path = '/'. $language->get("IsoName") . $path;
                }
                break;
            case App::SUFFIX:
                if ($addNodeName) {
                    $path .= '/' . $this->getPublishedNodeName($channelID);
                }
        }
        return str_replace('//', '/', $path);
    }
}
