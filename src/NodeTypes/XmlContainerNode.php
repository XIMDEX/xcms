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

use Ximdex\Models\Language;
use Ximdex\Models\Node;
use Ximdex\Models\NodeType;
use Ximdex\Models\StructuredDocument;

class XmlContainerNode extends FolderNode
{
    function RenderizeNode()
    {
        return null;
    }

    /**
     * Get the schema for the current document.
     * Old method. It stay here because backward compatibility.
     * 
     * @return int Schema Id.
     */
    function getVisualTemplate()
    {
        return $this->getIdSchema();
    }

    /**
     * Get the schema for the current document.
     * 
     * @return int Schema Id.
     */
    public function getIdSchema()
    {
        $relTemplateContainer = new \Ximdex\Models\RelTemplateContainer();
        $result = $relTemplateContainer->find("IdTemplate", "IdContainer = %s", array(
            $this->nodeID
        ), MONO);
        if (! $result || ! is_array($result) || ! count($result)) {
            return false;
        }
        return $result[0];
    }

    /**
     * Build a new document and all its language versions.
     * 
     * @param string $name Node name.
     * @param int $parentID Parent node id.
     * @param int $nodeTypeID NodeType id.
     * @param int $stateID state id.
     * @param int $idSchema Schema id.
     * @param array $aliasLangList Array[idLang] = alias
     * @param array $channelList Channel ids array.
     * @param int $nodeMaster
     * @param array $dataChildren Required data.
     */
    function CreateNode($name = null, $parentID = null, $nodeTypeID = null, $stateID = null, $idSchema = null, $aliasLangList = null
        , $channelList = null, $idNodeMaster = null, $dataChildren = null)
    {
        $result = false;
        $reltemplate = new \Ximdex\Models\RelTemplateContainer();
        $reltemplate->createRel($idSchema, $this->nodeID);
        if (is_array($aliasLangList)) {
            foreach ($aliasLangList as $idLanguage => $alias) {
                $result = $this->addLanguageVersion($idLanguage, $alias, $channelList, $dataChildren);
            }
            // $this->buildMetadata($nodeTypeID, $aliasLangList);
        }
        if (! $result) {
            return false;
        }
        if ($idNodeMaster) {
            $this->setNodeMaster($idNodeMaster);
        }
        if ($nodeTypeID == \Ximdex\NodeTypes\NodeTypeConstants::XML_CONTAINER 
            or $nodeTypeID == \Ximdex\NodeTypes\NodeTypeConstants::HTML_CONTAINER) {
            $this->UpdatePath();
        }
        return true;
    }

    private function buildMetadata($idNodeType, $aliases)
    {
        $langs = array();
        foreach ($aliases as $idLang => $alias) {
            $langs[] = $idLang;
        }
        $mm = new \Ximdex\Metadata\MetadataManager($this->nodeID);
        $mm->generateMetadata($langs);
        $mm->updateSystemMetadata();
    }

    /**
     * Add a new language version for the current document
     * 
     * @param int $idLang
     * @param string $alias Description name for the language version. Useful in breadcrum.
     * @param array $channelList Array within idchannels
     * @param array $data Required data to create the language version.
     */
    public function addLanguageVersion($idLang, $alias, $channelList, $data = null)
    {
        $xmldoc = new Node();
        $childrenNodeType = new NodeType();
        
        // TODO: Every container nodetype should implement a getLanguageVersionNodeType.
        // $childrenNodetype = $this->getLanguageVersionNodeType;
        // It would be better than this switch.
        switch ($this->nodeType->GetName()) {
            case 'XmlContainer':
                $childrenNodeType->SetByName('XmlDocument');
                break;
            case 'XimletContainer':
                $childrenNodeType->SetByName('Ximlet');
                break;
            case 'XimPdfDocumentFolder':
                $childrenNodeType->SetByName('XimPdfDocumentLang');
                break;
            case 'MetaDataContainer':
                $childrenNodeType->SetByName('MetaDataDoc');
                break;
            case 'HTMLContainer':
                $childrenNodeType->SetByName('HTMLDocument');
                break;
            default:
                return;
        }
        if ($childrenNodeType->HasError()) {
            $this->parent->SetError(1);
            return false;
        }
        $lang = new Language($idLang);
        if ($lang->HasError()) {
            $this->parent->SetError(1);
            return false;
        }
        $idSchema = $this->getIdSchema();
        $nameDoc = $this->parent->getNodeName() . "-id" . $lang->GetIsoName();
        $idDoc = $xmldoc->CreateNode($nameDoc, $this->nodeID, $childrenNodeType->GetID(), $stateID = null, $idSchema, $idLang, $alias
            , $channelList, $data);
        if ($xmldoc->HasError()) {
            $this->parent->SetError(1);
            return false;
        }
        return $idDoc;
    }

