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

namespace Ximdex\NodeTypes;

use Ximdex\Runtime\App;
use Ximdex\Runtime\DataFactory;
use Ximdex\Deps\DepsManager;
use Ximdex\Models\NodeType;
use Ximdex\Utils\PipelineManager;
use Ximdex\Models\Dependencies;
use Ximdex\Models\RelTagsNodes;
use Ximdex\Models\Channel;
use Ximdex\Models\Language;
use Ximdex\Models\Node;
use Ximdex\Models\NodeDependencies;
use Ximdex\Models\StructuredDocument;
use Ximdex\Utils\FsUtils;
use Ximdex\Logger;
use Ximdex\Runtime\Session;
use Ximdex\Properties\ChannelProperty;
use Ximdex\Models\NodeProperty;

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
     * @param null $name
     * @param null $parentID
     * @param null $nodeTypeID
     * @param null $stateID
     * @param null $templateID
     * @param null $IdLanguage
     * @param string $aliasName
     * @param null $channelList
     */
    function CreateNode($name = null, $parentID = null, $nodeTypeID = null, $stateID = null, $templateID = null, $IdLanguage = null
        , $aliasName = '', $channelList = null)
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
                $templateContent = $templateNode->class->GetContent();
                $templateContent = explode('##########', $templateContent);
                if (isset($templateContent[1])) {
                    $content = str_replace("'", "\'", $templateContent[1]);
                }
            }
        }
        $doc = new StructuredDocument();
        $doc->CreateNewStrDoc($this->nodeID, $name, $loginID, $IdLanguage, $templateID, $channelList, $content);
        if ($doc->HasError()) {
            $this->parent->SetError(5);
        }
        $nodeContainer = new Node($this->parent->GetParent());
        $nodeContainer->SetAliasForLang($IdLanguage, $aliasName);
        if ($nodeContainer->HasError()) {
            $this->parent->SetError(5);
        }
        $this->updatePath();
    }

    /**
     * Name for the file/resource on production servers
     * 
     * @param null $channel
     * @return string
     */
    function GetPublishedNodeName($channel = null)
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
    public function getPublishabledDeps($params)
    {
        $idDoc = $this->parent->get('IdNode');

        // Only for dependences with ximlets
        $dependencies = new Dependencies();
        $idDepXimlets = [$dependencies->getDepTypeId(Dependencies::XIMLET)];
        $depsMngr = new DepsManager();
        $structure = $depsMngr->getBySource(DepsManager::XML2XML, $idDoc, $idDepXimlets);
        $asset = empty($params['withstructure']) ? array() :
        $depsMngr->getBySource(DepsManager::NODE2ASSET, $idDoc);
        $node = new Node($idDoc);
        $tmpWorkFlowSlaves = $node->GetWorkFlowSlaves();
        $workFlowSlaves = is_null($tmpWorkFlowSlaves) ? array() : $tmpWorkFlowSlaves;
        return array_merge($workFlowSlaves, $asset, $structure);
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
     * @param $content
     * @param null $commitNode
     */
    public function SetContent($content, $commitNode = NULL, Node $node = null)
    {
        //Checking the valid XML of the given content, if it is necessary
        if ($node) {
            switch ($node->getNodeType()) {
                case \Ximdex\NodeTypes\NodeTypeConstants::XML_DOCUMENT:
                case \Ximdex\NodeTypes\NodeTypeConstants::METADATA_DOCUMENT:
                case \Ximdex\NodeTypes\NodeTypeConstants::XIMLET:
                    
                    // In this case we will format the XML content with correct indentation
                    $domDoc = new \DOMDocument();
                    $domDoc->formatOutput = true;
                    $domDoc->preserveWhiteSpaces = false;
                    $res = @$domDoc->loadXML($content);
                    $domDoc->encoding = 'UTF-8';
                    $domDoc->version = '1.0';
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
                $error = \Ximdex\Utils\Messages::error_message('DOMDocument::loadXML(): ');
                if ($error) {
                    $this->messages->add($error, MSG_TYPE_WARNING);
                    Logger::error($error . ' (' . $node->GetNodeName() . ')');
                }
            }
        }
        $strDoc = new StructuredDocument($this->nodeID);
        $res = $strDoc->SetContent($content, $commitNode);
        $this->messages->mergeMessages($strDoc->messages);
        if ($res === false) {
            return false;
        }
        $wfSlaves = $this->parent->GetWorkflowSlaves();
        if (!is_null($wfSlaves)) {
            foreach ($wfSlaves as $docID) {
                $strDoc = new StructuredDocument($docID);
                $strDoc->SetContent($content, $commitNode);
            }
        }
        return true;
    }

    /**
     * @return bool|string
     */
    function GetIcon()
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

    /**
     * @param $viewType
     * @param $channel
     * @param null $content
     * @param null $idVersion
     * @return bool|null
     */
    function view($viewType, $channel, $content = NULL, $idVersion = NULL)
    {
        switch ($viewType) {
            case DOCXAP_VIEW:
                return $this->RenderizeNode($channel, $content);
        }
        return NULL;
    }

    /**
     * @return bool|null|string
     */
    function _getPermissionGroups()
    {
        $node = new Node($this->nodeID);
        if (!($node->get('IdNode') > 0)) {
            return false;
        }
        $idNodeType = $node->get('IdNodeType');
        switch ($idNodeType) {
            case \Ximdex\NodeTypes\NodeTypeConstants::XML_DOCUMENT:
                $folderNodeType = \Ximdex\NodeTypes\NodeTypeConstants::XML_ROOT_FOLDER;
                break;
            case \Ximdex\NodeTypes\NodeTypeConstants::XIMLET:
                $folderNodeType = \Ximdex\NodeTypes\NodeTypeConstants::XIMLET_FOLDER;
                break;
            case 8002: //pdf
                $folderNodeType = 8000;
                break;
        }
        do {
            $idNodeType = 0;
            $node = new Node($node->get('IdParent'));
            if (!($node->get('IdNode') > 0)) {
                return NULL;
            }
            $idNodeType = $node->get('IdNodeType');
        } while ($idNodeType == $folderNodeType);

        $groups = $node->GetGroupList();
        if (!empty($groups)) {
            return implode(' ', $groups);
        }
        return NULL;
    }

    /**
     * Renderiza el nodo en el sistema de archivos
     * @param null $channel
     * @param null $content
     * @return bool
     */
    function RenderizeNode($channel = null, $content = null)
    {
        // Se obtiene el id del nodo padre (ya que parent contiene una instancia del node actual)
        // y creamos un objeto nodo con ese id
        $parentID = $this->parent->GetParent();
        $parent = new Node($parentID);

        // Renderizamos hacia arriba toda la jerarqu\EDa
        if (!$parent->IsRenderized()) {
            if ($parent->RenderizeNode() === false)
                return false;
        }

        // Conseguimos el path del archivo de destino
        $fileName = $this->GetNodePath();
        $fileContent = $this->GetRenderizedContent($channel, $content);

        // Lo guardamos en el sistema de archivos
        if (!FsUtils::file_put_contents($fileName, $fileContent)) {
            $this->parent->SetError(7);
            $this->parent->messages->add(_('An error occured while trying to save the document'), MSG_TYPE_ERROR);
            return false;
        }
        return true;
    }

    /**
     * Builds the docxap header for a structured document
     *
     * @param int $channel
     * @param int $idLanguage
     * @param int $documentType
     * @param boolean $solrView
     * @return string
     */
    public function _getDocXapHeader($channel, $idLanguage, $documentType)
    {
        $schema = new Node($documentType);
        $schemaName = $schema->get('Name');
        $schemaTag = 'schema="' . $schemaName . '"';
        $layoutName = str_replace('.xml', '', $schemaName);
        $layoutTag = 'layout ="' . $layoutName . '"';
        $node = new Node($this->nodeID);
        $nt = $node->nodeType->get('IdNodeType');
        $metadata = '';
        if ($nt == \Ximdex\NodeTypes\NodeTypeConstants::XML_DOCUMENT) {
            $metadata = 'metadata_id=""';
        }
        
        // Include the associated semantic tags of the document into the docxap tag.
        $xtags = '';
        if (\Ximdex\Modules\Manager::isEnabled('ximTAGS')) {
            $rtn = new RelTagsNodes();
            $nodeTags = $rtn->getTags($this->nodeID);
            if (!empty($nodeTags)) {
                foreach ($nodeTags as $tag) {
                    $ns = new \Ximdex\Models\Namespaces();
                    $idns = $ns->getNemo($tag['IdNamespace']);
                    $xtags .= $tag['Name'] . ":" . $idns . ",";
                }
            }
            $xtags = substr_replace($xtags, "", -1);
        }
        $xtags = 'xtags = "' . $xtags . '"';
        $docxap = sprintf("<docxap %s %s %s %s %s %s %s %s %s>",
            $layoutTag,
            $this->_langXapAttrib($idLanguage),
            $schemaTag,
            $this->ChannelsXapAttrib($channel),
            $this->_buildDocXapAttribs($idLanguage),
            $this->_getDocXapPropertiesAttrib(true),
            $xtags,
            $metadata,
            NULL
        );
        return $docxap;
    }

    /**
     * @param null $channel
     * @param null $content
     * @param null $onlyDocXap
     * @return null|string
     */
    function GetRenderizedContent($channel = null, $content = null, $onlyDocXap = null)
    {
        $strDoc = new StructuredDocument($this->nodeID);
        if (!($strDoc->get('IdDoc') > 0)) {
            return NULL;
        }
        $documentType = $strDoc->GetDocumentType();
        $idLanguage = $strDoc->GetLanguage();
        $docXapHeader = $this->_getDocXapHeader($channel, $idLanguage, $documentType);
        if ($onlyDocXap) {
            return $docXapHeader;
        }
        $doctypeTag = App::getValue("DoctypeTag");
        $encodingTag = App::getValue("EncodingTag");
        if (is_null($content)) {
            $content = $strDoc->GetContent();
        }
        return ($encodingTag . "\n" . $doctypeTag . "\n\n" .
            $docXapHeader .
            $this->InsertLinkedximletS($idLanguage) . "\n" .
            $content . "\n" .
            "</docxap>\n");
    }

    function DeleteNode()
    {
        $parent = new Node($this->parent->get('IdParent'));
        $st = new StructuredDocument($this->parent->get('IdNode'));
        $dbObj = new \Ximdex\Runtime\Db();
        $query = sprintf("DELETE FROM NodeNameTranslations WHERE IdNode = %s AND IdLanguage = %s",
        $dbObj->sqlEscapeString($parent->get('IdNode')),
        $dbObj->sqlEscapeString($st->get('IdLanguage')));
        $dbObj->execute($query);
        $doc = new StructuredDocument();
        $doc->SetID($this->nodeID);
        if ($doc->HasError()) {
            $this->parent->SetError(5);
            return;
        }
        $doc->DeleteStrDoc();
        if ($doc->HasError()) {
            $this->parent->SetError(5);
        }

        // Deletes dependencies in rel tables
        $depsMngr = new DepsManager();
        $depsMngr->deleteByTarget(DepsManager::XML2XML, $this->parent->get('IdNode'));
        $depsMngr->deleteBySource(DepsManager::XML2XML, $this->parent->get('IdNode'));
        $depsMngr->deleteBySource(DepsManager::NODE2ASSET, $this->parent->get('IdNode'));
        Logger::info('StrDoc dependencies deleted');
    }

    /**
     * @param null $name
     */
    function RenameNode($name = null)
    {
        $doc = new StructuredDocument($this->nodeID);
        $doc->SetName($name);
        $this->updatePath();
    }

    /**
     * @return array
     */
    function GetAllGenerations()
    {
        $result = array();
        $chanList = $this->GetChannels();
        if ($chanList) {
            foreach ($chanList as $chanID) {
                $result[] = array('channel' => $chanID, 'content' => $this->Generate($chanID));
            }
        }
        return $result;
    }

    /**
     * Return true if the specified channel ID is in the node's properties
     * @param $channelID
     * @return bool
     */
    public function HasChannel($channelID)
    {
        $values = $this->GetChannels();
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
     * @return array
     */
    public function GetChannels()
    {
        $channelProperty = new ChannelProperty($this->nodeID);
        $values = $channelProperty->getValues($this->nodeID);
        if ($values === false) {
            return false;
        }
        $res = [];
        foreach ($values as $channel) {
            if ($channel['Checked'] or $channel['Inherited']) {
                $res[] = $channel['Id'];
            }
        }
        return $res;
    }

    /**
     * @param $depth
     * @param $files
     * @param $recurrence
     * @return string
     */
    function ToXml($depth, & $files, $recurrence)
    {
        $xmlBody = parent::ToXML($depth, $files, $recurrence);
        $channelList = $this->GetChannels();
        if (is_array($channelList)) {
            reset($channelList);
            while (list(, $idChannel) = each($channelList)) {
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
            $this->parent->GetParent());
        $this->dbObj->Query($query);
        while (!$this->dbObj->EOF) {
            $idLanguage = $this->dbObj->GetValue('IdLanguage');
            $name = $this->dbObj->GetValue('Name');
            $xmlBody .= sprintf("%s<NodeNameTranslation IdLang=\"%d\">\n", $indexTabs, $idLanguage);
            $xmlBody .= sprintf("%s\t<![CDATA[%s]]>\n", $indexTabs, utf8_encode($name));
            $xmlBody .= sprintf("%s</NodeNameTranslation>\n", $indexTabs);
            $this->dbObj->Next();
        }
        return $xmlBody;
    }

    /**
     * @return string
     */
    function getXmlTail()
    {
        $returnValue = '';
        $query = sprintf("SELECT TargetLink FROM StructuredDocuments WHERE IdDoc = %d", $this->nodeID);
        $this->dbObj->Query($query);
        if ($this->dbObj->numRows == 1) {
            $targetLink = $this->dbObj->GetValue('TargetLink');
            if ((int)$targetLink > 0) {
                $returnValue = sprintf(' targetLink="%d"', $targetLink);
            }
        }
        return $returnValue;
    }

    /**
     * @return bool|string
     */
    function getTemplate()
    {
        $structuredDocument = new StructuredDocument($this->nodeID);
        if ($structuredDocument->get('IdDoc') > 0) {
            return $structuredDocument->get('IdTemplate');
        }
        return false;
    }

    /**
     * @return bool|null|string
     */
    function getLanguage()
    {
        $structuredDocument = new StructuredDocument($this->nodeID);
        $idLanguage = $structuredDocument->get('IdLanguage');
        return $idLanguage > 0 ? $idLanguage : NULL;
    }

    /**
     * @param $idLang
     * @return null|string
     */
    function _langXapAttrib($idLang)
    {
        // Inserting languages
        $outPut2 = NULL;
        $colectible = ' languages="';
        $node = new Node($this->parent->get('IdNode'));
        $idParent = $node->get('IdParent');
        $nodeParent = new Node($idParent);
        $docList[] = $nodeParent->GetChildren();
        foreach ($docList as $docID) {
            foreach ($docID as $docdocID) {
                
                // Getting the language
                $strDoc = new StructuredDocument($docdocID);
                $langID = $strDoc->GetLanguage();
                $lang = new Language($langID);
                $colectible .= $lang->GetIsoName() . ',';
            }
        }
        $colectible = substr($colectible, 0, strlen($colectible) - 1);
        $outPut2 .= $colectible . '"';
        $lang = new Language($idLang);
        $outPut2 .= ' language="' . $lang->GetIsoName() . '"';
        return $outPut2;

    }

    /**
     * @param $idLang
     * @return string
     */
    function _buildDocXapAttribs($idLang)
    {
        return $this->DocXapAttribLevels($idLang);
    }

    /**
     * @param null $channelID
     * @return null|string
     */
    function ChannelsXapAttrib($channelID = null)
    {
        $doc = new StructuredDocument($this->nodeID);
        $channelList = $doc->GetChannels();
        $outPut = NULL;
        if ($channelList) {
            if (in_array($channelID, $channelList)) {
                $channel = new Channel($channelID);
                $outPut = 'channel="' . $channel->GetName() . '"';
                $outPut .= ' extension="' . $channel->GetExtension() . '"';
            } else {
                $outPut = 'channel="" ';
            }
            reset($channelList);
            while (list(, $channelID) = each($channelList)) {
                $channel = new Channel($channelID);
                $channelNames[] = $channel->get('Name');
                $channelDesc[] = $channel->get('Description');
            }
            $outPut .= ' channels="' . implode(",", $channelNames) . '"';
        }
        return $outPut;
    }

    /**
     * @param $channel
     * @return null|string
     */
    private function Generate($channel)
    {
        $nodeid = $this->nodeID;
        $node = new Node($nodeid);
        $dataFactory = new DataFactory($nodeid);
        $version = $dataFactory->GetLastVersionId();
        $data['CHANNEL'] = $channel;
        $transformer = $node->getProperty('Transformer');
        $data['TRANSFORMER'] = $transformer[0];
        $data['DISABLE_CACHE'] = App::getValue("DisableCache");
        $data['NODEID'] = $nodeid;
        if ($node->GetNodeType() == NodeTypeConstants::HTML_DOCUMENT) {
            $process = 'HTMLToPrepared';
        } else {
            $process = 'StrDocToDexT';
        }
        $pipeMng = new PipelineManager();
        $content = $pipeMng->getCacheFromProcessAsContent($version, $process, $data);
        return $content;
    }

    /**
     * @param $langID
     * @param null $sectionId
     * @return string
     */
    function InsertLinkedximletS($langID, $sectionId = null)
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

    /**
     * @param $langID
     * @param null $sectionId
     * @return array
     */
    function getLinkedximletS($langID, $sectionId = null)
    {
        if (is_null($sectionId)) {
            $node = New Node($this->nodeID);
            $sectionId = $node->GetSection();
        }
        $depsMngr = new DepsManager();
        $ximletContainers = $depsMngr->getBySource(DepsManager::SECTION_XIMLET, $sectionId);
        $linkedXimlets = array();
        if (!empty($ximletContainers) > 0) {
            foreach ($ximletContainers as $ximletContaineId) {
                $node = new Node($ximletContaineId);
                $ximlets = $node->GetChildren();
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

    /**
     * @param $nodeID
     * @return string
     */
    function DocXapDynamicAttrib($nodeID)
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

    /**
     * @param $langID
     * @return string
     */
    function DocXapAttribLevels($langID)
    {
        $node = new Node($this->parent->get('IdNode'));
        $parent = new Node($node->get('IdParent'));
        $s = ' nodeid="' . $node->get('IdNode') . '"  parentnodeid="' . $parent->get('IdNode') . '"';
        $s .= ' nodetype-name="' . $node->nodeType->get('Name') . '"  nodetype-id="' . $node->nodeType->get('IdNodeType') . '"';
        $s .= ' document-name="' . $parent->get('Name') . '" alias="' . $parent->GetAliasForLang($langID) . '"';
        $tree = $node->TraverseToRoot();

        // It must exclude from length the node itself, its container, and its folder
        $length = count($tree) - 3;
        
        // the level
        $j = 0;
        for ($i = 1; $i < $length; $i++) {
            $ancestor = new Node($tree[$i]);
            $alias = $node->GetAliasForLang($langID);
            switch ($i) {
                case 1:
                    $s .= ' proyect="' . $ancestor->get('Name') . '"';
                    continue;
                case 2:
                    $s .= ' server="' . $ancestor->get('Name') . '"';
                    continue;
                default:
                    if ($ancestor->nodeType->get('IsSection') == 1) {
                        $j++;
                        $s .= " level$j=\"" . $ancestor->get('Name') . "\" level_name$j=\"" .
                            $ancestor->GetAliasForLang($langID) . "\"\n";
                    }
                    continue;
            }
        }
        return $s;
    }

    /**
     * @return array
     */
    function GetDependencies()
    {
        $nodeDependencies = new NodeDependencies();
        return $nodeDependencies->getByTarget($this->nodeID);
    }

    /**
     * @param $idLanguage
     * @return bool|null|string
     */
    function getChildrenByLanguage($idLanguage)
    {
        $childrens = $this->parent->GetChildren();
        if (is_array($childrens) && !empty($childrens)) {
            foreach ($childrens as $idChildren) {
                $node = new Node($idChildren);
                if ($node->class->getLanguage == $idLanguage) {
                    return $node->get('IdNode');
                }
            }
        }
        return NULL;
    }

    /**
     * @param bool $withInheritance
     * @return string
     */
    function _getDocXapPropertiesAttrib($withInheritance = false)
    {
        $node = new Node($this->nodeID);
        $properties = $node->getAllProperties($withInheritance);
        $docxapPropAttrs = "";
        if (is_array($properties) & count($properties) > 0) {
            foreach ($properties as $idProperty => $propertyValue) {
                $docxapPropAttrs .= 'property_' . $idProperty . '="' . $propertyValue[0] . '" ';
            }
        }
        return $docxapPropAttrs;
    }
    
    /**
     * {@inheritDoc}
     * @see \Ximdex\NodeTypes\Root::GetPublishedPath()
     */
    public function GetPublishedPath($channelID = NULL, $addNodeName = null) {
        if (!$this->parent->GetID()) {
            $error = 'Missing ID for structured document';
            $this->messages->add($error, MSG_TYPE_ERROR);
            Logger::error($error);
            return false;
        }
        $structuredDocument = new StructuredDocument($this->parent->GetID());
        if (!$structuredDocument->get('IdLanguage')) {
            $error = 'Language has not been specified for document: ' . $this->parent->GetNodeName();
            $this->messages->add($error, MSG_TYPE_ERROR);
            Logger::error($error);
            return false;
        }
        if (App::getValue('PublishPathFormat') == App::PREFIX or App::getValue('PublishPathFormat') == App::SUFFIX) {
            $language = new Language($structuredDocument->get("IdLanguage"));
            if (!$language->GetID()) {
                $error = 'Language not found for ID: ' . $structuredDocument->get("IdLanguage");
                $this->messages->add($error, MSG_TYPE_ERROR);
                Logger::error($error);
                return false;
            }
        }
        $path = '';
        switch (App::getValue('PublishPathFormat')) {
            case App::PREFIX:
                $path = parent::GetPublishedPath($channelID, $addNodeName);
                
                // If the language is different than the default server one, the path include its ISO name
                $nodeProperty = new NodeProperty();
                $property = $nodeProperty->getProperty($this->parent->getServer(), NodeProperty::DEFAULTSERVERLANGUAGE);
                if ($property) {
                    if ($language->GetID() != $property[0]) {
                        $path = '/'. $language->get("IsoName") . $path;
                    }
                }
                else {
                    
                    // No default language in prefix mode, always include the language in path
                    $path = '/'. $language->get("IsoName") . $path;
                }
                break;
            case App::SUFFIX:
                $path = parent::GetPublishedPath($channelID);
                if ($addNodeName) {
                    $path .= '/' . $this->GetPublishedNodeName($channelID);
                }
                break;
            default:
                $path = parent::GetPublishedPath($channelID, $addNodeName);
        }
        return str_replace('//', '/', $path);
    }
}