    /**
     * Set the current idNodeMaster
     * 
     * @param int $idNodeMaster
     */
    public function setNodeMaster($idNodeMaster)
    {
        $children = $this->parent->GetChildren();
        foreach ($children as $idChild) {
            $child = new Node($idChild);
            $childLanguage = $child->class->GetLanguage();
            if ($childLanguage != $idNodeMaster) {
                $child = new Node($idChild);
                $child->SetWorkflowMaster($idNodeMaster);
                $strDoc = new StructuredDocument($idChild);
                $strDoc->SetSymLink($idNodeMaster);
            }
        }
    }

    function DeleteNode()
    {
        $templatecontainer = new \Ximdex\Models\RelTemplateContainer();
        $templatecontainer->deleteRel($this->nodeID);
        $mm = new \Ximdex\Metadata\MetadataManager($this->nodeID);
        $mm->deleteMetadata();
    }

    function RenameNode($name = null)
    {
        if (! $name)
            return false;
        $listaDocs = $this->parent->GetChildren();
        if (sizeof($listaDocs) > 0) {
            foreach ($listaDocs as $docID) {
                $node = new Node($docID);
                $strdoc = new StructuredDocument($docID);
                $langId = $strdoc->GetLanguage();
                $lang = new Language($langId);
                $nameDoc = $name . "-id" . $lang->GetIsoName();
                $node->RenameNode($nameDoc);
            }
        }
        $this->updatePath();
        $mm = new \Ximdex\Metadata\MetadataManager($this->nodeID);
        $mm->updateSystemMetadata();
    }

    function GetLanguages()
    {
        $node = new Node($this->nodeID);
        $docList = $node->GetChildren();
        if ($node->HasError()) {
            $this->parent->SetError(1);
            return false;
        }
        foreach ($docList as $docID) {
            $strDoc = new StructuredDocument($docID);
            $langList[] = $strDoc->GetLanguage();
        }
        return $langList;
    }
    
    public function GetChildByLang($langID)
    {
        $node = new Node($this->nodeID);
        $docList = $node->GetChildren();
        if ($node->HasError()) {
            $this->parent->SetError(1);
            return false;
        }
        foreach ($docList as $docID) {
            $strDoc = new StructuredDocument($docID);
            $docLang = $strDoc->GetLanguage();
            if ($docLang == $langID) {
                return $docID;
            }
        }
        return null;
    }

    function ToXml($depth, & $files, $recurrence)
    {
        $xml = '';
        $query = sprintf("SELECT IdTemplate FROM `RelTemplateContainer` WHERE IdContainer = %d", $this->parent->nodeID);
        $this->dbObj->Query($query);
        while (! $this->dbObj->EOF) {
            $idTemplate = $this->dbObj->GetValue('IdTemplate');
            if (! (int) $idTemplate > 0)
                continue;
            $template = new Node($idTemplate);
            $xml .= $template->ToXml($depth, $files, $recurrence);
            $this->dbObj->Next();
        }
        return $xml;
    }
}